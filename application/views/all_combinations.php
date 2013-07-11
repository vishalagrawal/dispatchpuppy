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
		var all_lanes = <?php echo json_encode($all_lanes);?>;
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
				$CONSIGNEE_IMAGE = '-CONSIGNEE-IMAGE';
				$WEEKS = 20;
				$EMPTY_DIV = '-EMPTY-DIV';
				$i = 0;
				foreach($all_lanes as $lane_id => $lane)
				{
					if($i==0)
					{
						$first_lane = 'first-lane';
					}
					else
					{
						$first_lane = '';
					}
					echo '<div class="primary-lane-info'.' '.$first_lane.'" id="'.$lane_id.'" onclick="getLaneLocation(\''.$lane_id.'\')">'
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
									.'</div>'
								.'</div>'
							.'</div>'
							.'<div class="frequency-commodity-miles-info">'
								.'<div class="frequency">'
									.ceil($lane['number_of_loads']/$WEEKS).' <span>per week</span>'
								.'</div>'
								.'<div class="commodity-miles">'
									.'<span id="'.$lane_id.$EMPTY_DIV.'"></span>'.$lane['commodity_code'].' <b>&middot;</b> '.round($lane['miles'],1).' mi'
								.'</div>'
							.'</div>'
						.'</div>';

						if($lane['secondary_lanes'] != null)
						foreach($lane['secondary_lanes'] as $sub_lane_id => $sub_lane)
						{
							echo '<div class="primary-lane-info sub-lane-info" id="'.$lane_id.'-'.$sub_lane_id.'" onclick="getSubLaneLocation(\''.$lane_id.'\',\''.$sub_lane_id.'\')">'
									.'<div class="shipper-consignee-info">'
										.'<div class="shipper">'
											.'<div class="marker">'
												.'<img id="'.$lane_id.'-'.$sub_lane_id.$SHIPPER_IMAGE.'" src="'.asset_url().'images/google-icons/measle-green-black-white.png">'
											.'</div>'
											.'<div class="shipper-info">'
												.'<div class="shipper-name">'
													.$sub_lane['shipper_name']
												.'</div>'
												.'<div class="shipper-address">'
													.$sub_lane['shipper_city'].', '.$sub_lane['shipper_state']
												.'</div>'
											.'</div>'
										.'</div>'
										.'<div class="consignee">'
											.'<div class="marker">'
												.'<img id="'.$lane_id.'-'.$sub_lane_id.$CONSIGNEE_IMAGE.'" src="'.asset_url().'images/google-icons/measle-red-black-white.png">'
											.'</div>'
											.'<div class="consignee-info">'
												.'<div class="consignee-name">'
													.$sub_lane['consignee_name']
												.'</div>'
												.'<div class="consignee-address">'
													.$sub_lane['consignee_city'].', '.$sub_lane['consignee_state']
												.'</div>'
											.'</div>'
										.'</div>'
									.'</div>'
									.'<div class="frequency-commodity-miles-info">'
										.'<div class="frequency">'
											.ceil($sub_lane['number_of_loads']/$WEEKS).' <span>per week</span>'
										.'</div>'
										.'<div class="commodity-miles">'
											.'<span id="'.$lane_id.'-'.$sub_lane_id.$EMPTY_DIV.'"></span>'.$sub_lane['commodity_code'].' <b>&middot;</b> '.round($sub_lane['miles']).' mi'
										.'</div>'
									.'</div>'
								.'</div>';
					}
					$i++;
				}
			?>
		</div>
	</div>
</body>
</html>