/**
 * Created by faustos on 16.05.14.
 */
function GoogleMapWrapper() {
    var _this = this;

    this._googleMap = null;

    this.init = function(overlayId) {
        var _overlay = document.getElementById(overlayId),
            mapOptions = {
                zoom: 8,
                center: new google.maps.LatLng(-34.397, 150.644)
            };
        if(_overlay) {
            _this._googleMap = new google.maps.Map(_overlay, mapOptions);
        }
//        console.log(_this._googleMap);
    }


}
