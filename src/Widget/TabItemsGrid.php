<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Plugin\ImageAlbum\Widget;

use Gm;
use Gm\Panel\Helper\ExtGrid;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;

/**
 * Виджет для формирования интерфейса вкладки с сеткой данных фотоальбомов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Widget
 * @since 1.0
 */
class TabItemsGrid extends \Gm\Panel\Widget\TabGrid
{
    /**
     * {@inheritdoc}
     */
    public array $passParams = ['gallery'];

    /**
     * {@inheritdoc}
     */
    public array $css = ['/grid-images.css'];

    /**
     * Атрибуты фотоальбома.
     * 
     * @see ItemsGrid::init()
     * 
     * @var array
     */
    protected array $gallery;

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // вкладка
        $this->setViewID('items-tab' . $this->gallery['id'], false); // items-tab => gm-media-gallery-items-tab
        $this->title = $this->creator->t('Image album "{0}"', [$this->gallery['name']]);
        $this->tooltip['title'] = $this->title;

        // столбцы (Gm.view.grid.Grid.columns GmJS)
        $this->grid->columns = [
            ExtGrid::columnAction(),
            [
                'text'      => 'ID',
                'tooltip'   => '#Image identifier',
                'dataIndex' => 'id',
                'filter'    => ['type' => 'numeric'],
                'hidden'    => true,
                'width'     => 70
            ],
            [
                'text'      => '№',
                'tooltip'   => '#Sequence number in the list',
                'dataIndex' => 'index',
                'filter'    => ['type' => 'numeric'],
                'sortable'  => true,
                'width'     => 60
            ],
            [
                'text'    => ExtGrid::columnInfoIcon($this->creator->t('Name')),
                'cellTip' => HtmlGrid::tags([
                    HtmlGrid::tplIf(
                        'name',
                        HtmlGrid::header('{name:ellipsis(50)}'),
                        HtmlGrid::header('{itemFilename:ellipsis(50)}')
                    ),
                    HtmlGrid::tplIf(
                        'imgFilename', 
                        HtmlGrid::tag('div', '', [
                            'class' => 'gm-plg-image_album__celltip-preview', 
                            'style' => 'background-image: url({itemUrl})'
                        ]), 
                        ''
                    ),
                    HtmlGrid::tag('fieldset', [
                        HtmlGrid::legend($this->creator->t('Image')),
                        HtmlGrid::fieldLabel($this->creator->t('Format'), '{itemFormat}'),
                        HtmlGrid::fieldLabel($this->creator->t('File size (Mb)'), '{itemFilesize}'),
                        HtmlGrid::tplIf(
                            'imgSize',
                            HtmlGrid::fieldLabel($this->creator->t('Size (px)'), '{itemSize}'),
                            ''
                        )
                    ]),
                    HtmlGrid::tag('fieldset', [
                        HtmlGrid::legend($this->creator->t('Thumb')),
                        HtmlGrid::fieldLabel($this->creator->t('Format'), '{imgFormat}'),
                        HtmlGrid::fieldLabel($this->creator->t('File size (Mb)'), '{imgFilesize}'),
                        HtmlGrid::tplIf(
                            'imgSize',
                            HtmlGrid::fieldLabel($this->creator->t('Size (px)'), '{imgSize}'),
                            ''
                        )
                    ])
                ]),
                'dataIndex' => 'name',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'width'     => 200
            ],
            [
                'text'      => '#Description',
                'dataIndex' => 'description',
                'cellTip'   => '{description}',
                'filter'    => ['type' => 'string'],
                'width'     => 200,
                'hidden'    => true
            ],
            [
                'text'    => '#Image',
                'columns' => [
                    [
                        'text'    => ExtGrid::columnInfoIcon($this->creator->t('Filename')),
                        'cellTip' => HtmlGrid::tags([
                            HtmlGrid::header('{itemFilename:ellipsis(50)}'),
                            HtmlGrid::tplIf(
                                'imgFilename', 
                                HtmlGrid::tag('div', '', [
                                    'class' => 'gm-plg-image_album__celltip-preview', 
                                    'style' => 'background-image: url({itemUrl})'
                                ]), 
                                ''
                            ),
                            HtmlGrid::fieldLabel($this->creator->t('Format'), '{itemFormat}'),
                            HtmlGrid::fieldLabel($this->creator->t('File size (Mb)'), '{itemFilesize}'),
                            HtmlGrid::tplIf(
                                'imgSize',
                                HtmlGrid::fieldLabel($this->creator->t('Size (px)'), '{itemSize}'),
                                ''
                            )
                        ]),
                        'dataIndex' => 'itemFilename',
                        'filter'    => ['type' => 'string'],
                        'width'     => 200
                    ],
                    [
                        'text'      => '#Format',
                        'tooltip'   => '#Image format',
                        'dataIndex' => 'itemFormat',
                        'filter'    => ['type' => 'string'],
                        'width'     => 80
                    ],
                    [
                        'text'      => '#Size (Mb)',
                        'tooltip'   => '#File size (Mb)',
                        'dataIndex' => 'itemFilesize',
                        'filter'    => ['type' => 'string'],
                        'width'     => 110
                    ],
                    [
                        'text'      => '#Size (px)',
                        'tooltip'   => '#Image size in pixels',
                        'dataIndex' => 'itemSize',
                        'filter'    => ['type' => 'string'],
                        'width'     => 110
                    ]
                ]
            ],
            [
                'text'    => '#Thumb',
                'columns' => [
                    [
                        'text'    => ExtGrid::columnInfoIcon($this->creator->t('Filename')),
                        'cellTip' => HtmlGrid::tags([
                            HtmlGrid::header('{imgFilename:ellipsis(50)}'),
                            HtmlGrid::tplIf(
                                'imgFilename', 
                                HtmlGrid::tag('div', '', [
                                    'class' => 'gm-plg-image_album__celltip-preview', 
                                    'style' => 'background-image: url({imgUrl})'
                                ]), 
                                ''
                            ),
                            HtmlGrid::fieldLabel($this->creator->t('Format'), '{imgFormat}'),
                            HtmlGrid::fieldLabel($this->creator->t('File size (Mb)'), '{imgFilesize}'),
                            HtmlGrid::tplIf(
                                'imgSize',
                                HtmlGrid::fieldLabel($this->creator->t('Size (px)'), '{imgSize}'),
                                ''
                            )
                        ]),
                        'dataIndex' => 'imgFilename',
                        'filter'    => ['type' => 'string'],
                        'width'     => 200
                    ],
                    [
                        'text'      => '#Size (px)',
                        'tooltip'   => '#Thumbnail image size in pixels',
                        'dataIndex' => 'imgSize',
                        'filter'    => ['type' => 'string'],
                        'width'     => 110
                    ]
                ]
            ],
            [
                'text'        => ExtGrid::columnIcon('g-icon-m_visible', 'svg'),
                'xtype'       => 'g-gridcolumn-switch',
                'tooltip'     => '#The image is displayed in the image album',
                'selector'    => 'grid',
                'collectData' => ['name'],
                'dataIndex'   => 'visible',
                'filter'      => ['type' => 'boolean']
            ]
        ];

        // панель инструментов (Gm.view.grid.Grid.tbar GmJS)
        $this->grid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit' => [
                    'items' => [
                        // инструмент "Добавить"
                        'add' => [
                            'tooltip'     => '#Adding a new image',
                            'iconCls'     => 'g-icon-svg gm-plg-image_album__icon-img-add',
                            'handlerArgs' => ['route' => Gm::alias('@match', '/item?pid='. $this->gallery['pluginId'] . '&gid=' . $this->gallery['id'])],
                            'caching'     => false
                        ],
                        // инструмент "Добавить"
                        /*
                        ExtGrid::button([
                            'text'        => Gm::t(BACKEND, 'Add'),
                            'tooltip'     => '#Adding a new images',
                            'iconCls'     => 'g-icon-svg gm-plg-image_album__icon-imgs-add',
                            'handlerArgs' => ['route' => Gm::alias('@match', '/item?pid='. $this->gallery['pluginId'] . '&gid=' . $this->gallery['id'])],
                            'handler'     => 'loadWidget'
                        ]),
                        */
                        // инструмент "Удалить"
                        'delete' => [
                            'iconCls' => 'g-icon-svg gm-plg-image_album__icon-img-delete',
                            'tooltip' => '#Deleting selected images'
                        ],
                        // инструмент "Очистить"
                        'cleanup' => [
                            'tooltip' => '#Deleting all images'
                        ],
                        '-',
                        'edit',
                        'select',
                        '-',
                        'refresh'
                    ]
                ],
                'columns',
                'search' => [
                    'items' => [
                        // инструмент "Справка"
                        'help' => [
                            'component' => 'plugin:' . $this->creator->id,
                            'subject'   => 'items-grid'
                        ],
                        'search'
                    ]
                ]
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Grid.popupMenu GmJS)
        $this->grid->popupMenu = [
            'cls'        => 'g-gridcolumn-popupmenu',
            'titleAlign' => 'center',
            'items'      => [
                [
                    'text'        => '#Edit',
                    'iconCls'     => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handlerArgs' => [
                          'route'   => Gm::alias('@match', '/item/view/{id}?pid='. $this->gallery['pluginId'] . '&gid=' . $this->gallery['id']),
                          'pattern' => 'grid.popupMenu.activeRecord'
                      ],
                      'handler' => 'loadWidget'
                ]
            ]
        ];

        $this->grid->setViewID('items-grid' . $this->gallery['id'], false); // items-grid => gm-media-gallery-items-grid
        // 2-й клик на строке сетки
        $this->grid->rowDblClickConfig = [
            'allow' => true,
            'route' => Gm::alias('@match', '/item/view/{id}?pid='. $this->gallery['pluginId'] . '&gid=' . $this->gallery['id'])
        ];
        // сортировка сетки по умолчанию
        $this->grid->sorters = [
           ['property' => 'name', 'direction' => 'ASC']
        ];
        // количество строк в сетке
        $this->grid->store->pageSize = 50;
        // поле аудита записи
        $this->grid->logField = 'header';
        // плагины сетки
        $this->grid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $this->grid->bodyCls = 'g-grid_background';
        $this->grid->router->setAll([
            'rules' => [
                'clear'      => '{route}/clear?gid=' . $this->gallery['id'],
                'delete'     => '{route}/delete?gid=' . $this->gallery['id'],
                'data'       => '{route}/data?gid=' . $this->gallery['id'],
                'deleteRow'  => '{route}/delete/{id}?gid=' . $this->gallery['id'],
                'updateRow'  => '{route}/update/{id}'
            ],
            'route' => Gm::alias('@backend', '/media-gallery/items')
        ]);

        // панель навигации (Gm.view.navigator.Info GmJS)
        $this->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::tplIf(
                'name',
                HtmlNav::header('{name:ellipsis(50)}'),
                HtmlNav::header('{itemFilename:ellipsis(50)}')
            ),
            ['div', '{description}', ['align' => 'center']],
            HtmlNav::tplIf(
                'imgFilename', 
                HtmlNav::tag('div', '', [
                    'class' => 'gm-plg-image_album__celltip-preview', 
                    'style' => 'background-image: url({itemUrl})'
                ]), 
                ''
            ),
            HtmlNav::tag('fieldset', [
                HtmlNav::legend($this->creator->t('Image')),
                HtmlNav::fieldLabel($this->creator->t('Format'), '{itemFormat}'),
                HtmlNav::fieldLabel($this->creator->t('File size (Mb)'), '{itemFilesize}'),
                HtmlNav::tplIf(
                    'imgSize',
                    HtmlNav::fieldLabel($this->creator->t('Size (px)'), '{itemSize}'),
                    ''
                )
            ]),
            HtmlNav::tag('fieldset', [
                HtmlNav::legend($this->creator->t('Thumb')),
                HtmlNav::fieldLabel($this->creator->t('Format'), '{imgFormat}'),
                HtmlNav::fieldLabel($this->creator->t('File size (Mb)'), '{imgFilesize}'),
                HtmlNav::tplIf(
                    'imgSize',
                    HtmlNav::fieldLabel($this->creator->t('Size (px)'), '{imgSize}'),
                    ''
                )
            ]),
            HtmlNav::fieldLabel(
                $this->creator->t('Visible'),
                HtmlNav::tplChecked('visible==1')
            ),
            ['fieldset',
                [
                    HtmlNav::widgetButton(
                        $this->creator->t('Edit'),
                        ['route' => Gm::alias('@match', '/form/view/{id}'), 'long' => true],
                        ['title' => $this->creator->t('Edit')]
                    )
                ]
            ]
        ]);

        $this
            ->addRequire('Gm.view.grid.column.Switch');
    }
}
