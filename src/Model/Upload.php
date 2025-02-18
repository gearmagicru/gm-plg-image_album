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
use Gm\Mvc\Plugin\Plugin;
use Gm\Stdlib\BaseObject;
use Gm\Uploader\UploadedFile;
use Gm\Filesystem\Filesystem as Fs;

/**
 * Класс загрузки файла фотоальбома.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Model
 * @since 1.0
 */
class Upload extends BaseObject
{
    use Gm\Stdlib\ErrorTrait;

    /**
     * Плагин.
     * 
     * @var Plugin
     */
    public Plugin $plugin;

    /**
     * Идентификатор фотоальбома.
     * 
     * @var int
     */
    public int $galleryId = 0;

    /**
     * Настройки фотоальбома.
     * 
     * @var array
     */
    public array $galleryOptions = [];

    /**
     * Временный путь к загруженным файлам фотоальбома.
     * 
     * @var string
     */
    public string $tempPath = '';

    /**
     * Загруженный файл.
     * 
     * @var UploadedFile|null
     */
    protected ?UploadedFile $file = null;

    /**
     * Выполнить загрузку файла на сервер.
     * 
     * @return bool
     */
    public function run(): bool
    {
        // если каталог не создан
        if (!Fs::makeDirectory($this->tempPath, 0755, true, true)) {
            $this->addError(
                GM_MODE_PRO ? 
                    $this->plugin->t('Unable to load images') :
                    Gm::t('app', 'Unable to create directory "{0}"', [$this->tempPath])                  
            );
            return false;
        }

        // если каталог не существует
        if (!file_exists($this->tempPath)) {
            $this->addError(
                GM_MODE_PRO ? 
                    $this->plugin->t('Unable to load images') :
                    Gm::t('app', 'Parameter passed incorrectly "{0}"', ['galleryTemp'])
            );
            return false;
        }

        /** @var \Gm\Uploader\Uploader $uploader */
        $uploader = Gm::$app->uploader;
        $uploader->setPath($this->tempPath);

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
        ],  $this->galleryOptions);

        /** @var \Gm\Uploader\UploadedImageFile $uploadedFile */
        $this->file = $uploader->getFile('qqfile', 'image') ?: false;
        $this->file->setOptions($options);

        // если невозможно загрузить файл
        if (!$this->file->move()) {
            $this->addError(
                GM_MODE_PRO ? 
                    $this->plugin->t('Unable to load images') :
                    Gm::t('app', $this->file->getErrorMessage())
            );
            return false;
        }
        return true;
    }

    /**
     * Возвращает загруженный файл.
     * 
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
}
