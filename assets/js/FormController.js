/*!
 * Контроллер формы добавления фотоальбома.
 * Плагин "Фотоальбом".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.plg.image_album.FormController', {
    extend: 'Gm.view.form.PanelController',
    alias: 'controller.gm-plg-image_album-form',

    /**
     * @param {Ext.tab.Panel} tabPanel
     * @param {newCard} Ext.Component
     * @param {oldCard} Ext.Component
     * @param {eOpts} Object
     */
    onTabChange: function (tabPanel, newCard, oldCard, eOpts) {
        let isUploadTab = Ext.isDefined(newCard.uploadTab) ? newCard.uploadTab : false;
        if (isUploadTab) {
            let form = tabPanel.up('form'), 
                controller = this;
            // если добавление
            if (form.router.id === 0)  {
                tabPanel.setActiveTab(oldCard);
                Ext.Msg.show({
                    title: form.continueTitle,
                    message: form.continueMsg,
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.QUESTION,
                    fn: function(btn) {
                        if (btn === 'yes') {
                            form.isValid() ? controller.doFormAdd(form, {submit: {params: {activeTab: 2}}}) : tabPanel.setActiveTab(oldCard);
                        }
                    }
                });
            }
        }
    }
});
