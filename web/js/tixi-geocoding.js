/**
 * Created with JetBrains PhpStorm.
 * User: jonasse
 * Date: 21.11.13
 * Time: 15:41
 * To change this template use File | Settings | File Templates.
 */
/* Derived from Google Maps, Geocoding service */
/* 21.11.2013 martin jonasse initial file for displaying addresse information and retrieving geocode */

var geocoder;
var map;

function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(46.818188,8.227511999);
    var mapOptions = {
        zoom: 7,
        center: latlng
    }
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

function codeAddress() {
    //var address = document.getElementById('address').value;

    var myaddress = document.getElementById('fpw_strasse_nr').value + " " +
        document.getElementById('fpw_postleitzahl').value + " " +
        document.getElementById('fpw_ortsname').value + " " +
        document.getElementById('fpw_land').value;

    geocoder.geocode( { 'address': myaddress},
        function(results, status)
        {
            if (status == google.maps.GeocoderStatus.OK)
            {
                map.setCenter(results[0].geometry.location);
                map.setZoom(15);
                var marker = new google.maps.Marker(
                    {
                        map: map,
                        position: results[0].geometry.location
                    });
                /* write geocode to form element */
                var gc = document.getElementById('fpw_geocode');
                gc.value = results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
                /* test the precision of the result */
                if (results[0].geometry.location_type != "ROOFTOP") {
                    alert("Adresse nicht präzise sondern eine Annäherung, bitte überprüfen und ändern!");
                }
            }
            else
            {
                alert('Adresssuche mit Google Geocode war nicht erfolgreich, aus folgendem Grund: ' + status);
            }
        });
}

google.maps.event.addDomListener(window, 'load', initialize);
