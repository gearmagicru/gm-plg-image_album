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
use Gm\Panel\Widget\Form;

/**
 * Виджет для формирования интерфейса окна редактирования изображения фотоальбома.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Widget
 * @since 1.0
 */
class ItemEditWindow extends \Gm\Panel\Widget\EditWindow
{
    /**
     * Идентификатор фотоальбома.
     * 
     * @var int
     */
    protected int $galleryId;

    /**
     * {@inheritdoc}
     */
    public array $passParams = ['galleryId'];

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        /** @var int|null $pluginRowId Идент. плагина в базе данных */
        $pluginRowId = $this->creator->getRowId() ?: 0;

        if ($this->isInsertMode()) {
            /** @var \Gm\Panel\User\UserIdentity $identity */
            $identity = Gm::userIdentity();
            $author = $identity->getProfile()->getCallName();
        } else
            $author = '';

        // панель формы (Gm.view.form.Panel GmJS)
        $this->form->autoScroll = true;
        $this->form->loadJSONFile('/' . ($this->isInsertMode() ? 'add' : 'edit') . '-item-form', 'items', [
            '@author' => $author
        ]);
        $this->form->stateButtons = [
            Form::STATE_UPDATE => [
                'help' => ['component' => 'plugin:' . $this->creator->id, 'subject' => 'item-form'],
                'reset', 'save', 'delete', 'cancel'
            ],
            Form::STATE_INSERT => [
                'help' => ['component' => 'plugin:' . $this->creator->id, 'subject' => 'item-form'],
                'add', 'cancel'
            ]
        ];

        if ($this->isInsertMode()) {
            $this->form->bodyPadding = 0;
            // т.к. параметры ("_csrf", "X-Gjax") не передаются через заголовок, 
            // то передаём их через метод POST
            $this->form->items[] = [
                'xtype' => 'hidden',
                'name'  => 'X-Gjax',
                'value' => true
            ];
            $this->form->items[] = [
                'xtype' => 'hidden',
                'name'  => Gm::$app->request->csrfParamName,
                'value' => Gm::$app->request->getCsrfTokenFromHeader()
            ];
            $this->form->items[] = [
                'xtype' => 'hidden',
                'name'  => 'gid',
                'value' => $this->galleryId
            ];

        } else {
            $this->form->bodyPadding = 10;
            $this->form->defaults = [
                'labelAlign' => 'right',
                'labelWidth' => 120
            ];    
        }

        // маршрутизация
        $this->form->router->pid = $pluginRowId;
        $this->form->router->gid = $this->galleryId;
        $this->form->router->route = Gm::alias('@match', '/item');
        $this->form->router->rules = [
            'update' => '{route}/update/{id}?pid={pid}&gid={gid}',
            'delete' => '{route}/delete/{id}?pid={pid}&gid={gid}',
            'add'    => '{route}/add?pid={pid}&gid={gid}',
            'data'   => '{route}/data/{id}?pid={pid}'
        ];

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $this->title = '#{item.title}';
        $this->titleTpl = '#{item.titleTpl}';
        $this->width = 500;
        $this->autoHeight = true;
        $this->layout = 'fit';
        $this->resizable = false;
        $this->responsiveConfig = [
            'height < 500' => ['height' => '99%'],
            'width < 500' => ['height' => '99%'],
        ];
    }
}
