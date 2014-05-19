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

    this._routingInformationWrapper = null;

    this.init = function(lookaheadFromId, lookaheadToId, passengerId, routingMachineSrcUrl) {
        _this._passengerId = passengerId;
        _this._routingMachineSrcUrl = routingMachineSrcUrl;
        _this._initELements();
        _this._initListeners();
        _this._initLookaheadAddresses(lookaheadFromId, lookaheadToId);

    }

    this._initLookaheadAddresses = function(lookaheadFromId, lookaheadToId) {
        var _lookaheadFrom = new AddressLookahead(),
            _lookaheadTo = new AddressLookahead();
        _lookaheadFrom.init('lookahead_'+lookaheadFromId, _this._passengerId);
        _lookaheadTo.init('lookahead_'+lookaheadToId, _this._passengerId);
        _this._lookaheadAddressFrom = _lookaheadFrom;
        _this._lookaheadAddressTo = _lookaheadTo;
    }

    this._initELements = function() {
        var _wrapper = $('.drivingOrderWrapper'),
            _routingInformationWrapper = _wrapper.find('.routingInformationWrapper');
        _this._routingInformationWrapper = _routingInformationWrapper;
    }

    this._initListeners = function() {
        $('body').on('addressChanged', _this._onAddressChanged);
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
        $(_this._routingInformationWrapper).show();
    }

    this._resetRouteInformation = function() {
        _this._route = null;
        $(_this._routingInformationWrapper).hide();
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
        _toReturn += 'Fahrtdauer: '+_this._convertSecToMin(_this._duration)+' min ';
        _toReturn += 'Fahrdistanz: '+_this._distance+' m';
        return _toReturn;
    }

    this._convertSecToMin = function(sec) {
        return Math.ceil(sec/60);
    }

}

