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

	function getLocation()
	{
		initialize();
		getBillToLocation();
		getShipperLocation();
		getConsigneeLocation();
	}

	function getBillToLocation()
	{
		var location_bill_to = "<?php echo $all_lanes[0]['bill_to_address'].','.$all_lanes[0]['bill_to_city'].','.$all_lanes[0]['bill_to_state'].','.$all_lanes[0]['bill_to_zipcode'];?>";

		var location_XML_request = "http://maps.googleapis.com/maps/api/geocode/xml?address="+location_bill_to+"&sensor=false";
		loadXMLGeocode(location_XML_request);
	}

	function getShipperLocation()
	{
		var location_shipper = "<?php echo $all_lanes[0]['shipper_address'].','.$all_lanes[0]['shipper_city'].','.$all_lanes[0]['shipper_state'].','.$all_lanes[0]['shipper_zipcode'];?>";

		var location_XML_request = "http://maps.googleapis.com/maps/api/geocode/xml?address="+location_shipper+"&sensor=false";
		loadXMLGeocode(location_XML_request);
	}

	function getConsigneeLocation()
	{
		var location_consignee = "<?php echo $all_lanes[0]['consignee_address'].','.$all_lanes[0]['consignee_city'].','.$all_lanes[0]['consignee_state'].','.$all_lanes[0]['consignee_zipcode'];?>";

		var location_XML_request = "http://maps.googleapis.com/maps/api/geocode/xml?address="+location_consignee+"&sensor=false";
		loadXMLGeocode(location_XML_request);
	}

	function loadXMLGeocode(locationXMLRequest)
	{
		var xmlhttp;
		var xmlDoc;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.open("GET",locationXMLRequest,true);
		xmlhttp.send();

		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				xmlDoc=xmlhttp.responseXML;
				parseLocationXML(xmlDoc);
			}	
		}
	}

	function parseLocationXML(xmlDoc)
	{
		// check each "status"
		var status = xmlDoc.getElementsByTagName("status")[0].childNodes[0].nodeValue;

		if(status === 'OK')
		{
			var lat = xmlDoc.getElementsByTagName("lat")[0].childNodes[0].nodeValue;
			var lng = xmlDoc.getElementsByTagName("lng")[0].childNodes[0].nodeValue;
			var location = new google.maps.LatLng(lat, lng);
			addMarker(location);
			document.getElementById('directions-panel').innerHTML += 'failure: ' + location + '<br>';
		}
		else
		{
			document.getElementById('directions-panel').innerHTML += 'failure: ' + locationXMLRequest + '<br>';
		}	
	}

	function initialize() 
	{
		//initialize map
		var mapOptions = new Object();
		mapOptions.zoom = 6;
		mapOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
		mapOptions.center = new google.maps.LatLng(40.406122, -76.53691750000002);
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
	}

	function addMarker(location)
	{	
		var marker = new google.maps.Marker({
			position: location,
			map: map
		});
	}
	
	</script>

</head>
<body onload="getLocation()">
	<div id="map-canvas"></div>
	<div id="directions-panel"></div>
</body>
</html>