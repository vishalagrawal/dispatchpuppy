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

	<script>
		/* variable to store center location of map when initialized */
		var center_location = new google.maps.LatLng(<?php echo $google_map_center_location['location_lat'];?>, <?php echo $google_map_center_location['location_lng'];?>);

		/* variable to store all the lane combinations */
		var customers_without_lat_lng = <?php echo json_encode($customers_without_lat_lng);?>;
	</script>

	<!--Javascript file to display map-->
	<script type="text/javascript" src="<?php echo asset_url();?>js/map.js"></script>
</head>

<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="control-panel">
		<div id="all-lanes">
			<?php
				$i = 0;
				foreach($customers_without_lat_lng as $customer_id => $customer)
				{
					$LAT = '-LAT';
					$LNG = '-LNG';
					$STATUS = '-STATUS';
					if($i==0)
					{
						$first_customer = 'first-lane';
					}
					else
					{
						$first_customer = '';
					}
					echo '<div class="primary-lane-info'.' '.$first_customer.'" id="'.$customer_id.'" onclick="getLatLng(\''.$customer_id.'\')">'
							.'<div class="shipper-consignee-info">'
								.'<div class="shipper">'
									.'<div class="marker">'
										.'<img src="'.asset_url().'images/google-icons/green/measle-green.png">'
									.'</div>'
									.'<div class="shipper-info">'
										.'<div class="shipper-name">'
											.$customer['name']
										.'</div>'
										.'<div class="shipper-address">'
											.$customer['city'].', '.$customer['state'].', '.$customer['zipcode']
										.'</div>'
										.'<div class="shipper-address">'
											.'<span id="'.$customer_id.$LAT.'">'.$customer['lat'].'</span>, <span id="'.$customer_id.$LNG.'">'.$customer['lng'].'</span>'
										.'</div>'
										.'<div class="status" id="'.$customer_id.$STATUS.'">'
										.'</div>'
									.'</div>'
								.'</div>'
							.'</div>'
							.'<div class="commodity-miles-info">'
							.'</div>'
						.'</div>';
					$i++;
				}
			?>
		</div>
	</div>
</body>
</html>