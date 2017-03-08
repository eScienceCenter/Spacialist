spacialistApp.controller('mapCtrl', ['$scope', 'mapService', 'mainService', 'modalFactory', 'httpGetFactory', '$compile', function($scope, mapService, mainService, modalFactory, httpGetFactory, $compile) {
    $scope.map = mapService.map;
    $scope.mapObject = mapService.mapObject;
    $scope.markerIcons = mapService.markerIcons;
    $scope.markers = mapService.markers;
    $scope.currentElement = mainService.currentElement;
    $scope.currentGeodata = mapService.currentGeodata;
    ////
    $scope.markerOptions = {};
    $scope.closedAlerts = {};
    $scope.output = {};
    $scope.relations = [];
    $scope.allImages = [];
    $scope.unlinkedFilter = {
        contexts: {}
    };
    $scope.markerValues = {};
    $scope.hideLists = {};
    $scope.lists = {};
    $scope.input = {};
    $scope.editEntry = {};
    ////

    $scope.renameMarker = function(oldName, newName) {
        mapService.renameMarker(oldName, newName);
    };

    $scope.addMarker = function(elem) {
        mapService.addContextToMarkers(elem);
    };

    $scope.updateMarkerOptions = function(markerId, markerKey, color, icon) {
        if(typeof markerId == 'undefined') return;
        if(markerId <= 0) return;
        var formData = new FormData();
        formData.append('id', markerId);
        if(typeof color != 'undefined') formData.append('color', color);
        if(typeof icon != 'undefined') formData.append('icon', icon.icon);
        httpPostPromise.getData('api/context/set/icon', formData).then(
            function(icon) {
                console.log(icon);
                angular.extend($scope.markers[markerKey].icon, {
                    className: 'fa fa-fw fa-lg fa-' + icon.icon,
                    color: icon.color
                });
            }
        );
    };

    /**
     * listener for different leaflet actions
     */
    $scope.$on('leafletDirectiveMap.mainmap.popupclose', function(event, args) {
        mapService.unsetCurrentGeodata();
    });
    $scope.$on('leafletDirectiveMap.mainmap.popupopen', function(event, args) {
        var popup = args.leafletEvent.popup;
        var newScope = $scope.$new();
        newScope.stream = popup.options.feature;
        $compile(popup._contentNode)(newScope);
        var center = popup._source.getBounds().getCenter();
        popup.setLatLng(center);
        var featureId = args.leafletEvent.popup._source.feature.id;
        mapService.setCurrentGeodata(featureId);
        var promise = mapService.getMatchingContext(featureId);
        promise.then(function(response) {
            if(response.error) {
                modalFactory.errorModal(response.error);
            } else {
                var path = response.path;
                if(path !== null && path.length > 0) {
                    mainService.expandTreeTo(path);
                } else {
                    var dontUnsetUnlinked = true;
                    mainService.unsetCurrentElement(dontUnsetUnlinked);
                }
            }
        });
    });

    var getCoords = function(layer, type) {
        var coords;
        if(type == 'marker' || type == 'Point') {
            coords = [ layer.getLatLng() ];
        } else {
            coords = layer.getLatLngs();
            if(type.toLowerCase() == 'polygon') coords.push(angular.copy(coords[0]));
        }
        return coords;
    };

    $scope.$on('leafletDirectiveDraw.mainmap.draw:edited', function(event, args) {
        var layers = args.leafletEvent.layers.getLayers();
        angular.forEach(layers, function(layer, key) {
            var type = layer.feature.geometry.type;
            var coords = getCoords(layer, type);
            var id = layer.feature.id;
            mapService.addGeodata(type, coords, id);
        });
    });

    $scope.$on('leafletDirectiveDraw.mainmap.draw:deleted', function(event, args) {
        var layers = args.leafletEvent.layers.getLayers();
        angular.forEach(layers, function(layer, key) {
            var id = layer.feature.id;
            httpGetFactory('api/context/delete/geodata/' + id, function(response) {
                //
            });
        });
    });
    /**
     * If the marker has been created, add the marker to the marker-array and store it in the database
     */
    $scope.$on('leafletDirectiveDraw.mainmap.draw:created', function(event, args) {
        var type = args.leafletEvent.layerType;
        var layer = args.leafletEvent.layer;
        var coords = getCoords(layer, type);
        mapService.addGeodata(type, coords);
    });

    $scope.linkGeodata = function(cid, gid) {
        var promise = mapService.linkGeodata(cid, gid);
        promise.then(function(response) {
            if(response.error) {
                modalFactory.errorModal(response.error);
                return;
            }
            var updatedContext = response.context;
            var updatedValues = {
                geodata_id: updatedContext.geodata_id
            };
            mainService.updateContextById(cid, updatedValues);
        });
    };

    $scope.unlinkGeodata = function(cid) {
        var promise = mapService.unlinkGeodata(cid);
        promise.then(function(response) {
            if(response.error) {
                modalFactory.errorModal(response.error);
                return;
            }
            var updatedValues = {
                geodata_id: undefined
            };
            mainService.updateContextById(cid, updatedValues);
        });
    };

    $scope.isEmpty = function(obj) {
        if (typeof obj === 'undefined') return false;
        return Object.keys(obj).length === 0;
    };

    $scope.addListEntry = function(aid, oid, text, arr) {
        var index = aid + '_' + (oid || '');
        var tmpArr = $scope.$eval(arr);
        var inp = $scope.$eval(text);
        if(typeof tmpArr[index] == 'undefined') tmpArr[index] = [];
        tmpArr[index].push({
            'name': inp[index]
        });
        inp[index] = '';
    };

    $scope.editListEntry = function(ctid, aid, $index, val, tableIndex) {
        $scope.cancelEditListEntry();
        var name = ctid + "_" + aid;
        $scope.currentEditName = name;
        $scope.currentEditIndex = $index;
        if (typeof tableIndex !== 'undefined') {
            $scope.currentEditCol = tableIndex;
            $scope.editEntry[name][$index][tableIndex] = true;
        } else {
            $scope.editEntry[name][$index] = true;
        }
        $scope.initialListVal = val;
    };

    $scope.cancelEditListEntry = function() {
        if (typeof $scope.currentEditName !== 'undefined' && typeof $scope.currentEditIndex !== 'undefined') {
            if (typeof $scope.currentEditCol !== 'undefined') {
                $scope.editEntry[$scope.currentEditName][$scope.currentEditIndex][$scope.currentEditCol] = false;
                $scope.markerValues[$scope.currentEditName].selectedEpochs[$scope.currentEditIndex][$scope.currentEditCol] = $scope.initialListVal;
            } else {
                $scope.editEntry[$scope.currentEditName][$scope.currentEditIndex] = false;
                $scope.markerValues[$scope.currentEditName][$scope.currentEditIndex] = $scope.initialListVal;
            }
        }
        $scope.currentEditName = undefined;
        $scope.currentEditIndex = undefined;
        $scope.currentEditCol = undefined;
        $scope.initialListVal = undefined;
    };

    $scope.storeEditListEntry = function() {
        if (typeof $scope.currentEditName !== 'undefined' && typeof $scope.currentEditIndex !== 'undefined') {
            if (typeof $scope.currentEditCol !== 'undefined') {
                $scope.editEntry[$scope.currentEditName][$scope.currentEditIndex][$scope.currentEditCol] = false;
            } else {
                $scope.editEntry[$scope.currentEditName][$scope.currentEditIndex] = false;
            }
        }
        $scope.currentEditName = undefined;
        $scope.currentEditIndex = undefined;
        $scope.currentEditCol = undefined;
        $scope.initialListVal = undefined;
    };

    $scope.removeListItem = function(aid, oid, arr, $index) {
        var index = aid + '_' + (oid || '');
        var tmpArr = $scope.$eval(arr);
        tmpArr[index].splice($index, 1);
        //var name = aid + "_" + oid;
        //$scope.markerValues[name].splice($index, 1);
    };

    $scope.toggleList = function(ctid, aid) {
        var index = ctid + "_" + aid;
        $scope.hideLists[index] = !$scope.hideLists[index];
    };

    $scope.updateInput = function($event) {
        setMarkerOption($event.target.id, $event.target.value);
    };

    $scope.updateSelectInput = function($model) {
        setMarkerOption($model.$name, $model.$modelValue);
    };

    $scope.updateMSelectInput = function($select) {
        $model = $select.ngModel;
        $scope.markerValues[$model.$name] = $select.selected;
    };

    /**
     * Updates the markerValues array with values from the given `opts` array
     */
    var updateMarkerOpts = function(opts) {
        angular.extend($scope.markerValues, opts);
        if (typeof opts.lat != 'undefined' && typeof opts.lng != 'undefined') updateMarkerPos(opts.lat, opts.lng);
    };

    var updateMarkerPos = function(lat, lng) {
        $scope.markerValues.lat = lat;
        $scope.markerValues.lng = lng;
    };

    var resetMarkerOpts = function() {
        $scope.markerValues = {};
        $scope.activeMarker = -1;
        $scope.markerActive = false;
    };
}]);
