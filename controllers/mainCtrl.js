spacialistApp.controller('mainCtrl', ['$rootScope', '$scope', 'scopeService', 'httpPostFactory', 'httpGetFactory', 'httpPostPromise', 'httpGetPromise', 'modalService', 'userService', 'literatureService', '$uibModal', '$auth', '$state', '$http', 'modalFactory', 'moduleHelper', '$timeout', function($rootScope, $scope, scopeService, httpPostFactory, httpGetFactory, httpPostPromise, httpGetPromise, modalService, userService, literatureService, $uibModal, $auth, $state, $http, modalFactory, moduleHelper, $timeout) {
    $scope.markerChoices = scopeService.markerChoices = {};
    $scope.dimensionUnits = [
        'nm', 'µm', 'mm', 'cm', 'dm', 'm', 'km'
    ];
    $scope.literature = literatureService.literature;
    $scope.currentUser = userService.currentUser;
    $scope.can = userService.can;

    var getContexts = function() {
        httpGetFactory('api/context/get', function(callback) {
            var ctxts = [];
            var ctxtRefs = {};
            angular.forEach(callback, function(value, key) {
                var index = value.index;
                var title = value.title;
                if (typeof ctxtRefs[index] === 'undefined') {
                    ctxtRefs[index] = [];
                    ctxts.push({
                        title: title,
                        index: index,
                        type: value.type,
                        ctid: value.ctid
                    });
                }
                if (value.ctid !== null || value.aid !== null || value.val !== null || value.datatype !== null) {
                    ctxtRefs[index].push({
                        aid: value.aid,
                        val: value.val,
                        ctid: value.ctid,
                        datatype: value.datatype
                    });
                }
            });
            var dt = new Date();
            scopeService.ctxts = ctxts;
            scopeService.ctxtRefs = ctxtRefs;
            if (typeof $scope.choices === 'undefined') $scope.choices = [];
            scopeService.choices = $scope.choices;
            $scope.dateOptions = {
                showWeeks: false,
                maxDate: dt
            };
            $scope.date = {
                opened: false
            };
        });
    };

    var getArtifacts = function() {
        httpGetFactory('api/context/artifacts/get', function(callback) {
            var artifacts = [];
            var artiRefs = [];
            angular.forEach(callback, function(value, key) {
                var index = value.index;
                var title = value.title;
                if (typeof artiRefs[index] === 'undefined') {
                    artiRefs[index] = [];
                    artifacts.push({
                        title: title,
                        index: index,
                        type: value.type,
                        ctid: value.ctid
                    });
                }
                if (value.ctid !== null || value.aid !== null || value.val !== null || value.datatype !== null) {
                    artiRefs[index].push({
                        aid: value.aid,
                        val: value.val,
                        ctid: value.ctid,
                        datatype: value.datatype
                    });
                }
            });
            scopeService.artifacts = artifacts;
            scopeService.artiRefs = artiRefs;
        });
    };

    var getMarkerChoices = function() {
        httpGetFactory('api/context/getChoices', function(callback) {
            for(var i=0; i<callback.length; i++) {
                var value = callback[i];
                var index = value.aid + "_";
                if(typeof value.oid != 'undefined') index += value.oid;
                if (value.datatype == 'string-sc' || value.datatype == 'string-mc') {
                    if(value.choices !== null) {
                        scopeService.markerChoices[index] = value.choices;
                    }
                } else if (value.datatype == 'epoch') {
                    scopeService.markerChoices[index] = value.choices;
                }
            }
        });
    };

    var getStoredQueries = function() {
        httpGetFactory('api/analysis/queries/getAll', function(queries) {
            console.log(queries);
            $rootScope.storedQueries = queries;
        });
    };

    var updateInformations = function() {
        getContexts();
        getArtifacts();
        getMarkerChoices();
        getStoredQueries();
    };

    $scope.updateInformations = function() {
        updateInformations();
    };

    updateInformations();

    var createModalHelper = function($itemScope, elemType, copyPosition) {
        var parent = $itemScope.parent;
        var selection = [];
        var msg = '';
        if(elemType == 'context') {
            selection = scopeService.ctxts.slice();
            msg = 'Neuen Kontext anlegen';
        } else if(elemType == 'find') {
            selection = scopeService.artifacts.slice();
            msg = 'Neuen Fund anlegen';
        }
        modalFactory.createModal(parent.name, msg, selection, function(name, type) {
            var elem = {
                name: name,
                ctid: type.ctid,
                root_cid: parent.id,
                reclevel: parent.reclevel + 1,
                typeid: type.type,
                typename: type.index,
                typelabel: type.title,
                data: [],
                children: []
            };
            var hasPos = typeof parent.lat != 'undefined' && typeof parent.lng != 'undefined' && parent.lat !== null && parent.lng !== null;
            var formData = new FormData();
            formData.append('name', name);
            formData.append('ctid', type.ctid);
            if(typeof parent.id != 'undefined') formData.append('root_cid', parent.id);
            if(hasPos && copyPosition) {
                formData.append('lat', parent.lat);
                formData.append('lng', parent.lng);
                elem.lat = parent.lat;
                elem.lng = parent.lng;
            }
            httpPostFactory('api/context/set', formData, function(newElem) {
                elem.id = newElem.cid;
                parent.children.push(elem);
                if(hasPos) addMarker(elem);
                $scope.setCurrentElement(elem, $scope.currentElement);
                $itemScope.expand();
            });
        });
    };

    $scope.newElementContextMenu = [
        [
            function($itemScope, $event, modelValue, text, $li) {
                return 'Optionen für ' + $itemScope.parent.name;
            },
            function($itemScope, $event, modelValue, text, $li) {
            },
            function() { return false; }
        ],
        null,
        ['<span class="fa fa-fw fa-plus-circle fa-light fa-green"></span> Neuer Fund', function($itemScope, $event, modelValue, text, $li) {
            createModalHelper($itemScope, 'find', false);
        }, function($itemScope) {
            return $itemScope.parent.typeid === 0;
        }],
        ['<span class="fa fa-fw fa-plus-circle fa-light fa-green"></span> Neuer Kontext', function($itemScope, $event, modelValue, text, $li) {
            createModalHelper($itemScope, 'context', false);
        }, function($itemScope) {
            return $itemScope.parent.typeid === 0;
        }],
        null,
        ['<span class="fa fa-fw fa-clone fa-light fa-green"></span> Element duplizieren', function($itemScope, $event, modelValue, text, $li) {
            var parent = $itemScope.parent;
            var id = parent.id;
            httpGetFactory('api/context/duplicate/' + id, function(newElem) {
                var copy = newElem.obj;
                var elem = {
                    id: copy.id,
                    name: copy.name,
                    ctid: copy.ctid,
                    root_cid: parent.root_cid,
                    reclevel: parent.reclevel,
                    typeid: parent.typeid,
                    typename: parent.typename,
                    typelabel: parent.typelabel,
                    icon: copy.icon,
                    color: copy.color,
                    lat: copy.lat,
                    lng: copy.lng,
                    data: copy.data,
                    children: []
                };
                $itemScope.$parent.$parent.$modelValue.push(elem);
                addMarker(elem);
                $scope.setCurrentElement(elem, $scope.currentElement);
            });
        }],
        null,
        ['<span class="fa fa-fw fa-trash-o fa-light fa-red"></span> Löschen', function($itemScope, $event, modelValue, text, $li) {
            modalFactory.deleteModal($itemScope.parent.name, function() {
                deleteElement($itemScope.parent, function() {
                    $itemScope.remove();
                });
            }, 'Wenn Sie dieses Element löschen, werden auch alle Kind-Elemente gelöscht!');
        }]
    ];

    $scope.layerTwo = {
        activeTab: 'map',
        imageTab: {}
    };
    scopeService.layerTwo = $scope.layerTwo;

    $scope.setActiveTab = function(tabId) {
        $scope.layerTwo.activeTab = tabId;
    };

    scopeService.createNewContext = function(data) {
        $scope.createNewContext(data);
    };

    $scope.createNewContext = function(data) {
        defaults = {
            name: 'Neuer Top-Kontext',
            reclevel: -1,
            children: scopeService.contextList
        };
        var parent = defaults;
        angular.extend(parent, data);
        createModalHelper({
            parent: parent,
            expand: function() {}
        }, 'context', true);
    };

    $scope.getContextList = function() {
        $scope.getContextListStarted = true;
        $scope.testingElement = {};
        httpGetFactory('api/context/getRecursive', function(contextList) {
            $scope.contextList = scopeService.contextList = contextList;
            $scope.getContextListStarted = false;
            displayMarkers($scope.contextList);
        });
    };

    var displayMarkers = function(contextList) {
        if(!moduleHelper.controllerExists('mapCtrl')) return;
        scopeService.displayMarkers(contextList);
    };

    var addMarker = function(elem) {
        if(!moduleHelper.controllerExists('mapCtrl')) return;
        scopeService.addMarker(elem);
    };

    var setMarker = function(currentElement, focus) {
        var name = currentElement.name.replace(/-/, '');
        if(typeof scopeService.markers[name] != 'undefined') scopeService.markers[name].focus = focus;
    };

    var loadLinkedImages = function(currentElement) {
        if(!moduleHelper.controllerExists('imageCtrl')) return;
        if(typeof scopeService.getImagesForContext == 'undefined') return;
        scopeService.getImagesForContext(currentElement.id);
    };

    $rootScope.$on('unsetCurrentElement', function(event, args) {
        $scope.unsetCurrentElement();
    });

    $scope.unsetCurrentElement = function() {
        if(typeof $scope.currentElement == 'undefined') return;
        setMarker($scope.currentElement, false);
        $scope.currentElementData = undefined;
        $scope.currentElementFields = undefined;
        $scope.currentElement = undefined;
    };

    $rootScope.$on('setCurrentElement', function(event, args) {
        $scope.setCurrentElement(args.source, args.target);
    });

    $scope.setCurrentElement = function(target, elem) {
        $scope.unsetCurrentElement();
        if(typeof elem != 'undefined' && elem.id == target.id) {
            return;
        }
        elem = target;
        console.log(elem);
        if(elem.typeid === 0) { //context
            elem.fields = scopeService.ctxtRefs[elem.typename].slice();
        } else if(elem.typeid == 1) { //find
            elem.fields = scopeService.artiRefs[elem.typename].slice();
        }
        var data = {};
        data = parseData(elem.data);
        $scope.currentElementData = data;
        $scope.currentElementFields = elem.fields;
        $scope.currentElement = {
            id: elem.id,
            name: elem.name,
            root_cid: elem.root_cid || -1,
            typeLabel: elem.typelabel,
            typeId: elem.typeid,
            ctid: elem.ctid
        };
        setMarker($scope.currentElement, true);
        loadLinkedImages($scope.currentElement);
    };

    var parseData = function(data) {
        var parsedData = {};
        for(var i=0; i<data.length; i++) {
            var value = data[i];
            //console.log(value.toSource());
            var index = value.attribute_id + '_' + (value.o_id || '');
            var posIndex = index + 'pos';
            var val = value.str_val;
            var dType = value.datatype;
            parsedData[posIndex] = value.possibility || 100;
            if(dType == 'list') {
                if(typeof parsedData[index] == 'undefined') parsedData[index] = [];
                parsedData[index].push({
                    name: val
                });
            } else if(dType == 'string-sc') {
                parsedData[index] = value.val;
            } else if(dType == 'string-mc') {
                if(typeof parsedData[index] == 'undefined') parsedData[index] = [];
                parsedData[index].push(value.val);
            } else if(dType == 'dimension') {
                if(typeof value.val != 'undefined') parsedData[index] = JSON.parse(value.val);
            } else if(dType == 'epoch') {
                if(typeof value.val != 'undefined') parsedData[index] = JSON.parse(value.val);
            } else {
                parsedData[index] = val;
            }
        }
        return parsedData;
    };

    var updateElementData = function(elem) {
        updateElementDataHelper(elem, scopeService.contextList);
    };

    var updateElementDataHelper = function(elem, children) {
        if(typeof children == 'undefined') return;
        for(var i=0; i<children.length; i++) {
            var child = children[i];
            if(child.id == elem.id) {
                child.data = elem.data;
                if(child.name != elem.name) {
                    console.log(child.name);
                    console.log(elem.name);
                    $scope.renameMarker(child.name, elem.name);
                    child.name = elem.name;
                    setMarker(elem, true);
                }
                break;
            }
            updateElementDataHelper(elem, child.children);
        }
    };

    $scope.storeElement = function(elem, data) {
        var parsedData = [];
        angular.forEach(data, function(value, key) {
            if(key != 'name' && !key.endsWith('pos')) {
                var attr = {};
                attr.key = key;
                attr.value = value;
                parsedData.push(attr);
            }
        });
        elem.data = parsedData;
        var promise = storeElement(elem);
        promise.then(function(newRealId){
            elem.data = newRealId.data;
            updateElementData(elem);
        });
    };

    /**
     * Stores a single element of the context tree in the database
     * @return: returns a promise which returns the ID of the newly inserted context
     */
    var storeElement = function(elem) {
        console.log("store context " + elem.name);
        var root_cid = elem.root_cid;
        var formData = new FormData();
        formData.append('name', elem.name);
        formData.append('root_cid', root_cid);
        formData.append('ctid', elem.ctid);
        if(typeof elem.id !== 'undefined' && elem.id != -1) {
            formData.append('realId', elem.id);
        }
        for(var i=0; i<elem.data.length; i++) {
            var d = elem.data[i];
            var currValue = '';
            if (typeof d.value === 'object') {
                currValue = angular.toJson(d.value);
            } else {
                currValue = d.value;
            }
            formData.append(d.key, currValue);
        }
        var promise = httpPostPromise.getData('api/context/set', formData);
        return promise;
    };

    $scope.deleteElement = function(elem) {
        modalFactory.deleteModal(elem.name, function() {
            deleteElement(elem, function() {
                //$itemScope.remove(); TODO remove from list
            });
        }, 'Wenn Sie dieses Element löschen, werden auch alle Kind-Elemente gelöscht!');
    };

    var deleteElement = function(elem, onSuccess) {
        console.log("Removing element " + elem.name + " with ID " + elem.id);
        httpGetFactory('api/context/delete/' + elem.id, function(callback) { onSuccess(); });
    };

    /**
     * Opens a modal window which allows the user to add/delete sources from a literature list for a particular attribute.
     * One has to pass the field name `fieldname` and the attribute id `fieldid` as parameters.
     */
    $scope.openSourceModal = function(fieldname, fieldid, currentVal) {
        $scope.modalFields = {
            name: fieldname,
            id: fieldid,
            literature: $scope.literature.slice(),
            addedSources: [],
            value: currentVal || 100,
            setPossibility: function(event) {
                var max = event.currentTarget.scrollWidth;
                var click = event.originalEvent.layerX;
                var curr = $scope.modalFields.value;
                var newVal = parseInt(click/max*100);
                if(Math.abs(newVal-curr) < 10) {
                    if(newVal > curr) newVal = parseInt((newVal+10)/10)*10;
                    else newVal = parseInt(newVal/10)*10;
                } else {
                    newVal = parseInt((newVal+5)/10)*10;
                }
                event.currentTarget.children[0].style.width = newVal+"%";
                $scope.modalFields.value = newVal;
            }
        };
        var aid = fieldid;
        var cid = $scope.currentElement.id;
        httpGetFactory('api/sources/get/' + aid + '/' + cid, function(sources) {
            angular.forEach(sources, function(src, key) {
                $scope.modalFields.addedSources.push({
                    id: src.id,
                    cid: src.cid,
                    aid: src.attribute_id,
                    src: src.literature,
                    desc: src.description
                });
            });
        });
        var modalInstance = $uibModal.open({
            templateUrl: 'layouts/source-modal.html',
            windowClass: 'wide-modal',
            controller: function($uibModalInstance) {
                $scope.cancel = function(result) {
                    $uibModalInstance.dismiss('cancel');
                },
                $scope.savePossibility = function() {
                    var formData = new FormData();
                    formData.append('cid', cid);
                    formData.append('aid', fieldid);
                    formData.append('possibility', $scope.modalFields.value);
                    httpPostFactory('api/context/set/possibility', formData, function(callback) {
                        $scope.currentElementData[fieldid+'_pos'] = $scope.modalFields.value;
                    });
                }
            },
            scope: $scope
        });
        modalInstance.result.then(function(selectedItem) {}, function() {});
    };

    /**
     * Remove a source entry at the given index `index` from the given array `arr`.
     */
    scopeService.deleteSourceEntry = $scope.deleteSourceEntry = function(index, arr) {
        var src = arr[index];
        var title = '';
        if(typeof src.src !== 'undefined' && typeof src.src.title !== 'undefined') title = src.src.title;
        else if(typeof src.literature !== 'undefined' && typeof src.literature.title !== 'undefined') title = src.literature.title;
        modalFactory.deleteModal(title, function() {
            var cid = -1;
            var aid = -1;
            var lid = -1;
            if(typeof src.cid !== 'undefined') cid = src.cid;
            else if(typeof src.cid !== 'undefined') cid = src.cid;
            else return;
            if(typeof src.aid !== 'undefined') aid = src.aid;
            else if(typeof src.attribute_id !== 'undefined') aid = src.attribute_id;
            else return;
            if(typeof src.src !== 'undefined' && src.src.lid !== 'undefined') lid = src.src.id;
            else if(typeof src.literature_id !== 'undefined') lid = src.literature_id;
            else return;
            httpGetFactory('api/sources/delete/literature/'+aid+'/'+cid+'/'+lid, function(callback) {
                arr.splice(index, 1);
            });
        }, '');
        /*$scope.deleteModalFields = {
            name: title
        };
        var modalInstanceDelConfirm = $uibModal.open({
            templateUrl: 'layouts/delete-confirm.html',
            //windowClass: 'wide-modal',
            controller: function($uibModalInstance) {
                $scope.cancel = function(result) {
                    $uibModalInstance.dismiss('cancel');
                },
                $scope.deleteConfirmed = function() {
                    $uibModalInstance.dismiss('ok');
                    var fid = -1;
                    var aid = -1;
                    var lid = -1;
                    if(typeof src.fid !== 'undefined') fid = src.fid;
                    else if(typeof src.find_id !== 'undefined') fid = src.find_id;
                    else return;
                    if(typeof src.aid !== 'undefined') aid = src.aid;
                    else if(typeof src.attribute_id !== 'undefined') aid = src.attribute_id;
                    else return;
                    if(typeof src.src !== 'undefined' && src.src.lid !== 'undefined') lid = src.src.id;
                    else if(typeof src.literature_id !== 'undefined') lid = src.literature_id;
                    else return;
                    httpGetFactory('api/sources/delete/literature/'+aid+'/'+fid+'/'+lid, function(callback) {
                        arr.splice(index, 1);
                    });
                }
            },
            scope: $scope
        });
        modalInstanceDelConfirm.result.then(function(selectedItem) {}, function() {});*/
    };

    /**
     * Adds the current selected source entry `currentSource` with the given description `currentDesc` for the given attribute `aid` to the database and the source modal window array
     */
    $scope.addSource = function(currentSource, currentDesc, aid) {
        var cid = $scope.currentElement.id;
        var formData = new FormData();
        formData.append('cid', cid);
        formData.append('aid', aid);
        formData.append('lid', currentSource.id);
        formData.append('desc', currentDesc);
        httpPostFactory('api/sources/add', formData, function(row) {
            $scope.modalFields.addedSources.push({
                id: row.sid,
                cid: cid,
                aid: aid,
                src: currentSource,
                desc: currentDesc
            });
        });
        $scope.modalFields.currentSource = undefined;
        $scope.modalFields.currentDesc = undefined;
    };

    $scope.existsLiterature = function(cid, aid) {
        var promise = httpGetPromise.getData('api/sources/get/' + aid + '/' + cid);
        promise.then(function(sources) {
            console.log(aid);
            console.log(sources);
            console.log((sources.length > 0));
            return sources.length > 0;
        });
        /*httpGetFactory('api/sources/get/' + aid + '/' + fid, function(sources) {
            return sources.length > 0;
        });*/
    };

    /**
     * `name` is the name of the controller which is bound to this module
     * @returns whether the given controller exists or not
     */
    $scope.moduleExists = function(name) {
        return moduleHelper.controllerExists(name);
    };

    $scope.can = scopeService.can;

    /**
     * @returns hash code for a given string `str`
     */
    var getHashCode = function(str) {
        var hash = 0;
        if(str.length === 0) return hash;
        for(var i=0; i<str.length; i++) {
            var chr = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + chr;
            hash |= 0;
        }
        return hash;
    };

    /**
     * @returns json object with color settings for different context types, based on their id `id`.
     * Colors are in hsl format with fixed saturation and lightness. Hue is computed based on the `id`'s hash.
     */
    $scope.getColorForId = function(id) {
        var sat = '90%';
        var lgt = '65%';
        var hue = 0;
        if(typeof id !== 'undefined') {
            var newId = '';
            for(var i=0; i<id.length; i++) {
                newId += id.charCodeAt(i);
            }
            hue = getHashCode(newId) % 360;
        }
        var hsl = [
            hue,
            sat,
            lgt
        ];
        return {
            'color': 'hsl(' + hsl.join(',') + ')'
        };
    };
}]);
