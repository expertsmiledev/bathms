<a href="/customer/delete_customer/<?php echo $cust_id;?>" ><button id="deleteCustomer">Delete Customer</button></a>
<h1><?php echo $cust_name; ?></h1>
<div id="accordion">
	<h2>Customer Details</h2>
	<div class="custDetails clear"> <?php echo $customer; ?> </div>
	<h2>Assets</h2>
	<div class="custAssetList">
	<div class="custFilter">
		<input type="text" value="" placeholder="Enter filter text" class="assetFilter" title="Searches on Asset ID or Customer Serial No">
	</div>
<?php if($user_role == 1 || $user_role == 2){?>
		<button id='addNewAsset'>Add Asset</button>
<?php } ?>
	<a href='/certificate/generate_bulk_certificates/<?php echo $cust_id;?>' target="_blank" title="Opens a single pdf containing latest test certificates for all assets"><button>Print Bulk Certificates</button></a>
		<div class="assetTableWrapper"> <?php echo $assets; ?></div>
	</div>
	<h2>Print Bulk Certificates</h2>
	<div class="custPrintBulk">
		
	</div>
	<h2>Locations</h2>
	<div class="custLocations">
		<button id='addNewLocation'>Add Location</button>
		<div> <?php echo $locations; ?> </div>
	</div>
	
<?php if($user_role == 1 || $user_role == 2){?>
	<h2>Users </h2>
	<div class="custUsers">
		<button id='addNewUser'>Add User</button>
		<div> <?php echo $users; ?> </div>
	</div>
<?php } ?>

</div>
