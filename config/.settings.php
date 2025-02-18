<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации настроек плагина.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'pathTemplate'      => '/uploads/g/{year}/{month}/{id}', // шаблон папки фотоальбома
    'watermarkFile'     => '/uploads/img/watermark.png', // файл
    'watermarkOpacity'  => 50, // прозрачность
    'watermarkPosition' => 'center', // положение
    'watermarkOffsetX'  => 0, // смещение по горизонтали
    'watermarkOffsetY'  => 0 // смещение по вертикали
];
