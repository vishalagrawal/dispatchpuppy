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
		}
		#map-canvas
		{
			width: 75%;
			height:100%;
			float: left;
		}
		#directions-panel
		{
			width: 25%;
			height: 100%;
			float: right;
		}
		#text
		{
			margin: 2%;
		}
		input
		{
			padding: 1%;
		}
		#info
		{
			padding: 5%;
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
	var all_lanes = <?php echo json_encode($all_lanes);?>

	function initialize() 
	{
		// create variable for to store co-ordinates of center location
		var center_location = new google.maps.LatLng(<?php echo $google_map_center_location['location_lat'];?>, <?php echo $google_map_center_location['location_lng'];?>);

		// set the map options
		var mapOptions = {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			zoom: 7,
			center: center_location
		};

		// render map
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

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
		// add marker and directions for primary run
		var shipper_primary = new google.maps.LatLng(all_lanes[lane_id].shipper_lat, all_lanes[lane_id].shipper_lng);
		var consignee_primary = new google.maps.LatLng(all_lanes[lane_id].consignee_lat, all_lanes[lane_id].consignee_lng);
		addMarker(shipper_primary);
		addMarker(consignee_primary);
		calcRoute(shipper_primary,consignee_primary,"#373737");

		// add marker and directions for primary run
		if(all_lanes[lane_id].secondary_lanes != null)
		{
			for(var i=0; i<all_lanes[lane_id].secondary_lanes.length; i++)
			{
				var shipper_secondary = new google.maps.LatLng(all_lanes[lane_id].secondary_lanes[i].shipper_lat, all_lanes[lane_id].secondary_lanes[i].shipper_lng);
				var consignee_secondary = new google.maps.LatLng(all_lanes[lane_id].secondary_lanes[i].consignee_lat, all_lanes[lane_id].secondary_lanes[i].consignee_lng);
				addMarker(shipper_secondary);
				addMarker(consignee_secondary);
				calcRoute(consignee_primary,shipper_secondary,"#2C9AB7");
				calcRoute(shipper_secondary,consignee_secondary,"#DB3A1B");
			}
		}
	}

	function addMarker(location)
	{	
		var marker = new google.maps.Marker({
			position: location,
			map: map
		});

		markerBounds.extend(location);
		map.fitBounds(markerBounds);
	}

	function myLine(path,color)
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
      			myLine(allLatLong,color);
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
				foreach($all_lanes as $row)
				{
					//echo json_encode($all_lanes);
					echo '<div id="info '.$row['consignee_code'].'-'.$row['commodity_code'].'">'
						.'<input type="checkbox" onclick="getLocation(\''.$row['consignee_code'].'-'.$row['commodity_code'].'\')">'
						.$row['shipper_city'].', '.$row['shipper_state']
						.' - '
						.$row['consignee_city'].', '.$row['consignee_state']
						.' ('.$row['commodity_code'].')'
						.'</div>';
				}
			?>
		</div>
	</div>
</body>
</html>