// global variables
var PRIMARY_LANE_LOADED_MILES = '#327EA3';
var LOADED_MILES = '#6CA338';
var EMPTY_MILES = '#E82C0C';
var MY_MAPTYPE_ID = 'JPD';
var CHECKBOX = '-CHECKBOX';
var SHIPPER = '-SHIPPER';
var CONSIGNEE = '-CONSIGNEE';
var EMPTY = '-EMPTY';

var map;
var markerBounds = new google.maps.LatLngBounds();
var directionsDisplay = new google.maps.DirectionsRenderer();
var directionsService = new google.maps.DirectionsService();
var poly_lines = {};
var all_marker = {};

function initialize() 
{
	// set custom style
	var mapStyle = [
		{
			featureType: 'road',
			elementType: 'all',
  			stylers: [
    			{ /*lightness: 40*/}
  			]
		},
		{
			featureType: 'administrative.locality',
			elementType: 'labels.text',
  			stylers: [
    			{ /*lightness: 30*/ }
  			]
		},
		{
			featureType: 'poi',
			elementType: 'label.text',
			stylers: [
    			{ /*visibility: 'off'*/ }
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
		mapTypeId: MY_MAPTYPE_ID
	};

	// render map
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
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
	var consignee_primary_location = new google.maps.LatLng(lane.consignee_lat, lane.consignee_lng);
	
	if(document.getElementById(lane_id+CHECKBOX).checked)
	{
		// add marker for primary shipper
		var shipper_primary_title = lane.shipper_name;
		var shipper_primary_content = lane.shipper_name+'<br>'+lane.shipper_address+'<br>'+lane.shipper_city+' ,'+lane.shipper_state+' '+lane.shipper_zipcode;
		addMarker(shipper_primary_location, shipper_primary_title, shipper_primary_content, lane_id+SHIPPER);

		// add marker for primary consignee
		var consignee_primary_title = lane.consignee_name;
		var consignee_primary_content = lane.consignee_name+'<br>'+lane.consignee_address+'<br>'+lane.consignee_city+' ,'+lane.consignee_state+' '+lane.consignee_zipcode;
		addMarker(consignee_primary_location, consignee_primary_title, consignee_primary_content, lane_id+CONSIGNEE);

		// crow fly from primary shipper to primary consignee
		//drawPath([shipper_primary_location, consignee_primary_location], PRIMARY_LANE_LOADED_MILES, lane_id);

		// map directions from primary shipper to primary consignee
		calcRoute(shipper_primary_location, consignee_primary_location, PRIMARY_LANE_LOADED_MILES, lane_id);

		for(var sub_lane_id in lane.secondary_lanes)
		{
			// hide the sub-lane div
			document.getElementById(lane_id+'-'+sub_lane_id).style.visibility = 'visible';
			document.getElementById(lane_id+'-'+sub_lane_id).style.position = 'relative';
		}

		document.getElementById(lane_id).className += ' active-lane';
	}
	else
	{
		// remove the markers for primary shipper
		all_marker[lane_id+SHIPPER].setMap(null);
		delete all_marker[lane_id+SHIPPER];

		// remove the path from primary shipper to consignee
		poly_lines[lane_id].setMap(null);
	
		// remove the markers for primary consignee
		all_marker[lane_id+CONSIGNEE].setMap(null);
		delete all_marker[lane_id+CONSIGNEE];

		if(lane.secondary_lanes != null)
		{
			for(var sub_lane_id in lane.secondary_lanes)
			{
				var sub_lane_div_id = lane_id+'-'+sub_lane_id;
				if(document.getElementById(sub_lane_div_id+CHECKBOX).checked)
				{
					// remove the poly line from primary consignee to secondary shipper
					poly_lines[sub_lane_div_id+EMPTY].setMap(null);
					delete poly_lines[sub_lane_div_id+EMPTY];
					
					// remove the marker for secondary shipper
					all_marker[sub_lane_div_id+SHIPPER].setMap(null);
					delete all_marker[sub_lane_div_id+SHIPPER];

					// remove the poly line from secondary shipper to secondary consignee 
					poly_lines[sub_lane_div_id].setMap(null);
					delete poly_lines[sub_lane_div_id];

					// remove the marker for secondary consignee
					all_marker[sub_lane_div_id+CONSIGNEE].setMap(null);
					delete all_marker[sub_lane_div_id+CONSIGNEE];	
				}
				document.getElementById(sub_lane_div_id).className = document.getElementById(sub_lane_div_id).className.replace( /(?:^|\s)active-sub-lane(?!\S)/g , '' );

				// uncheck the checkbox
				document.getElementById(sub_lane_div_id+CHECKBOX).checked = false;

				// hide the sub-lane div
				document.getElementById(sub_lane_div_id).style.visibility = 'hidden';
				document.getElementById(sub_lane_div_id).style.position = 'absolute';
			}
		}

		document.getElementById(lane_id).className = document.getElementById(lane_id).className.replace( /(?:^|\s)active-lane(?!\S)/g , '' );
	}
}

function getSubLaneLocation(lane_id, sub_lane_id)
{
	// get the lane info
	var lane = all_lanes[lane_id];
	var consignee_primary_location = new google.maps.LatLng(lane.consignee_lat, lane.consignee_lng);

	var sub_lane = lane.secondary_lanes[sub_lane_id];
	var sub_lane_div_id = lane_id+'-'+sub_lane_id;

	if(document.getElementById(sub_lane_div_id+CHECKBOX).checked)
	{
		// add marker for secondary shipper
		var shipper_secondary_location = new google.maps.LatLng(sub_lane.shipper_lat, sub_lane.shipper_lng);
		var shipper_secondary_title = sub_lane.shipper_name;
		var shipper_secondary_content = sub_lane.shipper_name+'<br>'+sub_lane.shipper_address+'<br>'+sub_lane.shipper_city+' ,'+sub_lane.shipper_state+' '+sub_lane.shipper_zipcode;
		addMarker(shipper_secondary_location, shipper_secondary_title, shipper_secondary_content, sub_lane_div_id+SHIPPER);

		// crow fly from primary consignee to secondary shipper
		//drawPath([consignee_primary_location, shipper_secondary_location], EMPTY_MILES, sub_lane_div_id+EMPTY);

		//map directions from primary consignee to secondary shipper
		calcRoute(consignee_primary_location,shipper_secondary_location,EMPTY_MILES, sub_lane_div_id+EMPTY);

		// add marker for secondary consignee
		var consignee_secondary_location = new google.maps.LatLng(sub_lane.consignee_lat, sub_lane.consignee_lng);
		var consignee_secondary_title = sub_lane.consignee_name;
		var consignee_secondary_content = sub_lane.consignee_name+'<br>'+sub_lane.consignee_address+'<br>'+sub_lane.consignee_city+' ,'+sub_lane.consignee_state+' '+sub_lane.consignee_zipcode;
		addMarker(consignee_secondary_location, consignee_secondary_title, consignee_secondary_content, sub_lane_div_id+CONSIGNEE)
		// crow fly from secondary shipper to secondary consignee		
		//drawPath([shipper_secondary_location, consignee_secondary_location],LOADED_MILES, sub_lane_div_id);

		// map directions from secondary shipper to secondary consignee			
		calcRoute(shipper_secondary_location, consignee_secondary_location, LOADED_MILES, sub_lane_div_id);

		document.getElementById(sub_lane_div_id).className += ' active-sub-lane';
	}
	else
	{
		// remove the poly line from primary consignee to secondary shipper
		poly_lines[sub_lane_div_id+EMPTY].setMap(null);
		delete poly_lines[sub_lane_div_id+EMPTY];
					
		// remove the marker for secondary shipper
		all_marker[sub_lane_div_id+SHIPPER].setMap(null);
		delete all_marker[sub_lane_div_id+SHIPPER];

		// remove the poly line from secondary shipper to secondary consignee 
		poly_lines[sub_lane_div_id].setMap(null);
		delete poly_lines[sub_lane_div_id];

		// remove the marker for secondary consignee
		all_marker[sub_lane_div_id+CONSIGNEE].setMap(null);
		delete all_marker[sub_lane_div_id+CONSIGNEE];

		document.getElementById(sub_lane_div_id).className = document.getElementById(sub_lane_div_id).className.replace( /(?:^|\s)active-sub-lane(?!\S)/g , '' );
	}
}

function addMarker(location, title, info, lane_id)
{	
	var marker = new google.maps.Marker({
		position: location,
		map: map,
		title: title
	});

	markerBounds.extend(location);
	map.fitBounds(markerBounds);
	all_marker[lane_id] = marker;
}

function calcRoute(origin, destination, color, lane_id)
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
  			drawPath(allLatLong, color, lane_id);
  		}
  	});
}

function drawPath(path, color, lane_id)
{	
	var myLine = new google.maps.Polyline({
		map: map,
		path: path,
		strokeColor: color,
		strokeOpacity: 0.75,
		strokeWeight: 4
	});

	poly_lines[lane_id] = myLine;
}