/**
 * nestable
 *
 * @copyright Copyright © Boolfly. All rights reserved.
 * @author    info@boolfly.com
 * @project   Megamenu
 */
define([
    'jquery',
    'underscore',
    'uiCollection',
    'uiRegistry',
    'mageUtils',
    'uiLayout'
], function ($, _, Collection, registry, utils, layout) {
    'use strict';

    return Collection.extend({
        defaults: {
            addButton: true,
            visible: true,
            label: '',
            addButtonLabel: 'Add Menu Item',
            newItemLabel: 'New Menu Item',
            required: false,
            template: 'Boolfly_Megamenu/menu/tree',
            templates: {
                item: {
                    parent: '${ $.$data.collection.name }',
                    name: '${ $.$data.index }',
                    dataScope: '${ $.$data.collection.index }.${ $.name }',
                    nodeTemplate: '${ $.parent }.${ $.$data.collection.itemTemplate }'
                }
            },
            links: {
                itemTitle: '${ $.name }.menu_title:value'
            },
            itemTitle: '',
            targetName: null,
            modalMap: '',
            actionName: 'toggleModal',
            itemEditScope: 'item_data_scope',
            childElems: [],
            menuTitle: null,
            elementTmpl: 'Boolfly_Megamenu/menu/item',
            buttonTmpl: 'Boolfly_Megamenu/menu/button',
            additionalClasses: {}
        },

        /**
         * Extends this with defaults and config.
         * Then calls initObservable, iniListenes and extractData methods.
         */
        initialize: function () {
            this._super()
                ._setClasses();

            return this;
        },

        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         *
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this._super()
                .observe([
                    'visible',
                    'itemTitle'
                ])
                .observe('childElems', [])
                .observe({
                    required: !! + this.required
                });

            return this;
        },

        addChild: function (data) {
            var template = this.templates.item,
                child, index, prop;

            index = 20;
            prop  = 20;

            _.extend(this.templates.item, {
                itemId: prop
            });

            child = utils.template(template, {
                collection: this,
                index: index
            });

            layout([child]);

            return this;

        },

        /**
         * Extends 'additionalClasses' object.
         *
         * @returns {Group} Chainable.
         */
        _setClasses: function () {
            var additional = this.additionalClasses,
                classes;

            if (_.isString(additional)) {
                additional = this.additionalClasses.split(' ');
                classes    = this.additionalClasses = {};

                additional.forEach(function (name) {
                    classes[name] = true;
                }, this);
            }

            _.extend(this.additionalClasses, {
                'admin__control-grouped': !this.breakLine,
                'admin__control-fields': this.breakLine,
                required:   this.required,
                _error:     this.error,
                _disabled:  this.disabled
            });

            return this;
        },

        /**
         * Delete record handler.
         *
         * @param {Number} index
         * @param {Number} id
         */
        deleteMenuItem: function (index, id) {
            this.bubble('deleteRecord', index, id);
        },

        /**
         * Edit Menu Item
         */
        editMenuItem: function () {
            var data   = this.source.get(this.dataScope) || {},
                target,
                params = [];
            if (this.targetName) {
                if (!registry.has(this.targetName)) {
                    this.getFromTemplate(this.targetName);
                }
                data[this.itemEditScope] = this.name;
                this.source.set(this.modalMap, data);
                target = registry.async(this.targetName);
                if (target && typeof target === 'function' && this.actionName) {
                    params.unshift(this.actionName);
                    target.apply(target, params);
                }
            }
        }
    });
});
