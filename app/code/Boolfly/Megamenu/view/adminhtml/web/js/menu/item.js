/**
 * Record
 *
 * @copyright Copyright © Boolfly. All rights reserved.
 * @author    info@boolfly.com
 * @project   Megamenu
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/dynamic-rows/record'
], function (_, registry, dynamicRowsRecord) {
    'use strict';

    return dynamicRowsRecord.extend({
        defaults: {
            listens: {
                visible: 'setVisible',
                disabled: 'setDisabled',
                recordTemplate: 'onUpdateRecordTemplate',
                recordData: 'setDifferedFromDefault setRecordDataToCache',
                elems: 'checkSpinner',
                changed: 'updateTrigger',
                childElems: 'updateChildElement'
            },
            parentItem: false,
            isChild: false,
            hasChild: false,
            isCollapse: false,
            expandTmpl: 'Boolfly_Megamenu/menu/button/expand',
            collapseTmpl: 'Boolfly_Megamenu/menu/button/collapse',
            childElems: []
        },

        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         *
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this._super()
                .observe(['isChild', 'hasChild', 'parentItem', 'isCollapse'])
                .observe('childElems', []);

            return this;
        },

        /**
         *
         * @param elems
         */
        updateChildElement: function (elems) {
            this.hasChild(elems.length > 0);
            var component;
            elems.forEach(function (elem) {
                component = registry.get(elem.name + '.item.parent_id');
                if (component) {
                    component.value(this.recordId);
                }
            }, this);
        },

        /**
         *
         * @returns {exports}
         */
        toggleChildItems: function () {
            this.isCollapse(!this.isCollapse());
            return this;
        },

        /**
         *
         */
        destroy: function () {
            this.childElems.each(function (elem) {
                elem.destroy(true);
            });

            this._super();
        }
    });
});
