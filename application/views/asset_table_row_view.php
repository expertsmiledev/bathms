<tr class="<?php echo $rowclass; ?>">
<?php if($user_role == 1 || $user_role == 2){ ?>
	<td class='button'><img src='/images/copy-icon.png' class='copyAsset' id='copy_<?php echo $ast_id; ?>' ></td>
	<td class="button"><img src="/images/inspection-icon.png" class="inspectAsset" alt=""  id='inspect_<?php echo $ast_id; ?>' /></td>
<?php } ?>
	<td><input class='ast_id' value='<?php echo $ast_id; ?>'></td>
	<td><input class='ast_serial' value='<?php echo $ast_serial; ?>'></td>
	<td><select class='loc_id'>
			<?php echo $loc_dropdown; ?>
		</select></td>
	<td><select class='prd_id'>
			<?php echo $prd_dropdown; ?>
		</select></td>
	<td><input class='ast_length' value='<?php echo $ast_length; ?>'></td>
	<td><select class='cpl_id_a'>
			<?php echo $cpl_id_a_dropdown; ?>
		</select></td>
	<td><select class='cpa_id_a'>
			<?php echo $cpa_id_a_dropdown; ?>
		</select></td>
	<td><select class='atm_id_a'>
			<?php echo $atm_id_a_dropdown; ?>
		</select></td>
	<td><select class='cpm_id_a'>
			<?php echo $cpm_id_a_dropdown; ?>
		</select></td>
	<td><select class='cpl_id_b'>
			<?php echo $cpl_id_b_dropdown; ?>
		</select></td>
	<td><select class='cpa_id_b'>
			<?php echo $cpa_id_b_dropdown; ?>
		</select></td>
	<td><select class='atm_id_b'>
			<?php echo $atm_id_b_dropdown; ?>
		</select></td>
	<td><select class='cpm_id_b'>
			<?php echo $cpm_id_b_dropdown; ?>
		</select></td>
	<td><select class='nmb_id'>
			<?php echo $nmb_dropdown; ?>
		</select></td>
	<td>
	<?php
	if($ast_lastcert != NULL && $ast_lastcert != ""){
		echo "<a href='$ast_lastcert' target='_blank' title='Download latest certificate'><img src='/images/pdficon.png' alt='Adobe pdf' /></a>";
	}else{
		echo "&nbsp;";	
	}
	?>
	
	</td>
	<td><input class='ast_manufacturedate' value='<?php echo $ast_manufacturedate; ?>'></td>
	<td><input class='ast_gravedate' value='<?php echo $ast_gravedate; ?>'></td>
	<td><textarea class='ast_notes'><?php echo $ast_notes; ?></textarea></td>
<?php if($user_role == 1 || $user_role == 2){ ?>
	<td class="button"><img src="/images/delete-icon.png" class='deleteAsset' id='delete_<?php echo $ast_id; ?>' ></td>
<?php } ?>
</tr>