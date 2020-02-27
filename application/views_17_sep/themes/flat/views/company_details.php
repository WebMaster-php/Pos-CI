
<div class="wrapper">
    <div class="container-fluid">
		
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                <ol class="breadcrumb hide-phone p-0 m-0">
                                    <li class="breadcrumb-item"><a href="<?php base_url();?>clients/profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
                                    <li class="breadcrumb-item active">Edit Company Details</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Edit Company Details</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

         

        <div class="row">
                    <div class="col-md-12">
                        <div class="card-box">
                            
                            <ul class="nav nav-pills navtab-bg nav-justified pull-in ">
                               <?php $i=1;?>	
								<?php foreach($company as $com){?>
									<li class="nav-item">
										<a href="#info<?php echo $i;?>" data-toggle="tab" aria-expanded="false" class="nav-link <?php if($i == 1){ echo 'active';}?>">
											<?php echo $com['company'];?>
										</a>
									</li>
								<?php $i++; 
								} ?>
                            </ul>


        <div class="tab-content">		
				<?php $c=1;  foreach($company as $co){?>
					<div class="tab-pane <?php if($c == 1){ echo 'show active';}?>" id="info<?php echo $c;?>">
							 <!-- Overview -->
								<div class="row">
									<div class="col-12">
										<div class="card-box"> 
										   <div class="col-12">
											 <div class="card-box">
												<form action="<?php echo base_url();?>clients/company" method = "POST">
													
													<div class="form-group">
														<input type = "hidden" name = "company_id" value = "<?php echo $co['userid']?>"/> 
														<label for="userName">Company Name<span class="text-danger">*</span></label>
														<input type="text" name="company" parsley-trigger="change"  
															   value ="<?php echo $co['company']?>" class="form-control" id="userName">
															   <?php echo form_error('company'); ?>
													</div>
			
													<div class="form-group">
														<label for="phonenumber"><?php echo _l('clients_phone'); ?></label>
														<input type="text" class="form-control" name="phonenumber" id="phonenumber" value="<?php echo $co['phonenumber']; ?>">
													</div>
													
													 <div class="form-group">
														<label class="control-label" for="website"><?php echo _l('client_website'); ?></label>
														<input type="text" class="form-control" name="website" id="website" value="<?php echo $co['website']; ?>">
													</div>
													<div class="form-group">
														<label for="lastname"><?php echo _l('clients_country'); ?></label>
														<select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" name="country" class="form-control" id="country">
															<option value=""></option>
															<?php foreach(get_all_countries() as $country){ ?>
															<option value="<?php echo $country['country_id']; ?>" <?php if($country['country_id'] == $co['country']){echo 'selected';} ?>><?php echo $country['short_name']; ?></option>
															<?php } ?>
														</select>
													</div>
													
													<div class="form-group">
														<label for="city"><?php echo _l('clients_city'); ?></label>
														<input type="text" class="form-control" name="city" id="city" value="<?php echo $co['city']; ?>">
													</div>													
								                    <div class="form-group">
														<label for="state"><?php echo _l('clients_state'); ?></label>
														<input type="text" class="form-control" name="state" id="state" value="<?php echo $co['state']; ?>">
													</div>
													<div class="form-group">
														<label for="address"><?php echo _l('clients_address'); ?></label>
														<textarea name="address" id="address" class="form-control" rows="4"><?php echo clear_textarea_breaks($co['address']); ?></textarea>
													</div>
								                    <div class="form-group">
														<label for="zip"><?php echo _l('clients_zip'); ?></label>
														<input type="text" class="form-control" name="zip" id="zip" value="<?php echo $co['zip']; ?>">
													</div>
												   

													<div class="form-group text-right m-b-0">
														<div class="form-group">
															<button class="btn btn-custom waves-effect waves-light" type="submit">Update</button>
															<button type="reset" class="btn btn-light waves-effect m-l-5">Cancel</button>
														</div>
													</div>
												</form>
											 </div> <!-- end card-box -->
											</div>
											<!-- end row -->
										</div>
									</div>
								</div>
								<!-- end row -->
						   </div>
				<?php
				$c++;	
				} 
				?>		   
			</div>
		</div>
	</div>
		</div>	
	</div>
</div>