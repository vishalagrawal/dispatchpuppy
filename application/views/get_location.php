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
		var DEFAULT_MARKER_PRIMARY_SHIPPER = '<?php echo asset_url();?>images/google-icons/green/measle-green.png';
		var DEFAULT_MARKER_PRIMARY_CONSIGNEE = '<?php echo asset_url();?>images/google-icons/red/measle-red.png';
		var DEFAULT_MARKER_SECONDARY_SHIPPER = '<?php echo asset_url();?>images/google-icons/measle-green-black-white.png';
		var DEFAULT_MARKER_SECONDARY_CONSIGNEE = '<?php echo asset_url();?>images/google-icons/measle-red-black-white.png';

		/* variable to store center location of map when initialized */
		var center_location = new google.maps.LatLng(<?php echo $google_map_center_location['location_lat'];?>, <?php echo $google_map_center_location['location_lng'];?>);

		/* variable to store all the lane combinations */
		var all_lanes = <?php echo json_encode($lanes_without_lat_lng);?>;
	</script>

	<!--Javascript file to display map-->
	<script type="text/javascript" src="<?php echo asset_url();?>js/map.js"></script>
</head>

<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="control-panel">
		<div id="all-lanes">
			<?php
				$SHIPPER_IMAGE = '-SHIPPER-IMAGE';
				$SHIPPER_LAT = '-SHIPPER-LAT';
				$SHIPPER_LNG = '-SHIPPER-LNG';
				$SHIPPER_STATUS = '-SHIPPER-STATUS';

				$CONSIGNEE_IMAGE = '-CONSIGNEE-IMAGE';
				$CONSIGNEE_LAT = '-CONSIGNEE-LAT';
				$CONSIGNEE_LNG = '-CONSIGNEE-LNG';
				$CONSIGNEE_STATUS = '-CONSIGNEE-STATUS';

				$i = 0;
				foreach($lanes_without_lat_lng as $lane_id => $lane)
				{
					if($i==0)
					{
						$first_lane = 'first-lane';
					}
					else
					{
						$first_lane = '';
					}
					echo '<div class="primary-lane-info'.' '.$first_lane.'" id="'.$lane_id.'" onclick="getLatLng(\''.$lane_id.'\')">'
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
											.'<span id="'.$lane_id.$SHIPPER_LAT.'">'.$lane['shipper_lat'].'</span>, <span id="'.$lane_id.$SHIPPER_LNG.'">'.$lane['shipper_lng'].'</span>'
										.'</div>'
										.'<div class="status" id="'.$lane_id.$SHIPPER_STATUS.'">'
									
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
											.'<span id="'.$lane_id.$CONSIGNEE_LAT.'">'.$lane['consignee_lat'].'</span>, <span id="'.$lane_id.$CONSIGNEE_LNG.'">'.$lane['consignee_lng'].'</span>'
										.'</div>'
										.'<div class="status" id="'.$lane_id.$CONSIGNEE_STATUS.'">'
									
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
					$i++;
				}
			?>
		</div>
	</div>
</body>
</html>