<?php
/**
 * Виджет веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Plugin\ImageAlbum\Settings;

use Gm;
use Gm\Panel\Widget\SettingsWindow;

/**
 * Настройки плагина.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Settings
 * @since 1.0
 */
class Settings extends SettingsWindow
{
    /**
     * {@inheritdoc}
     * 
     * Т.к. виджет вызывает {@see \Gm\Backend\Marketplace\PluginManager}, то
     * необходимо указать свой путь к ресурсам (иначе, URL-путь будет указан 
     * относительно менеджера).
     */
    public array $css = [
        '@module::/gm/gm.plg.image_album/assets/css/settings.css'
    ];

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        $this->responsiveConfig = [
            'height < 570' => ['height' => '99%'],
            'width < 570' => ['width' => '99%'],
        ];
        $this->width = 570;
        $this->height = 570;
        $this->resizable = false;
        $this->form->autoScroll = true;
        $this->form->defaults = [
            'labelWidth' => 200,
            'labelAlign' => 'right'
        ];
        $this->form->items = [
            [
                'xtype' => 'fieldset',
                'title' => '#Album image folder template',
                'items' => [
                    [
                        'xtype'      => 'textfield',
                        'name'       => 'pathTemplate',
                        'fieldLabel' => '#Template',
                        'labelWidth' => 70,
                        'labelAlign' => 'right',
                        'anchor'     => '100%',
                        'value'      => '{year}/{month}/{day}/{id}',
                        'allowBlank' => true
                    ],
                    [
                        'xtype' => 'label',
                        'cls'   => 'gm-plg-image_album__info-tpl',
                        'html'  => [
                            '#Symbol in template',
                            '#{Year} - year number, 2 digits',
                            '#{year} - full numeric representation of the year, at least 4 digits',
                            '#{Month} - serial number of the month without a leading zero',
                            '#{month} - serial number of the month with a leading zero',
                            '#{Day} - day of the month without a leading zero',
                            '#{day} - day of the month, 2 digits with leading zero',
                            '#{id} - image album identifier'
                        ]
                    ]
                ]
            ],
            [
                'xtype'    => 'fieldset',
                'title'    => '#Watermark',
                'defaults' => [
                    'labelWidth' => 200,
                    'labelAlign' => 'right'   
                ],
                'items' => [
                    [
                        'id'         => $this->creator->viewId('form__watermark'),
                        'xtype'      => 'textfield',
                        'name'       => 'watermarkFile',
                        'fieldLabel' => '#Filename',
                        'tooltip'    => '#Filename watermak',
                        'anchor'     => '100%',
                        'triggers'   => [
                            'browse' => [
                                'cls'         => 'g-form__field-trigger g-form__field-trigger_browse',
                                'handler'     => 'onTriggerWidget',
                                'handlerArgs' => [
                                    'route'  => Gm::getAlias('@backend/mediafiles/dialog'),
                                    'params' => [
                                        // идент. поля, которое получит результат
                                        'applyTo' => $this->creator->viewId('form__watermark'), 
                                        // псевдоним диалога
                                        'alias' => 'image',
                                    ]
                                ]
                            ]
                        ],
                        'allowBlank' => false
                    ],
                    [
                        'xtype'      => 'sliderfield',
                        'name'       => 'watermarkOpacity',
                        'fieldLabel' => '#Opacity',
                        'increment'  => 1,
                        'minValue'   => 0,
                        'maxValue'   => 100,
                        'anchor'     => '100%'
                    ],
                    [
                        'xtype'      => 'combo',
                        'name'       => 'watermarkPosition',
                        'fieldLabel' => '#Position',
                        'store'      => [
                            'fields' => ['id', 'name'],
                            'data'   => [
                                ['id' => 'left|bottom', 'name' => '#Left bottom'],
                                ['id' => 'left|west', 'name' => '#Left center'],
                                ['id' => 'left|top', 'name' => '#Left top'],
                                ['id' => 'right|top', 'name' => '#Right top'],
                                ['id' => 'right|east', 'name' => '#Right center'],
                                ['id' => 'right|bottom', 'name' => '#Right bottom'],
                                ['id' => 'center', 'name' => '#Center'],
                                ['id' => 'bottom|center', 'name' => '#Bottom center'],
                                ['id' => 'top|center', 'name' => '#Top center']
                            ]
                        ],
                        'editable'     => false,
                        'queryMode'    => 'local',
                        'displayField' => 'name',
                        'valueField'   => 'id',
                        'allowBlank'   => false
                    ],
                    [
                        'xtype'      => 'numberfield',
                        'fieldLabel' => '#Offset X',
                        'name'       => 'watermarkOffsetX',
                        'width'      => 350,
                        'allowBlank' => true
                    ],
                    [
                        'xtype'      => 'numberfield',
                        'fieldLabel' => '#Offset Y',
                        'name'       => 'watermarkOffsetY',
                        'width'      => 350,
                        'allowBlank' => true
                    ]
                ]
            ]
        ];
    }
}