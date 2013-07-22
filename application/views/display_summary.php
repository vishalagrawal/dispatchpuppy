<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?> | Dispatch Puppy</title>
	
	<!--HTML5 Boilerplate CSS-->
	<link rel="stylesheet" type="text/css" href="<?php echo asset_url();?>css/main.css">

</head>

<body>
	<table style="border: 0.1em dotted #000000; width: 100%;">
		<tr>
			<th>Trip Number</th>
			<th>Shipper Code</th>
			<th>Shipper Name</th>
			<th>Shipper City</th>
			<th>Shipper State</th>
			<th>Consignee Code</th>
			<th>Consignee Name</th>
			<th>Consignee City</th>
			<th>Consignee State</th>
			<th>Commodity</th>
			<th>Rate</th>
			<th>Driver Pay</th>
			<th>Distance</th>
			<th>Loads</th>
		</tr>

		<?php
			foreach($all_lanes as $lane_id => $lane)
			{
				echo 	'<tr>'
							.'<td>'.$lane['trip_number'].'</td>'
							.'<td>'.$lane['shipper_code'].'</td>'
							.'<td>'.mb_convert_case($lane['shipper_name'], MB_CASE_TITLE).'</td>'
							.'<td>'.mb_convert_case($lane['shipper_city'], MB_CASE_TITLE).'</td>'
							.'<td>'.$lane['shipper_state'].'</td>'
							.'<td>'.$lane['consignee_code'].'</td>'
							.'<td>'.mb_convert_case($lane['consignee_name'], MB_CASE_TITLE).'</td>'
							.'<td>'.mb_convert_case($lane['consignee_city'], MB_CASE_TITLE).'</td>'
							.'<td>'.$lane['consignee_state'].'</td>'
							.'<td>'.$lane['commodity_code'].'</td>'
							.'<td>'.$lane['rate'].'</td>'
							.'<td>'.$lane['driver_pay'].'</td>'
							.'<td>'.$lane['distance'].'</td>'
							.'<td>'.$lane['number_of_loads'].'</td>'
						.'</tr>';
				
			}
		?>
	</table>
</body>
</html>