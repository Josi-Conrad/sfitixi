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
    this._dateFromField = null;
    this._dateToField = null;

    this._repeatedEndDateWrapper = null;
    this._singleTimeWrapper = null;
    this._repeatedTimeWrapper = null;
    this._repeatedWithHolidaysWrapper = null;

    this._zoneServiceSrcUrl = null;
    this._zoneIdField = null;
    this._zoneStatusField = null;
    this._zoneNameField = null;

    this._rideCheckSingleSrcUrl = null;
    this._rideCheckRepeatedSrcUrl = null;
    this._rideTimeOutwardWrapper = null;
    this._rideTimeReturnWrapper = null;
    this._routingInformationWrapper = null;

    this._rideTimes = new Array();
    this._rideOutwardDirection = 'outward';
    this._rideReturnDirection = 'return';

    this._trans = null;

    this.init = function(lookaheadFromId, lookaheadToId, passengerId, serviceUrls, trans, isEdit) {
        _this._passengerId = passengerId;
        _this._routingMachineSrcUrl = serviceUrls.routingMachine;
        _this._zoneServiceSrcUrl = serviceUrls.zone;
        _this._rideCheckSingleSrcUrl = serviceUrls.singleRideCheck;
        _this._rideCheckRepeatedSrcUrl = serviceUrls.repeatedRideCheck;
        _this._trans = trans;
        _this._initElements();
        _this._initRideCheck();
        _this._initLookaheadAddresses(lookaheadFromId, lookaheadToId);
        _this._initListeners();
        _this._toggleState();
        //force route poll if view is in edit state
        if(isEdit) {
            _this._onAddressChanged();
        }
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
            _dateFromField = _wrapper.find('.dateFromInput'),
            _dateToField = _wrapper.find('.dateToInput'),
            _repeatedEndDateWrapper = _wrapper.find('.repeatedEndDateWrapper'),
            _repeatedWithHolidaysWrapper = _wrapper.find('.repeatedOrderWithHolidays'),
            _singleTimeWrapper = _wrapper.find('.singleTimeWrapper'),
            _repeatedTimeWrapper = _wrapper.find('.repeatedTimeWrapper'),
            _routingInformationWrapper = _wrapper.find('.routingInformationWrapper'),
            _rideTimeOutwardWrapper = $(_routingInformationWrapper).find('.rideTimeOutward'),
            _rideTimeReturnWrapper = $(_routingInformationWrapper).find('.rideTimeReturn'),
            _zoneStatusField = _wrapper.find('.zoneStatus'),
            _zoneIdField = _wrapper.find('.zoneId'),
            _zoneNameField = _wrapper.find('.zoneName');
        _this._isRepeatedCheckbox = _isRepeatedCheckbox;
        _this._dateFromLabel = _dateFromLabel;
        _this._dateFromField = _dateFromField;
        _this._dateToField = _dateToField;
        _this._repeatedEndDateWrapper = _repeatedEndDateWrapper;
        _this._repeatedWithHolidaysWrapper = _repeatedWithHolidaysWrapper;
        _this._singleTimeWrapper = _singleTimeWrapper;
        _this._repeatedTimeWrapper = _repeatedTimeWrapper;
        _this._routingInformationWrapper = _routingInformationWrapper;
        _this._rideTimeOutwardWrapper = _rideTimeOutwardWrapper;
        _this._rideTimeReturnWrapper = _rideTimeReturnWrapper;
        _this._zoneStatusField = _zoneStatusField;
        _this._zoneIdField = _zoneIdField;
        _this._zoneNameField = _zoneNameField;
    }

    this._initRideCheck = function() {
        _this._rideTimes.push(new RideTime($(_this._singleTimeWrapper).find('.outwardTimeWrapper').first(), _this._rideOutwardDirection, false, _this));
        _this._rideTimes.push(new RideTime($(_this._singleTimeWrapper).find('.returnTimeWrapper').first(), _this._rideReturnDirection, false, _this));
        $(_this._repeatedTimeWrapper).find('.outwardTimeWrapper').each(function() {
            _this._rideTimes.push(new RideTime(this, _this._rideOutwardDirection, true, _this));
        });
        $(_this._repeatedTimeWrapper).find('.returnTimeWrapper').each(function() {
            _this._rideTimes.push(new RideTime(this, _this._rideReturnDirection, true, _this));
        });
    }

    this._initListeners = function() {
        $(_this._isRepeatedCheckbox).on('change', _this._toggleState);
        $('body').on('addressChanged', _this._onAddressChanged);
        $(_this._dateFromField).on('change', _this._onDateFieldChanged);
        $(_this._dateToField).on('change', _this._onDateFieldChanged);
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
        $(_this._repeatedWithHolidaysWrapper).hide();
        $(_this._singleTimeWrapper).show();
        $(_this._dateFromLabel).text(_this._trans.dateFromLabelSingleText);

    }

    this._switchToRepeatedState = function() {
        $(_this._singleTimeWrapper).hide();
        $(_this._repeatedEndDateWrapper).show();
        $(_this._repeatedTimeWrapper).show();
        $(_this._repeatedWithHolidaysWrapper).show();
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
        _this._resetRideCheckInformations();
    }

    this._onDateFieldChanged =function() {
        _this._resetRideCheckInformations();
    }

    this._updateRouteInformation = function(addressFrom, addressTo) {
        var _route = new Route(addressFrom, addressTo);
        _route.updateRoutingInformation(_this._routingMachineSrcUrl, _this._routeInformationUpdated)
        _this._route = _route;
    }

    this._routeInformationUpdated = function(route) {
        $(_this._rideTimeOutwardWrapper).html(_this._trans['drivingDuration']+': '+route.getOutwardDurationAsString());
        $(_this._rideTimeReturnWrapper).html(_this._trans['drivingDuration']+': '+route.getReturnDurationAsString());
    }

    this._resetRouteInformation = function() {
        _this._route = null;
        $(_this._rideTimeOutwardWrapper).html('');
        $(_this._rideTimeReturnWrapper).html('');
    }

    this._updateZoneInformation = function(cityFrom, cityTo) {
        var _zoneManager = new ZoneManager();
        _zoneManager.requestZoneInformation(cityFrom, cityTo, _this._zoneServiceSrcUrl, _this._zoneInformationUpdated);
    }

    this._zoneInformationUpdated = function(zone) {
        if(zone && zone.getStatus() != -1) {
            $(_this._zoneNameField).val(zone.getName());
        }
    }

    this._resetZoneInformation = function() {
        $(_this._zoneNameField).val('');
    }

    this.getRideSingleSrcUrl = function() {
        return _this._rideCheckSingleSrcUrl;
    }

    this.getRideRepeatedSrcUrl = function() {
        return _this._rideCheckRepeatedSrcUrl;
    }

    this.isRideCheckPossible = function(timeInput) {
        return (_this._route !== null && timeInput !== '' && $(_this._dateFromField).val() !== '');
    }

    this.createRideSingleCheckParams = function(timeInput, direction) {
        var _params = {},
            _duration;
        if(_this.isRideCheckPossible()) {
            _duration = (direction === _this._rideOutwardDirection) ? _this._route.getOutwardDuration : _this._route.getReturnDuration;
            _params['day'] = $(_this._dateFromField).val();
            _params['time'] = timeInput;
            _params['duration'] = _duration;
        }
        return _params;
    }

    this.createRideRepeatedCheckParams = function(timeInput, direction, weekday) {
        var _params = {},
            _duration;
        if(_this.isRideCheckPossible()) {
            _duration = (direction === _this._rideOutwardDirection) ? _this._route.getOutwardDuration : _this._route.getReturnDuration;
            _params['fromDate'] = $(_this._dateFromField).val();
            _params['toDate'] = $(_this._dateToField).val();
            _params['time'] = timeInput;
            _params['duration'] = _duration;
            _params['weekday'] = weekday;
        }
        return _params;
    }

    this._resetRideCheckInformations = function() {
        _this._rideTimes.forEach(function(rideTime) {
            rideTime.reset();
        })
    }
}

function RideTime(elementWrapper, direction, isRepeated, orderController) {
    var _this = this;

    this._direction = null;
    this._isRepeated = null;
    this._orderController = null;


    this._formGroupWrapper = null;
    this._inputField = null;
    this._feedbackWrapper = null;

    this._weekday = null;

    this._init = function() {
        _this._direction = direction;
        _this._isRepeated = isRepeated;
        _this._orderController = orderController;
        _this._formGroupWrapper = elementWrapper;
        _this._inputField = $(elementWrapper).find('input');
        _this._weekday = $(_this._inputField).data('weekday');
        _this._feedbackWrapper = $(elementWrapper).find('span');
        _this._initListeners();
    }

    this.reset = function() {
        _this._resetFeasableState();
    }

    this._initListeners = function() {
        $(_this._inputField).on('focusout', _this._onInputChange);
    }

    this._onInputChange = function() {
        if(orderController.isRideCheckPossible($(_this._inputField).val())) {
            if(_this._isRepeated) {
                _this._pollFeasabilityInformationForRepeatedCheck().done(function(data) {
                    if(data.isFeasible) {
                        _this._setFeasableState();
                    }else {
                        _this._setUnfeasableState();
                    }
                }).fail(function() {

                });
            }else {
                _this._pollFeasabilityInformationForSingleCheck().done(function(data) {
                    if(data.isFeasible) {
                        _this._setFeasableState();
                    }else {
                        _this._setUnfeasableState();
                    }
                }).fail(function() {

                });
            }
        }


    }

    this._pollFeasabilityInformationForSingleCheck = function() {
        var _src = _this._orderController.getRideSingleSrcUrl(),
            _params = _this._orderController.createRideSingleCheckParams($(_this._inputField).val(),_this._direction);

        return $.ajax({
            type: 'GET',
            url: _src + '?' + $.param(_params, true),
            dataType: 'json'
        });
    }

    this._pollFeasabilityInformationForRepeatedCheck = function() {
        var _src = _this._orderController.getRideRepeatedSrcUrl(),
            _params = _this._orderController.createRideRepeatedCheckParams($(_this._inputField).val(),_this._direction, _this._weekday);

        return $.ajax({
            type: 'GET',
            url: _src + '?' + $.param(_params, true),
            dataType: 'json'
        });
    }

    this._setFeasableState = function(){
        _this._resetFeasableState();
        $(_this._feedbackWrapper).addClass('glyphicon-ok');
        $(_this._formGroupWrapper).addClass('has-success');
    }

    this._setUnfeasableState = function() {
        _this._resetFeasableState();
        $(_this._feedbackWrapper).addClass('glyphicon-remove');
        $(_this._formGroupWrapper).addClass('has-error');
    }

    this._resetFeasableState = function() {
        $(_this._formGroupWrapper).removeClass('has-success has-error');
        $(_this._feedbackWrapper).removeClass('glyphicon-ok glyphicon-remove');
    }


    _this._init();
}

function Route(from, to) {
    var _this = this;

    this._from = null;
    this._to = null;
    this._durationOutward = null;
    this._distanceOutward = null;
    this._durationReturn = null;
    this._distanceReturn = null;

    this._init = function(from, to) {
        _this._from = from;
        _this._to = to;
    }

    this.updateRoutingInformation = function(srcUrl, callback) {
        _this._pollRoutingInformation(srcUrl, _this._constructParams()).done(function(data) {
            _this._distanceOutward = data.routeOutwardDistance;
            _this._durationOutward = data.routeOutwardDuration;
            _this._durationReturn = data.routeReturnDuration;
            _this._distanceReturn = data.routeReturnDistance;
            callback(_this);
        }).fail(function() {
            //fail silently
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

    this.getOutwardDurationAsString = function() {
        return _this._convertSecToMin(_this._durationOutward)+' min';
    }

    this.getOutwardDuration = function() {
        return _this._durationOutward;
    }

    this.getReturnDurationAsString = function() {
        return _this._convertSecToMin(_this._durationReturn)+' min';
    }

    this.getReturnDuration = function() {
        return _this._durationReturn;
    }

    this._convertSecToMin = function(sec) {
        return Math.ceil(sec/60);
    }

}

function ZoneManager() {
    var _this = this;

    this.requestZoneInformation = function(cityFrom, cityTo, zoneServiceUrl, callback) {
        _this._pollZoneInformation(zoneServiceUrl, _this._createParam(cityFrom, cityTo)).done(function(data) {
            callback(new Zone(data));
        }).fail(function() {
            callback(null);
        });
    }

    this._createParam = function(cityFrom, cityTo) {
        return {'cities':[cityFrom, cityTo]};

    }

    this._pollZoneInformation = function(srcUrl, params) {
        return $.ajax({
            type: 'GET',
            url: srcUrl + '?' + $.param(params, false),
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

