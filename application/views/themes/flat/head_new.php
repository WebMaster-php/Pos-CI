<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php if (isset($title)){ echo $title; } ?></title>
  <?php if(get_option('favicon') != ''){ ?>
  <link href="<?php echo base_url('uploads/company/'.get_option('favicon')); ?>" rel="shortcut icon">
  <?php } ?>
  <?php if(!isset($exclude_reset_css)){ ?>
  <?php echo app_stylesheet('assets/css','reset.css'); ?>
  <?php } ?>
  <link href='<?php echo base_url('assets/plugins/roboto/roboto.css'); ?>' rel='stylesheet'>
  <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <?php if(is_rtl(true)){ ?>
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-arabic/css/bootstrap-arabic.min.css'); ?>">
  <?php } ?>
  <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url();?>assets/js/popper.min.js"></script>
  <link href="<?php echo base_url('assets/plugins/datatables/datatables.min.css'); ?>" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>">
  <link href="<?php echo base_url('assets/plugins/datetimepicker/jquery.datetimepicker.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet">
  <?php if(is_client_logged_in()){ ?>
  <!-- App favicon -->				
  <link href="<?php echo base_url()?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url()?>assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url()?>assets/css/icons.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url()?>assets/css/style_new.css" rel="stylesheet" type="text/css" />
		
		<!--<link href='<?php echo base_url('assets/plugins/gantt/css/style.css'); ?> rel='stylesheet' />-->
  <link href="<?php echo base_url('assets/plugins/dropzone/min/basic.min.css'); ?>" rel='stylesheet'>
  <link href="<?php echo base_url('assets/plugins/dropzone/min/dropzone.min.css'); ?>" rel='stylesheet'>
  <link href='<?php echo base_url('assets/plugins/gantt/css/style.css'); ?>' rel='stylesheet' />
  <link href='<?php echo base_url('assets/plugins/jquery-comments/css/jquery-comments.css'); ?>' rel='stylesheet' />
  <link href='<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.css'); ?>' rel='stylesheet' />
  <?php } ?>
  <link href="<?php echo base_url('assets/plugins/lightbox/css/lightbox.min.css'); ?>" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css'); ?>">
  <?php //echo app_stylesheet('assets/css','bs-overides.css'); ?>
  <?php //echo app_stylesheet(template_assets_path().'/css','style.css'); ?>
  <?php if(file_exists(FCPATH.'assets/css/custom.css')){ ?>
  <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet" type='text/css'>
  <?php } ?>
  <?php render_custom_styles(array('general','tabs','buttons','customers','modals')); ?>
  <?php $isRTL = (is_rtl(true) ? 'true' : 'false'); ?>
  <!-- DO NOT REMOVE -->
  <?php do_action('app_customers_head',array('language'=>$language)); ?>
</head>
	    <!-- Navigation Bar-->
        <header id="topnav">
            <div class="topbar-main">
                <div class="container-fluid">

                    <!-- Logo container-->
                    <div class="logo">
                        <!-- Text Logo -->
                        <!-- <a href="index.html" class="logo">
                            <span class="logo-small"><i class="mdi mdi-radar"></i></span>
                            <span class="logo-large"><i class="mdi mdi-radar"></i> Highdmin</span>
                        </a> -->
                        <!-- Image Logo -->
                        <a href="<?php echo base_url('clients/profile_new');?>" class="logo">
                            <img src="<?php echo base_url()?>assets/images/logo_sm.png" alt="" height="26" class="logo-small">
                            <img src="<?php echo base_url()?>assets/images/logo_new.png" alt="" height="22" class="logo-large">
                        </a>

                    </div>
                    <!-- End Logo container-->


                    <div class="menu-extras topbar-custom">

                        <ul class="list-unstyled topbar-right-menu float-right mb-0">

                            <li class="menu-item">
                                <!-- Mobile menu toggle-->
                                <a class="navbar-toggle nav-link">
                                    <div class="lines">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </a>
                                <!-- End mobile menu toggle-->
                            </li>
                            

                            <?php 
									if(is_client_logged_in()){
										$announcements = getAnouncements();
										
									}
								
							?>
                            <li class="dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false">
                                    <i class="fi-bell noti-icon"></i>									
                                    <span class="badge badge-danger badge-pill noti-icon-badge countr-zro">
										<?php echo getAnounceStatusCount_(); ?>
									</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                                    <!-- item-->
                                    <div class="dropdown-item noti-title">
                                        <h6 class="m-0"><span class="float-right"><a href="javascript:void(0);" class="text-dark clearanouncement" id="<?php echo get_contact_user_id();?>"><small>Clear All</small></a> </span>Annoucements</h6>
                                    </div>

                                    <div class="slimscroll" style="max-height: 230px;">
                                        <!-- item-->
										<div class="announc_clr_rmv">
										<?php if(is_array($announcements)){
											foreach($announcements as $anounce){?>
											<?php  ?>
											<?php if(getAnounceStatus_($anounce['announcementid'])) {} else {?>
                                        <a href="javascript:void(0);?>" onclick="statusUpdate(<?php echo $anounce['announcementid']; ?>)" class="opn_annouce_pop dropdown-item notify-item" id="<?php echo $anounce['announcementid'] ?>">
                                            <div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
                                            <p class="notify-details"><?php echo $anounce['name'];?><small class="text-muted"><?php echo date('M-d-Y H:i', strtotime($anounce['dateadded']));?></small></p>
                                        </a>
											<?php } ?>
											
										<?php 
											} 
											
											?>
											</div>
											<?php 
											} else { 
												echo 'No Anouncement to read!';
											}
											if($announcements != FALSE){
										?>
                                      <!-- All-->
                                    <a href="<?php echo base_url('clients/announcement_new');?>" class="dropdown-item text-center text-primary notify-item notify-all">
                                        View all <i class="fi-arrow-right"></i>
                                    </a>
											<?php } ?>
                                    </div>
                            </li>

                            

                            <li class="dropdown notification-list">
                                <a class="nav-link dropdown-toggle waves-effect nav-user" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false">
                                    <img src="<?php echo contact_profile_image_url($contact->id,'thumb'); ?>" alt="user" class="rounded-circle"> <span class="ml-1 pro-user-name"><?php echo $contact->firstname; echo " "; echo $contact->lastname?><i class="mdi mdi-chevron-down"></i> </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                    <!-- item-->
                                    <div class="dropdown-item noti-title">
                                        <h6 class="text-overflow m-0">Welcome !</h6>
                                    </div>

                                    <!-- item-->
                                    <a href="<?php echo base_url();?>clients/profile_new" class="dropdown-item notify-item">
                                        <i class="fi-head"></i> <span>Profile</span>
                                    </a>

                                    <!-- item-->
                                    <a href="<?php echo base_url();?>clients/company" class="dropdown-item notify-item">
                                        <i class="dripicons-store"></i> <span>Company Details</span>
                                    </a>

                                    <!-- item-->
                                     <a href="<?php echo base_url();?>clients/rad" class="dropdown-item notify-item">
                                        <i class="fi-cog"></i> <span>RAD</span>
                                    </a>

                                    <!-- item-->
                                    <a href="<?php echo base_url();?>clients/announcement_new" class="dropdown-item notify-item">
                                        <i class="fi-help"></i> <span>Annoucements</span>
                                    </a>

                                    <!-- item-->
                                    <a href="<?php echo base_url();?>clients/logout" class="dropdown-item notify-item">
                                        <i class="fi-power"></i> <span>Logout</span>
                                    </a>

                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- end menu-extras -->

                    <div class="clearfix"></div>

                </div> <!-- end container -->
            </div>
            <!-- end topbar-main -->

            <div class="navbar-custom">
                <div class="container-fluid">
                    <div id="navigation">
                        <!-- Navigation Menu-->
                        <ul class="navigation-menu">
							<?php do_action('customers_navigation_start'); ?>
                            <li class="has-submenu">
                                <a href="<?php echo base_url();?>"><i class="icon-speedometer"></i>Dashboard</a><!--<i class="mdi mdi-chevron-down"></i></a>
                                <ul class="submenu megamenu">
                                    <li>
                                        <ul>
                                            <li><a href="#.html">VersiPOS</a></li>
                                            <li><a href="#.html">VersiEats</a></li>
                                            <li><a href="#.html">VersiPay</a></li>
                                            <li><a href="#.html">VersiGift</a></li>
                                        </ul>
                                    </li>
                                </ul>-->
                            </li> 
							 <?php if(has_contact_permission('support')){ ?>
                            <li class="has-submenu">
                                <a href="<?php echo base_url();?>clients/tickets"><i class="icon-support"></i>Support</a>
                            </li>
							<?php } ?>
                            <!--<li class="has-submenu">
                                <a href="#apps-projects.html"><i class="icon-layers"></i>Projects</a>
                            </li>-->
							<?php if(has_contact_permission('proposals')){ ?>
                            <li class="has-submenu">
                                <a href="<?php echo base_url();?>clients/proposals"><i class="icon-docs"></i>Proposals</a>
                            </li>
							<?php } ?>
							<?php  if((get_option('use_knowledge_base') == 1 && !is_client_logged_in() && get_option('knowledge_base_without_registration') == 1) || (get_option('use_knowledge_base') == 1 && is_client_logged_in())){ ?>
                            <li class="has-submenu">
                                <a href="<?php echo base_url();?>clients/knowledge_base"><i class="icon-briefcase"></i>Knowledge Base</a>
                            </li>
							<?php } ?>
                            <li class="has-submenu">
                                <a href="<?php echo base_url();?>clients/files_new"><i class="icon-layers"></i>Files</a>
                            </li>

                            <li class="has-submenu">
                                <a href="<?php echo base_url();?>clients/versieats_settings"><i class="fa fa-bars"></i>Versieats</a>
                            </li>

                             <li class="has-submenu">
                                <a href="<?php echo base_url();?>clients/reports"><i class="fa fa-book"></i>Reports</a>
                            </li>


                        </ul>
                        <!-- End navigation menu -->
                    </div> <!-- end #navigation -->
                </div> <!-- end container -->
            </div> <!-- end navbar-custom -->
        </header>
        <!-- End Navigation Bar-->