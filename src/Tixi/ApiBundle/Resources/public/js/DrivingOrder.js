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

    this._routingInformationWrapper = null;

    this._trans = null;

    this.init = function(lookaheadFromId, lookaheadToId, passengerId, routingMachineSrcUrl, trans) {
        _this._passengerId = passengerId;
        _this._routingMachineSrcUrl = routingMachineSrcUrl;
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
            _routingInformationWrapper = _wrapper.find('.routingInformationWrapper');
        _this._isRepeatedCheckbox = _isRepeatedCheckbox;
        _this._dateFromLabel = _dateFromLabel;
        _this._repeatedEndDateWrapper = _repeatedEndDateWrapper;
        _this._singleTimeWrapper = _singleTimeWrapper;
        _this._repeatedTimeWrapper = _repeatedTimeWrapper;
        _this._routingInformationWrapper = _routingInformationWrapper;
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
            _addressTo = _this._lookaheadAddressTo.getAddress();

        if(_addressFrom && _addressTo) {
            _this._updateRouteInformation(_addressFrom, _addressTo);
        }else {
            _this._resetRouteInformation();
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
    }
}

function Route(from, to) {
    var _this = this;

    this._from = null;
    this._to = null;
    this._zone = null;
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

