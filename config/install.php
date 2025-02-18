<?php
/**
 * Этот файл является частью виджета веб-приложения GearMagic.
 * 
 * Файл конфигурации установки виджета.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'id'          => 'gm.plg.image_album',
    'ownerId'     => 'gm.be.media_gallery',
    'category'    => 'album',
    'name'        => 'Image album',
    'description' => 'Image album for Media gallery',
    'namespace'   => 'Gm\Plugin\ImageAlbum',
    'path'        => '/gm/gm.plg.image_album',
    'locales'     => ['ru_RU', 'en_GB'],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'GM MS'],
        ['app', 'code' => 'GM CMS'],
        ['app', 'code' => 'GM CRM'],
        ['module', 'id' => 'gm.be.media_gallery']
    ]
];
