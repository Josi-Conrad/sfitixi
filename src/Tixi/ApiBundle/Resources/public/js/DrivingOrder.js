/**
 * Created by faustos on 19.05.14.
 */
function DrivingOrder() {
    var _this = this;

    this._lookaheadAddressFrom = null;
    this._lookaheadAddressTo = null;

    this._passengerId = null;

    this._routingMachineSrcUrl = null;
    this._route = null;

    this._isRepeatedCheckbox = null;

    this._dateFromLabel = null;
    this._repeatedEndDateWrapper = null;
    this._singleTimeWrapper = null;
    this._repeatedTimeWrapper = null;

    this._zoneServiceSrcUrl = null;
    this._zoneIdField = null;
    this._zoneStatusField = null;
    this._zoneNameField = null;

    this._routingInformationWrapper = null;

    this._trans = null;

    this.init = function(lookaheadFromId, lookaheadToId, passengerId, routingMachineSrcUrl, zoneServiceSrcUrl, trans) {
        _this._passengerId = passengerId;
        _this._routingMachineSrcUrl = routingMachineSrcUrl;
        _this._zoneServiceSrcUrl = zoneServiceSrcUrl;
        _this._trans = trans;
        _this._initElements();
        _this._initListeners();
        _this._initLookaheadAddresses(lookaheadFromId, lookaheadToId);
        _this._toggleState();

    }

    this._initLookaheadAddresses = function(lookaheadFromId, lookaheadToId) {
        var _lookaheadFrom = new AddressLookahead(),
            _lookaheadTo = new AddressLookahead();
        _lookaheadFrom.init('lookahead_'+lookaheadFromId, _this._passengerId);
        _lookaheadTo.init('lookahead_'+lookaheadToId, _this._passengerId);
        _this._lookaheadAddressFrom = _lookaheadFrom;
        _this._lookaheadAddressTo = _lookaheadTo;
    }

    this._initElements = function() {
        var _wrapper = $('.drivingOrderWrapper'),
            _isRepeatedCheckbox = _wrapper.find('.isRepeatedCheckbox'),
            _dateFromLabel = _wrapper.find('.orderDateFromLabel').find('label'),
            _repeatedEndDateWrapper = _wrapper.find('.repeatedEndDateWrapper'),
            _singleTimeWrapper = _wrapper.find('.singleTimeWrapper'),
            _repeatedTimeWrapper = _wrapper.find('.repeatedTimeWrapper'),
            _routingInformationWrapper = _wrapper.find('.routingInformationWrapper'),
            _zoneStatusField = _wrapper.find('.zoneStatus'),
            _zoneIdField = _wrapper.find('.zoneId'),
            _zoneNameField = _wrapper.find('.zoneName');
        _this._isRepeatedCheckbox = _isRepeatedCheckbox;
        _this._dateFromLabel = _dateFromLabel;
        _this._repeatedEndDateWrapper = _repeatedEndDateWrapper;
        _this._singleTimeWrapper = _singleTimeWrapper;
        _this._repeatedTimeWrapper = _repeatedTimeWrapper;
        _this._routingInformationWrapper = _routingInformationWrapper;
        _this._zoneStatusField = _zoneStatusField;
        _this._zoneIdField = _zoneIdField;
        _this._zoneNameField = _zoneNameField;
    }

    this._initListeners = function() {
        $(_this._isRepeatedCheckbox).on('change', _this._toggleState);
        $('body').on('addressChanged', _this._onAddressChanged);
    }

    this._toggleState = function() {
        if($(_this._isRepeatedCheckbox).prop('checked')) {
            _this._switchToRepeatedState();
        }else {
            _this._switchToSingleState();
        }
    }

    this._switchToSingleState = function() {
        $(_this._repeatedEndDateWrapper).hide();
        $(_this._repeatedTimeWrapper).hide();
        $(_this._singleTimeWrapper).show();
        $(_this._dateFromLabel).text(_this._trans.dateFromLabelSingleText);

    }

    this._switchToRepeatedState = function() {
        $(_this._singleTimeWrapper).hide();
        $(_this._repeatedEndDateWrapper).show();
        $(_this._repeatedTimeWrapper).show();
        $(_this._dateFromLabel).text(_this._trans.dateFromLabelRepeatedText);
    }

    this._onAddressChanged = function() {
        var _addressFrom = _this._lookaheadAddressFrom.getAddress(),
            _addressTo = _this._lookaheadAddressTo.getAddress(),
            _cityFrom = _this._lookaheadAddressFrom.getAddressCity(),
            _cityTo = _this._lookaheadAddressTo.getAddressCity();
        if(_addressFrom && _addressTo) {
            _this._updateRouteInformation(_addressFrom, _addressTo);
            _this._updateZoneInformation(_cityFrom, _cityTo);
        }else {
            _this._resetRouteInformation();
            _this._resetZoneInformation();
        }
    }

    this._updateRouteInformation = function(addressFrom, addressTo) {
        var _route = new Route(addressFrom, addressTo);
        _route.updateRoutingInformation(_this._routingMachineSrcUrl, _this._routeInformationUpdated)
    }

    this._routeInformationUpdated = function(route) {
        $(_this._routingInformationWrapper).html(route.toString());
    }

    this._resetRouteInformation = function() {
        _this._route = null;
        $(_this._routingInformationWrapper).html('');
    }

    this._updateZoneInformation = function(cityFrom, cityTo) {
        var _zoneManager = new ZoneManager();
        _zoneManager.requestZoneInformation(cityFrom, cityTo, _this._zoneServiceSrcUrl, _this._zoneInformationUpdated);
    }

    this._zoneInformationUpdated = function(zone) {
        if(!zone || zone.getStatus() == -1) {
            //error state
            $(_this._zoneStatusField).val(-1);
        }else {
            $(_this._zoneNameField).val(zone.getName());
            $(_this._zoneStatusField).val(zone.getStatus());
            if(zone.getStatus() != 0) {
                $(_this._zoneIdField).val(zone.getId());
            }
        }
    }

    this._resetZoneInformation = function() {
        $(_this._zoneStatusField).val('');
        $(_this._zoneNameField).val('');
        $(_this._zoneIdField).val('');

    }
}

function Route(from, to) {
    var _this = this;

    this._from = null;
    this._to = null;
    this._duration = null;
    this._distance = null;

    this._init = function(from, to) {
        _this._from = from;
        _this._to = to;
    }

    this.updateRoutingInformation = function(srcUrl, callback) {
        _this._pollRoutingInformation(srcUrl, _this._constructParams()).done(function(data) {
            _this._distance = data.routeDistance;
            _this._duration = data.routeDuration;
            callback(_this);
        }).fail(function() {
            //ToDo on routing fail
        })
    }

    this._constructParams = function() {
        var _jsonToReturn = {},
            _fromLatLng = _this._from.getCoordinates(),
            _toLatLng = _this._to.getCoordinates();
        _jsonToReturn['latFrom'] = _fromLatLng.lat;
        _jsonToReturn['lngFrom'] = _fromLatLng.lng;
        _jsonToReturn['latTo'] = _toLatLng.lat;
        _jsonToReturn['lngTo'] = _toLatLng.lng;
        return _jsonToReturn;
    }

    if(undefined === from || undefined === to) {
        return null;
    }else {
        _this._init(from, to);
    }

    this._pollRoutingInformation = function(srcUrl, params) {
        return $.ajax({
            type: 'GET',
            url: srcUrl + '?' + $.param(params, true),
            dataType: 'json'
        });
    }

    this.toString = function() {
        var _toReturn = '';
//        _toReturn += 'Zone: ';
        _toReturn += 'Fahrtdauer: '+_this._convertSecToMin(_this._duration)+' min - ';
        _toReturn += 'Fahrdistanz: '+_this._distance+' m';
        return _toReturn;
    }

    this._convertSecToMin = function(sec) {
        return Math.ceil(sec/60);
    }

}

function ZoneManager() {
    var _this = this;

    this.requestZoneInformation = function(cityFrom, cityTo, zoneServiceUrl, callback) {
        var _tempZoneFrom,
            _tempZoneTo;
        _this._pollZoneInformation(zoneServiceUrl, _this._createParam(cityFrom)).done(function(data) {
            _tempZoneFrom = new Zone(data);
            _this._pollZoneInformation(zoneServiceUrl, _this._createParam(cityTo)).done(function(data) {
                _tempZoneTo = new Zone(data);
                if(_tempZoneFrom.getPriority()>_tempZoneTo.getPriority) {
                    callback(_tempZoneFrom);
                }else {
                    callback(_tempZoneTo);
                }
            }).fail(function() {
                callback(null);
            });
        }).fail(function() {
            callback(null);
        });
    }

    this._createParam = function(city) {
        return {'city':city};

    }

    this._pollZoneInformation = function(srcUrl, params) {
        return $.ajax({
            type: 'GET',
            url: srcUrl + '?' + $.param(params, true),
            dataType: 'json'
        });
    }

}

function Zone(zoneTransferJson) {
    var _this = this;

    this._status = null;
    this._id = null;
    this._name = null;
    this._priority = null;

    this._init = function(zoneTransferJson) {
        //status: -1 = error, 0 = unclassified, 1 = classified
        _this._status = zoneTransferJson.status;
        if(_this._status != -1) {
            _this._name = zoneTransferJson.zonename;
            _this._priority = zoneTransferJson.zonepriority;
            if(_this._status == 1) {
                _this._id = zoneTransferJson.zoneid;
            }
        }
    }

    this.getStatus = function() {
        return _this._status;
    }

    this.getId = function() {
        return _this._id;
    }

    this.getName = function() {
        return _this._name;
    }

    this.getPriority = function() {
        return _this._priority;
    }

    _this._init(zoneTransferJson);



}

