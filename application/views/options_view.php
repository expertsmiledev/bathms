<h1>Admin Options</h1>
<div id="accordion">
	<h2>Asset Attributes</h2>
	<div class="assetAttributes">
		<?php echo $asset_attributes; ?>
	</div>
	<h2>Inspection Questions</h2>
	<div class="inspectionQuestions">
		<?php echo $questions; ?>
	</div>
	<h2>Admin Users</h2>
	<div class="adminUsers">
		<button id='addAdminUser'>Add Admin User</button>
		<div class="userTableWrapper"> <?php echo $admin_users; ?> </div>
	</div>
	<h2>Assembly Users</h2>
	<div class="assemblyUsers">
		<button id='addAssemblyUser'>Add Assembly User</button>
		<div class="userTableWrapper"> <?php echo $assembly_users; ?> </div>
	</div>
</div>
<div class="spinner"><img src="/images/spinner.gif" alt=""/></div>
