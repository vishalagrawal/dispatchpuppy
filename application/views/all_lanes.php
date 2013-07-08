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
		var all_lanes = <?php echo json_encode($all_lanes);?>;
	</script>

	<script type="text/javascript" src="<?php echo asset_url();?>js/map.js"></script>

</head>
<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="control-panel">
		<div class="all-lanes">
			<?php
				$checkbox = '-CHECKBOX';
				foreach($all_lanes as $lane_id => $lane)
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
									
									if($lane['secondary_lanes'] != null)
									foreach($lane['secondary_lanes'] as $sub_lane_id => $sub_lane)
									{
										echo '<div class="sub-lane-info" id="'.$lane_id.'-'.$sub_lane_id.'">'
												.'<div class="left-checkbox">'
													.'<input type="checkbox" id="'.$lane_id.'-'.$sub_lane_id.$checkbox.'" onclick="getSubLaneLocation(\''.$lane_id.'\',\''.$sub_lane_id.'\')">'
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
														.round($sub_lane['miles'],1).' mi'
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