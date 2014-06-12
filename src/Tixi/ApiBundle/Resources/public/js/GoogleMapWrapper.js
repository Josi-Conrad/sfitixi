/**
 * Created by faustos on 16.05.14.
 */
function GoogleMapWrapper() {
    var _this = this;

    this.DISPLAYSTATENOCONTROLS = 1;
    this.DISPLAYSTATECONTROLS = 2;
    this.EDITSTATE = 3;
    this.GEOCODEEDITSTATE = 4;

    this._canvasWrapper = null;
    this._canvas = null;
    this._isCanvasVisible = false;

    this._map = null;
    this._marker = null;

    this._geocoder = null;

    this._geocodeInputWrapper = null;
    this._geocodeInput = null;
    this._geocodeInputButton = null;

    this._state = null;

    this.init = function(canvasWrapper, canvas) {
        _this._canvasWrapper = canvasWrapper;
        _this._canvas = canvas;
        //only init if google services have been loaded with success
        if('google' in window) {
            _this._initElements();
            _this._initGeocoder();
        }
    }

    this._initElements = function() {
        var _geocodeInputWrapper = $(_this._canvasWrapper).find('.geocodeInputWrapper'),
            _geocodeInput = $(_geocodeInputWrapper).find('.geocodeInput'),
            _geocodeInputButton = $(_geocodeInputWrapper).find('.geocodeInputButton');
        $(_geocodeInputButton).on('click', _this._onGeocodeInputButtonClick);
        _this._geocodeInputWrapper = _geocodeInputWrapper;
        _this._geocodeInput = _geocodeInput;
        _this._geocodeInputButton = _geocodeInputButton;

    }

    this.displayAddress = function(lat, lng, controls) {
        var _latLng = _this._createLatLng(lat, lng),
            _controls = (undefined === controls) ? false : controls;
        if(!_this._isCanvasVisible) {
            _this.showCanvas();
        }
        if(controls) {
            if(_this._state !== _this.DISPLAYSTATECONTROLS) {
                _this._switchToDisplayStateWithControls();
            }
        }else {
            if(_this._state !== _this.DISPLAYSTATENOCONTROLS) {
                _this._switchToDisplayStateWithoutControls();
            }
        }
        _this._setMapCenter(_latLng);
        _this._moveMarker(_latLng);
    }

    this.displayEditableAddress = function(lat, lng, eventCallback) {
        var _latLng = _this._createLatLng(lat, lng);
        if(!_this._isCanvasVisible) {
            _this.showCanvas();
        }
        if(_this._state !== _this.EDITSTATE) {
            _this._switchToEditState(eventCallback);
        }
        _this._setMapCenter(_latLng);
        _this._moveMarker(_latLng);
    }

    this.displayGeocodeEditableCanvas = function(eventCallback) {
        if(!_this._isCanvasVisible) {
            _this.showCanvas();
        }
        if(_this._state !== _this.GEOCODEEDITSTATE) {
            _this._switchToGeocodeEditState(eventCallback);
        }
    }

    this._switchToDisplayStateWithControls = function() {
        _this._hideGeocodeInput();
        _this._initDisplayMap(true);
        _this._initDisplayMarker();
        _this._state = _this.DISPLAYSTATECONTROLS;
    }

    this._switchToDisplayStateWithoutControls = function() {
        _this._hideGeocodeInput();
        _this._initDisplayMap(false);
        _this._initDisplayMarker();
        _this._state = _this.DISPLAYSTATENOCONTROLS;
    }

    this._switchToEditState = function(eventCallback) {
        _this._hideGeocodeInput();
        _this._initEditMap();
        _this._initEditMarker(eventCallback);
        _this._state = _this.EDITSTATE;
    }

    this._switchToGeocodeEditState = function(eventCallback) {
        _this._initGeocodeEditMap();
        _this._initEditMarker(eventCallback);
        _this._showGeocodeInput();
        _this._state = _this.GEOCODEEDITSTATE;
    }

    this._setMapCenter = function(latLng) {
        _this._map.setCenter(latLng);
    }

    this._moveMarker = function(latLng) {
        _this._marker.setPosition(latLng);
    }

    this.showCanvas = function() {
        $(_this._canvasWrapper).show();
        _this._isCanvasVisible = true;
    }

    this.hideCanvas = function() {
        $(_this._canvasWrapper).hide();
        _this._isCanvasVisible = false;
    }

    this._createLatLng = function(lat, lng) {
        return new google.maps.LatLng(lat, lng);
    }

    this._showGeocodeInput = function() {
        $(_this._geocodeInputWrapper).show();
    }

    this._hideGeocodeInput = function() {
        $(_this._geocodeInputWrapper).hide();
    }

    this._onGeocodeInputButtonClick = function() {
        var _geocodeInput = $(_this._geocodeInput).val();
        if(_geocodeInput) {
            _this._geocoder.geocode( { 'address': _geocodeInput}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    _this._setMapCenter(results[0].geometry.location);
                    _this._moveMarker(results[0].geometry.location);
                } else {
                    //fail silently
                }
            });
        }
    }

    this._onMarkerDrop = function(eventCallback) {
        var _markerLatLng = _this._marker.getPosition();
        eventCallback(_markerLatLng.lat(), _markerLatLng.lng());
    }

    this._initGeocoder = function() {
        _this._geocoder = new google.maps.Geocoder();
    }

    this._initDisplayMap = function(_controls) {
        _this._map = new google.maps.Map(_this._canvas, {
            zoom: 17,
            disableDefaultUI: !_controls,
            mapTypeId: google.maps.MapTypeId.HYBRID
        });
    }

    this._initEditMap = function() {
        _this._map = new google.maps.Map(_this._canvas, {
            zoom: 17,
            streetViewControl: false,
            mapTypeId: google.maps.MapTypeId.HYBRID
        });
    }

    this._initGeocodeEditMap = function() {
        _this._map = new google.maps.Map(_this._canvas, {
            zoom: 15,
            streetViewControl: false,
            mapTypeId: google.maps.MapTypeId.HYBRID
        });
    }

    this._initDisplayMarker = function() {
        var _marker = new google.maps.Marker({
            map: _this._map
        });
        _this._marker = _marker;
    }

    this._initEditMarker = function(eventCallback) {
        var _marker = new google.maps.Marker({
            map: _this._map,
            draggable: true
        });
        google.maps.event.addListener(_marker, 'dragend', function() {
            _this._onMarkerDrop(eventCallback);
        });
        _this._marker = _marker;
    }
}
