<?php
$listid = $_GET['listid'];
$name = $_GET['name'];
$public = $_GET['public'];

?>
<tr>
	<td colspan="5" class="edit-list-form">
		<div class="edit-form">
			<input type="hidden" value="<?php echo $listid; ?>" id="list-id">
			<input type="text" id="list-name" value="<?php echo $name; ?>"/>
			<input class="edit-list-checkbox" value="<?php echo $public; ?>" type="checkbox" <?php if( $public == 1 ){ echo 'checked'; } ?> id="list-public" name="list-public"/> 
			<label for="list-public"><?php echo 'Allow user to signup to this list'; ?></label>
			<a href="#" class="button" id="save-edit-list">Save</a> <a href="#" class="button" id="cancel-edit-list">Cancel</a>
		</div>
	</td>
</tr>