<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?> | Dispatch Puppy</title>
	<link rel="stylesheet" type="text/css" href="<?php echo asset_url().'css/main.css'; ?>">

	<style>
		html,body
		{
			height: 100%;
			margin: 0;
			padding: 0;
			font-size: 1em;
		}

		#map-canvas
		{
			width: 61.8%; /*golden ration percentage*/
			height:100%;
			float: left;
			-moz-box-shadow: 0 0 10px #888;
			-webkit-box-shadow: 0 0 10px#888;
			box-shadow: 0 0 10px #888;
			display: inline-block;
			position: absolute;
		}

		#directions-panel
		{
			width: 38.2%; /*golden ration percentage*/
			height: 100%;
			float: right;
			position: relative;
			overflow-y: scroll;
		}

		div.all-lanes
		{
			padding: 0 5% 0 5%;
			height: 95%;
		}

		div.lane-info
		{
			padding-top: 5%;
			clear: both;
		}
		
		div.left-checkbox
		{
			width: 8%;	
			float: left;
			position: relative;
		}

		div.right-text
		{
			width: 92%;
			float: right;
			position: relative;
			
		}

		div.secondary-right-text
		{
			border-bottom: 1px dotted #373737;
		}

		div.primary-shipper
		{
			color: #2C9AB7;
		}

		div.primary-consignee
		{
			color: #449A88;
		}

		div.primary-commodity-info,div.primary-miles
		{
			font-size: 0.9em;
			color: #373737;
		}

		div.sub-lane-info
		{
			padding: 2% 2% 0 0;
			clear: both;
			visibility: hidden;
			position: absolute;
		}

		div.secondary-shipper
		{
			color: #52BAD5;
			font-size: 0.9em;
		}

		div.secondary-consignee
		{
			color: #72C1B0;
			font-size: 0.9em;
		}

		div.secondary-commodity-info,div.secondary-miles
		{
			font-size: 0.8em;
			color: #5D5C5D;
		}

		div.secondary-miles
		{
			padding-bottom: 2%;
		}

	</style>

	<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6NH-9BT7BhMwmzicGvy_XPgeaXIdOexA&sensor=false">
	</script>

	<script>
	
	var map;
	var markerBounds = new google.maps.LatLngBounds();
	var directionsDisplay = new google.maps.DirectionsRenderer();
	var directionsService = new google.maps.DirectionsService();
	var primary_lane_loaded_miles = "#327EA3";
	var loaded_miles = "#6CA338";
	var empty_miles = "#E82C0C";
	var MY_MAPTYPE_ID = 'JPD';
	var all_lanes = <?php echo json_encode($all_lanes);?>;
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

		// create variable for to store co-ordinates of center location
		var center_location = new google.maps.LatLng(<?php echo $google_map_center_location['location_lat'];?>, <?php echo $google_map_center_location['location_lng'];?>);

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
		// add marker to center_location
		var marker = new google.maps.Marker({
			position: center_location,
			map: map
		});

		markerBounds.extend(center_location);
		map.fitBounds(markerBounds);
		*/

		
		directionsDisplay.setMap(map);

		/* 
		//code to show the driving directions text
		directionsDisplay.setPanel(document.getElementById("directions-panel"));
		*/
	}

	function getLaneLocation(lane_id)
	{
		// get the lane info
		var lane = all_lanes[lane_id];
		var shipper_primary_location = new google.maps.LatLng(lane.shipper_lat, lane.shipper_lng);
		var consignee_primary_location = new google.maps.LatLng(lane.consignee_lat, lane.consignee_lng);
		
		if(document.getElementById(lane_id+'-checkbox').checked)
		{
			// add marker for primary shipper
			var shipper_primary_title = lane.shipper_name;
			var shipper_primary_content = lane.shipper_name+'<br>'+lane.shipper_address+'<br>'+lane.shipper_city+' ,'+lane.shipper_state+' '+lane.shipper_zipcode;
			addMarker(shipper_primary_location,shipper_primary_title,shipper_primary_content, lane_id+'-shipper');

			// add marker for primary consignee
			var consignee_primary_title = lane.consignee_name;
			var consignee_primary_content = lane.consignee_name+'<br>'+lane.consignee_address+'<br>'+lane.consignee_city+' ,'+lane.consignee_state+' '+lane.consignee_zipcode;
			addMarker(consignee_primary_location, consignee_primary_title, consignee_primary_content, lane_id+'-consignee');

			// crow fly from primary shipper to primary consignee
			//drawPath([shipper_primary_location, consignee_primary_location],primary_lane_loaded_miles, lane_id);

			// map directions from primary shipper to primary consignee
			calcRoute(shipper_primary_location,consignee_primary_location,primary_lane_loaded_miles, lane_id);

			for(var sub_lane_id in lane.secondary_lanes)
			{
				// hide the sub-lane div
				document.getElementById(sub_lane_id).style.visibility = 'visible';
				document.getElementById(sub_lane_id).style.position = 'relative';
			}
		}
		else
		{
			// remove the path from primary shipper to consignee
			poly_lines[lane_id].setMap(null);
			
			// remove the markers for primary shipper
			all_marker[lane_id+'-shipper'].setMap(null);
			delete all_marker[lane_id+'-shipper'];

			// remove the markers for primary consignee
			all_marker[lane_id+'-consignee'].setMap(null);
			delete all_marker[lane_id+'-consignee'];

			if(lane.secondary_lanes != null)
			{
				for(var sub_lane_id in lane.secondary_lanes)
				{
					// create the sub_lane_id
					//var sub_lane_id = lane.consignee_code+'-'+lane.commodity_code+'-'+sub_lane.consignee_code+'-'+sub_lane.commodity_code;

					if(document.getElementById(sub_lane_id+'-checkbox').checked)
					{

						// remove the poly line from primary consignee to secondary shipper
						//poly_lines[sub_lane_id+'-empty'].setMap(null);
						//delete poly_lines[sub_lane_id+'-empty'];
						
						// remove the marker for secondary shipper
						all_marker[sub_lane_id+'-shipper'].setMap(null);
						delete all_marker[sub_lane_id+'-shipper'];

						// remove the poly line from secondary shipper to secondary consignee 
						poly_lines[sub_lane_id].setMap(null);
						delete poly_lines[sub_lane_id];

						// remove the marker for secondary consignee
						all_marker[sub_lane_id+'-consignee'].setMap(null);
						delete all_marker[sub_lane_id+'-consignee'];	
					}
					// uncheck the checkbox
					document.getElementById(sub_lane_id+'-checkbox').checked = false;

					// hide the sub-lane div
					document.getElementById(sub_lane_id).style.visibility = 'hidden';
					document.getElementById(sub_lane_id).style.position = 'absolute';
				}
			}
		}
		console.log(all_marker);
		console.log(poly_lines);
	}

	function getSubLaneLocation(lane_id, sub_lane_id)
	{
		var sub_lane = all_lanes[lane_id].secondary_lanes[sub_lane_id];
		if(document.getElementById(sub_lane_id+'-checkbox').checked)
		{
			// add marker for secondary shipper
			var shipper_secondary_location = new google.maps.LatLng(sub_lane.shipper_lat, sub_lane.shipper_lng);
			var shipper_secondary_title = sub_lane.shipper_name;
			var shipper_secondary_content = sub_lane.shipper_name+'<br>'+sub_lane.shipper_address+'<br>'+sub_lane.shipper_city+' ,'+sub_lane.shipper_state+' '+sub_lane.shipper_zipcode;
			addMarker(shipper_secondary_location,shipper_secondary_title,shipper_secondary_content,sub_lane_id+'-shipper');

			// crow fly from primary consignee to secondary shipper
			//drawPath([consignee_primary_location, shipper_secondary_location],empty_miles, sub_lane_id+'-empty');

			// map directions from primary consignee to secondary shipper
			//calcRoute(consignee_primary_location,shipper_secondary_location,empty_miles, sub_lane_id+'-empty');

			// add marker for secondary consignee
			var consignee_secondary_location = new google.maps.LatLng(sub_lane.consignee_lat, sub_lane.consignee_lng);
			var consignee_secondary_title = sub_lane.consignee_name;
			var consignee_secondary_content = sub_lane.consignee_name+'<br>'+sub_lane.consignee_address+'<br>'+sub_lane.consignee_city+' ,'+sub_lane.consignee_state+' '+sub_lane.consignee_zipcode;
			addMarker(consignee_secondary_location,consignee_secondary_title,consignee_secondary_content,sub_lane_id+'-consignee');

			// crow fly from secondary shipper to secondary consignee		
			//drawPath([shipper_secondary_location, consignee_secondary_location],loaded_miles, sub_lane_id);

			// map directions from secondary shipper to secondary consignee			
			calcRoute(shipper_secondary_location,consignee_secondary_location,loaded_miles, sub_lane_id);
		}
		else
		{
			// remove the poly line from primary consignee to secondary shipper
			//poly_lines[sub_lane_id+'-empty'].setMap(null);
			//delete poly_lines[sub_lane_id+'-empty'];
						
			// remove the marker for secondary shipper
			all_marker[sub_lane_id+'-shipper'].setMap(null);
			delete all_marker[sub_lane_id+'-shipper'];

			// remove the poly line from secondary shipper to secondary consignee 
			poly_lines[sub_lane_id].setMap(null);
			delete poly_lines[sub_lane_id];

			// remove the marker for secondary consignee
			all_marker[sub_lane_id+'-consignee'].setMap(null);
			delete all_marker[sub_lane_id+'-consignee'];	
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

	function drawPath(path,color,lane_id)
	{	
		var myLine = new google.maps.Polyline({
			map: map,
			path: path,
			strokeColor: color,
			strokeOpacity: 0.75,
			strokeWeight: 3.5
		});

		poly_lines[lane_id] = myLine;
	}

	function calcRoute(origin,destination,color,lane_id)
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
      			drawPath(allLatLong,color,lane_id);
      		}
      	});
	}
	
	</script>

</head>
<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="directions-panel">
		<div class="all-lanes">
			<?php
			
				foreach($all_lanes as $lane)
				{
					echo '<div class="lane-info" id="'.$lane['consignee_code'].'-'.$lane['commodity_code'].'">'
								.'<div class="left-checkbox">'
									.'<input type="checkbox" id="'.$lane['consignee_code'].'-'.$lane['commodity_code'].'-checkbox" onclick="getLaneLocation(\''.$lane['consignee_code'].'-'.$lane['commodity_code'].'\')">'
								.'</div>'
								.'<div class="right-text">'
									.'<div class="primary-shipper">'
										.$lane['shipper_name'].' - '.$lane['shipper_city'].', '.$lane['shipper_state']
									.'</div>'
									.'<div class="primary-consignee">'
										.$lane['consignee_name'].' - '.$lane['consignee_city'].', '.$lane['consignee_state']
									.'</div>'
									.'<div class="primary-commodity-info">'
										.$lane['commodity'].' ('.$lane['commodity_code'].')'
									.'</div>'
									.'<div class="primary-miles">'
										.round($lane['miles']).' mi'
									.'</div>';

									if($lane['secondary_lanes'] != NULL)
									foreach($lane['secondary_lanes'] as $sub_lane)
									{
										echo '<div class="sub-lane-info" id="'.$lane['consignee_code'].'-'.$lane['commodity_code'].'-'.$sub_lane['consignee_code'].'-'.$sub_lane['commodity_code'].'">'
												.'<div class="left-checkbox">'
													.'<input type="checkbox" id="'.$lane['consignee_code'].'-'.$lane['commodity_code'].'-'.$sub_lane['consignee_code'].'-'.$sub_lane['commodity_code'].'-checkbox" onclick="getSubLaneLocation(\''.$lane['consignee_code'].'-'.$lane['commodity_code'].'\',\''.$lane['consignee_code'].'-'.$lane['commodity_code'].'-'.$sub_lane['consignee_code'].'-'.$sub_lane['commodity_code'].'\')">'
												.'</div>'
												.'<div class="right-text secondary-right-text">'
													.'<div class="secondary-shipper">'
														.$sub_lane['shipper_name'].' - '.$sub_lane['shipper_city'].', '.$sub_lane['shipper_state']
													.'</div>'
													.'<div class="secondary-consignee">'
														.$sub_lane['consignee_name'].' - '.$sub_lane['consignee_city'].', '.$sub_lane['consignee_state']
													.'</div>'
													.'<div class="secondary-commodity-info">'
														.$sub_lane['commodity'].' ('.$sub_lane['commodity_code'].')'
													.'</div>'
													.'<div class="secondary-miles">'
														.round($sub_lane['miles']).' mi'
													.'</div>'
												.'</div>'
											.'</div>';
									}

							echo '</div>'
						.'</div>';	
				}
			
			?>
		</div>
	</div>
</body>
</html>