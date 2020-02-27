                               
        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                <ol class="breadcrumb hide-phone p-0 m-0">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url();?>clients/profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
                                    <li class="breadcrumb-item active">Announcements</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

         

         <div class="row">
                    <div class="col-12">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title">Announcements</h4>
                            <p class="text-muted m-b-30 font-13">
                                It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. 
                            </p>

                            <div class="mb-3">
                                
                            </div>
                            <table class="table table-hover m-0 tickets-list table-actions-bar dt-responsive nowrap" cellspacing="0" width="100%" id="datatable">
                                <thead>
                                <tr>
                                
                                    <th>Subject</th>
                                    <th>Announcement</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="hidden-sm">Actions</th>
                                </tr>
                                </thead>

                                <tbody>
								<?php foreach($announcement as $ann){?>
								<div style="display: none;">
									<div id="inline-<?php echo $ann['announcementid']; ?>" style="width:400px;height:100px;overflow:auto;">
									</div>
								</div>
								<tr>
																																			
                                    <td><b><a class="popupannouncement opn_annouce_pop" href="javascript:void(0);" onclick="statusUpdate(<?php echo $ann['announcementid']; ?>)" id="<?php echo $ann['announcementid'] ?>" style="color:#212529"><?php echo $ann['name']?></a></b></td>
                                  
                                    <td>
                                        <?php echo character_limiter( strip_tags($ann['message']), 50);?>
                                    </td>

                                    <td>
										<?php 
											if(getAnounceStatus_($ann['announcementid'])){ 
											  echo "<a href='javascript:void(0)'><span class='badge badge-success' id='statusUpdate_".$ann['announcementid'] . "' name='hid' data-status='" . $ann['status'] . " '>Read</span></a>"; 
											} 
											else { 
											  echo "<a href='javascript:void(0)'><span class='badge badge-danger' id='statusUpdate_".$ann['announcementid']."' name='hid' data-status='".$ann['status']."'>Not Read</span></a>"; 
											}
										?>
                                    </td>

                                    <td>
                                        <?php  echo date('m-d-Y H:i A', strtotime($ann['dateadded']));?>
                                    </td>

                                    <td>
                                        <div class="btn-group dropdown">
                                            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-light btn-sm" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="statusUpdate(<?php echo $ann['announcementid']; ?>)" >Mark as Read</a>
                                                <!--<a class="dropdown-item" href="#" data-toggle="modal" data-target="#myModal"  >Read</a>-->                                                
                                                <a class="dropdown-item opn_annouce_pop" href="javascript:void(0);" id="<?php echo $ann['announcementid'] ?>">Read</a>                                                
                                            </div> 
                                        </div>
                                    </td>
                                </tr>
								<?php } ?>

                                 </tbody>
                            </table>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
		
		
		

		
		
	
		
