<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?> | Dispatch Puppy</title>
		<!--HTML5 Boilerplate CSS-->
	<link rel="stylesheet" type="text/css" href="<?php echo asset_url();?>css/main.css">

	<!--Google Maps API-->
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6NH-9BT7BhMwmzicGvy_XPgeaXIdOexA&sensor=false">
	</script>

	<script type="text/javascript" src="<?php echo asset_url();?>js/map.js"></script>
	<script>
	/* variable to store center location of map when initialized */
	var center_location = new google.maps.LatLng(<?php echo $google_map_center_location['location_lat'];?>, <?php echo $google_map_center_location['location_lng'];?>);

	/* variable to store all the lane combinations */
	var lanes_without_lat_lng = <?php echo json_encode($lanes_without_lat_lng);?>;

	/*function getLocation()
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
	}*/
	
	</script>

</head>
<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="control-panel">
		<div class="all-lanes">
			testing

			<?php
			
				$checkbox = '-CHECKBOX';
				foreach($lanes_without_lat_lng as $lane_id => $lane)
				{
					echo '<div class="lane-info" id="'.$lane_id.'">'
								.'<div class="left-checkbox">'
									.'<input type="checkbox" id="'.$lane_id.$checkbox.'" onclick="getLaneLocation(\''.$lane_id.'\')">'
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
										.round($lane['miles'],1).' mi'
									.'</div>';
								.'</div>'
						.'</div>';	
				}
			?>
		</div>
	</div>
</body>
</html>