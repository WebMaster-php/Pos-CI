
<div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                <ol class="breadcrumb hide-phone p-0 m-0">
                                    <li class="breadcrumb-item"><a href="#"><?php echo $contact->firstname?> <?php echo " "?><?php echo $contact->lastname?></a></li>
                                    <li class="breadcrumb-item active">Edit Profile</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Edit Profile</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

         

         <div class="row">
                    <div class="col-lg-6">

                        <div class="card-box">
                            <h4 class="header-title m-t-0"><?php echo _l('clients_profile_heading'); ?></h4>
                            <p class="text-muted font-14 m-b-20">
                            </p>

                            <form action="<?php echo base_url()?>clients/profile" enctype="multipart/form-data" method = "Post">
								<?php echo form_hidden('profile',true); ?>
                                <div class="form-group">
                                    <label for="profile_image" class="profile-image"><?php echo _l('client_profile_image'); ?></label>
                                    <input type="file" name="profile_image" class="form-control" id="profile_image">
                                </div>
                                <div class="form-group">
                                    <label for="firstname"><?php echo _l('clients_firstname'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo set_value('firstname',$contact->firstname); ?>">
										   <?php echo form_error('firstname'); ?>
                                </div>
                                <div class="form-group">
									<label for="lastname"><?php echo _l('clients_lastname'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" parsley-trigger="change" 
                                           value ="<?php echo $contact->lastname?>" class="form-control" id="lastName">
										   <?php echo form_error('lastname'); ?>
                                </div>
                                <div class="form-group">
                                    <label for="title"><?php echo _l('contact_position'); ?></label>
                                    <input type="text" name="title" id="title" value="<?php echo $contact->title; ?>" class="form-control">
										   <?php echo form_error('position'); ?>
                                </div>
                                <div class="form-group">
                                    <label for="email"><?php echo _l('clients_email'); ?></label>
                                    <input type="email" disabled="true" id="email" value="<?php echo $contact->email; ?>" class="form-control">
										   <?php echo form_error('email'); ?>
                                </div>
                                <div class="form-group">
                                    <label for="pass1">Phone Number<span class="text-danger">*</span></label>
                                    <input id="pass1" name ="phonenumber" type="text" value ="<?php echo $contact->phonenumber?>" 
									<?php echo form_error('phonenumber'); ?>
                                           class="form-control">
                                </div>
                               

                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-custom waves-effect waves-light"  value = "profile" name = "profile" type="submit">
                                        Update
                                    </button>
                                    <button type="reset" class="btn btn-light waves-effect m-l-5">
                                        Cancel
                                    </button>
                                </div>

                            </form>
                        </div> <!-- end card-box -->
                    </div>
                    <!-- end col -->

                    <div class="col-lg-6">
                        <div class="card-box">
                            <h4 class="header-title m-t-0">Change Password</h4>
                            <p class="text-muted font-14 m-b-20"></p>

                            <form role="form" action="<?php echo base_url()?>clients/profile" method = "Post">
								<?php echo form_hidden('change_password',true); ?>
                                <div class="form-group row">
									<label class="col-4 col-form-label" for="oldpassword"><?php echo _l('clients_edit_profile_old_password'); ?><span class="text-danger">*</span></label>
                                    <div class="col-7">
                                        
										<input type="password" class="form-control" name="oldpassword" id="oldpassword">
										<?php echo form_error('oldpassword'); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
									<label for="newpassword" class="col-4 col-form-label"><?php echo _l('clients_edit_profile_new_password'); ?><span class="text-danger">*</span></label>
                                    <div class="col-7">
                                        <input type="password" class="form-control" name="newpassword" id="newpassword">
										<?php echo form_error('newpassword'); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
										<label for="newpasswordr" class="col-4 col-form-label"><?php echo _l('clients_edit_profile_new_password_repeat'); ?><span class="text-danger">*</span></label>
                                    <div class="col-7">
                                        <input type="password" class="form-control" name="newpasswordr" id="newpasswordr">
										<?php echo form_error('newpasswordr'); ?>
                                    </div>
                                </div>

                                
                                <!--<div class="form-group row">
                                    <div class="col-8 offset-4">
                                        <div>
                                           
                                            <label for="checkbox6">
                                                Password last changed 5 days ago
                                            </label>
                                        </div>
                                    </div>
                                </div>
								-->
                                <div class="form-group row">
                                    <div class="col-8 offset-4">
                                        <button type="submit"  value = "change_password" name = "change_password" class="btn btn-custom waves-effect waves-light">
                                            Change Password
                                        </button>
                                        <button type="reset"
                                                class="btn btn-light waves-effect m-l-5">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div> <!-- end col -->
                </div>
                <!-- end row -->