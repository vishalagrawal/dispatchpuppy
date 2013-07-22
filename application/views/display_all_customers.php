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
			<th>Name</th>
			<th>Address</th>
			<th>City</th>
			<th>State</th>
			<th>Zipcode</th>
			<th>Lat</th>
			<th>Lng</th>
			<th>Bill To</th>
			<th>Shipper</th>
			<th>Consignee</th>
		</tr>

		<?php
			foreach($all_customers as $customer_id => $customer)
			{
				$bill_to = '';
				//$bill_to = 'No';
				if(array_key_exists('bill_to',$customer))
				{	
					$bill_to = $customer['bill_to_revenue'];
					//$bill_to = 'Yes';
				}

				$shipper = '';
				//$shipper = 'No';
				if(array_key_exists('shipper',$customer))
				{
					$shipper = $customer['shipper_revenue'];
					//$shipper = 'Yes';
				}

				$consignee = '';
				//$consignee = 'No';
				if(array_key_exists('consignee',$customer))
				{
					$consignee = $customer['consignee_revenue'];
					//$consignee = 'Yes';
				}
				echo 	'<tr>'
							.'<td>'.$customer['code'].'</td>'
							.'<td>'.mb_convert_case($customer['name'], MB_CASE_TITLE).'</td>'
							.'<td>'.mb_convert_case($customer['address'], MB_CASE_TITLE).'</td>'
							.'<td>'.mb_convert_case($customer['city'], MB_CASE_TITLE).'</td>'
							.'<td>'.$customer['state'].'</td>'
							.'<td>'.$customer['zipcode'].'</td>'
							.'<td>0</td>'
							.'<td>0</td>'
							.'<td>'.$bill_to.'</td>'
							.'<td>'.$shipper.'</td>'
							.'<td>'.$consignee.'</td>'
						.'</tr>';
				
			}
		?>
	</table>
</body>
</html>