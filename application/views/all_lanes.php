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
			font-size: 11pt;
		}

		#map-canvas
		{
			width: 61.8%; /*golden ration percentage*/
			height:100%;
			float: left;
		}

		#directions-panel
		{
			width: 38.2%; /*golden ration percentage*/
			height: 100%;
			float: right;
		}

		#text
		{
			margin: 2%;
			height: 98%;
		}

		div.all-lanes-info
		{
			padding: 10%;
		}
		
		#left-checkbox
		{
			width: 5%;
			height: 100%;	
			float: left;
		}

		#right-text
		{
			width: 95%;
			height: 100%;
			float: right;
		}

		div.commodity-info
		{
			font-size: 10pt;
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
	var MY_MAPTYPE_ID = 'custom_style';
	var all_lanes = <?php echo json_encode($all_lanes);?>;

	function initialize() 
	{
		// create variable for to store co-ordinates of center location
		var center_location = new google.maps.LatLng(<?php echo $google_map_center_location['location_lat'];?>, <?php echo $google_map_center_location['location_lng'];?>);

		// set the map options
		var mapOptions = {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			zoom: 7,
			center: center_location,
			mapTypeId: MY_MAPTYPE_ID
		};

		var featureOpts = [
			{
				featureType: 'road',
				elementType: 'all',
      			stylers: [
        			{ lightness: 40 }
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
				featureType: 'poi',
				elementType: 'label.text',
				stylers: [
        			{ visibility: 'off' }
      			]
			}
		];

		var styledMapOptions = {
			name: 'JPD'
		}

		// render map
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		
		// add custom style
		var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
  		map.mapTypes.set(MY_MAPTYPE_ID, customMapType);

		// add marker to center_location
		/*var marker = new google.maps.Marker({
			position: center_location,
			map: map
		});

		markerBounds.extend(center_location);
		map.fitBounds(markerBounds);*/

		directionsDisplay.setMap(map);

		/* 
		//code to show the driving directions text
		directionsDisplay.setPanel(document.getElementById("directions-panel"));*/
	}

	function getLocation(lane_id)
	{
		// get the lane info
		var lane = all_lanes[lane_id];

		// add marker for primary shipper
		var shipper_primary_location = new google.maps.LatLng(lane.shipper_lat, lane.shipper_lng);
		var shipper_primary_title = lane.shipper_name;
		var shipper_primary_content = lane.shipper_name+'<br>'+lane.shipper_address+'<br>'+lane.shipper_city+' ,'+lane.shipper_state+' '+lane.shipper_zipcode;
		addMarker(shipper_primary_location,shipper_primary_title,shipper_primary_content);

		// add marker for primary consignee
		var consignee_primary_location = new google.maps.LatLng(lane.consignee_lat, lane.consignee_lng);
		addMarker(consignee_primary_location);
		var consignee_primary_title = lane.consignee_name;
		var consignee_primary_content = lane.consignee_name+'<br>'+lane.consignee_address+'<br>'+lane.consignee_city+' ,'+lane.consignee_state+' '+lane.consignee_zipcode;
		addMarker(consignee_primary_location,consignee_primary_title,consignee_primary_content);


		// crow fly from primary shipper to primary consignee
		drawPath([shipper_primary_location, consignee_primary_location],primary_lane_loaded_miles);

		// map directions from primary shipper to primary consignee
		//calcRoute(shipper_primary_location,consignee_primary_location,loaded_miles);

		// add marker and directions for primary run
		if(lane.secondary_lanes != null)
		{
			for(var i=0; i<lane.secondary_lanes.length; i++)
			{
				// get the sub lane info
				var sub_lane = lane.secondary_lanes[i];

				// add marker for secondary shipper
				var shipper_secondary_location = new google.maps.LatLng(sub_lane.shipper_lat, sub_lane.shipper_lng);
				var shipper_secondary_title = sub_lane.shipper_name;
				var shipper_secondary_content = sub_lane.shipper_name+'<br>'+sub_lane.shipper_address+'<br>'+sub_lane.shipper_city+' ,'+sub_lane.shipper_state+' '+sub_lane.shipper_zipcode;
				addMarker(shipper_secondary_location,shipper_secondary_title,shipper_secondary_content);

				// crow fly from primary consignee to secondary shipper
				//drawPath([consignee_primary_location, shipper_secondary_location],empty_miles);


				// map directions from primary consignee to secondary shipper
				calcRoute(consignee_primary_location,shipper_secondary_location,empty_miles);

				// add marker for secondary consignee
				var consignee_secondary_location = new google.maps.LatLng(sub_lane.consignee_lat, sub_lane.consignee_lng);
				var consignee_secondary_title = sub_lane.consignee_name;
				var consignee_secondary_content = sub_lane.consignee_name+'<br>'+sub_lane.consignee_address+'<br>'+sub_lane.consignee_city+' ,'+sub_lane.consignee_state+' '+sub_lane.consignee_zipcode;
				addMarker(consignee_secondary_location,consignee_secondary_title,consignee_secondary_content);

				// crow fly from secondary shipper to secondary consignee		
				//drawPath([shipper_secondary_location, consignee_secondary_location],loaded_miles);

				// map directions from secondary shipper to secondary consignee			
				calcRoute(shipper_secondary_location,consignee_secondary_location,loaded_miles);
			}
		}
		else
		{
			calcRoute(consignee_primary_location, shipper_primary_location, empty_miles);
		}
	}

	function addMarker(location, title, info)
	{	
		var marker = new google.maps.Marker({
			position: location,
			map: map,
			title: title
		});

		markerBounds.extend(location);
		map.fitBounds(markerBounds);
	}

	function drawPath(path,color)
	{	
		var myLine = new google.maps.Polyline({
			map: map,
			path: path,
			strokeColor: color,
			strokeOpacity: 0.75,
			strokeWeight: 3.5
		});
	}

	function calcRoute(origin,destination,color)
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
      			drawPath(allLatLong,color);
      		}
      	});
	}
	
	</script>

</head>
<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="directions-panel">
		<div id="text">
			<?php
				foreach($all_lanes as $lane)
				{
					echo '<div class="all-lanes-info" id="'.$lane['consignee_code'].'-'.$lane['commodity_code'].'">'
							.'<div id="left-checkbox">'
								.'<input type="checkbox" onclick="getLocation(\''.$lane['consignee_code'].'-'.$lane['commodity_code'].'\')">'
							.'</div>'
							.'<div id="right-text">'
								.'<div class="lane-info">'
									.$lane['shipper_name'].' - '.$lane['shipper_city'].', '.$lane['shipper_state']
									.'<br>'
									.$lane['consignee_name'].' - '.$lane['consignee_city'].', '.$lane['consignee_state']
									.'<br>'
								.'</div>'
								.'<div class="commodity-info">'
									.$lane['commodity'].' ('.$lane['commodity_code'].')'
								.'</div>'
							.'</div>'
						.'</div>';
				}
			?>
		</div>
	</div>
</body>
</html>