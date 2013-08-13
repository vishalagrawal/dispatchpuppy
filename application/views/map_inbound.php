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
	<div id="header">
		<div id="left-panel">
			<div id="logo">
				<img id="logo-image" src="<?php echo asset_url();?>images/logo.png" title="DispatchPuppy" alt="DispatchPuppy">
			</div>
			<div id="nav">
			</div>
		</div>
		<div id="right-panel">
			<div id="title">
				<?php echo $title;?>
			</div>
		</div>
	</div>
	<div id="map-canvas">
		<div id="map"></div>
		<div id="key">
			<div class="key-content">
				<div class="marker">
					<img id="" src="<?php echo asset_url();?>images/loaded_miles.png" title="Loaded Miles" alt="Loaded Miles">
				</div>
				<div class="key-info">
					Loaded Miles
				</div>
			</div>
			<div class="key-content">
				<div class="marker">
					<img id="" src="<?php echo asset_url();?>images/empty_miles.png" title="Empty Miles" alt="Empty Miles">
				</div>
				<div class="key-info">
					Empty Miles
				</div>
			</div>
			<div class="key-content">
				<div class="marker">
					<img id="" src="<?php echo asset_url();?>images/google-icons/green/measle-green.png" title="Shipper" alt="Shipper">
				</div>
				<div class="key-info">
					Shipper
				</div>
			</div>
			<div class="key-content">
				<div class="marker">
					<img id="" src="<?php echo asset_url();?>images/google-icons/red/measle-red.png" title="Consignee" alt="Consignee">
				</div>
				<div class="key-info">
					Consignee
				</div>
			</div>
			<div class="key-content">
				<div class="marker">
					<img id="" src="<?php echo asset_url();?>images/bins.png" title="BINS" alt="BINS">
				</div>
				<div class="key-info">
					BINS
				</div>
			</div>
			<div class="key-content">
				<div class="marker">
					<img id="" src="<?php echo asset_url();?>images/combinations.png" title="BINS" alt="BINS">
				</div>
				<div class="key-info">
					Combinations
				</div>
			</div>
		</div>
	</div>
	<div id="control-panel">
		<!--
		<div id="top-header">
			<?php echo $title;?>
		</div>-->
		<div id="all-lanes">
			<?php
				$IMAGE = '-IMAGE';

				if($TYPE === '-CONSIGNEE')
				{
					$SECONDARY_TYPE = '-SHIPPER';
					$PRIMARY_IMAGE = $TYPE.$IMAGE;
					$PRIMARY_MEASLE = 'images/google-icons/red/measle-red.png';
					$SECONDARY_IMAGE = $SECONDARY_TYPE.$IMAGE;
					$SECONDARY_MEASLE = 'images/google-icons/green/measle-green.png';
				}
				else if($TYPE === '-SHIPPER')
				{
					$SECONDARY_TYPE = '-CONSIGNEE';
					$PRIMARY_IMAGE = $TYPE.$IMAGE;
					$PRIMARY_MEASLE = 'images/google-icons/green/measle-green.png';
					$SECONDARY_IMAGE = $SECONDARY_TYPE.$IMAGE;
					$SECONDARY_MEASLE = 'images/google-icons/red/measle-red.png';
				}

				$WEEKS = 20;
				$EMPTY_DIV = '-EMPTY-DIV';
				$i = 0;
				foreach($all_combinations as $primary_id => $primary)
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
					$primary_frequency = floor($primary['number_of_loads']/$WEEKS);
					if($primary_frequency < 1)
					{
						$primary_frequency = '&#60; 1';
					}

					echo '<div class="primary-lane-info'.' '.$first_lane.'" id="'.$primary_id.'" onclick="getPrimaryLocation(\''.$primary_id.'\',\''.$TYPE.'\')">'
							.'<div class="shipper-consignee-info">'
								.'<div class="shipper">'
									.'<div class="marker">'
										.'<img id="'.$primary_id.$PRIMARY_IMAGE.'" src="'.asset_url().$PRIMARY_MEASLE.'" title="Consignee" "alt="Consignee">'
									.'</div>'
									.'<div class="shipper-info">'
										.'<div class="shipper-name">'
											.$primary['primary_name']
										.'</div>'
										.'<div class="shipper-address">'
											.$primary['primary_city'].', '.$primary['primary_state']
										.'</div>'
									.'</div>'
								.'</div>'
								.'<div class="consignee">'
								.'</div>'
							.'</div>'
							.'<div class="frequency-commodity-miles-info">'
								.'<div class="frequency">'
									.$primary_frequency.' <span>per week</span>'
								.'</div>'
								.'<div class="commodity-miles">'
									.'<span class="commodity-info">'
										.'<span class="commodity">'.$primary['primary_commodity'].'</span>'
										.'<span class="commodity-code">'.$primary['primary_commodity_code'].'</span>'
									.'</span>'
								.'</div>'
							.'</div>'
						.'</div>';
						
						if($primary['secondary'] != null)
						{
							foreach($primary['secondary'] as $secondary_id => $secondary)
							{									
								// check frequency
								$secondary_frequency = floor($secondary['number_of_loads']/$WEEKS);
								if($secondary_frequency < 1)
								{
									$secondary_frequency = '&#60; 1';
								}

								echo '<div class="primary-lane-info sub-lane-info" id="'.$primary_id.'-'.$secondary_id.'" onclick="getSecondaryLocation(\''.$primary_id.'\',\''.$secondary_id.'\',\''.$SECONDARY_TYPE.'\')">'
										.'<div class="shipper-consignee-info">'
											.'<div class="shipper">'
												.'<div class="marker">'
													.'<img id="'.$primary_id.'-'.$secondary_id.$SECONDARY_IMAGE.'" src="'.asset_url().$SECONDARY_MEASLE.'" title="Shipper" "alt="Shipper">'
												.'</div>'
												.'<div class="shipper-info">'
													.'<div class="shipper-name">'
														.$secondary['secondary_name']
													.'</div>'
													.'<div class="shipper-address">'
														.$secondary['secondary_city'].', '.$secondary['secondary_state']
													.'</div>'
												.'</div>'
											.'</div>'
											.'<div class="consignee">'
											.'</div>'
										.'</div>'
										.'<div class="frequency-commodity-miles-info">'
											.'<div class="frequency">'
												.$secondary_frequency.' <span>per week</span>'
											.'</div>'
											.'<div class="commodity-miles">'
												.'<span class="empty-miles" id="'.$primary_id.'-'.$secondary_id.$EMPTY_DIV.'"></span>'
												.'<span class="commodity-info">'
													.'<span class="commodity">'.$secondary['secondary_commodity'].'</span>'
													.'<span class="commodity-code">'.$secondary['secondary_commodity_code'].'</span>'
												.'</span>'
												.' <b>&middot;</b> '
												.round($secondary['miles']).' mi'
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