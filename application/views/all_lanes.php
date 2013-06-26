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
	</style>

	<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6NH-9BT7BhMwmzicGvy_XPgeaXIdOexA&sensor=false">
	</script>

	<script>
	
	var map;
	var markerBounds = new google.maps.LatLngBounds();
	var directionsDisplay = new google.maps.DirectionsRenderer();
	var directionsService = new google.maps.DirectionsService();
	var allLatLong;

	function findCenter()
	{
		initialize();
	}

	function getLocation()
	{
		// add bill to markers
		//addMarker(<?php echo $all_lanes[0]['bill_to_lat'];?>,<?php echo $all_lanes[0]['bill_to_lng'];?>);

		addMarker(<?php echo $all_lanes[0]['shipper_lat'];?> , <?php echo $all_lanes[0]['shipper_lng'];?>);
		addMarker(<?php echo $all_lanes[0]['consignee_lat'];?>,<?php echo $all_lanes[0]['consignee_lng'];?>);
		calcRoute(<?php echo $all_lanes[0]['shipper_lat'];?> , <?php echo $all_lanes[0]['shipper_lng'];?>, <?php echo $all_lanes[0]['consignee_lat'];?>,<?php echo $all_lanes[0]['consignee_lng'];?>);
	}


	function initialize(lat, lng) 
	{
		//initialize map
		var mapOptions = new Object();
		mapOptions.zoom = 10;
		mapOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
		mapOptions.center = new google.maps.LatLng(40.4061220, -76.5369175);
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		getLocation();
		directionsDisplay.setMap(map);
		//directionsDisplay.setPanel(document.getElementById("directions-panel"));
	}

	function addMarker(lat, lng)
	{	
		var location = new google.maps.LatLng(lat, lng);
		var marker = new google.maps.Marker({
			position: location,
			map: map
		});

		markerBounds.extend(location)
		map.fitBounds(markerBounds);
	}

	function myLine(location)
	{	
		var myLine = new google.maps.Polyline({
			map: map,
			path: location,
    		strokeColor: '#FF0000',
    		strokeOpacity: 0.75,
    		strokeWeight: 3
  		});
	}

	function calcRoute(lat1, lng1, lat2, lng2)
	{
		var request = {
			origin: new google.maps.LatLng(lat1, lng1),
			destination: new google.maps.LatLng(lat2, lng2),
			travelMode: google.maps.TravelMode.DRIVING
		};

		directionsService.route(request, function(result, status) {
    		if (status == google.maps.DirectionsStatus.OK) 
    		{
      			//directionsDisplay.setDirections(result);
      			allLatLong = result.routes[0].overview_path;
     			myLine(allLatLong);
    		}
  		});
	}
	
	</script>

</head>
<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="directions-panel"></div>
</body>
</html>