/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/record'
], function (_, dynamicRowsRecord) {
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
                changed: 'updateTrigger'
            },
            isChild: false,
            hasChild: false,
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
                .observe(['isChild', 'hasChild'])
                .observe('childElems', []);

            return this;
        },

    });
});
