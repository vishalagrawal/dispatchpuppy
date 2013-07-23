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

		/* variable to store center location of map when initialized */
		var center_location = new google.maps.LatLng(<?php echo $google_map_center_location['location_lat'];?>, <?php echo $google_map_center_location['location_lng'];?>);

		/* variable to store all the lane combinations */
		var all_combinations = <?php echo json_encode($all_combinations);?>;
	</script>

	<!--Javascript file to display map-->
	<script type="text/javascript" src="<?php echo asset_url();?>js/map.js"></script>
</head>

<body onload="initialize()">
	<div id="map-canvas"></div>
	<div id="control-panel">
		<!--
		<div id="top-header">
			<?php echo $title;?>
		</div>-->
		<div id="all-lanes">
			<?php
				$SHIPPER_IMAGE = '-SHIPPER-IMAGE';
				$CONSIGNEE_IMAGE = '-CONSIGNEE-IMAGE';
				$WEEKS = 20;
				$EMPTY_DIV = '-EMPTY-DIV';
				$i = 0;
				foreach($all_combinations as $consignee_id => $consignee)
				{
					// check lane number
					if($i==0)
					{
						$first_lane = 'first-lane';
					}
					else
					{
						$first_lane = '';
					}

					// check frequency
					$consignee_frequency = floor($consignee['number_of_loads']/$WEEKS);
					if($consignee_frequency < 1)
					{
						$consignee_frequency = '&#60; 1';
					}

					echo '<div class="primary-lane-info'.' '.$first_lane.'" id="'.$consignee_id.'" onclick="getConsigneeLocation(\''.$consignee_id.'\')">'
							.'<div class="shipper-consignee-info">'
								.'<div class="shipper">'
									.'<div class="marker">'
										.'<img id="'.$consignee_id.$CONSIGNEE_IMAGE.'" src="'.asset_url().'images/google-icons/red/measle-red.png" title="Consignee" "alt="Consignee">'
									.'</div>'
									.'<div class="shipper-info">'
										.'<div class="shipper-name">'
											.$consignee['consignee_name']
										.'</div>'
										.'<div class="shipper-address">'
											.$consignee['consignee_city'].', '.$consignee['consignee_state']
										.'</div>'
									.'</div>'
								.'</div>'
								.'<div class="consignee">'
								.'</div>'
							.'</div>'
							.'<div class="frequency-commodity-miles-info">'
								.'<div class="frequency">'
									.$consignee_frequency.' <span>per week</span>'
								.'</div>'
							.'</div>'
						.'</div>';
						
						if($consignee['shippers'] != null)
						{
							foreach($consignee['shippers'] as $shipper_id => $shipper)
							{									
								// check frequency
								$shipper_frequency = floor($shipper['number_of_loads']/$WEEKS);
								if($shipper_frequency < 1)
								{
									$shipper_frequency = '&#60; 1';
								}

								echo '<div class="primary-lane-info sub-lane-info" id="'.$consignee_id.'-'.$shipper_id.'" onclick="getShipperLocation(\''.$consignee_id.'\',\''.$shipper_id.'\')">'
										.'<div class="shipper-consignee-info">'
											.'<div class="shipper">'
												.'<div class="marker">'
													.'<img id="'.$consignee_id.'-'.$shipper_id.$SHIPPER_IMAGE.'" src="'.asset_url().'images/google-icons/green/measle-green.png" title="Shipper" "alt="Shipper">'
												.'</div>'
												.'<div class="shipper-info">'
													.'<div class="shipper-name">'
														.$shipper['shipper_name']
													.'</div>'
													.'<div class="shipper-address">'
														.$shipper['shipper_city'].', '.$shipper['shipper_state']
													.'</div>'
												.'</div>'
											.'</div>'
											.'<div class="consignee">'
											.'</div>'
										.'</div>'
										.'<div class="frequency-commodity-miles-info">'
											.'<div class="frequency">'
												.$shipper_frequency.' <span>per week</span>'
											.'</div>'
											.'<div class="commodity-miles">'
												.'<span class="empty-miles" id="'.$consignee_id.'-'.$shipper_id.$EMPTY_DIV.'"></span>'
												.'<span class="commodity-info">'
													.'<span class="commodity">'.$shipper['commodity'].'</span>'
													.'<span class="commodity-code">'.$shipper['commodity_code'].'</span>'
												.'</span>'
												.' <b>&middot;</b> '
												.round($shipper['miles']).' mi'
											.'</div>'
										.'</div>'
									.'</div>';
							}
						}
					$i++;
				}
			?>
		</div>
	</div>
</body>
</html>