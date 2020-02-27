   <style>
   .addzindex_div{ height: 189px;
    border-radius: 5px;
    position: absolute;
    width: 93%;
   z-index: 999;}
    .removezindex_div{ height: 189px;
    border-radius: 5px;
    position: absolute;
    width: 93%;
   z-index: 0;}
   .dropzone{
	   border:2px dashed rgba(0, 0, 0, 0.3);
   }
   .alert-danger {
		color: #f1556c;
		background-color: #fef0f2;
		border-color: #f9b3bd;
		position: fixed;
		top: 33.7%;
		left: 35%;
		z-index: 999999;
	}
   </style>
<div class="wrapper">
	<div class="container-fluid">

		<!-- Page-Title -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="btn-group pull-right">
						<ol class="breadcrumb hide-phone p-0 m-0">
							<li class="breadcrumb-item"><a href="#">Jonathan G</a></li>
							<li class="breadcrumb-item active"><?php echo _l('customer_profile_files'); ?></li>
						</ol>
					</div>
					<h4 class="page-title"><?php echo _l('customer_profile_files'); ?></h4>
				</div>
			</div>
		</div>
		<!-- end page title end breadcrumb -->

		<div class="row">
			<div class="col-12">
				<div class="card-box">
					<div class='row'>
						<div class="col-md-6 pull-left">
							<h4 class="header-title m-t-0">Drop files here to upload</h4>
						</div>
						<div class="col-md-6 pull-right">
							<div class='row'>
								<div class="col-md-4">
									<label for="" class="control-label">
									<small class="req text-danger">* </small>
									Select Company</label>
								</div>
								<div class="col-md-8 pull-left">
									<select required class="selectpicker display-block" data-live-search="true" data-width="100%" class="ajax-search" id="select_business" name="select_business" data-none-selected-text="<?php echo _l('Please Choose Company'); ?>">
										<option value=""></option>
										<?php foreach($companies as $company){?>
											<option data-dismiss="modal" value="<?php echo $company['userid'];?>"><?php echo $company['company'];?></option>
										<?php } ?>
									</select>
									<p id="existingfield-error" class="text-danger" style="display: none; margin: 0px;">This field is required.</p>
								</div>
							</div>
						</div>
					</div>
					<p class="text-muted font-14 m-b-10"></p>
					<!--<form action="<?php echo base_url('clients/upload_files');?>" class="dropzone" id="dropzone" enctype="multipart/form-data">
						<div class="fallback">
							<input name="file" type="file" multiple />
						</div>
					</form>
					<div class="zindex_div"></div>-->
					<?php echo form_open_multipart(site_url('clients/upload_files'),array('class'=>'dropzone','id'=>'files-upload')); ?>
						<input type="file" name="file" multiple style="display: none;" class="hide"/>
						<input type="hidden" id="companyvalueid" name="companyvalueid" value=""/>
					 <?php echo form_close(); ?>
				</div>
			</div>
		</div>
		<?php if(get_option('dropbox_app_key') != ''){ ?>
			<div class="mtop15 mbot15">
				<div id="dropbox-chooser-files"></div>
			</div>
		<?php } ?>
		<!-- end row -->
		
		<div class="row">
			<div class="col-12">
				<div class="card-box">
					
					<h4 class="header-title m-b-30">My Files</h4>

					<div class="row" id="loadfiles">
						<?php if(count($files) == 0){ ?>
							<hr />
							<p class="no-margin"><?php echo _l('no_files_found'); ?></p>
						<?php } else { 
						$fileId = 0;
						foreach($files as $file){ ?>
						<div class="col-lg-2 col-xl-2">
							<div class="file-man-box">
								<?php if(get_option('allow_contact_to_delete_files') == 1){ 
											if($file['contact_id'] == get_contact_user_id()){ ?>
												<a href="<?php echo site_url('clients/delete_file/'.$file['id'].'/general'); ?>" class="file-close"><i class="mdi mdi-close-circle"></i></a>
											<?php } 
											
											} ?>
								
								<div class="file-img-box">
									<?php 
										$url = site_url() .'download/file/client/';
										$path = get_upload_path_by_type('customer') . $file['rel_id'] . '/' . $file['file_name'];
										$is_image = false;
										if(!isset($file['external'])) {
											$attachment_url = $url . $file['attachment_key'];
											$is_image = is_image($path);
											$img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$file['filetype']);
										} else if(isset($file['external']) && !empty($file['external'])){
											if(!empty($file['thumbnail_link'])){
												$is_image = true;
												$img_url = optimize_dropbox_thumbnail($file['thumbnail_link']);
											}
											$attachment_url = $file['external_link'];
										}
										$ext = explode(".",$file['file_name']);
										$ext = $ext[1];
										if($is_image){
							}
							?>
								<?php if($is_image){ ?>
									  <img src="<?php echo $img_url; ?>" style="width: 100%; height: 100%;">
								<?php } else { ?>
								<img src="<?php echo base_url();?>assets/images/file_icons/<?php echo $ext;?>.svg" alt="icon" style="width: 80%; height: 80%;">
								<?php }  ?>
								</div>
								<a href="<?php echo $attachment_url; ?>" class="file-download"><i class="mdi mdi-download"></i> </a>
								<div class="file-man-title">
									<h5 class="mb-0 text-overflow"><?php echo $file['file_name']; ?></h5>
									<p class="mb-0"><small><?php echo _dt($file['dateadded']); ?></small></p>
								</div>
							</div>
						</div>
						<?php  $fileId = $file['id'];} }?>
					</div>	
					<div class="text-center mt-3">
						<button type="button" class="btn btn-outline-danger w-md waves-effect waves-light" value="<?php echo $fileId;?>" id="load_more"><i class="mdi mdi-refresh"></i> Load More Files</button>
					</div>

				</div>
			</div><!-- end col -->
		</div>
		<!-- end row -->

	</div> <!-- end container -->
</div>
<!-- end wrapper -->

<!-- Modal -->
<div id="company_modal_img" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Select Company</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
			<label for="" class="control-label">
			<small class="req text-danger">* </small>
			Select Company</label>
			<select required class="selectpicker display-block" data-live-search="true" data-width="100%" class="ajax-search" id="select_business" name="select_business" data-none-selected-text="<?php echo _l('Please Choose Company'); ?>">
				<option value=""></option>
				<?php foreach($companies as $company){?>
					<option data-dismiss="modal" value="<?php echo $company['userid'];?>"><?php echo $company['company'];?></option>
				<?php } ?>
			</select>
			<p id="existingfield-error" class="text-danger" style="display: none;">This field is required.</p>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>