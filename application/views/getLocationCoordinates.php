<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?> | Dispatch Puppy</title>
	
	<!--HTML5 Boilerplate CSS-->
	<link rel="stylesheet" type="text/css" href="<?php echo asset_url();?>css/main.css">

	<script>
		var DEFAULT_MARKER_PRIMARY_SHIPPER = '<?php echo asset_url();?>images/google-icons/green/measle-green.png';
		var DEFAULT_MARKER_PRIMARY_CONSIGNEE = '<?php echo asset_url();?>images/google-icons/red/measle-red.png';
		var DEFAULT_MARKER_SECONDARY_SHIPPER = '<?php echo asset_url();?>images/google-icons/measle-green-black-white.png';
		var DEFAULT_MARKER_SECONDARY_CONSIGNEE = '<?php echo asset_url();?>images/google-icons/measle-red-black-white.png';

		/* variable to store all the lane combinations */
		var lanes_without_lat_lng = <?php echo json_encode($lanes_without_lat_lng);?>;

function getLaneLatLng(lane_id)
{
	//getBillToLocation(lane_id);
	getShipperLocation(lane_id);
	//getConsigneeLocation(lane_id);
}

function getBillToLocation(lane_id)
{
	var location_bill_to = lanes_without_lat_lng[lane_id].bill_to_address+','+lanes_without_lat_lng[lane_id].bill_to_city+','+lanes_without_lat_lng[lane_id].bill_to_state+','+lanes_without_lat_lng[lane_id].bill_to_zipcode;

	var location_XML_request = 'http://maps.googleapis.com/maps/api/geocode/xml?address='+location_bill_to+'&sensor=false';
	loadXMLGeocode(location_XML_request,0);
}

function getShipperLocation(lane_id)
{
	var location_shipper = lanes_without_lat_lng[lane_id].shipper_address+','+lanes_without_lat_lng[lane_id].shipper_city+','+lanes_without_lat_lng[lane_id].shipper_state+','+lanes_without_lat_lng[lane_id].shipper_zipcode;

	var location_XML_request = 'http://maps.googleapis.com/maps/api/geocode/xml?address='+location_shipper+'&sensor=false';
	loadXMLGeocode(location_XML_request,1);
}

function getConsigneeLocation(lane_id)
{
	var location_consignee = lanes_without_lat_lng[lane_id].consignee_address+','+lanes_without_lat_lng[lane_id].consignee_city+','+lanes_without_lat_lng[lane_id].consignee_state+','+lanes_without_lat_lng[lane_id].consignee_zipcode;

	var location_XML_request = 'http://maps.googleapis.com/maps/api/geocode/xml?address='+location_consignee+'&sensor=false';
	loadXMLGeocode(location_XML_request,2);
}

function loadXMLGeocode(locationXMLRequest,type_id)
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

	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			xmlDoc=xmlhttp.responseXML;
			parseLocationXML(xmlDoc,type_id);
		}	
	}

	xmlhttp.open("GET",locationXMLRequest,true);
	xmlhttp.send();
}

function parseLocationXML(xmlDoc,type_id)
{
	// check each "status"
	var status = xmlDoc.getElementsByTagName("status")[0].childNodes[0].nodeValue;

	if(status === 'OK')
	{
		var lat = xmlDoc.getElementsByTagName("lat")[0].childNodes[0].nodeValue;
		var lng = xmlDoc.getElementsByTagName("lng")[0].childNodes[0].nodeValue;
		document.getElementById('control-panel').innerHTML += 'success: ' + location + '<br>';
	}
	else
	{
		document.getElementById('control-panel').innerHTML += 'failure: ' + locationXMLRequest + '<br>';
	}	
}
	</script>
</head>

<body>
	<div id="map-canvas"></div>
	<div id="control-panel">
		<div id="all-lanes">
			<?php
				$SHIPPER_IMAGE = '-SHIPPER-IMAGE';
				$CONSIGNEE_IMAGE = '-CONSIGNEE-IMAGE';
				foreach($lanes_without_lat_lng as $lane_id => $lane)
				{
					echo '<div class="primary-lane-info" id="'.$lane_id.'" onclick="getLaneLatLng(\''.$lane_id.'\')">'
							.'<div class="shipper-consignee-info">'
								.'<div class="shipper">'
									.'<div class="marker">'
										.'<img id="'.$lane_id.$SHIPPER_IMAGE.'" src="'.asset_url().'images/google-icons/green/measle-green.png">'
									.'</div>'
									.'<div class="shipper-info">'
										.'<div class="shipper-name">'
											.$lane['shipper_name']
										.'</div>'
										.'<div class="shipper-address">'
											.$lane['shipper_city'].', '.$lane['shipper_state']
										.'</div>'
										.'<div class="shipper-address">'
											.$lane['shipper_lat'].', '.$lane['shipper_lng']
										.'</div>'
									.'</div>'
								.'</div>'
								.'<div class="consignee">'
									.'<div class="marker">'
										.'<img id="'.$lane_id.$CONSIGNEE_IMAGE.'" src="'.asset_url().'images/google-icons/red/measle-red.png">'
									.'</div>'
									.'<div class="consignee-info">'
										.'<div class="consignee-name">'
											.$lane['consignee_name']
										.'</div>'
										.'<div class="consignee-address">'
											.$lane['consignee_city'].', '.$lane['consignee_state']
										.'</div>'
										.'<div class="consignee-address">'
											.$lane['consignee_lat'].', '.$lane['consignee_lng']
										.'</div>'
									.'</div>'
								.'</div>'
							.'</div>'
							.'<div class="commodity-miles-info">'
								.'<div class="commodity-code">'
									.$lane['commodity_code']
								.'</div>'
								.'<div class="miles">'
									.round($lane['miles'],1).' mi'
								.'</div>'
							.'</div>'
						.'</div>';
				}
			?>
		</div>
	</div>
</body>
</html>