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
 * Виджет для формирования интерфейса окна редактирования фотоальбома.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Plugin\ImageAlbum\Widget
 * @since 1.0
 */
class GalleryEditWindow extends \Gm\Panel\Widget\EditWindow
{
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
        $this->form->bodyPadding = 10;
        $this->form->defaults = [
            'labelAlign' => 'right',
            'labelWidth' => 120
        ];
        $this->form->controller = 'gm-plg-image_album-form';
        $this->form->continueTitle = $this->creator->t('Add and continue');
        $this->form->continueMsg = $this->creator->t('To add items to a gallery on this tab, you must first add a gallery. Add and continue?');
        $this->form->bodyPadding = 0;
        $this->form->loadJSONFile('/gallery-form', 'items', [
            '@uploadUrl' => '/' . Gm::getAlias('@match/upload/view/' . $this->getRowID() . '?pid=' . $pluginRowId),
            '@activeTab' => $this->getActiveTabIndex(),
            '@author'    => $author,
            '@pluginId'  => $pluginRowId
        ]);
        $this->form->stateButtons = [
            Form::STATE_UPDATE => [
                'help' => ['component' => 'plugin:' . $this->creator->id, 'subject' => 'gallery-form'],
                'reset', 'save', 'delete', 'cancel'
            ],
            Form::STATE_INSERT => [
                'help' => ['component' => 'plugin:' . $this->creator->id, 'subject' => 'gallery-form'],
                'add', 'cancel'
            ]
        ];
        // маршрутизация
        $this->form->router->pid = $pluginRowId;
        $this->form->router->rules = [
            'update' => '{route}/update/{id}?pid={pid}',
            'delete' => '{route}/delete/{id}?pid={pid}',
            'add'    => '{route}/add?pid={pid}',
            'data'   => '{route}/data/{id}?pid={pid}'
        ];

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $this->title = '#{gallery.title}';
        $this->titleTpl = '#{gallery.titleTpl}';
        $this->width = 700;
        $this->height = 700;
        $this->layout = 'fit';
        $this->resizable = false;
        $this->responsiveConfig = [
            'height < 700' => ['height' => '99%'],
            'width < 700' => ['height' => '99%'],
        ];

        $this
            ->setNamespaceJS('Gm.plg.image_album')
            ->addRequire('Gm.plg.image_album.FormController')
            ->addRequire('Gm.view.IFrame');
    }

    /**
     * Возвращаяет порядковый номер активной вкладки.
     * 
     * @return int
     */
    protected function getActiveTabIndex(): int
    {
        return Gm::$app->request->getQuery('activeTab', 0);
    }
}
