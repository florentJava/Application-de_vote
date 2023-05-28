<div class="container-fluid">
	<div class="col-lg-12">
		<form action="" id="manage-settings">
			<input type="hidden" name="voting_id" value="<?php echo $_GET['vid'] ?>">
			<input type="hidden" name="category_id" value="<?php echo $_GET['cid'] ?>">
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
			<div class="form-group">
				<label for="" class="control-label">Maximum number of Selection</label>
				<input type="number" class="form-control" name="max_selection" value="<?php echo $_GET['max'] ?>">
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	
	$('#manage-settings').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_settings',
		    method: 'POST',
		    data: $(this).serialize(),
			error:err=>{
				console.log(err)
			},
			success:function(resp){
				if(resp == 1){
					alert_toast('Data successfully updated.','success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	})
</script>