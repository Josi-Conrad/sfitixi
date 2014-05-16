/**
 * Created by faustos on 16.05.14.
 */
function GoogleMapWrapper() {
    var _this = this;

    this._canvas = null;
    this._isCanvasVisible = false;

    this._map = null;
    this._marker = null;

    this.init = function(canvasId) {
        var _canvas = _this._adjustCanvas($('#'+canvasId));
        _this._canvas = _canvas.get(0);

    }

    this.showAddress = function(lat, lng, draggable, eventCallback) {
        if(!_this._isCanvasVisible) {
            _this.showCanvas();
        }
        _this._moveMarker(_this._createLatLng(lat,lng));
    }

    this._moveMarker = function(latLng) {
        _this._map.setCenter(latLng);
        _this._marker.setPosition(latLng);
    }

    this.showCanvas = function() {
        $(_this._canvas).show();
        _this._initMap();
        _this._initMarker();
        _this._isCanvasVisible = true;
    }

    this.hideCanvas = function() {
        $(_this._canvas).hide();
        _this._isCanvasVisible = false;
    }

    this._createLatLng = function(lat, lng) {
        return new google.maps.LatLng(lat, lng);
    }

//    this._createMarker = function(latLng, draggable, title) {
//        var _draggable = (undefined !== draggable) ? draggable : false,
//            _marker = new google.maps.Marker({
//                position: latLng,
//                map: _this._map,
//                draggable:_draggable,
//                title:"Drag me!"
//            });
//        _this._marker = _marker;
//    }

    this._adjustCanvas = function(canvas) {
        var _width = canvas.width(),
            _paddedWitdh = _width+30,
            _height = canvas.height();
        canvas.css('right','-'+_paddedWitdh+'px');
        canvas.css('top','-'+_height/2+'px');
        return canvas;
    }

    this._initMap = function() {
        _this._map = new google.maps.Map(_this._canvas, _this._getDefaultMapOptions());
    }

    this._initMarker = function() {
        var _marker = new google.maps.Marker({
            map: _this._map
        });
        _this._marker = _marker;
    }

    this._getDefaultMapOptions = function() {
        var _mapOptions = {
            zoom: 17,
            mapTypeId: google.maps.MapTypeId.HYBRID
        };
        return _mapOptions;
    }
}
