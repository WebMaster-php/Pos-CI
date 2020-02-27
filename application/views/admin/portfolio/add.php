<?php
		// echo "<pre>"; print_r($single_portfolio_data); exit('kooo'); 
		
		if(isset($single_portfolio_data))
		{
			$view_perms 	= json_decode($single_portfolio_data[0]['view_perms'] );
			$view_own_perms = json_decode($single_portfolio_data[0]['view_own_perms']);
			$create_perms 	= json_decode($single_portfolio_data[0]['create_perms']);
			$edit_perms 	= json_decode($single_portfolio_data[0]['edit_perms']);
			$delete_perms 	= json_decode($single_portfolio_data[0]['delete_perms']);
			$modules 		= json_decode($single_portfolio_data[0]['modules']);
		}
?>

<?php init_head(); ?>
<form  id ='port'  method="POST">
<div id="wrapper">
	<div class="content">
		<div class="row">		
		<?php ?> 	
		<div class = "col-md-12">	
			 <div class="panel_s">
				<div class="panel-body">
					<h4 class="customer-profile-group-heading">Portfolio</h4>
					<div class="clearfix"></div>
					<input type = "hidden" name = "id" id = "hide" value ="<?php if(isset($single_portfolio_data)){echo $single_portfolio_data[0]['id'];} else{ 0 ; }?> "> 
					<input type = "hidden" name = "portfolio_name_id" id = "portfolio_name_id" value ="<?php if(isset($single_portfolio_data)){echo $single_portfolio_data[0]['portfolio_name_id'];} else{}?> "> 
					<div class = "col-md-6">	
						<div class="form-group">
							<label for="portfolio_name" class="portfolio_name">Portfolio name</label>
							<input type="text" name="portfolio_name" class="form-control" id="portfolio_name" value = "<?php if(isset($single_portfolio_data)){echo $single_portfolio_data[0]['names'];}?>" required>
						</div>
					</div> 
					<!--<input type="text" name="portfolio_type" class="form-control" id="portfolio_type" value = "<?php //if(isset($single_portfolio_data)){echo $single_portfolio_data[0]['portfolio_type'];}?>">-->
					<div class = "col-md-6">
						<div class="form-group">
							<label for="portfolio_type" class="portfolio_type">Portfolio Type</label>
							<select name="portfolio_type" id="portfolio_type" class="selectpicker" data-width="100%"  required> 
								<option></option>
								<option value = 'main' name= 'main' 
									<?php if(isset($single_portfolio_data) && $single_portfolio_data[0]['portfolio_type'] == 'main') {echo 'selected';}
										  if(empty($single_portfolio_data)  && isset($created_portfolios)){ echo 'disabled'; }?>>
										  Main 
									<?php
										  if(empty($single_portfolio_data)  && isset($created_portfolios)){ echo "   "." This type of portfolio can only be created once.";}	
										  if(!empty($single_portfolio_data)  && isset($created_portfolios)){ echo "   "." This type of portfolio can only be created once.";}
										  ?>
								 </option>
								<option value = 'staff'name = 'staff' 
									<?php if(isset($single_portfolio_data) && $single_portfolio_data[0]['portfolio_type'] == 'staff') {echo 'selected';}?>>Staff</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="description" class="description">Description</label>
						<textarea id="description" name="description" class="form-control" rows="4" > <?php if(isset($single_portfolio_data)) {echo $single_portfolio_data[0]['description'];}?> </textarea>
					</div>
					<div class="form-group checkbox">
						<?php 
						if($this->uri->segment(4) == ''){?>
							<input type="checkbox" class="form-control" value="1" name="followers" checked >
						 <?php } else{?>
						<input type="checkbox" class="form-control" value="1" name="followers" <?php if(isset($single_portfolio_data) && $single_portfolio_data[0]['followers'] == 1 ){ echo "checked" ;} ?> >
						<?php ;}?>
						<label for="followers">Make Ticket followers required</label>
					</div>
					<div class="form-group checkbox">
						<?php 
						if($this->uri->segment(4) == ''){?>
							<input type="checkbox" class="form-control" value="1" name="user_picture" checked >
						 <?php } else{?>
						<input type="checkbox" class="form-control" value="1" name="user_picture" <?php if(isset($single_portfolio_data) && $single_portfolio_data[0]['user_picture'] == 1 ){ echo "checked" ;} ?> >
						<?php ;}?>
						<label for="user_picture">Show user pictures</label>
					</div>

			 </div>		
		</div>
		
		<div class="panel_s">
			<div class="panel-body">
				<h4 class="customer-profile-group-heading">Add Modules</h4>					
						<div id = "checkboxes_of_modules">
							<?php foreach($permissions as $per){?>
									<div class = "col-md-4">
										<div class="input-group">
											<span class="input-group-addon beautiful">
												<input type="checkbox" class = "class" name = "modules[<?php echo $per['shortname'];?>]" id = "<?php echo $per['permissionid'];?>"  value = '1'
													<?php if(isset($modules->$per['shortname']) && $modules->$per['shortname'] == 1 ) {echo "checked" ;} else { }?>
												>
											</span>
											<input type="text" readonly class="form-control" value  ="<?php echo $per['name'];?>">
										</div>
									</div>
							<?php }?>
						</div>
			</div>
		 </div>
		
	</div>

</div>
<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
    <button class="btn btn-info " onclick = "form_submit()"> Save  </button>
 </div>
 </form>
<?php init_tail(); ?>
<script>
var a = $('#portfolio_type').val();
if(a =='main')
  {
	//$('#portfolio_name').prop('disabled',true);
	$('#portfolio_type').prop('disabled',true);
	//$('#description').prop('readonly',true);	
	$('#checkboxes_of_modules input[type="checkbox"]').click(function(e) {
			e.preventDefault();
			e.stopPropagation();
		});
  }
  
$('#portfolio_type').on('change', function(){
	  if(this.value =='main')
	  {
		$('input:checkbox').prop('checked',true); 
				$('input[type="checkbox"]').click(function(e) {
				e.stopPropagation();
				return false; 
		});	
	  }
	  else
	  {
			$('input:checkbox').unbind('click');
			$('input:checkbox').prop('checked',false);		
	  }
  });

function form_submit()
{
	$('#portfolio_name').valid();
		$('#portfolio_type').prop('disabled',false);
		var a = [];
		$('input:checkbox.class').each(function () 
			{
				 var p = $(this).attr('name')+'='+$(this).val();
				 a.push(p);
			}
		 );  
		var c = []; 
		$('input:text.form-control').each(function ()  
			{
				 var t = $(this).attr('name')+'='+$(this).val();
				 c.push(t);
			}
		 );
		ajax({
				type:'post',
				url:'<?php echo base_url()?>admin/portfolio/portfolio',
				data:{checkboxes : a, text : c },
				success:function(dt){
				}
			});
	 
}
</script>
