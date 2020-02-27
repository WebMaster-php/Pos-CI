<?php
init_head();
?>
<style>
    .checkbox label::before {
        width: 20px;
        height: 20px;
        border: 2px solid #000;
    }

    .checkbox label::after {
        font-size: 13px;
    }

    .checkbox label {
        position: absolute;
        bottom: 24px;
        z-index: 999999;
        right: 45px;
    }

    .checkbox_input_area{
        padding-top: 15px;
    }

    .checkbox_input_area input[type="color"]{
        border: none;
        padding: 0px;
        box-shadow: none;
        width: 20px;
        height: 22px;
        background: none;
    }

    .colorpicker_div .input-group-addon{
        padding: 5px 10px !important;
    }

    .checkbox_input_area input[type="text"]{
        cursor: pointer;
        background: #fff;
        color: #000;
    }
</style>
<div id="wrapper">
    <!-- <div class="screen-options-area"></div>
    <div class="screen-options-btn">
        <?php echo _l('dashboard_options'); ?>
    </div> -->
    <div class="content">
        <div class="row" style="background: #fff">
            <div class="col-md-12 col-sm-12">
                <div class="panel_s">
                    <div class="panel_body">
                        <div class="row">
            <div class="col-sm-4 checkbox_input_area">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon primary">Address:</span>
                        <input type="text" class="form-control" id="pac-input" style="cursor: text;">
                    </div>
                </div>
            </div>
            <div class="col-sm-8 checkbox_input_area">
                
                <div class="col-sm-4"></div>
                <div class="col-sm-4 colorpicker_div">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="Customers" disabled="">
                            <span class="input-group-addon primary"><input class="js-color-picker" type="color" value="#ffa500" onchange="changePinColor('client')"></span>
                        </div>
                    </div>    
                    <div class="checkbox">
                        <input id="checkbox1" type="checkbox" value="customers" onchange="filter_map()" checked>
                        <label for="checkbox1"></label>
                    </div>
                </div>

                <div class="col-sm-4 colorpicker_div">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" value="Leads" disabled="">
                            <span class="input-group-addon primary"><input class="js-color-picker" type="color" value="#ff0000" onchange="changePinColor('lead')"></span>
                        </div>
                    </div>    
                    <div class="checkbox">
                        <input id="checkbox2" type="checkbox" value="leads" onchange="filter_map()" checked>
                        <label for="checkbox2"></label>
                    </div>
                </div>  
            </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        <div class="row" style="background: #fff">
            <div class="col-sm-12 col-md-12">
                <div class="panel_s">
                    <div class="panel_body">
                        <div id="map" style="height: 500px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var lcs = JSON.parse("<?php echo addslashes(json_encode($lcs)) ?>");
    console.log(lcs);
    var marker, infoWindow= null;
    var Ccolor = '#ffa500';
    var Lcolor = '#ff0000';
    var placeFound = 'unknown';
    var map, circle;
    var allMarkers = []; 
    function pinSymbol(color) {
    return {
        path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#000',
        strokeWeight: 2,
        scale: 1
    };
}
    function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, denideByUser, {maximumAge:600000, timeout:5000, enableHighAccuracy: true});

    } else {
        
        console.log("GEO LOCATION NOT SUPPORTED");
    }

    

}

function denideByUser(err)
{
    initMap(-34.397,150.644, 'denied');
    map.setZoom(3);
}
function showPosition(position) {
    initMap(position.coords.latitude,position.coords.longitude, 'allowed');
    
    whenAllDone(position.coords.latitude,position.coords.longitude);

    getNamebyCoords(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
}
    
function initMap(lt,ln, who) {

    marker = new google.maps.Marker({icon:pinSymbol("pink"), position: new google.maps.LatLng(lt, ln), animation: google.maps.Animation.BOUNCE});
    marker.addListener('click', function(){
        //map.setZoom(8);
        map.setCenter(marker.getPosition());
    });
    
    //allMarkers.push({type:'general', marker: marker});
    infoWindow = new google.maps.InfoWindow({
        content: 'Current Location'
    });

  map = new google.maps.Map(document.getElementById('map'), {
    center: new google.maps.LatLng(lt, ln),
    radius: 8046.72, // 5 miles
    zoom: 10
  });
  if(who == 'allowed')
    {marker.setMap(map);
      infoWindow.open(map, marker);
     
        circle = new google.maps.Circle({
            map: map,
            radius: 24139.5,
            fillColor: '#098765',
            strokeColor: '#FF0000',
            strokeOpacity: 0.7
        });

    
  circle.bindTo('center', marker, 'position');}
      else
        marker.setMap(null);
  searchBox();
}

function addMarkers(lt, ln, type, info, phone, address, f1, f2, uid)
{
    var url = "<?php echo base_url('admin') ?>"+(type == 'client' ? '/clients/client/' : '/leads/index/')+uid;
    var color = null;
    if(type == "lead")
        color = Lcolor;
    else if(type == "client")
        color = Ccolor;
    else
        color = "green";
    var cnt = 'Current Location';
    if(type != 'bySearch')
    {
        cnt = '';
        if(info != undefined){
            cnt += '<a target="_blank" href="'+url+'">';
            cnt += '<b>'+info+'</b></a>';}
        if(address != undefined)
            cnt += '<br>'+address;
        if(phone != undefined)
            cnt += '<br>'+phone+'<br>';
    }
    var marker = new google.maps.Marker({position: new google.maps.LatLng(lt,ln), icon: pinSymbol(color)});
    var iw = new google.maps.InfoWindow({content: cnt});
    if(getDistanceFromLatLonInKm(lt, ln, f1, f2) <= 15)
        marker.setMap(map);
    else
        marker.setMap(null);
    marker.addListener('click', function(){
        //map.setZoom(8);
        iw.open(map, marker);
        map.setCenter(marker.getPosition());
    });
    map.setCenter(new google.maps.LatLng(f1,f2));
    
    allMarkers.push({type:type, marker: marker});
}

function mapById(id, type, content = null)
{
    //map = new google.maps.Map(document.getElementById('map'), {zoom: 15});
    var markerOptions = null;
    var service = new google.maps.places.PlacesService(map);
    service.getDetails({placeId: id}, function(p,s){
    if(s == google.maps.places.PlacesServiceStatus.OK){
        console.log(p);
        if(type == "lead")
            markerOptions = {map: map, position: p.geometry.location, icon: pinSymbol("red")};
        else if(type == "client")
            markerOptions = {map: map, position: p.geometry.location, icon: pinSymbol("ornage")};
        else
            markerOptions = {map: map, position: p.geometry.location, icon: pinSymbol("green")};
        
        var marker = new google.maps.Marker(markerOptions);
        allMarkers.push({type: type, marker: marker});
        marker.addListener('click', function(){
        //map.setZoom(5);
        map.setCenter(marker.getPosition());
        });
        //map.setCenter(marker.getPosition());
        var infoWindow = new google.maps.InfoWindow({content: (content == null) ? 'Your Searched Area' : content});
        //infoWindow.setContent((content == null) ? 'Your Searched Area' : content);
        infoWindow.open(map, marker);
    }
 });
}

function getStringLocation(address, type, content)
{
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({address: address}, function(results, status){
        if(status == "OK")
         {
            mapById(results[0].place_id, type, content);
         }
        else
            console.log("some error");
    }); 
}

function getCoordsByString(address, type,name, phone, lt, ln, uid)
{
    var obj = 'hahaha';
    $.ajax({
        url: 'https://maps.googleapis.com/maps/api/geocode/json?address='+address+'&key=AIzaSyBuY9AvXvghSizyeeLnKfc1P_PrZRBoY3A',
        dataType: 'json',
        success: function(res){
            if(res.status == 'OK'){
                if(type == 'bySearch')
                {
                    circle.setCenter(new google.maps.LatLng(res.results[0].geometry.location.lat, res.results[0].geometry.location.lng));
                    whenAllDone(res.results[0].geometry.location.lat, res.results[0].geometry.location.lng);
                    return;
                }
                addMarkers(res.results[0].geometry.location.lat, res.results[0].geometry.location.lng, type, name, phone, address, lt, ln, uid);
            }
        },
        error: function(err){
            console.log('some error occured');
        }

    });
}

function getNamebyCoords(coords)
{
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({latLng: coords}, function(res, status){
        if(status == google.maps.GeocoderStatus.OK)
        {
            
            placeFound = res[0].formatted_address;
            document.getElementById('pac-input').value = placeFound;
            //infoWindow.setContent('Current Location');
            
        }
    });
}

function whenAllDone(lt, ln)
{
    lcs.filter(function(res){
        if(res.Type == 'client' && res.Lat && res.Lan){
            addMarkers(res.Lat, res.Lan, res.Type, res.Name, res.phone, res.Address, lt, ln, res.pkey);
        }
        else if(res.Type == 'client' && res.Address)
        {
            getCoordsByString(res.Address, res.Type, res.Name, res.phone, lt, ln, res.pkey);
        }
        else if(res.Type == 'lead' && res.Address)
        {
            getCoordsByString(res.Address, res.Type, res.Name, res.phone, lt, ln, res.pkey);
        }
        else
            {//console.log("going away");
            }
    });
}

function filter_map()
{
   var cmarks = document.getElementById('checkbox1').checked;
   var lmarks = document.getElementById('checkbox2').checked;

   for(var i =0; i<allMarkers.length; i++)
   {
    if(allMarkers[i].type == "client")
    {
        if(cmarks == true)
            allMarkers[i].marker.setMap(map);
        else
            allMarkers[i].marker.setMap(null);
    }
    if(allMarkers[i].type == "lead")
    {
        if(lmarks == true)
            allMarkers[i].marker.setMap(map);
        else
            allMarkers[i].marker.setMap(null);
    }
   } 

}

function searchBox()
{
    // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();
          
          if (places.length == 0) {
            alert('No Location Found Against This Address');
            return;
          }
          //marker.setMap(null);
          getCoordsByString(places[0].formatted_address, 'bySearch', 'whatever', '+xx xxx xxxx', 12, 21, 12);
          // Clear out the old markers.
          allMarkers.forEach(function(marker) {
            marker.marker.setMap(null);
          });
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {

            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }


            var icon = {
              url: place.icon,
              size: new google.maps.Size(150, 150),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            marker.setMap(null);
            markers.push(new google.maps.Marker({
              map: map,
              icon: pinSymbol("pink"),
              title: place.name,
              position: place.geometry.location,
              animation: google.maps.Animation.BOUNCE
            }));
            infoWindow = new google.maps.InfoWindow({
                content: 'Current Location'
            });
            infoWindow.open(map, markers[markers.length -1]);
            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
          map.setZoom(9);
        });

}

// getting distance betweern two locations
function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2-lat1);  // deg2rad below
  var dLon = deg2rad(lon2-lon1); 
  var a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
    Math.sin(dLon/2) * Math.sin(dLon/2)
    ; 
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
  var d = R * c; // Distance in km
  var miles = d*0.621371; // Distance in miles
  return miles;
}

function deg2rad(deg) {
  return deg * (Math.PI/180)
}
//end getting distance between two locations
</script>
<script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuY9AvXvghSizyeeLnKfc1P_PrZRBoY3A&libraries=places&callback=getLocation">
</script>
<script type="text/javascript">

function changePinColor(type)
{
    if(type == 'client')
        var color = Ccolor = $('.js-color-picker:eq(0)').val();
    else
        var color = Lcolor = $('.js-color-picker:eq(1)').val();
    console.log(color);
    allMarkers.forEach(function(marker){
        if(marker.type == type)
            marker.marker.setIcon(pinSymbol(color));
    });
}

</script>

<?php init_tail();  ?>