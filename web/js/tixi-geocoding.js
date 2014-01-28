/**
 * Created with JetBrains PhpStorm.
 * User: jonasse
 * Date: 21.11.13
 * Time: 15:41
 * To change this template use File | Settings | File Templates.
 */
/* Derived from Google Maps, Geocoding service */
/* 21.11.2013 martin jonasse initial file for displaying addresse information and retrieving geocode ------ */
/* 09.01.2014 martin jonasse if applicable, write error text (!= ROOFTOP) to database --------------------- */

var geocoder;
var map;

function showGoogleMap( mylat, mylng )
{/* called as soon as an address has been successfully fetched from google */
    var latlng = new google.maps.LatLng( mylat, mylng );
    var myOptions = {
        zoom: 15,
        center: latlng
    };
    if (map === undefined ) {
        map = new google.maps.Map($('#mymap')[0], myOptions);
    };
    var marker = new google.maps.Marker({
        position: latlng,
        map: map
    });
    $('#mymodal').dialog({
        width: 675,
        height: 700,
        modal: true
    });
    google.maps.event.trigger(map, 'resize');
    map.setCenter(marker.getCurrentPosition());
}

function codeAddress()
{/* called as soon as the "googlen" button is clicked */
    var myaddress = document.getElementById('fpw_strasse_nr').value + " " +
        document.getElementById('fpw_postleitzahl').value + " " +
        document.getElementById('fpw_ortsname').value + " " +
        document.getElementById('fpw_land').value;

    geocoder.geocode( { 'address': myaddress},
        function(results, status)
        {/* runs as soon as google answers the address request */
            if (status == google.maps.GeocoderStatus.OK)
            {/* write geocode to form element */
                var gc = document.getElementById('fpw_geocode');
                gc.value = results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
                if (results[0].geometry.location_type != "ROOFTOP")
                {
                    alert("Adresse nicht präzise sondern eine Annäherung, bitte überprüfen und ändern!");
					gc.value = results[0].geometry.location_type;
                }
                /* display address in a modal popup window */
                showGoogleMap(results[0].geometry.location.lat(), results[0].geometry.location.lng());
            }
            else
            {
                alert('Adresssuche mit Google Geocode war nicht erfolgreich, aus folgendem Grund: ' + status);
            }
        });
}

function initialize()
{/* called as soon as page is loaded */
    geocoder = new google.maps.Geocoder();
}

google.maps.event.addDomListener(window, 'load', initialize);
