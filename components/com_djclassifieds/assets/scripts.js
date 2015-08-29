
var MAPFILES_URL = "http://maps.gstatic.com/intl/en_us/mapfiles/";

var map = null;
var geocoder = null;
var shadow = null;
var clickIcon = null;
var clickMarker = null;
var markers = null;
var selected = null;
var infowindow = null;
var boundsOverlay = null;
var viewportOverlay = null;
var initialized = false;
var hashFragment = "";

var GeocoderStatusDescription = {
  "OK": "The request did not encounter any errors",
  "UNKNOWN_ERROR": "A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known",
  "OVER_QUERY_LIMIT": "The webpage has gone over the requests limit in too short a period of time",
  "REQUEST_DENIED": "The webpage is not allowed to use the geocoder for some reason",
  "INVALID_REQUEST": "This request was invalid",
  "ZERO_RESULTS": "The request did not encounter any errors but returns zero results",
  "ERROR": "There was a problem contacting the Google servers"
};

var GeocoderLocationTypeDescription = {
  "ROOFTOP": "The returned result reflects a precise geocode.",
  "RANGE_INTERPOLATED": "The returned result reflects an approximation (usually on a road) interpolated between two precise points (such as intersections). Interpolated results are generally returned when rooftop geocodes are unavilable for a street address.",
  "GEOMETRIC_CENTER": "The returned result is the geometric center of a result such a line (e.g. street) or polygon (region).",
  "APPROXIMATE": "The returned result is approximate."
}

function init() {
  var params = parseUrlParams();
  clearOptions();
  setOptions(params);

  var mapOptions = {
    'zoom': (params.zoom ? params.zoom : 1),
    'center': (params.center ? params.center : new google.maps.LatLng(0.0,0.0)),
    'mapTypeId': google.maps.MapTypeId.ROADMAP,
    'scaleControl': true
  }
  map = new google.maps.Map(document.getElementById("map"), mapOptions);
  
  geocoder = new google.maps.Geocoder();
  
  infowindow = new google.maps.InfoWindow({
    'size': new google.maps.Size(292, 120)
  });
  
  shadow = new google.maps.MarkerImage(
    MAPFILES_URL + "shadow50.png",
    new google.maps.Size(37, 34),
    new google.maps.Point(0, 0),
    new google.maps.Point(10, 34)
  );
   
  clickIcon = new google.maps.MarkerImage(
    MAPFILES_URL + 'dd-start.png',
    new google.maps.Size(20, 34),
    new google.maps.Point(0, 0),
    new google.maps.Point(10, 34)
  );
    
  google.maps.event.addListener(map, 'click', onClickCallback);
  
  // Bounds changes are asynchronous in v3, so we have to wait for the idle
  // event to ensure that viewport biasing picks up the correct viewport
  google.maps.event.addListener(map, 'idle', function() {
    if (document.getElementById("query").value && ! initialized) {
      submitQuery();
    }
    initialized = true;
  });
  
  document.getElementById('query').onkeyup = function(e) {
    if (!e) var e = window.event;
    if (e.keyCode != 13) return;
    document.getElementById("query").blur();
    submitQuery();
  }
  
  setInterval(checkHashFragment, 200);
}

function onClickCallback(event){
    document.getElementById("query").value = event.latLng.toUrlValue(6);
    geocode({ 'latLng': event.latLng });

}

function checkHashFragment() {
  if (unescape(window.location.hash) != unescape(hashFragment)) {
    var params = parseUrlParams();
    clearOptions();
    setOptions(params);
    if (params.zoom && params.center) {
      map.setZoom(params.zoom);
      map.setCenter(params.center);
      initialized = false;
    } else if (document.getElementById("query").value) {
      submitQuery();
    }
  }
}

function parseUrlParams() {
  var params = {};
  
  if (window.location.search) {
    params.query = unescape(window.location.search.substring(1));
  }
  
  if (window.location.hash) {
    hashFragment = unescape(window.location.hash);
    var args = hashFragment.substring(1).split('&');
    
    for (var i=0 ; i<args.length ;i++) {
      var param = args[i].split('=');
      switch (param[0]) {
        case 'q':
          params.query = unescape(param[1]);
          break;
        case 'vpcenter':
          var center = parseLatLng(param[1]);
          if (center != null) {
            params.center = center;
          }
          break;
        case 'vpzoom':
          var zoom = parseInt(param[1]);
          if (! isNaN(zoom)) {
            params.zoom = zoom;
          }
          break;
        case 'country':
          params.country = unescape(param[1]);
          break;
        case 'language':
          params.language = unescape(param[1]);
          break;
      }
    }
  }
  
  return params;
}

function clearOptions() {
  document.getElementById("query").value = '';
  document.getElementById("biasViewport").checked = false;
  document.getElementById("country").value = '';
  document.getElementById("language").value = '';  
}

function setOptions(params) {
  if (params.query) {
    document.getElementById("query").value = params.query;
  }
  
  if (params.zoom && params.center) {
    document.getElementById("biasViewport").checked = true;
  }
  
  if (params.country) {
    document.getElementById("country").value = params.country;
  }

  if (params.language) {
    document.getElementById("language").value = params.language;
  }
}

function submitQuery() {
  var query = document.getElementById("query").value;
  if (/\s*^\-?\d+(\.\d+)?\s*\,\s*\-?\d+(\.\d+)?\s*$/.test(query)) {
    var latlng = parseLatLng(query);
    if (latlng == null) {
      document.getElementById("query").value = "";
    } else {
      geocode({ 'latLng': latlng });
    }
  } else {
    geocode({ 'address': query });
  }
}

function geocode(request) {  
  resetMap();
  var hash = '';
  
  if (request.latLng) {
    clickMarker = new google.maps.Marker({
      'position': request.latLng,
      'map': map,
      'title': request.latLng.toString(),
      'clickable': false,
      'icon': clickIcon,
      'shadow': shadow
    });
    hash = 'q=' + request.latLng.toUrlValue(6);
  } else {
    hash = 'q=' + request.address;
  }
  
  var vpbias = document.getElementById("biasViewport").checked;
  var country = document.getElementById("country").value;
  var language = document.getElementById("language").value;
  
  if (vpbias) {
    hash += '&vpcenter=' + map.getCenter().toUrlValue(6);
    hash += '&vpzoom=' + map.getZoom();
    request.bounds = map.getBounds();
  }
  
  if (country) {
    hash += '&country=' + country;
    request.country = country;
  }
  
  if (language) {
    hash += '&language=' + language;
    request.language = language;
  }

  hashFragment = '#' + escape(hash);
  window.location.hash = escape(hash);
  geocoder.geocode(request, showResults);
}

function parseLatLng(value) {
  value.replace('/\s//g');
  var coords = value.split(',');
  var lat = parseFloat(coords[0]);
  var lng = parseFloat(coords[1]);
  if (isNaN(lat) || isNaN(lng)) {
    return null;
  } else {
    return new google.maps.LatLng(lat, lng);
  }
}

function resetMap() {
  infowindow.close();

  if (clickMarker != null) {
    clickMarker.setMap(null);
    clickMarker = null;
  }
  if(markers){
	  for (var i=0 ; i<markers.length ;i++) {
	    markers[i].setMap(null);
	  }	
  }
  
  
  markers = [];
  selected = null;
  clearBoundsOverlays();
  
  document.getElementById("responseCount").style.display = "none";
  document.getElementById("matches").style.display = "none";
}

function showResults(results, status) {
  var reverse = (clickMarker != null);
  
  if (! results) {
    alert("Geocoder did not return a valid response");
  } else {
    document.getElementById("statusValue").innerHTML = status;
    document.getElementById("statusDescription").innerHTML = GeocoderStatusDescription[status];

    document.getElementById("responseInfo").style.display = "block";    
    document.getElementById("responseStatus").style.display = "block";

    if (status == google.maps.GeocoderStatus.OK) {
      document.getElementById("matchCount").innerHTML = results.length;       
      document.getElementById("responseCount").style.display = "block";
      plotMatchesOnMap(results, reverse);
    } else {
      if (! reverse) {
        map.setCenter(new google.maps.LatLng(0.0, 0.0));
        map.setZoom(1);
      }
    }
  }
}

function plotMatchesOnMap(results, reverse) {
  
  markers = new Array(results.length);
  var resultsListHtml = "";
  
  var openInfoWindow = function(resultNum, result, marker) {
  	
    return function() {
      if (selected != null) {
        document.getElementById('p' + selected).style.backgroundColor = "white";
        clearBoundsOverlays();
      }
      
      map.fitBounds(result.geometry.viewport);
      infowindow.setContent(getAddressComponentsHtml(result.address_components));
      infowindow.open(map, marker);      
      
      if (result.geometry.bounds) {
        boundsOverlay = new google.maps.Rectangle({
          'bounds': result.geometry.bounds,
          'strokeColor': '#ff0000',
          'strokeOpacity': 1.0,
          'strokeWeight': 2.0,
          'fillOpacity': 0.0
        });
        boundsOverlay.setMap(map);
        google.maps.event.addListener(boundsOverlay, 'click', onClickCallback);
        google.maps.event.addListener(boundsOverlay, 'click', document.id('latlong').value = result.geometry.location.toString());
          for (var ii = 0; ii < result.address_components.length; ii++) {    
		    //html += tr("Long name", result.address_components[i].long_name);
		    if(result.address_components[ii].types[0]=='country'){
		    	google.maps.event.addListener(boundsOverlay, 'click', document.id('g_country').value = result.address_components[ii].long_name);
		    	//alert(result.address_components[ii].long_name);
		    }
		    if(result.address_components[ii].types[0]=='administrative_area_level_1'){
		    	google.maps.event.addListener(boundsOverlay, 'click', document.id('g_area').value = result.address_components[ii].long_name);
		    	//alert(result.address_components[ii].long_name);
		    }
		     if(result.address_components[ii].types[0]=='locality'){
		    	google.maps.event.addListener(boundsOverlay, 'click', document.id('g_locality').value = result.address_components[ii].long_name);
		    	//alert(result.address_components[ii].long_name);
		    }		    
		  }
        
        
        document.getElementById('boundsLegend').style.display = 'block';
      } else {
        boundsOverlay = null;
      }
      
      viewportOverlay = new google.maps.Rectangle({
          'bounds': result.geometry.viewport,
          'strokeColor': '#0000ff',
          'strokeOpacity': 1.0,
          'strokeWeight': 2.0,
          'fillOpacity': 0.0
        });
      viewportOverlay.setMap(map);
      google.maps.event.addListener(viewportOverlay, 'click', onClickCallback);
      document.getElementById('viewportLegend').style.display = 'block';

      document.getElementById('p' + resultNum).style.backgroundColor = "#eeeeff";
      document.getElementById('matches').scrollTop =
        document.getElementById('p' + resultNum).offsetTop -
        document.getElementById('matches').offsetTop;
      selected = resultNum;
    }
  }
    
  for (var i = 0; i < results.length; i++) {
    var icon = new google.maps.MarkerImage(
      getMarkerImageUrl(i),
      new google.maps.Size(20, 34),
      new google.maps.Point(0, 0),
      new google.maps.Point(10, 34)
    );
    
    markers[i] = new google.maps.Marker({
      'position': results[i].geometry.location,
      'map': map,
      'icon': icon,
      'shadow': shadow
    });

    google.maps.event.addListener(markers[i], 'click', openInfoWindow(i, results[i], markers[i]));
    
    resultsListHtml += getResultsListItem(i, getResultDescription(results[i]));
    
  }
  
  document.getElementById("matches").innerHTML = resultsListHtml;
  document.getElementById("p0").style.border = "none";
  document.getElementById("matches").style.display = "block";

  if (reverse){
      // make a smooth movement to the clicked position
      map.panTo(clickMarker.getPosition());
      google.maps.event.addListenerOnce(map, 'idle', function(){
        selectMarker(0);
      });
  }

  else {
      zoomToViewports(results);
      selectMarker(0);
  }
  
  
}

function selectMarker(n) {
  google.maps.event.trigger(markers[n], 'click'); 
}

function zoomToViewports(results) {
  var bounds = new google.maps.LatLngBounds();


for (var i = 0; i < results.length; i++) {
    bounds.union(results[i].geometry.viewport);
  }

  map.fitBounds(bounds);
}

function getMarkerImageUrl(resultNum) {
  return MAPFILES_URL + "marker" + String.fromCharCode(65 + resultNum) + ".png";
}

function getResultsListItem(resultNum, resultDescription) {
  var html  = '<a onclick="selectMarker(' + resultNum + ')">';
      html += '<div class="info" id="p' + resultNum + '">';
      html += '<table><tr valign="top">';
      html += '<td style="padding: 2px"><img src="' + getMarkerImageUrl(resultNum) + '"/></td>';
      html += '<td style="padding: 2px">' + resultDescription + '</td>';
      html += '</tr></table>';
      html += '</div></a>';
  return html;
}

function getResultDescription(result) {
  var bounds = result.geometry.bounds;
  var html  = '<table class="tabContent">';
      html += tr('Address', result.formatted_address);
      html += tr('Types', result.types.join(", "));
      html += tr('Location', result.geometry.location.toString());
      html += tr('Bounds', (bounds ? boundsToHtml(bounds) : "None"));
      html += tr('Viewport', boundsToHtml(result.geometry.viewport));
      html += tr('Location type', result.geometry.location_type);
      if (result.partial_match) {
        html += tr('Partial match', 'Yes');
      }
      html += '</table>';
  return html;
}

function getAddressComponentsHtml(components) {
  var html = '<div class="infoWindowContent">' +
               '<table class="tabContent">';
               
  for (var i = 0; i < components.length; i++) {    
    html += tr("Long name", components[i].long_name);
    html += tr("Short name", components[i].short_name);
    html += tr("Types", components[i].types[0]);
    for (var j = 1; j < components[i].types.length; j++) {
      html += tr("", components[i].types[j]);
    }
    if (i < components.length-1) {
      html += br();
    }
  }
  
  html += '</table></div>';
  return html;
}

function tr(key, value) {
  return '<tr>' +
           '<td class="key">' + key + (key ? ':' : '') + '</td>' +
           '<td class="value">' + value + '</td>' +
         '</tr>';
}

function br() {
  return '<tr><td colspan="2"><div style="width: 100%; border-bottom: 1px solid grey; margin: 2px;"</td></tr>';
}

function clearBoundsOverlays() {
  if (boundsOverlay != null) {
    boundsOverlay.setMap(null);
    document.getElementById('boundsLegend').style.display = 'none';
  }
  if (viewportOverlay != null) {
    viewportOverlay.setMap(null);
    document.getElementById('viewportLegend').style.display = 'none';
  }
}

function boundsToHtml(bounds) {
  return '(' +
    bounds.getSouthWest().toUrlValue(6) +
    ') -<br/>(' +
    bounds.getNorthEast().toUrlValue(6) +
    ')';
}

	window.addEvent('domready', function(){
		init();
	});