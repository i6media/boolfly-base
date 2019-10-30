/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
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
                currentPage: 'changePage',
                elems: 'checkSpinner',
                changed: 'updateTrigger',
                childElems: 'updateChildElement'
            },
            parentItem: false,
            isChild: false,
            hasChild: false,
            isCollapse: false,
            expandTmpl: 'Boolfly_Megamenu/menu/expand',
            collapseTmpl: 'Boolfly_Megamenu/menu/collapse',
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
            var component, children = [];
            elems.forEach(function (elem) {
                children.push(elem.index);
            }, this);
            component = registry.get(this.name + '.item.menu_children');
            if (component) {
                component.value(children);
            }
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
