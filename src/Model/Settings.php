<?php
/**
 * Виджет веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Plugin\ImageAlbum\Model;

use Gm\Panel\Data\Model\WidgetSettingsModel;

/**
 * Модель настроек плагина.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Model
 * @since 1.0
 */
class Settings extends WidgetSettingsModel
{
    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'pathTemplate'      => 'pathTemplate', // шаблон создания папки
            'watermarkFile'     => 'watermarkFile', // файл
            'watermarkOpacity'  => 'watermarkOpacity', // прозрачность
            'watermarkPosition' => 'watermarkPosition', // положение 
            'watermarkOffsetX'  => 'watermarkOffsetX', // смещение по горизонтали
            'watermarkOffsetY'  => 'watermarkOffsetY', // смещение по вертикали
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'pathTemplate'      => 'Template',
            'watermarkFile'     => 'Filename', 
            'watermarkOpacity'  => 'Opacity',
            'watermarkPosition' => 'Position',
            'watermarkOffsetX'  => 'Offset X',
            'watermarkOffsetY'  => 'Offset Y',
        ];
    }
}