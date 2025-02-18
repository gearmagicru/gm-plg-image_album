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
use Gm\Uploader\UploadedFile;
use Gm\Plugin\ImageAlbum\Plugin;
use Gm\Panel\Data\Model\FormModel;
use Gm\Filesystem\Filesystem as Fs;

/**
 * Модель данных профиля изображения фотоальбома.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Model
 * @since 1.0
 */
class ItemForm extends FormModel
{
    /**
     * Плагин фотоальбома.
     * 
     * @var Plugin
     */
    public Plugin $plugin;

    /**
     * Идентификатор альбома.
     * 
     * @var int
     */
    public int $galleryId = 0;

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => true,
            'tableName'  => '{{gallery_items}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'],
                ['gallery_id', 'alias' => 'gid'],
                ['index'], // порядковый номер
                ['name'], // название
                ['description'], // описание
                ['author'], // автор
                ['visible'], // отображется
                // медиа файл
                [ // имя загруженного файла
                    'item_filename', 'alias' => 'itemFilename'
                ],
                [ // размер файла
                    'item_filesize', 'alias' => 'itemFilesize'
                ],
                [ // разрешение (только изображение)
                    'item_size', 'alias' => 'itemSize'
                ],
                [ // формат файла: JPG, MP3...
                    'item_format', 'alias' => 'itemFormat'
                ],
                [ // локальный URL-адрес
                    'item_url', 'alias' => 'itemUrl'
                ],
                // эскиз, обложка медиа файл
                [ // имя файла
                    'image_filename', 'alias' => 'imgFilename'
                ],
                [ // размер файла
                    'image_filesize', 'alias' => 'imgFilesize'
                ],
                [ // разрешение (только изображение)
                    'image_size', 'alias' => 'imgSize'
                ],
                [ // формат файла: JPG, MP3...
                    'image_format', 'alias' => 'imgFormat'
                ],
                [ // локальный URL-адрес
                    'image_url', 'alias' => 'imgUrl'
                ]
            ],
            // правила форматирования полей
            'formatterRules' => [
                [['name', 'description', 'author'], 'safe'],
                [['visible'], 'logic']
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
                    $canSave = $this->uploadFile();
                }
            })
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                /** @var \Gm\Panel\Http\Response $response */
                $response = $this->response();
                // всплывающие сообщение
                $response
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                // если запись добавлена или обновлена
                if ($result > 0) {
                    if ($this->galleryId) {
                        // обновить список
                        $controller->cmdReloadGrid('items-grid' . $this->galleryId);
                    }
                }
            })
            ->on(self::EVENT_BEFORE_DELETE, function (&$canDelete) {
                $canDelete = $this->deleteFile();
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
                // если запись удалена
                if ($result > 0) {
                    if ($this->galleryId) {
                        // обновить список
                        $controller->cmdReloadGrid('items-grid' . $this->galleryId);
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            // если добавление
            if (!$this->hasIdentifier()) {
                // указан ли ранее идент. фотоальбома
                if (empty($this->galleryId)) {
                    $this->setError(
                        GM_MODE_PRO ? 
                            $this->plugin->t('Error adding album image') :
                            Gm::t('app', 'Invalid parameter specified "{0}"', ['galleryId'])
                    );
                    return false;
                }

                // корректно ли указан фотоальбом
                $this->gallery = $this->module->getModel('Gallery')->get($this->galleryId);
                if ($this->gallery === null) {
                    $this->setError(
                        GM_MODE_PRO ? 
                            $this->plugin->t('Error adding album image') :
                            Gm::t('app', 'Invalid parameter specified "{0}"', ['gid'])
                    );
                    return false;
                }

                // попытка создать каталог фотоальбома если он не создан ранее
                if (!$this->makeGalleryPath($this->gallery->path)) {
                    $this->setError(
                        GM_MODE_PRO ? 
                            $this->plugin->t('Error adding album image') :
                            $this->plugin->t('Error creating image album folder "{0}"', [$this->gallery->path])
                    );
                    return false;
                }

                /** @var false|UploadedImageFile $file */
                $file = $this->getUploadedFile();
                // проверка загрузки файла
                if ($file === false) {
                    $this->setError('No file selected for upload');
                    return false;
                }

                // если была ошибка загрузки
                if (!$file->hasUpload()) {
                    $this->setError(Gm::t('app', $file->getErrorMessage()));
                    return false;
                }

                // если файл не соответствует указанным параметрам
                if (!$file->validate()) {
                    $this->setError(Gm::t('app', $file->getErrorMessage()));
                    return false;
                }
            }
        }
        return $isValid;
    }

    /**
     * @see UploadForm::getUploadedFile()
     * 
     * @var UploadedImageFile|false
     */
    private UploadedFile|false $uploadedFile;

    /**
     * Возвращает загруженный файл.
     * 
     * @return UploadedFile|false Возвращает значение `false` если была ошибка загрузки.
     */
    public function getUploadedFile(): UploadedFile|false 
    {
        if (!isset($this->uploadedFile)) {
            /** @var \Gm\Uploader\Uploader $uploader */
            $uploader = Gm::$app->uploader;
            $uploader->setPath(Gm::getAlias('@published' . $this->gallery->path));

            /** @var \Gm\Uploader\UploadedFile $uploadedFile */
            $this->uploadedFile = $uploader->getFile('imageFile', 'image') ?: false;
            if ($this->uploadedFile) {
                /** @var array $settings Настройки плагина */
                $settings = $this->plugin->getSettings()->getAll();
                
                /** @var array $options Параметры загрузки */
                $options = array_merge([
                    'uniqueFilename'     => true,
                    'checkFileExtension' => true,
                    'allowedExtensions'  => ['jpg', 'jpeg'],
                    // миниатюра
                    'thumbCreate' => true,
                    'thumbWidth'  => 160,
                    'thumbHeight' => 160,
                    'thumbCrop'   => false,
                    'thumbWatermark' => false,
                    // оригинальное изображение
                    'originalApply'  => true,
                    'originalWidth'  => 1024,
                    'originalHeight' => 1024,
                    'originalCrop'   => false,
                    'originalWatermark' => true,
                    // водяной знак
                    'watermarkFile'     => '@published' . $settings['watermarkFile'],
                    'watermarkOpacity'  => $settings['watermarkOpacity'],
                    'watermarkPosition' => $settings['watermarkPosition'],
                    'watermarkOffsetX'  => $settings['watermarkOffsetX'],
                    'watermarkOffsetY'  => $settings['watermarkOffsetY']
                ], $this->gallery->optionsToArray());
                $this->uploadedFile->setOptions($options);
            }
        }
        return $this->uploadedFile;
    }

    /**
     * @param string $galleryPath
     * 
     * @return bool
     */
    protected function makeGalleryPath(string $galleryPath): bool
    {
        /** @var string $realPath Полный путь к каталогу фотоальбома */
        $galleryPath = Gm::getAlias('@published' . $galleryPath);
        if ($galleryPath === false) return false;

        // создаёт каталог фотоальбома
        if (!Fs::exists($galleryPath)) {
            if (!Fs::makeDirectory($galleryPath, 0755, true)) return false;
        }
        return true;
    }

    /**
     * Процесс подготовки загрузки файла.
     * 
     * @return bool Возвращает значение `false`, если ошибка загрузки файла.
     */
    protected function uploadFile(): bool
    {
        /** @var \Gm\Uploader\UploadedFile|null $file */
        $file = $this->getUploadedFile();
 
        // если файл не загружен
        if (!$file->move()) {
            $this->controller()->errorResponse(
                GM_MODE_PRO ? 
                    $this->plugin->t('Error adding album image') :
                    $file->getErrorMessage()
            );
            return false;
        }

        /** @var array $result */
        $result = $file->getResult();
        // оригинал изображения
        $filename = $result['original']['filename'];
        $filesize = filesize($filename) ?: 0;
        $info = pathinfo($filename);
        $mime = mime_content_type($filename);
        $this->itemFilename = $result['original']['uploaded'];
        $this->itemFilesize = $filesize ? round($filesize / 1024 / 1024, 2) : 0;
        $this->itemSize = $result['original']['size'];
        $this->itemFormat = strtoupper($info['extension']);
        $this->itemMime = $mime ?: null;
        $this->itemUrl = $this->gallery->path . '/' . $info['basename'];
        // миниатюра изображения
        $filename = $result['thumb']['filename'];
        $filesize = filesize($filename) ?: 0;
        $info = pathinfo($filename);
        $mime = mime_content_type($filename);
        $this->imgFilename = $info['basename'];
        $this->imgFilesize = $filesize ? round($filesize / 1024 / 1024, 2) : 0;
        $this->imgSize = $result['thumb']['size'];
        $this->imgFormat = $this->itemFormat;
        $this->imgMime = $mime ?: null;
        $this->imgUrl = $this->gallery->path . '/' . $info['basename'];
        return true;
    }

    /**
     * Удаляет изображения из папки фотоальбома.
     * 
     * @return bool
     */
    protected function deleteFile(): bool
    {
        // изображение
        if ($this->itemFilename) {
            $filename = Gm::getAlias('@published' . $this->itemUrl);
            if (Fs::exists($filename)) {
                if (!Fs::deleteFile($filename)) {
                    $this->controller()->errorResponse(
                        GM_MODE_PRO ? 
                            $this->plugin->t('Error deleting album image') :
                            $this->plugin->t('Error deleting image file "{0}"', [$filename])
                    );
                    return false;
                }
            }
        }
        // миниатюра
        if ($this->imgFilename) {
            $filename = Gm::getAlias('@published' . $this->imgUrl);
            if (Fs::exists($filename)) {
                if (!Fs::deleteFile($filename)) {
                    $this->controller()->errorResponse(
                        GM_MODE_PRO ? 
                            $this->plugin->t('Error deleting album image') :
                            $this->plugin->t('Error deleting image file "{0}"', [$filename])
                    );
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
        parent::processing();

        // для вывода в загаловок окна
        $this->title = $this->name ?: $this->itemFilename;
    }
}
