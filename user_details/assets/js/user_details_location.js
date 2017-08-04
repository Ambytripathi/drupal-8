jQuery( document ).ready(function() {  

/****************************************** Address Autocomplete Field ******************************************/
jQuery('#edit-address .panel-body').append('<div id="map"></div>'); 

function populatePlaceDetials(place, strElement, cityElement, stateElement, zipElement, latitudeElement, longitudeElement) {
  console.log(place);

  var streetAddress;
  var city;
  var state;
  var zip_code;
  var latitude;
  var longitude;
  if (place.address_components) {
    address = [
      (place.address_components[0] && place.address_components[1].short_name || ''), (place.address_components[1] && place.address_components[1].short_name || ''), (place.address_components[2] && place.address_components[2].short_name || '')
    ].join(' ');

    for (var i = 0; i < place.address_components.length; i++) {
      for (var j = 0; j < place.address_components[i].types.length; j++) {
        if (place.address_components[i].types[j] == "street_number") {
          streetAddress = place.address_components[i].long_name;
        }
        if (place.address_components[i].types[j] == "route") {
          if (streetAddress) {
            streetAddress += (streetAddress.length != 0) ? ' ' + place.address_components[i].short_name : place.address_components[i].short_name;
            strElement.value = streetAddress;
          }
        }
        if (place.address_components[i].types[j] == "locality") {
          city = place.address_components[i].long_name;
          //cityElement.value = city;
        }
        if (place.address_components[i].types[j] == "administrative_area_level_1") {
          state = place.address_components[i].short_name;
          stateElement.value = state;
        }
        if (place.address_components[i].types[j] == "postal_code") {
          zip_code = place.address_components[i].long_name;
          zipElement.value = zip_code;
          //alert("zip code- " + zip_code);
        }
      }
    }
    var geocoder = new google.maps.Geocoder();
    var address = place.address_components[0].short_name+' '+place.address_components[1].short_name+' '+place.address_components[2].short_name;
    geocoder.geocode({ 'address': address }, function (results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			var latitude = results[0].geometry.location.lat();
			var longitude = results[0].geometry.location.lng();
			latitudeElement.value = latitude;
			longitudeElement.value = longitude;
			/**************************************************/
			
			 var latlng = new google.maps.LatLng(latitude, longitude);
			 var map = new google.maps.Map(document.getElementById('map'), {
				  center: latlng,
				  zoom: 10
			});
			var marker = new google.maps.Marker({
			  map: map,
			  position: latlng,
			  draggable: false,
			  anchorPoint: new google.maps.Point(0, -29)
		   });
			var infowindow = new google.maps.InfoWindow();   
			google.maps.event.addListener(marker, 'click', function() {
			  var iwContent = '';
			});
			
			/**************************************************/
			
		} else {
			alert("Request failed.")
		}
    });
  }
}


window.onload = function() {
  sourceElement = document.getElementById("cbParamVirtual1");
  source = sourceElement.value;

  var country_value = jQuery("#countryvalue").val();
  var autocomplete = new google.maps.places.Autocomplete(sourceElement);

  jQuery("#edit-field-country").change(function(){
    var country_value = jQuery("#edit-field-country").val();
    //if(country_value){
 	jQuery.ajax({
				url: "/getcountrycode",
				type: "POST",
				data: {"country_id":country_value},
				success: function (response) {
					 var json = jQuery.parseJSON(response);
					//alert(json["country_code"]);
					 autocomplete.setComponentRestrictions({country:json["country_code"]});
				},
				error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
				}
			});
    
  });


  google.maps.event.addListener(autocomplete, 'place_changed', function() {
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }
    pickStr = document.getElementById("InsertRecordPICKUP_STREET_ADDRESS");
    pickCity = document.getElementById("InsertRecordPICKUP_CITY");
    pickState = document.getElementById("InsertRecordPICKUP_STATE");
    pickZip = document.getElementById("InsertRecordPICKUP_ZIP");
    pickLatitude = document.getElementById("InsertRecordPICKUP_LATITUDE");
    pickLongitude = document.getElementById("InsertRecordPICKUP_LONGITUDE");
    populatePlaceDetials(place, pickStr, pickCity, pickState, pickZip, pickLatitude, pickLongitude);
  });
  /*******************************************************************/
  var lat = jQuery("#InsertRecordPICKUP_LATITUDE").val();
	var long = jQuery("#InsertRecordPICKUP_LONGITUDE").val();
  var latlng = new google.maps.LatLng(lat, long);
		var map = new google.maps.Map(document.getElementById('map'), {
		  center: latlng,
		  zoom: 10
    });
    var marker = new google.maps.Marker({
      map: map,
      position: latlng,
      draggable: false,
      anchorPoint: new google.maps.Point(0, -29)
   });
    var infowindow = new google.maps.InfoWindow();   
    google.maps.event.addListener(marker, 'click', function() {
      var iwContent = '';
    });
  
  /*******************************************************************/
}

/**************************************************************************************************************/

	jQuery("#edit-where-do-you-live, #edit-address").removeClass("panel panel-default");
	jQuery("#edit-where-do-you-live > div, #edit-address > div").removeClass("panel-heading");
	jQuery("#edit-where-do-you-live > div, #edit-address > div").removeClass("panel-body");

});

jQuery(function(){
	  jQuery('#edit-field-country').on("change",function(){
		  var countryname = jQuery('#edit-field-country option:selected').val();
		  var countryhtml = jQuery('#edit-field-country option:selected').html();
		  var options;
		  var post_data = 'countryname='+countryname;
			jQuery.post('getcity', post_data, function(response){
				if(response){
					jQuery(response).each(function(k,v){
						options += '<option value="'+response[k]+'">'+response[k]+'</option>';
					})
				}
				jQuery('#InsertRecordPICKUP_CITY').html(options);
			},'json');
		  jQuery('#cbParamVirtual1').val(countryhtml+' ');
		  jQuery('#edit-field-address2').val(countryhtml+' ');
	  });
});
		
