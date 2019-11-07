/**
 * Tree
 *
 * @copyright Copyright © 2019 Boolfly. All rights reserved.
 * @author    info@boolfly.com
 * @project   Megamenu
 */
define([
    'jquery',
    'ko',
    'mageUtils',
    'underscore',
    'uiLayout',
    'uiCollection',
    'uiRegistry',
    'mage/translate',
    'Boolfly_Base/js/jquery.nestable'
], function ($, ko, utils, _, layout, uiCollection, registry, $t) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            dataProvider: '',
            defaultRecord: false,
            labels: [],
            recordTemplate: 'record',
            additionalClasses: {},
            visible: true,
            disabled: false,
            fit: false,
            addButton: true,
            addButtonLabel: $t('Add'),
            recordData: [],
            maxPosition: 0,
            deleteValue: false,
            showSpinner: false,
            isDifferedFromDefault: false,
            changed: false,
            map: null,
            deleteProperty: false,
            positionProvider: 'position',
            dataLength: 0,
            insertData: [],
            identificationProperty: 'record_id',
            identificationDRProperty: 'id',
            mappingSettings: {
                enabled: false,
                distinct: true
            },
            titleField: '',
            itemEditScope: '',
            sourceDataModal: '',
            templates: {
                record: {
                    parent: '${ $.$data.collection.name }',
                    name: '${ $.$data.index }',
                    dataScope: '${ $.$data.collection.index }.${ $.name }',
                    nodeTemplate: '${ $.parent }.${ $.$data.collection.recordTemplate }'
                }
            },
            links: {
                recordData: '${ $.provider }:${ $.dataScope }.${ $.index }'
            },
            listens: {
                visible: 'setVisible',
                disabled: 'setDisabled',
                recordTemplate: 'onUpdateRecordTemplate',
                changed: 'updateTrigger',
                recordData: "setToInsertData setRelatedData"
            },
            elementTree: [],
            elementNestable: null,
            relatedData: [],
            currentPage: 1,
            recordDataCache: [],
            structureMenu: {},
            currentIndexRecord: 0,
            menuTmpl: 'Boolfly_Megamenu/menu/li',
            menuButtonTmpl: 'Boolfly_Megamenu/menu/menu-button',
            startIndex: 0
        },

        /**
         * Extends instance with default config, calls initialize of parent
         * class, calls initChildren method, set observe variable.
         * Use parent "track" method - wrapper observe array
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            _.bindAll(
                this,
                'processingDeleteRecord',
                'deleteHandler'
            );

            this.setToInsertData = _.debounce(this.setToInsertData, 200);

            this._super()
                .initChildren()
                .setInitialProperty();
            this.observe('structureMenu', []);

            return this;
        },

        /**
         * Set data from recordData to insertData
         */
        setToInsertData: function () {
            var insertData = [],
                obj;

            if (this.recordData().length && !this.update) {
                _.each(this.recordData(), function (recordData) {
                    if (recordData) {
                        obj                              = {};
                        obj[this.identificationProperty] = recordData[this.identificationProperty];
                        insertData.push(obj);
                    }
                }, this);

                if (insertData.length && this.dataProvider) {
                    this.source.set(this.dataProvider, insertData);
                }
            }
        },

        /**
         * @inheritdoc
         */
        bubble: function (event) {
            if (event === 'deleteRecord' || event === 'update') {
                return false;
            }

            return this._super();
        },


        /**
         * Sets record data to cache
         */
        setRecordDataToCache: function (data) {
            this.recordDataCache = data;
        },

        /**
         * Show Menu Button
         *
         * @returns {boolean}
         */
        showMenuButton: function () {
            var isHasChildren = false;
            this.elems().each(function (elem) {
                if (elem.childElems().length > 0) {
                    isHasChildren = true;
                }
            });

            return isHasChildren;
        },

        /**
         * Expand All Item
         */
        expandAll: function () {
            this.toggleAllItems(false);
        },

        /**
         * Collapse All Item
         */
        collapseAll: function () {
            this.toggleAllItems(true);
        },

        /**
         * Toggle All Items
         *
         * @param isCollapse
         */
        toggleAllItems: function (isCollapse) {
            this.elems().each(function (elem) {
                elem.isCollapse(isCollapse);
                elem.childElems().each(function (child) {
                    child.isCollapse(isCollapse);
                })
            });
        },

        /**
         * Init Event
         *
         * @param element
         */
        initEventNestable: function (element) {
            this.elementNestable = $(element);
            this.elementNestable.nestable({
                callback: $.proxy(this.updateMenuTree, this)
            });
        },

        /**
         * Save Menu Item
         */
        saveMenuItem: function () {
            var itemScope,
                target,
                modalData = this.source.get(this.sourceDataModal);
            if (modalData && modalData[this.itemEditScope]) {
                itemScope = modalData[this.itemEditScope];
                delete modalData[this.itemEditScope];
                target = registry.get(itemScope + '.' + this.titleField);
                if (target && modalData[this.titleField]) {
                    target.value(modalData[this.titleField]);
                }
            }
        },
        
        /**
         *
         * @param data
         * @param level
         */
        updatePositionMenuItem: function (data, level) {
            var elem, positionComponent, levelComponent, position = 0;
            level = level || 0;
            level++;
            if (Array.isArray(data)) {
                data.each(function (item) {
                    elem   = registry.get(item.component);
                    positionComponent = registry.get(item.component + '.item.position');
                    levelComponent = registry.get(item.component + '.item.level');
                    if (elem) {
                        elem.position = position;
                    }
                    if (positionComponent) {
                        positionComponent.set('value', position);
                    }
                    if (levelComponent) {
                        levelComponent.set('value', level);
                    }
                    position++;

                    if (item.children && item.children.length > 0) {
                        this.updatePositionMenuItem(item.children, level);
                    }
                }, this);
            }
        },

        /**
         * Update menu after drap & drop
         *
         * @param root
         * @param element
         */
        updateMenuTree: function (root, element) {
            var elem, parentIndex, oldParentItem;
            elem = this.getElementTree(element.data('index'));
            this.updatePositionMenuItem(this.elementNestable.nestable('serialize'), 0);
            if (elem) {
                parentIndex   = this.getNewParentIndex(element);
                oldParentItem = elem.parentItem();
                if (parentIndex === false && oldParentItem === false) {
                    this.elems(this._sort());
                    return;
                }
                //Remove html move by nestable
                element.remove();
                if (!oldParentItem && parentIndex) {
                    //Move from root to child
                    utils.remove(this._elems, elem);
                } else if (oldParentItem) {
                    this.removeChildElems(elem, oldParentItem);
                    if (!parentIndex) {
                        var itemParent = registry.get(elem.name + '.item.parent_id');
                        if (itemParent) {
                            itemParent.value('');
                        }
                        //Move from child to root
                        utils.add(this._elems, elem);
                        elem.parentItem(false);
                    }
                }
                this.updateParentAndChild(elem, parentIndex);
                this.updateStructureMenu(elem.recordId, parentIndex);
                this._updateCollection();
            }
        },

        /**
         * Remove Child Element
         *
         * @param element
         * @param parent
         */
        removeChildElems: function (element, parent) {
            var childElems = parent.childElems();
            if (childElems.length > 0) {
                parent.childElems(_.filter(childElems, function (elem) {
                    return element.index !== elem.index;
                }, this));
            }
        },

        /**
         *
         * @param recordId
         * @param parentRecordId
         */
        updateStructureMenu: function (recordId, parentRecordId) {
            var structureMenu = this.structureMenu();
            if (parentRecordId === false) {
                delete structureMenu[recordId];
            } else {
                structureMenu[recordId] = parentRecordId;
            }
            this.structureMenu(structureMenu);
        },

        /**
         * Get Parent Index
         *
         * @param element
         * @returns {boolean}
         */
        getNewParentIndex: function (element) {
            if (element.parent().parent('li').length > 0) {
                return element.parent().parent('li').data('index');
            }

            return false;
        },

        /**
         * Inserts provided component into 'elems' array at a specified position.
         * @private
         *
         * @param {Object} elem - Element to insert.
         */
        _insert: function (elem) {
            if (!this.isChildElement(elem)) {
                elem.position = this.elems().length;
                this._super(elem);
            }
        },

        /**
         * Check is Child Item
         *
         * @param elem
         * @returns {boolean}
         */
        isChildElement: function (elem) {
            if (typeof elem.childElems === 'function') {
                var recordId = this.source.get(elem.dataScope + '.' + this.identificationProperty);
                var parentId = this.source.get(elem.dataScope + '.parent_id');
                this.elementTree[recordId] = elem;
                if (parentId) {
                    this.updateStructureMenu(recordId, parentId);
                    this.initElement(elem);
                    this.updateParentAndChild(elem, parentId);
                    return true;
                }
            }

            return false;
        },

        /**
         *
         * @param {Object} elem
         * @param {*} parentRecordId
         */
        updateParentAndChild: function (elem, parentRecordId) {
            if (parentRecordId) {
                var parentElement = this.getElementTree(parentRecordId);
                if (parentElement) {
                    var childElems = parentElement.childElems();
                    elem.parentItem(parentElement);
                    if (childElems.length < 1 || !this.checkChildElement(childElems, elem.index)) {
                        childElems.push(elem);
                        parentElement.childElems(this._sort(childElems));
                    }
                }
            }
        },

        /**
         *
         * @param elems
         * @param index
         * @returns {*}
         */
        checkChildElement: function (elems, index) {
            return _.find(elems, function (ele) {
                return ele.index === index;
            })
        },

        /**
         *
         * @param index
         * @returns {{Object}}
         */
        getElementTree: function (index) {
            return this.elementTree[index];
        },

        /**
         * Contains old data with new
         *
         * @param {Array} data
         *
         * @returns {Array} changed data
         */
        getNewData: function (data) {
            var changes = [],
                tmpObj  = {};

            if (data.length !== this.relatedData.length) {
                _.each(data, function (obj) {
                    tmpObj[this.identificationProperty] = obj[this.identificationProperty];

                    if (!_.findWhere(this.relatedData, tmpObj)) {
                        changes.push(obj);
                    }
                }, this);
            }

            return changes;
        },

        /** @inheritdoc */
        destroy: function () {
            if (this.dnd()) {
                this.dnd().destroy();
            }
            this._super();
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
                    'recordData',
                    'visible',
                    'disabled',
                    'labels',
                    'showSpinner',
                    'isDifferedFromDefault',
                    'changed',
                    'insertData'
                ]);

            return this;
        },

        /**
         * @inheritdoc
         */
        initElement: function (elem) {
            this._super();
            elem.on({
                'deleteRecord': this.deleteHandler
            });

            return this;
        },

        /**
         *
         * @returns {exports}
         * @private
         */
        _updateCollection: function () {
            this._super();
            this.elems(this._sort());
            return this;
        },

        /**
         * Handler for deleteRecord event
         *
         * @param {Number|String} index - element index
         * @param {Number|String} id
         */
        deleteHandler: function (index, id) {
            this.processingDeleteRecord(index, id);
        },

        /**
         * Set initial property to records data
         *
         * @returns {Object} Chainable.
         */
        setInitialProperty: function () {
            if (_.isArray(this.recordData())) {
                this.recordData.each(function (data, index) {
                    this.source.set(this.dataScope + '.' + this.index + '.' + index + '.initialize', true);
                }, this);
            }

            return this;
        },

        /**
         * Sets value to object by string path
         *
         * @param {Object} obj
         * @param {Array|String} path
         * @param {*} value
         */
        setValueByPath: function (obj, path, value) {
            var prop;

            if (_.isString(path)) {
                path = path.split('.');
            }

            if (path.length - 1) {
                prop = obj[path[0]];
                path.splice(0, 1);
                this.setValueByPath(prop, path, value);
            } else if (path.length && obj) {
                obj[path[0]] = value;
            }
        },

        /**
         * Returns elements which changed self state
         *
         * @param {Array} array - data array
         * @param {Array} changed - array with changed elements
         * @returns {Array} changed - array with changed elements
         */
        getChangedElems: function (array, changed) {
            changed = changed || [];

            array.forEach(function (elem) {
                if (_.isFunction(elem.elems)) {
                    this.getChangedElems(elem.elems(), changed);
                } else if (_.isFunction(elem.hasChanged) && elem.hasChanged()) {
                    changed.push(elem);
                }
            }, this);

            return changed;
        },


        /**
         * Triggers update event
         *
         * @param {Boolean} val
         */
        updateTrigger: function (val) {
            this.trigger('update', val);
        },

        /**
         * Returns component state
         */
        hasChanged: function () {
            return this.changed();
        },

        /**
         * Create header template
         *
         * @param {Object} prop - instance obj
         *
         * @returns {Object} Chainable.
         */
        createHeaderTemplate: function (prop) {
            var visible  = prop.visible !== false,
                disabled = _.isUndefined(prop.disabled) ? this.disabled() : prop.disabled;

            return {
                visible: ko.observable(visible),
                disabled: ko.observable(disabled)
            };
        },

        /**
         * Set max element position
         *
         * @param {Number} position - element position
         * @param {Object} elem - instance
         */
        setMaxPosition: function (position, elem) {
            if (position || position === 0) {
                this.checkMaxPosition(position);
                this.sort(position, elem);
            } else {
                this.maxPosition += 1;
            }
        },

        /**
         * Sort element by position
         *
         * @param {Number} position - element position
         * @param {Object} elem - instance
         */
        sort: function (position, elem) {
            var sorted,
                updatedCollection;
            if (this.elems().length < 1) {
                return false;
            }

            if (this.elems().filter(function (el) {
                    return el.position || el.position === 0;
            }).length !== this.getChildItems().length) {
                return false;
            }

            sorted = this.elems().sort(function (propOne, propTwo) {
                return ~~propOne.position - ~~propTwo.position;
            });

            updatedCollection = this.updatePosition(sorted, position, elem.name);
            this.elems(updatedCollection);
        },

        /**
         * Checking loader visibility
         *
         * @param {Array} elems
         */
        checkSpinner: function (elems) {
            this.showSpinner(!(!this.recordData().length || elems && elems.length === this.getChildItems().length));
        },

        /**
         * Reinit record data in order to remove deleted values
         *
         * @return void
         */
        reinitRecordData: function () {
            this.recordData(
                _.filter(this.recordData(), function (elem) {
                    return elem && elem[this.deleteProperty] !== this.deleteValue;
                }, this)
            );
        },

        /**
         * Get items to rendering on current page
         *
         * @returns {Array} data
         */
        getChildItems: function () {
            return this.relatedData;
        },

        /**
         * @inheritDoc
         */
        setRelatedData: function (data) {
            this.relatedData = this.deleteProperty ? _.filter(data, function (elem) {
                    return elem && elem[this.deleteProperty] !== this.deleteValue;
            }, this) : data;
        },

        /**
         * Processing pages before addChild
         *
         * @param {Object} ctx - element context
         * @param {Number|String} index - element index
         * @param {Number|String} prop - additional property to element
         */
        processingAddChild: function (ctx, index, prop) {
            this.bubble('addChild', false);

            this.addChild(ctx, index, prop);
        },

        /**
         * Processing pages before deleteRecord
         *
         * @param {Number|String} index - element index
         * @param {Number|String} recordId
         */
        processingDeleteRecord: function (index, recordId) {
            this.deleteRecord(index, recordId, true);
        },

        /**
         * Check dependency and set position to elements
         *
         * @param {Array} collection - elems
         * @param {Number} position - current position
         * @param {String} elemName - element name
         *
         * @returns {Array} collection
         */
        updatePosition: function (collection, position, elemName) {
            var curPos,
                parsePosition = ~~position,
                result        = _.filter(collection, function (record) {
                    return ~~record.position === parsePosition;
                });

            if (result[1]) {
                curPos                                           = parsePosition + 1;
                result[0].name === elemName ? result[1].position = curPos : result[0].position = curPos;
                this.updatePosition(collection, curPos);
            }

            return collection;
        },

        /**
         * Check max elements position and set if max
         *
         * @param {Number} position - current position
         */
        checkMaxPosition: function (position) {
            var max = 0,
                pos;

            this.elems.each(function (record) {
                pos             = ~~record.position;
                pos > max ? max = pos : false;
            });

            max < position ? max = position : false;
            this.maxPosition     = max;
        },

        /**
         * Remove and set new max position
         */
        removeMaxPosition: function () {
            this.maxPosition = 0;
            this.elems.each(function (record) {
                this.maxPosition < record.position ? this.maxPosition = ~~record.position : false;
            }, this);
        },

        /**
         * Update record template and rerender elems
         *
         * @param {String} recordName - record name
         */
        onUpdateRecordTemplate: function (recordName) {
            if (recordName) {
                this.recordTemplate = recordName;
                this.reload();
            }
        },

        /**
         * Delete record
         *
         * @param {Number} index - row index
         * @param {Number} recordId - recordId
         * @param {Boolean} update
         *
         */
        deleteRecord: function (index, recordId, update) {
            var recordInstance,
                tmpObj = {},
                recordData,
                recordsData;
            tmpObj[this.identificationProperty] = recordId;
            if (this.deleteProperty) {
                recordsData    = this.recordData();
                recordInstance = this.getElementTree(recordId);
                if (recordInstance) {
                    this.deleteChildrenRecord(recordId, recordsData);
                    recordInstance.destroy();
                    recordData = _.findWhere(recordsData, tmpObj);
                    if (recordData) {
                        index = _.indexOf(recordsData, recordData);
                        recordsData[index][this.deleteProperty] = this.deleteValue;
                        this.recordData(recordsData);
                    }
                    delete this.elementTree[recordId];
                    if (update) {
                        this.updateParent(recordId);
                        this._updateCollection();
                        this.reinitRecordData();
                    }
                }
            }
        },

        /**
         *
         * @param recordId
         */
        updateParent: function (recordId) {
            var structureMenu = this.structureMenu();
            if (structureMenu[recordId]) {
                var childElems, parent, parentRecordId = structureMenu[recordId];
                parent                              = this.getElementTree(parentRecordId);
                childElems                          = _.filter(parent.childElems(), function (item) {
                    return item.recordId !== recordId
                });
                parent.childElems(childElems);
            }
        },

        /**
         *
         * @param recordId
         * @param recordsData
         * @returns {exports}
         */
        deleteChildrenRecord: function (recordId, recordsData) {
            var children, tmpObj = {},
                recordData;
            tmpObj[this.identificationProperty] = recordId;
            recordData = _.findWhere(recordsData, tmpObj);
            if (recordData && recordData.menu_children) {
                children = recordData.menu_children;
                children.forEach(function (childIndex) {
                    this.deleteRecord(childIndex, childIndex, false);
                }, this);
            }

            return this;
        },

        /**
         * Get data object by some property
         *
         * @param {Number} id - element id
         * @param {String} prop - property
         */
        _getDataByProp: function (id, prop) {
            prop = prop || this.identificationProperty;

            return _.reject(this.getChildItems(), function (recordData) {
                return recordData[prop].toString() === id.toString();
            }, this);
        },

        /**
         * Sort elems by position property
         */
        _sort: function (elems) {
            if (!elems) {
                elems = this.elems();
            }
            return elems.sort(function (propOne, propTwo) {
                return ~~propOne.position - ~~propTwo.position;
            });
        },

        /**
         * Rerender dynamic-rows elems
         */
        reload: function () {
            this.clear();
            this.initChildren();
        },

        /**
         * Destroy all dynamic-rows elems
         *
         * @returns {Object} Chainable.
         */
        clear: function () {
            this.destroyChildren();

            return this;
        },

        /**
         * Reset data to initial value.
         * Call method reset on child elements.
         */
        reset: function () {
            var elems = this.elems();

            _.each(elems, function (elem) {
                if (_.isFunction(elem.reset)) {
                    elem.reset();
                }
            });
        },

        /**
         * Set classes
         *
         * @param {Object} data
         *
         * @returns {Object} Classes
         */
        setClasses: function (data) {
            var additional;

            if (_.isString(data.additionalClasses)) {
                additional             = data.additionalClasses.split(' ');
                data.additionalClasses = {};

                additional.forEach(function (name) {
                    data.additionalClasses[name] = true;
                });
            }

            if (!data.additionalClasses) {
                data.additionalClasses = {};
            }

            _.extend(data.additionalClasses, {
                '_fit': data.fit,
                '_required': data.required,
                '_error': data.error,
                '_empty': !this.elems().length,
                '_no-header': this.columnsHeaderAfterRender || this.collapsibleHeader
            });

            return data.additionalClasses;
        },

        /**
         * Initialize children
         *
         * @returns {Object} Chainable.
         */
        initChildren: function () {
            // this.showSpinner(true);
            this.getChildItems().forEach(function (data, index) {
                this.addChild(data, false, data[this.identificationProperty]);
            }, this);

            return this;
        },

        /**
         * Set visibility to dynamic-rows child
         *
         * @param {Boolean} state
         */
        setVisible: function (state) {
            this.elems.each(function (record) {
                record.setVisible(state);
            }, this);
        },

        /**
         * Set disabled property to dynamic-rows child
         *
         * @param {Boolean} state
         */
        setDisabled: function (state) {
            this.elems.each(function (record) {
                record.setDisabled(state);
            }, this);
        },

        /**
         * Add child components
         *
         * @param {Object} data - component data
         * @param {Number} index - record(row) index
         * @param {Number|String} prop - custom identify property
         *
         * @returns {Object} Chainable.
         */
        addChild: function (data, index, prop) {
            var template = this.templates.record,
                child;


            // index = index || _.isNumber(index) ? index : this.getNextIndex();
            index = index || _.isNumber(index) ? index : this.currentIndexRecord;
            prop = prop || _.isNumber(prop) ? prop : this.getNextRecordId();

            _.extend(this.templates.record, {
                recordId: prop
            });

            child = utils.template(template, {
                collection: this,
                index: index
            });
            this.currentIndexRecord++;
            layout([child]);

            return this;
        },

        getNextRecordId: function () {
            var timestamp = ~~(Date.now() / 1000);
            return  Math.floor(Math.random() * Math.floor(1000)) + '_' + timestamp;
        },

        /**
         * Restore value to default
         */
        restoreToDefault: function () {
            this.recordData(utils.copy(this.default));
            this.reload();
        },

        /**
         * Update whether value differs from default value
         */
        setDifferedFromDefault: function () {
            var recordData = utils.copy(this.recordData());

            Array.isArray(recordData) && recordData.forEach(function (item) {
                delete item['record_id'];
            });

            this.isDifferedFromDefault(!_.isEqual(recordData, this.default));
        }
    });
});
