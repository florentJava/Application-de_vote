<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
$vote = $conn->query("SELECT * FROM voting_opt where id=".$_GET['id']);
	foreach ($vote->fetch_array() as $key => $value) {
		$$key= $value;
	}
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<form action="" id="manage-opt">
			<input type="hidden" name="voting_id" value="<?php echo $_GET['vid'] ?>">
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
			<div class="form-group">
				<label for="" class="control-label">Category</label>
				<select name="category_id" id="" class="custom-select browser-default">
					<?php 
						$cats = $conn->query("SELECT * FROM category_list order by id asc");
						while($row=$cats->fetch_assoc()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? 'selected' :'' ?>><?php echo $row['category'] ?></option>
				<?php endwhile; ?>
				</select>
			</div>
			<div class="form-group">
				<label for="" class="control-label">Candidate Name</label>
				<input type="text" class="form-control" name="opt_txt" value="<?php echo isset($opt_txt) ? $opt_txt :'' ?>">
			</div>
			<div class="form-group">
				<label for="" class="control-label">Image</label>
				<input type="file" class="form-control" name="img" onchange="displayImg(this,$(this))">
			</div>
			<div class="form-group">
				<img src="<?php echo isset($image_path) ? 'assets/img/'.$image_path :'' ?>" alt="" id="cimg">
			</div>
		</form>
	</div>
</div>

<style>
	img#cimg{
		max-height: 10vh;
		max-width: 6vw;
	}
</style>
<script>
	function displayImg(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
$('#manage-opt').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_opt',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			error:err=>{
				console.log(err)
			},
			success:function(resp){
				if(resp == 1){
					alert_toast('Data successfully saved.','success')
					setTimeout(function(){
						location.reload()
					},1500)
				}else if(resp == 2){
					alert_toast('Data successfully updated.','success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})

	})
</script>