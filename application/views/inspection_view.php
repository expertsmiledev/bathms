<h2>Inspection/Certificate</h2>
<h2>Customer: <a class="customerLink" href='/customer/<?php echo $customer->cus_id; ?>' ><?php echo $customer->cus_name." (".$customer->cus_code.")";?></a></h2>
<h2>Asset: <?php echo $asset_id; ?></h2>
<h2><?php echo $type; ?></h2>
<div id="inspectionForm" class="clear">
<?php echo $inspection_form; ?>
</div>	
<div  class="printCertificate">
	<!--  <button onclick="window.location.href='/certificate/inspection/<?php echo $ins_id; ?>';">
      Print Certificate
    </button> -->
 		<input type="hidden" value="<?php echo $ins_id; ?>" id="ins_id" ?>
    	<a  class="pdfLink"
    		href="/certificate/inspection/<?php echo $ins_id; ?>/" 
    		target='_blank'
			style="text-decoration: none;
			   color: white;
			   background-color: #228fa4;
			   padding: 5px 10px;
			   margin-left: 24px;" 
    	>
    		Print Certificate
    	</a>
	
</div>
