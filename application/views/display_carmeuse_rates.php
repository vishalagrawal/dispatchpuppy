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
			<th>Shipper Address</th>
			<th>Shipper City</th>
			<th>Shipper State</th>
			<th>Shipper Zipcode</th>
			<th>Consignee Name</th>
			<th>Consignee City</th>
			<th>Consignee State</th>
			<th>Consignee Zipcode</th>
			<th>Rate</th>
			<th>Miles</th>
		</tr>
	<?php
		foreach($all_lanes as $lane_id => $particular_lane)
		{
			echo 	'<tr>'
						.'<td>'.$particular_lane['shipper_address'].'</td>'
						.'<td>'.$particular_lane['shipper_city'].'</td>'
						.'<td>'.$particular_lane['shipper_state'].'</td>'
						.'<td>'.$particular_lane['shipper_zipcode'].'</td>'
						.'<td>'.$particular_lane['consignee_name'].'</td>'
						.'<td>'.$particular_lane['consignee_city'].'</td>'
						.'<td>'.$particular_lane['consignee_state'].'</td>'
						.'<td>'.$particular_lane['consignee_zipcode'].'</td>'
						.'<td>'.$particular_lane['rate'].'</td>'
						.'<td>'.$particular_lane['miles'].'</td>'
					.'</tr>';
		}
	?>
	</table>
</body>
</html>