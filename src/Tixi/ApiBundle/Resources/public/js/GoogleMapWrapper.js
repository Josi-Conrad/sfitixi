/**
 * Created by faustos on 16.05.14.
 */
function GoogleMapWrapper() {
    var _this = this;

    this.DISPLAYSTATE = 1;
    this.EDITSTATE = 2;

    this._canvasWrapper = null;
    this._canvas = null;
    this._isCanvasVisible = false;

    this._map = null;
    this._marker = null;

    this._state = null;

    this.init = function(canvasWrapper, canvas) {
        _this._canvasWrapper = canvasWrapper;
        _this._canvas = canvas
    }

    this.displayAddress = function(lat, lng) {
        if(!_this._isCanvasVisible) {
            _this.showCanvas();
        }
        if(_this._state !== _this.DISPLAYSTATE) {
            _this._switchToDisplayState();
        }
        _this._moveMarker(_this._createLatLng(lat,lng));
    }

    this.displayEditableAddress = function(lat, lng, eventCallback) {

    }

    this._switchToDisplayState = function() {
        _this._initDisplayMap();
        _this._initDisplayMarker();
        _this._state = _this.DISPLAYSTATE;
    }

    this._switchToEditState = function() {
        _this._state = _this.EDITSTATE;
    }

    this._moveMarker = function(latLng) {
        _this._map.setCenter(latLng);
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

//    this._adjustCanvas = function(canvas) {
//        var _width = canvas.width(),
//            _paddedWitdh = _width+30,
//            _height = canvas.height();
////        canvas.css('right','-'+_paddedWitdh+'px');
////        canvas.css('top','-'+_height/2+'px');
//        return canvas;
//    }

    this._initDisplayMap = function() {
        _this._map = new google.maps.Map(_this._canvas, {
            zoom: 17,
            disableDefaultUI: true,
            mapTypeId: google.maps.MapTypeId.HYBRID
        });
    }

    this._initEditMap = function() {
        _this._map = new google.maps.Map(_this._canvas, {
            zoom: 17,
            mapTypeId: google.maps.MapTypeId.HYBRID
        });
    }

    this._initDisplayMarker = function() {
        var _marker = new google.maps.Marker({
            map: _this._map
        });
        _this._marker = _marker;
    }

    this._initEditMarker = function() {
        var _marker = new google.maps.Marker({
            map: _this._map,
            draggable: true
        });
        _this._marker = _marker;
    }
}
