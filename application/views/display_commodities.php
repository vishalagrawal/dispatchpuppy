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
			<th>Code</th>
			<th>Commodity</th>
			<th>Frequency</th>
			<th>Tractor Cube</th>
			<th>Bill Date</th>
			<th>Trip#</th>
			<th>Weight</th>
			<th>Tractor#</th>
			<th>Trailer#</th>
			<th>Driver ID</th>
		</tr>

		<?php
			foreach($all_commodities as $commodity)
			{
				echo 	'<tr>'
							.'<td>'.$commodity['commodity_code'].'</td>'
							.'<td>'.$commodity['commodity_name'].'</td>'
							.'<td>'.$commodity['commodity_frequency'].'</td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
						.'<tr>';

				foreach($commodity['commodity_info'] as $key => $commodity_info)
				{
					echo '<tr>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td>'.$key.'</td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
							.'<td></td>'
						.'</tr>';

					foreach($commodity_info as $info)
					{
						echo '<tr>'
								.'<td></td>'
								.'<td></td>'
								.'<td></td>'
								.'<td></td>'
								.'<td>'.$info['bill_date'].'</td>'
								.'<td>'.$info['trip_number'].'</td>'
								.'<td>'.$info['weight'].'</td>'
								.'<td>'.$info['tractor'].'</td>'
								.'<td>'.$info['trailer'].'</td>'
								.'<td>'.$info['driver_id'].'</td>'
							.'</tr>';
					}
				}
			}
		?>
	</table>
</body>
</html>