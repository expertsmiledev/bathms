	<table class="editTable customerTable" id="cust_<?php echo $cus_id;?>">
		<thead>
			<tr>
			<?php if($user_role == 1 || $user_role == 2){?>
				<th>Copy Asset</th>
				<th>Inspection</th>
			<?php } ?>
				<th>Asset ID</th>
				<th>Customer Serial No</th>
				<th>Location</th>
				<th>Product</th>
				<th>Length (m)</th>
				<th>Coupling A</th>
				<th>Addon A</th>
				<th>Attach Method A</th>
				<th>Material A</th>
				<th>Coupling B</th>
				<th>Addon B</th>
				<th>Attach Method B</th>
				<th>Material B</th>
				<th>Nominal Bore</th>
				<th>Last Certificate</th>
				<th>Manufacture Date</th>
				<th>Grave Date</th>
				<th>Notes</th>
			<?php if($user_role == 1 || $user_role == 2){?>
				<th>Delete Asset</th>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php echo $hose_table_rows; ?>
		</tbody>
	</table>
	<div id="inspectionDialog" title="Inspection Type">
		<p>What type of inspection is this?</p>
	</div>
