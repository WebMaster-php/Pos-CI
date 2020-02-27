<?php  //echo "<pre>"; print_r($company); exit('ni'); ?> 
<?php 
$services_clients = array();
foreach($company as $com)
    {
        $services_clients[$com['company']] = getServices($com['userid']);
        // array_push($services_clients, $res);
        // echo "<pre>"; print_r($res); exit('ser');  
}
// echo "<pre>"; print_r($services_clients); exit('ser');
?>

<div class="wrapper">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                <ol class="breadcrumb hide-phone p-0 m-0">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url();?>"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
                                    <li class="breadcrumb-item active">Profile</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Profile</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <div class="row">
                    <div class="col-sm-12">
                        <!-- meta -->
                        <div class="profile-user-box card-box bg-custom">
                            <div class="row">
                                <div class="col-sm-6">
                                    <span class="pull-left mr-3">
										<img src="<?php echo contact_profile_image_url($contact->id,'thumb'); ?>" alt="user" class="thumb-lg rounded-circle"> 
									</span>
                                    <div class="media-body text-white">
                                        <br>
                                        <h4 class="mt-1 mb-1 font-18"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></h4>
										
                                        <p class="font-13 text-light">Owner</p>
                                       <!-- <p class="text-light mb-0">Some Address</p> -->
                                    </div>
                                </div>
                               <div class="col-sm-6">
                                    <div class="text-right">
                                        <a href="<?php echo base_url();?>clients/edit_profile"><button href="edit.html" type="button" class="btn btn-light waves-effect">
                                            <i class="mdi mdi-account-settings-variant mr-1"></i>Edit Profile
                                        </button></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ meta -->
                    </div>
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-md-4">
                        <!-- Personal-Information -->
                        <div class="card-box">
                            <h4 class="header-title mt-0 m-b-20">Personal Information</h4>
                            <div class="panel-body">


                                
                                
                                <p class="text-muted font-13"><strong>Available Services :</strong> <span class="m-l-15"></span><br> 
                                  <?php foreach ($services_clients as $key => $sercl) {
                                      echo $key . '  ' .':' . ' ';  
                                        $st = '';
                                        foreach ($sercl as $scl) {
                                                $st .= $scl['name'] . ' ' . '|' . ' ';  
                                            }   
                                         $r = rtrim($st, ' ');
                                         echo rtrim($r, '|');   
                                         echo '<br>';    
                                        ?>
                                    <?php }?>
                                </p>
                                <hr/>
                                <div class="text-left">
                                    <p class="text-muted font-13"><strong>Full Name :</strong> <span class="m-l-15"><?php echo $contact->firstname ; echo " "; echo $contact->lastname?> </span></p>
									

                                    <p class="text-muted font-13"><strong>Mobile :</strong><span class="m-l-15"><?php echo $contact->phonenumber?></span></p>

                                    <p class="text-muted font-13"><strong>Email :</strong> <span class="m-l-15"><?php echo $contact->email?></span></p>

                                    <p class="text-muted font-13"><strong>Locations :</strong> 
										<span class="m-l-15"> 
											<?php foreach($company as $com)
												{?>
													<?php echo $com['company'];?><?php echo " "?>|
											<?php }?>
										</span>
									</p>	
                                    <p class="text-muted font-13"><strong>Position :</strong> <span class="m-l-15"><?php echo $contact->title;?></span></p>
                                </div>

                               <!-- <ul class="social-links list-inline m-t-20 m-b-0">
                                    <li class="list-inline-item">
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Facebook"><i class="fa fa-facebook"></i></a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Twitter"><i class="fa fa-twitter"></i></a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Skype"><i class="fa fa-skype"></i></a>
                                    </li>
                                </ul>-->
                            </div>
                        </div>
                        <!-- Personal-Information -->

                        <div class="card-box ribbon-box">
                            <div class="ribbon ribbon-primary">Announcements</div>
                            <div class="clearfix"></div>
                            <div class="inbox-widget">
								<?php foreach($announcement as $ann){?>
                                <a href="#">
                                    <div class="inbox-item">        
                                        <p class="inbox-item-text"><?php echo $ann['name'];?></p>
                                        <p class="inbox-item-date m-t-10">
                                            <a href="<?php echo base_url();?>clients/announcement_new"><button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-success"> View </button></a>
                                        </p>
                                    </div>
                                </a>
                                <?php }?>
                            </div>
                        </div>

                    </div>


						<div class="col-md-8">									
							                       <div class="row">
                    <div class="col-md-12">
                        <div class="card-box">
							<ul class="nav nav-pills navtab-bg nav-justified pull-in ">
                               
								<?php foreach($company as $ky=>$com){?>
									<li class="nav-item">
										<a href="#info<?php echo $ky;?>" data-toggle="tab" aria-expanded="false" class="nav-link <?php if($ky == 0){ echo 'active';}?>">
											<?php echo $com['company'];?>
										</a>
									</li>
								<?php 
								} ?>
                            </ul>

								<div class="tab-content">
								   
						<?php foreach($company as $key => $comp){  ?>
							<div class="tab-pane show <?php echo $key == 0 ? 'active' : ''; ?>" id="info<?php echo $key; ?>">
								<div class="row">
									<div class="col-12">
										<div class="card-box">
											<div class="col-12">
												<h4>Notes</h4>
												<div class="card-box">
													<div class="tab-pane show active" id="in">
														<?php foreach($notes[$comp['userid']] as $ntes){?>
															<h5 class="text-custom m-b-5"><?php echo $ntes['firstname']." ".$ntes['lastname']; ?></h5>
															<p class="m-b-0">Customer Support</p>
															<!--<p><b>04/03/2018 1:30 PM</b></p>-->
															<p><b><?php echo date('m/d/Y h:i A',strtotime($ntes['dateadded'])); ?></b></p>

															<p class="text-muted font-13 m-b-0"><?php echo $ntes['description']; ?> 
															</p>
															<hr>
														<?php } ?>
													</div>
												</div> <!-- end card-box -->
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
								

													</div>
												</div>
											</div> <!-- end col -->
										</div>



						
                        <div class="card-box">
                            <h4 class="header-title mb-3">My Inventory</h4>

                            <div class="table-responsive">
                                <table class="table m-b-0">
                                    <thead>
                                    <tr>
                                        <th>Type of Hardware</th>
                                        <th>Serial Number</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Date In</th>
                                        <th>Warranty Expiration Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php foreach($inventory as $key =>$in){
										   foreach($in as $inv){?>
											<tr>	
												<td><?php echo $inv['hardwareval'] ;?></td>
												<td><?php echo $inv['serial_number'] ;?></td>
												<td><?php echo $inv['companyval'] ;?></td>
												<td><?php echo $inv['statusval'] ;?></td>
												<td><?php echo date('m-d-Y', strtotime($inv['date_in']));?></td>
												
												<td>
													<?php 
														if ($inv['exp_date'] == 'No Warranty')
															{
																echo $inv['exp_date']; 
															}
														else 
															{
																echo date('m-d-Y', strtotime($inv['exp_date']));
															}
														?>
												
												</td>
											</tr>
										   <?php }
									 }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->