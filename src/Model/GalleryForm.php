<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Plugin\ImageAlbum\Model;

use Gm;
use Gm\Uploader\PathTemplate;
use Gm\Panel\Data\Model\FormModel;
use Gm\Filesystem\Filesystem as Fs;
use Gm\Plugin\ImageAlbum\Plugin;

/**
 * Модель данных профиля фотоальбома.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Model
 * @since 1.0
 */
class GalleryForm extends FormModel
{
    /**
     * Плагин фотоальбома.
     * 
     * @var Plugin
     */
    public Plugin $plugin;

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => true,
            'tableName'  => '{{gallery}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'],
                [
                    'plugin_id',
                    'alias' => 'pid'
                ],
                [
                    'name', 
                    'label' => 'Name'
                ],
                [
                    'description', 
                    'label' => 'Description'
                ],
                [
                    'published', 
                    'label' => 'Published'
                ],
                [
                    'cover', 
                    'label' => 'Cover'
                ],
                [
                    'author', 
                    'label' => 'Author'
                ],
                [
                    'path', 
                    'label' => 'Path'
                ],
                [
                    'options',
                    'label' => 'Options'
                ]
            ],
            'dependencies' => [
                'delete' => [
                    '{{gallery_items}}' => ['gallery_id' => 'id']
                ]
            ],
            // правила форматирования полей
            'formatterRules' => [
                [['name', 'description', 'author'], 'safe'],
                [['published'], 'logic'],
                [
                    'options', 
                    'json' => [
                        'merge' => [
                            'thumbWidth'        => 0,
                            'thumbHeight'       => 0,
                            'thumbCrop'         => false,
                            'thumbWatermark'    => false,
                            'originalWidth'     => 0,
                            'originalHeight'    => 0,
                            'originalCrop'      => false,
                            'originalWatermark' => false
                        ], 
                        'format' => [
                            [['thumbCrop', 'thumbWatermark', 'originalCrop', 'originalWatermark'], 'type' => ['int']]
                        ]
                    ]
                ]
            ],
            // правила валидации полей
            'validationRules' => [
                [['name'], 'notEmpty']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_BEFORE_SAVE, function ($isInsert, &$canSave) {
                if ($isInsert) {
                    // определяем путь к изображениям фотоальбома
                    $canSave = $this->definePath();
                }
            })
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                /** @var \Gm\Panel\Http\Response $response */
                $response = $this->response();

                if ($isInsert) {
                    /** @var int $activeTab Активная вкладка формы */
                    $activeTab = Gm::$app->request->getPost('activeTab', -1, 'int');
                    /** @var int $pluginId Идентификатор плагина */
                    $pluginId = Gm::$app->request->getPost('pid', 0, 'int');
                    if ($activeTab !== -1) {
                        $response
                            ->meta
                                ->cmdLoadWidget('@backend/media-gallery/form/view/' . $result . '?pid=' . $pluginId . '&activeTab=' . $activeTab);
                    } else
                        // обновить список
                        $controller->cmdReloadGrid();
                } else {
                    if ($this->moveFiles()) {
                        // обновить список
                        $controller->cmdReloadGrid();
                    } else {
                        $message['message'] = $this->plugin->t('Error saving image album files');
                        $message['type'] = 'error';
                    }
                }

                // всплывающие сообщение
                $response
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            })
            ->on(self::EVENT_BEFORE_DELETE, function (&$canDelete) {
                $canDelete = $this->deleteFiles();
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                /** @var \Gm\Panel\Http\Response $response */
                $response = $this->response();
                // всплывающие сообщение
                $response
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * Возвращает значение атрибута "options" элементу интерфейса формы.
     * 
     * @param null|array $value
     * 
     * @return null|array
     */
    public function outOptions(string|array|null $value): ?array
    {
        if ($value) {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }
            foreach ($value as $key => $val) {
                $this->attributes["options[$key]"] = $val;
            }
        }
        return null;
    }

    /**
     * Определяет путь к папке фотоальбома.
     * 
     * @return bool
     */
    protected function definePath(): bool
    {
        $id = $this->getNextId();
        if (empty($id)) {
            $this->controller()->errorResponse(
                GM_MODE_PRO ? 
                    $this->plugin->t('Error adding image album') :
                    Gm::t('app', 'Invalid parameter specified "{0}"', ['id[next]'])
            );
            return false;
        }

        /** @var Gm\Config\Config $settings */
        $settings = $this->plugin->getSettings();
        /** @var string|null $pathTemplate Шаблон папки фотоальбома */
        $pathTemplate = $settings->pathTemplate;
        if (empty($pathTemplate)) {
            $this->controller()->errorResponse(
                GM_MODE_PRO ? 
                    $this->plugin->t('Error adding image album') :
                    Gm::t('app', 'Invalid parameter specified "{0}"', ['pathTemplate'])
            );
            return false;
        }

        /** @var string $path */
        $path = (new PathTemplate($pathTemplate, ['id' => $id]))->render();
        if (empty($path)) {
            $this->controller()->errorResponse(
                GM_MODE_PRO ? 
                    $this->plugin->t('Error adding image album') :
                    Gm::t('app', 'Invalid parameter specified "{0}"', ['path'])
            );
            return false;
        }

        $this->path = '/' . $path;
        return true;
    }

    /**
     * Удаляет изображения из папки фотоальбома.
     * 
     * @return bool
     */
    protected function deleteFiles(): bool
    {
        if ($this->path) {
            /** @var string $realPath Полный путь к папке фотоальбома */
            $realPath = Gm::getAlias('@published' . $this->path);
            if (!Fs::deleteDirectory($realPath)) {
                $this->controller()->errorResponse(
                    GM_MODE_PRO ? 
                        $this->plugin->t('Error deleting image album') :
                        $this->plugin->t('Error deleting image album folder "{0}"', [$this->path])
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Перемещает изображения в папку фотоальбома.
     * 
     * @return true
     */
    protected function moveFiles(): bool
    {
        /** @var array|null $tempFiles Временные загруженные файлы изображений */
        $tempFiles = $this->module->storageGet('tempFiles');
        if (empty($tempFiles)) return true;

        /** @var string|null $tempPath Временный путь с загруженными изображениями*/
        $tempPath = $this->module->storageGet('tempPath');
        if (empty($tempPath)) return true;

        $this->module->storageRemove('tempPath');

        // если каталог не существует
        if (!file_exists($tempPath)) return true;

        /** @var \Gm\Panel\User\UserIdentity $identity */
        $identity = Gm::userIdentity();
        $author = $identity->getProfile()->getCallName();
        // поля аудита
        $createdDate = Gm::$app->db->makeDateTime(Gm::$app->dataTimeZone);
        $createdUser = $identity->getId();

        /** @var \Gm\Backend\MediaGallery\Model\Item $item */
        $item = $this->module->getModel('Item');
        /** @var int $index Порядковы номер последнего элемента */
        $index = $item->getLastIndex($this->id) + 1;
        foreach ($tempFiles as $file) {
            $item->index = $index++;
            $item->galleryId = $this->id;
            $item->author = $author;
            $item->visible = 1;
            // оригинал изображения
            $filename = $file['original']['filename'];
            $filesize = filesize($filename) ?: 0;
            $info = pathinfo($filename);
            $mime = mime_content_type($filename);
            $item->itemFilename = $file['original']['uploaded'];
            $item->itemFilesize = $filesize ? round($filesize / 1024 / 1024, 2) : 0;
            $item->itemSize = $file['original']['size'];
            $item->itemFormat = strtoupper($info['extension']);
            $item->itemMime = $mime ?: null;
            $item->itemUrl = $this->path . '/' . $info['basename'];
            // миниатюра изображения
            $filename = $file['thumb']['filename'];
            $filesize = filesize($filename) ?: 0;
            $info = pathinfo($filename);
            $mime = mime_content_type($filename);
            $item->imgFilename = $info['basename'];
            $item->imgFilesize = $filesize ? round($filesize / 1024 / 1024, 2) : 0;
            $item->imgSize = $file['thumb']['size'];
            $item->imgFormat = $item->itemFormat;
            $item->imgMime = $mime ?: null;
            $item->imgUrl = $this->path . '/' . $info['basename'];
            // атрибуты аудита
            $item->createdDate = $createdDate;
            $item->createdUser = $createdUser;

            if ($item->save() === false) {
                $this->controller()->errorResponse(
                    GM_MODE_PRO ? 
                        $this->plugin->t('Error saving image album files') :
                        'Unable to save album image'
                );
                return false;
            }
        }

        /** @var string $realPath Полный путь к папке фотоальбома */
        $realPath = Gm::getAlias('@published' . $this->path);
        if ($realPath === false) {
            $this->controller()->errorResponse(
                GM_MODE_PRO ? 
                    $this->plugin->t('Error saving image album files') :
                    Gm::t('app', 'Invalid parameter specified "{0}"', ['path'])
            );
        }

        // создаёт каталог фотоальбома
        if (!Fs::exists($realPath)) {
            Fs::makeDirectory($realPath, 0755, true);
        }

        Fs::copyDirectory($tempPath, $realPath);
        Fs::deleteDirectory($tempPath);
        return true;
    }
}
