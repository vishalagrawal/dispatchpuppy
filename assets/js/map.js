// global variables
var ALPHA = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
var LOADED_MILES = ['#000000', 0.3, 6];
var EMPTY_MILES = ['#DB3A1B', 1, 2];
var MY_MAPTYPE_ID = 'JPD';
var CHECKBOX = '-CHECKBOX';
var SHIPPER = '-SHIPPER';
var SHIPPER_IMAGE = '-SHIPPER-IMAGE';
var CONSIGNEE = '-CONSIGNEE';
var CONSIGNEE_IMAGE = '-CONSIGNEE-IMAGE';
var EMPTY = '-EMPTY';
var PRIMARY_LANE_MARKER_FONT_SIZE = 16;
var SECONDARY_LANE_MARKER_FONT_SIZE = 12;
var PRIMARY = 'PRIMARY';
var SECONDARY = 'SECONDARY';

var DIV = '-DIV';

var map;
var markerBounds = new google.maps.LatLngBounds();
var directionsDisplay = new google.maps.DirectionsRenderer();
var directionsService = new google.maps.DirectionsService();
var poly_lines = {};
var all_marker = {};

// create empty object to store all alphabet combinations
var lane_marker_availibility = [];
var all_lane_marker_ids = {};

/*
 *
 * start of helper functions
 *
 */
function alpha(number)
{
	var quotient = Math.floor(number/ALPHA.length);

	if(quotient < 1)
	{
		return ALPHA[number];
	}
	else
	{
		var alpha1 = ALPHA[quotient-1];
		var alpha2 = ALPHA[number%ALPHA.length];
		return alpha1+alpha2;
	}
}
/*
 *
 * end of helper functions
 *
 */

function initialize() 
{
	// set custom style
	var mapStyle = [
		{
			featureType: 'road.local',
			elementType: 'all',
  			stylers: [
    			{ visibility: 'off' }
  			]
		},
		{
			featureType: 'road.arterial',
			elementType: 'all',
			stylers: [
    			{ visibility: 'off' }
  			]

  		},
  		{
			featureType: 'road',
			elementType: 'all',
			stylers: [
    			{ lightness: 30 }
  			]
		},
		{
			featureType: 'administrative.locality',
			elementType: 'labels.text',
  			stylers: [
    			{ lightness: 30 }
  			]
		},
		{
			featureType: 'administrative.neighborhood',
			elementType: 'labels.text',
  			stylers: [
    			{ visibility: 'off' }
    		]
		},
  		{
  			featureType: 'poi',
			elementType: 'all',
			stylers: [
    			{ visibility: 'off' }
  			]
		}
	];

	var styledMapOptions = {
		name: MY_MAPTYPE_ID
	};

	// set the map options
	var mapOptions = {
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		zoom: 7,
		center: center_location,
		mapTypeControlOptions: {
			mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
    	},
		mapTypeId: MY_MAPTYPE_ID
	};

	// render map
	map = new google.maps.Map(document.getElementById('map'), mapOptions);
	
	// add custom style
	var customMapType = new google.maps.StyledMapType(mapStyle, styledMapOptions);
	map.mapTypes.set(MY_MAPTYPE_ID, customMapType);
	map.setMapTypeId(MY_MAPTYPE_ID);
	
	/* 
	// code to show driving directions on map 
	directionsDisplay.setMap(map);
	*/

	/* 
	//code to show the driving directions text
	directionsDisplay.setPanel(document.getElementById("control-panel"));
	*/
}

function getLaneLocation(lane_id)
{
	// get the lane info
	var lane = all_lanes[lane_id];
	var shipper_primary_location = new google.maps.LatLng(lane.shipper_lat, lane.shipper_lng);
	
	if(document.getElementById(lane_id).className.split(' ').indexOf('active-lane') < 0)
	{
		var consignee_primary_location = new google.maps.LatLng(lane.consignee_lat, lane.consignee_lng);
		
		var marker_id = getLaneMarkerID(lane_id);

		// add marker for primary shipper
		var shipper_primary_title = lane.shipper_name;
		var shipper_primary_content = lane.shipper_name+'<br>'+lane.shipper_address+'<br>'+lane.shipper_city+' ,'+lane.shipper_state+' '+lane.shipper_zipcode;
		addMarker(shipper_primary_location, [SHIPPER, marker_id, PRIMARY_LANE_MARKER_FONT_SIZE], shipper_primary_title, shipper_primary_content, lane_id+SHIPPER);
		document.getElementById(lane_id+SHIPPER_IMAGE).src = shipperMarker(marker_id, PRIMARY_LANE_MARKER_FONT_SIZE);

		// add marker for primary consignee
		var consignee_primary_title = lane.consignee_name;
		var consignee_primary_content = lane.consignee_name+'<br>'+lane.consignee_address+'<br>'+lane.consignee_city+' ,'+lane.consignee_state+' '+lane.consignee_zipcode;
		addMarker(consignee_primary_location, [CONSIGNEE, marker_id, PRIMARY_LANE_MARKER_FONT_SIZE], consignee_primary_title, consignee_primary_content, lane_id+CONSIGNEE);
		document.getElementById(lane_id+CONSIGNEE_IMAGE).src = consigneeMarker(marker_id, PRIMARY_LANE_MARKER_FONT_SIZE);

		// crow fly from primary shipper to primary consignee
		//drawPath([shipper_primary_location, consignee_primary_location], LOADED_MILES, lane_id);

		// map directions from primary shipper to primary consignee
		calcRoute(shipper_primary_location, consignee_primary_location, LOADED_MILES, lane_id, PRIMARY);
		
		if(lane.secondary_lanes != null)
		{
			for(var sub_lane_id in lane.secondary_lanes)
			{
				// make the sub-lane div visible
				document.getElementById(lane_id+'-'+sub_lane_id).style.display = 'block';
			}
		}
		else
		{
			// show a run for empty miles	
			calcRoute(consignee_primary_location, shipper_primary_location, EMPTY_MILES, lane_id+EMPTY, PRIMARY);
		}
		
		// add the active-lane class to the lane div
		document.getElementById(lane_id).className += ' active-lane';
	}
	else
	{
		// remove the markers for primary shipper
		all_marker[lane_id+SHIPPER].setMap(null);
		document.getElementById(lane_id+SHIPPER_IMAGE).src = DEFAULT_MARKER_PRIMARY_SHIPPER;
		delete all_marker[lane_id+SHIPPER];

		// remove the path from primary shipper to consignee
		if(typeof(poly_lines[lane_id]) != 'undefined')
		{
			poly_lines[lane_id].setMap(null);
			delete poly_lines[lane_id];
		}
	
		// remove the markers for primary consignee
		all_marker[lane_id+CONSIGNEE].setMap(null);
		document.getElementById(lane_id+CONSIGNEE_IMAGE).src = DEFAULT_MARKER_PRIMARY_CONSIGNEE;
		delete all_marker[lane_id+CONSIGNEE];

		if(lane.secondary_lanes != null)
		{
			for(var sub_lane_id in lane.secondary_lanes)
			{
				var sub_lane_div_id = lane_id+'-'+sub_lane_id;
				if(document.getElementById(sub_lane_div_id).className.split(' ').indexOf('active-lane') > 0)
				{
					// remove the poly line from primary consignee to secondary shipper
					if(typeof(poly_lines[sub_lane_div_id+EMPTY]) != 'undefined')
					{
						poly_lines[sub_lane_div_id+EMPTY].setMap(null);
						delete poly_lines[sub_lane_div_id+EMPTY];
					}
					
					// remove the marker for secondary shipper
					all_marker[sub_lane_div_id+SHIPPER].setMap(null);
					document.getElementById(sub_lane_div_id+SHIPPER_IMAGE).src = DEFAULT_MARKER_SECONDARY_SHIPPER;
					delete all_marker[sub_lane_div_id+SHIPPER];

					// remove the poly line from secondary shipper to secondary consignee 
					if(typeof(poly_lines[sub_lane_div_id]) != 'undefined')
					{
						poly_lines[sub_lane_div_id].setMap(null);
						delete poly_lines[sub_lane_div_id];
					}

					// remove the marker for secondary consignee
					all_marker[sub_lane_div_id+CONSIGNEE].setMap(null);
					document.getElementById(sub_lane_div_id+CONSIGNEE_IMAGE).src = DEFAULT_MARKER_SECONDARY_CONSIGNEE;
					delete all_marker[sub_lane_div_id+CONSIGNEE];	
				}

				document.getElementById(sub_lane_div_id+EMPTY+DIV).innerHTML = '';

				// hide the sub-lane div 
				document.getElementById(sub_lane_div_id).style.display = 'none';

				document.getElementById(sub_lane_div_id).className = document.getElementById(sub_lane_div_id).className.replace( /(?:^|\s)active-lane(?!\S)/g , '' );
			}
		}
		else
		{
			// remove the empty run
			if(typeof(poly_lines[lane_id+EMPTY]) != 'undefined')
			{
				poly_lines[lane_id+EMPTY].setMap(null);
				delete poly_lines[lane_id+EMPTY];
			}

			document.getElementById(lane_id+EMPTY+DIV).innerHTML = '';
		}
		
		// delete the laneMarkerID
		deleteLaneMarkerID(lane_id);

		document.getElementById(lane_id).className = document.getElementById(lane_id).className.replace( /(?:^|\s)active-lane(?!\S)/g , '' );
	}
}

function getLaneMarkerID(lane_id)
{
	var index;
	// check if an object with the same lane_id already exist in the system
	if(typeof(all_lane_marker_ids[lane_id]) == 'undefined')
	{
		var i;
		if(lane_marker_availibility.length > 0)
		{
			//check for first available "0"
			for(i=0; i<lane_marker_availibility.length; i++)
			{
				if(lane_marker_availibility[i] === 0)
				{
					break;
				}
			}

			if(i < lane_marker_availibility.length)
			{
				lane_marker_availibility[i] = 1;
				index = i;
			}
			else
			{
				// add "1" to end of array
				lane_marker_availibility.push(1);
				index = lane_marker_availibility.length-1;
			}
		}
		// when the array is empty aka adding the first element
		else
		{
			// add the first element
			lane_marker_availibility.push(1);
			index = lane_marker_availibility.length-1;
		}

		var object = {
			lane_marker_id: index,
			sub_lanes: {
				sub_lane_marker_availibility: [],
				all_sub_lane_maker_ids: {}
			}
		};		
		all_lane_marker_ids[lane_id] = object;
	}
	else
	{
		index = all_lane_marker_ids[lane_id].lane_marker_id;
	}

	return alpha(index);
}

function deleteLaneMarkerID(lane_id)
{
	// switch the flag
	lane_marker_availibility[all_lane_marker_ids[lane_id].lane_marker_id] = 0;

	//delete the object
	delete all_lane_marker_ids[lane_id];
}

function getSubLaneLocation(lane_id, sub_lane_id)
{
	// get the lane info
	var lane = all_lanes[lane_id];
	var consignee_primary_location = new google.maps.LatLng(lane.consignee_lat, lane.consignee_lng);

	var sub_lane = lane.secondary_lanes[sub_lane_id];
	var sub_lane_div_id = lane_id+'-'+sub_lane_id;

	if(document.getElementById(sub_lane_div_id).className.split(' ').indexOf('active-lane') < 0)
	{
		var marker_id = getSubLaneMarkerID(lane_id, sub_lane_id);

		// add marker for secondary shipper
		var shipper_secondary_location = new google.maps.LatLng(sub_lane.shipper_lat, sub_lane.shipper_lng);
		var shipper_secondary_title = sub_lane.shipper_name;
		var shipper_secondary_content = sub_lane.shipper_name+'<br>'+sub_lane.shipper_address+'<br>'+sub_lane.shipper_city+' ,'+sub_lane.shipper_state+' '+sub_lane.shipper_zipcode;
		addMarker(shipper_secondary_location, [SHIPPER, marker_id, SECONDARY_LANE_MARKER_FONT_SIZE], shipper_secondary_title, shipper_secondary_content, sub_lane_div_id+SHIPPER);
		document.getElementById(sub_lane_div_id+SHIPPER_IMAGE).src = shipperMarker(marker_id, SECONDARY_LANE_MARKER_FONT_SIZE);

		// crow fly from primary consignee to secondary shipper
		//drawPath([consignee_primary_location, shipper_secondary_location], EMPTY_MILES, sub_lane_div_id+EMPTY);

		//map directions from primary consignee to secondary shipper
		calcRoute(consignee_primary_location, shipper_secondary_location, EMPTY_MILES, sub_lane_div_id+EMPTY, SECONDARY);

		// add marker for secondary consignee
		var consignee_secondary_location = new google.maps.LatLng(sub_lane.consignee_lat, sub_lane.consignee_lng);
		var consignee_secondary_title = sub_lane.consignee_name;
		var consignee_secondary_content = sub_lane.consignee_name+'<br>'+sub_lane.consignee_address+'<br>'+sub_lane.consignee_city+' ,'+sub_lane.consignee_state+' '+sub_lane.consignee_zipcode;
		addMarker(consignee_secondary_location, [CONSIGNEE, marker_id, SECONDARY_LANE_MARKER_FONT_SIZE], consignee_secondary_title, consignee_secondary_content, sub_lane_div_id+CONSIGNEE);
		document.getElementById(sub_lane_div_id+CONSIGNEE_IMAGE).src = consigneeMarker(marker_id, SECONDARY_LANE_MARKER_FONT_SIZE);
		// crow fly from secondary shipper to secondary consignee		
		//drawPath([shipper_secondary_location, consignee_secondary_location],LOADED_MILES, sub_lane_div_id);

		// map directions from secondary shipper to secondary consignee			
		calcRoute(shipper_secondary_location, consignee_secondary_location, LOADED_MILES, sub_lane_div_id, SECONDARY);

		document.getElementById(sub_lane_div_id).className += ' active-lane';
	}
	else
	{
		// remove the poly line from primary consignee to secondary shipper
		if(typeof(poly_lines[sub_lane_div_id+EMPTY]) != 'undefined')
		{
			poly_lines[sub_lane_div_id+EMPTY].setMap(null);
			delete poly_lines[sub_lane_div_id+EMPTY];
		}
					
		// remove the marker for secondary shipper
		all_marker[sub_lane_div_id+SHIPPER].setMap(null);
		document.getElementById(sub_lane_div_id+SHIPPER_IMAGE).src = DEFAULT_MARKER_SECONDARY_SHIPPER;
		delete all_marker[sub_lane_div_id+SHIPPER];

		// remove the poly line from secondary shipper to secondary consignee 
		if(typeof(poly_lines[sub_lane_div_id]) != 'undefined')
		{
			poly_lines[sub_lane_div_id].setMap(null);
			delete poly_lines[sub_lane_div_id];
		}
		
		// remove the marker for secondary consignee
		all_marker[sub_lane_div_id+CONSIGNEE].setMap(null);
		document.getElementById(sub_lane_div_id+CONSIGNEE_IMAGE).src = DEFAULT_MARKER_SECONDARY_CONSIGNEE;
		delete all_marker[sub_lane_div_id+CONSIGNEE];

		// remove the maker_id from the array and the object
		deleteSubLaneMarkerID(lane_id, sub_lane_id);

		// remove the empty miles
		document.getElementById(sub_lane_div_id+EMPTY+DIV).innerHTML = '';

		// remove active-lane class
		document.getElementById(sub_lane_div_id).className = document.getElementById(sub_lane_div_id).className.replace( /(?:^|\s)active-lane(?!\S)/g , '' );
	}
}

function getSubLaneMarkerID(lane_id, sub_lane_id)
{
	var index;	
	var alpha_id = all_lane_marker_ids[lane_id].lane_marker_id;

	/*
		var object = {
			lane_marker_id: index,
			sub_lanes: {
				sub_lane_marker_availibility: [],
				all_sub_lane_maker_ids: {}
			}
		};	
	*/

	// check if an object with the same lane_id already exist in the system
	if(typeof(all_lane_marker_ids[lane_id].sub_lanes.all_sub_lane_maker_ids[sub_lane_id]) == 'undefined')
	{
		var availibilty = all_lane_marker_ids[lane_id].sub_lanes.sub_lane_marker_availibility;
		var i;

		if(availibilty.length > 0)
		{
			//check for first available "0"
			for(i=0; i<availibilty.length; i++)
			{
				if(availibilty[i] === 0)
				{
					break;
				}
			}

			if(i < availibilty.length)
			{
				all_lane_marker_ids[lane_id].sub_lanes.sub_lane_marker_availibility[i] = 1;
				index = i;
			}
			else
			{
				// add "1" to end of array
				all_lane_marker_ids[lane_id].sub_lanes.sub_lane_marker_availibility.push(1);
				index = all_lane_marker_ids[lane_id].sub_lanes.sub_lane_marker_availibility.length-1;
			}
		}
		// when the array is empty aka adding the first element
		else
		{
			// add the first element
			all_lane_marker_ids[lane_id].sub_lanes.sub_lane_marker_availibility.push(1);
			index = all_lane_marker_ids[lane_id].sub_lanes.sub_lane_marker_availibility.length-1;
		}

		var sub_lane_object = {
			sub_lane_marker_id: index
		};

		all_lane_marker_ids[lane_id].sub_lanes.all_sub_lane_maker_ids[sub_lane_id] = sub_lane_object;
	}
	else
	{
		index = all_lane_marker_ids[lane_id].sub_lanes.all_sub_lane_maker_ids[sub_lane_id];
	}

	return (alpha(alpha_id)+(index+1));
}

function deleteSubLaneMarkerID(lane_id, sub_lane_id)
{
	// switch the flag
	var index = all_lane_marker_ids[lane_id].sub_lanes.all_sub_lane_maker_ids[sub_lane_id].sub_lane_marker_id;
	all_lane_marker_ids[lane_id].sub_lanes.sub_lane_marker_availibility[index] = 0;

	//delete the object
	delete all_lane_marker_ids[lane_id].sub_lanes.all_sub_lane_maker_ids[sub_lane_id];
}

function addMarker(location, icon, title, info, lane_id)
{	
	var create_icon;

	if(icon[0] === SHIPPER)
	{
		create_icon = shipperMarker(icon[1],icon[2]);
	}
	else if(icon[0] === CONSIGNEE)
	{
		create_icon = consigneeMarker(icon[1],icon[2]);
	}
	else
	{
		create_icon = 'https://mts0.google.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1';
	}

	var marker = new google.maps.Marker({
		position: location,
		icon: create_icon,
		map: map,
		title: title
	});

	//marker.setAnimation(google.maps.Animation.DROP);
	markerBounds.extend(location);
	map.fitBounds(markerBounds);
	all_marker[lane_id] = marker;
}

function shipperMarker(text,size)
{
	return 'https://mts0.google.com/vt/icon/text='+text+'&psize='+size+'&font=fonts/Roboto-Regular.ttf&color=ff003300&name=icons/spotlight/spotlight-waypoint-a.png&ax=44&ay=48&scale=1';
}

function consigneeMarker(text,size)
{
	return 'https://mts0.google.com/vt/icon/text='+text+'&psize='+size+'&font=fonts/Roboto-Regular.ttf&color=ff330000&name=icons/spotlight/spotlight-waypoint-b.png&ax=44&ay=48&scale=1';
}

function calcRoute(origin, destination, path_style_info, lane_id, type)
{
	var request = {
		origin: origin,
		destination: destination,
		travelMode: google.maps.TravelMode.DRIVING
	};

	directionsService.route(request, function(result, status) {
		if (status == google.maps.DirectionsStatus.OK) 
		{
  			/*
  			//code to show the driving directions line
  			directionsDisplay.setDirections(result); 
  			*/

  			allLatLong = result.routes[0].overview_path;
  			drawPath(allLatLong, path_style_info, lane_id);

  			//var driving_time = getDrivingTime(origin,destination);

  			if(lane_id.indexOf(EMPTY) > 0)
  			{
  				var distance = result.routes[0].legs[0].distance.text;
  				if(type === PRIMARY)
  				{
  					document.getElementById(lane_id+DIV).innerHTML = '<b style="color:#8E8E93;"> &middot; </b>' + distance;
  				}
  				else
  				{
  					document.getElementById(lane_id+DIV).innerHTML = distance + '<b style="color:#8E8E93;"> &middot; </b>';
  				}
  			}
  		}
  		else
  		{
  			alert("Calc Route was not successful for the following reason: " + status);
  		}
  	});
}

function drawPath(path, path_style_info, lane_id)
{	
	var myLine = new google.maps.Polyline({
		map: map,
		path: path,
		strokeColor: path_style_info[0],
		strokeOpacity: path_style_info[1],
		strokeWeight: path_style_info[2]
		//zIndex: path_style_info[3]
	});

	poly_lines[lane_id] = myLine;
}

function getDrivingTime(origin, destination)
{
	var service = new google.maps.DistanceMatrixService();
	service.getDistanceMatrix(
  	{
    	origins: [origin],
    	destinations: [destination],
    	travelMode: google.maps.TravelMode.DRIVING,
    	unitSystem: google.maps.UnitSystem.IMPERIAL,
    	durationInTraffic: true,
    	avoidHighways: false,
    	avoidTolls: false
  	}, callback);
}

function callback(response, status) 
{
  if (status != google.maps.DistanceMatrixStatus.OK) 
  {
    alert('Error was: ' + status);
  	return null;
  }
  else
  {
  	var origins = response.originAddresses;
    var destinations = response.destinationAddresses;

    for (var i = 0; i < origins.length; i++) 
    {
      var results = response.rows[i].elements;
      for (var j = 0; j < results.length; j++) 
      {
        var element = results[j];
        var distance = element.distance.text;
        var duration = element.duration.text;
        //var from = origins[i];
        //var to = destinations[j];
      }
      alert(distance+','+duration);
    }
  }
}

function showCommodity(commodity)
{
	alert(commodity);
}

/*
 *
 * controller - get_customer_location
 * view - get_customer_location
 *
 */

var geocoder;

var LAT = '-LAT';
var LNG = '-LNG';
var STATUS = '-STATUS';

function getLatLng(customer_id)
{
	var address = customers_without_lat_lng[customer_id].address + ',' + customers_without_lat_lng[customer_id].city + ',' + customers_without_lat_lng[customer_id].state + ',' + customers_without_lat_lng[customer_id].zipcode;

	updateLatLng(address, customer_id);
}

function updateLatLng(address, customer_id)
{
	geocoder = new google.maps.Geocoder();

	geocoder.geocode( { 'address': address}, function(results, status) {
	    if (status == google.maps.GeocoderStatus.OK) 
	    {
	        var lat = results[0].geometry.location.lat();
	        var lng = results[0].geometry.location.lng();
	        //addMarker(consignee_secondary_location, [CONSIGNEE, marker_id, SECONDARY_LANE_MARKER_FONT_SIZE], consignee_secondary_title, consignee_secondary_content, sub_lane_div_id+CONSIGNEE);
	        updateRecord(customer_id, lat, lng);
	    }
	    else 
	    {
	    	alert("Geocode was not successful for the following reason: " + status);
	    }
    });
}

function updateRecord(customer_id, lat, lng)
{
	var xmlhttp;

	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
  		xmlhttp=new XMLHttpRequest();
  	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
    	{
    		document.getElementById(customer_id+LAT).innerHTML = lat;
	        document.getElementById(customer_id+LNG).innerHTML = lng;
    		document.getElementById(customer_id+STATUS).innerHTML=xmlhttp.responseText;
    		//addMarker(consignee_secondary_location, [CONSIGNEE, marker_id, SECONDARY_LANE_MARKER_FONT_SIZE], consignee_secondary_title, consignee_secondary_content, sub_lane_div_id+CONSIGNEE);
    	}
  	}
	xmlhttp.open("GET","get_customer_location/update_customer_lat_lng/"+customer_id+"/"+lat+"/"+lng,true);
	xmlhttp.send();
}

/*
 *
 * controller - map_inbound
 * view - map_inbound
 *
 */

 function getConsigneeLocation(consignee_id)
 {
	// get the consignee information
	var consignee = all_combinations[consignee_id];

	// adding a marker
	if(document.getElementById(consignee_id).className.split(' ').indexOf('active-consignee') < 0)
	{
 		// get the consignee location for marker
 		var consignee_location = new google.maps.LatLng(consignee.consignee_lat, consignee.consignee_lng);
 		
 		// get marker_id for the marker
 		var marker_id = getLaneMarkerID(consignee_id);

 		// set the title and content for the marker
 		var consignee_title = consignee.consignee_name;
		var consignee_content = consignee.consignee_name+'<br>'+consignee.consignee_city+' ,'+consignee.consignee_state;

		// add marker
		addMarker(consignee_location, [CONSIGNEE, marker_id, PRIMARY_LANE_MARKER_FONT_SIZE], consignee_title, consignee_content, consignee_id+CONSIGNEE);
		document.getElementById(consignee_id+CONSIGNEE_IMAGE).src = consigneeMarker(marker_id, PRIMARY_LANE_MARKER_FONT_SIZE);

		// check if shipper exisit
		if(consignee.shippers != null)
		{
			for(var shipper_id in consignee.shippers)
			{
				var shipper_div_id = consignee_id+'-'+shipper_id;
				// display the shippr divs
				document.getElementById(shipper_div_id).style.display = 'block';
			}
		}
		else
		{
			/* do something */
		}

		// add the active-consignee class to the consignee div
		document.getElementById(consignee_id).className += ' active-consignee';
	}
	else
	{
		// remove the marker for consignee
		all_marker[consignee_id+CONSIGNEE].setMap(null);
		document.getElementById(consignee_id+CONSIGNEE_IMAGE).src = DEFAULT_MARKER_PRIMARY_CONSIGNEE;
		delete all_marker[consignee_id+CONSIGNEE];

		// check if shipper exisit
		if(consignee.shippers != null)
		{
			for(var shipper_id in consignee.shippers)
			{
				var shipper_div_id = consignee_id+'-'+shipper_id;
				if(document.getElementById(shipper_div_id).className.split(' ').indexOf('active-shipper') > 0)
				{
					// remove the marker for secondary shipper
					all_marker[shipper_div_id+SHIPPER].setMap(null);
					document.getElementById(shipper_div_id+SHIPPER_IMAGE).src = DEFAULT_MARKER_PRIMARY_SHIPPER;
					delete all_marker[shipper_div_id+SHIPPER];

					// remove the poly line from shipper to consignee 
					if(typeof(poly_lines[shipper_div_id]) != 'undefined')
					{
						poly_lines[shipper_div_id].setMap(null);
						delete poly_lines[shipper_div_id];
					}
				}	
				
				// remove active-shipper class from the shipper div
				document.getElementById(shipper_div_id).className = document.getElementById(shipper_div_id).className.replace( /(?:^|\s)active-shipper(?!\S)/g , '' );

				// hide the shipper div
				document.getElementById(shipper_div_id).style.display = 'none';
			}
		}
		else
		{
			/* do something */
		}

		// delete the laneMarkerID
		deleteLaneMarkerID(consignee_id);

		// remove the active-consignee class from the consignee div
		document.getElementById(consignee_id).className = document.getElementById(consignee_id).className.replace( /(?:^|\s)active-consignee(?!\S)/g , '' );
	}	
 }

 function getShipperLocation(consignee_id, shipper_id)
 {
 	// get the consignee info
	var consignee = all_combinations[consignee_id];

	var shipper = consignee.shippers[shipper_id];
	var shipper_div_id = consignee_id+'-'+shipper_id;

	if(document.getElementById(shipper_div_id).className.split(' ').indexOf('active-shipper') < 0)
	{
		// get consignee location for marker
		var consignee_location = new google.maps.LatLng(consignee.consignee_lat, consignee.consignee_lng);

		// get shipper location for marker
		var shipper_location = new google.maps.LatLng(shipper.shipper_lat, shipper.shipper_lng);
		
		// get the marker id for the marker
		var marker_id = getSubLaneMarkerID(consignee_id, shipper_id);

		// set the title and content for the shipper
		var shipper_title = shipper.shipper_name;
		var shipper_content = shipper.shipper_name+'<br>'+shipper.shipper_city+' ,'+shipper.shipper_state;

		//add marker
		addMarker(shipper_location, [SHIPPER, marker_id, SECONDARY_LANE_MARKER_FONT_SIZE], shipper_title, shipper_content, shipper_div_id+SHIPPER);
		document.getElementById(shipper_div_id+SHIPPER_IMAGE).src = shipperMarker(marker_id, SECONDARY_LANE_MARKER_FONT_SIZE);

		// map directions from secondary shipper to secondary consignee			
		calcRoute(shipper_location, consignee_location, LOADED_MILES, shipper_div_id, SECONDARY);

		document.getElementById(shipper_div_id).className += ' active-shipper';
	}
	else
	{
		// remove the marker at shipper
		all_marker[shipper_div_id+SHIPPER].setMap(null);
		document.getElementById(shipper_div_id+SHIPPER_IMAGE).src = DEFAULT_MARKER_PRIMARY_SHIPPER;
		delete all_marker[shipper_div_id+SHIPPER];

		// remove the poly line from shipper to consignee 
		if(typeof(poly_lines[shipper_div_id]) != 'undefined')
		{
			poly_lines[shipper_div_id].setMap(null);
			delete poly_lines[shipper_div_id];
		}

		// remove the maker_id from the array and the object
		deleteSubLaneMarkerID(consignee_id, shipper_id);

		// remove active-shipper class from shipper div
		document.getElementById(shipper_div_id).className = document.getElementById(shipper_div_id).className.replace( /(?:^|\s)active-shipper(?!\S)/g , '' );
	}
 }
