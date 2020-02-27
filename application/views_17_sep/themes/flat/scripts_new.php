<div class="modal fade" id="announce_modal_md" role="dialog">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Announcement</h4>
        </div>
        <div class="modal-body">
          
		  <p class="app_announce_ment"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>
	
<?php if(isset($contracts_by_type_chart)){ ?>
<script>
    var contracts_by_type = '<?php echo $contracts_by_type_chart; ?>';
</script>
<?php } ?>
<script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/datatables.min.js'); ?>"></script>

<?php app_jquery_validation_plugin_js($locale); ?>
<?php app_select_plugin_js($locale); ?>
<script src="<?php echo base_url('assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/Chart.js/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js'); ?>"></script>
<?php echo app_script(template_assets_path().'/js','global.js'); ?>
<script src="<?php echo base_url('assets/plugins/lightbox/js/lightbox.min.js'); ?>"></script>
<?php if(is_client_logged_in()){ ?>
<script src="<?php echo base_url('assets/plugins/dropzone/min/dropzone.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/app-build/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery-comments/js/jquery-comments.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery-circle-progress/circle-progress.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js'); ?>"></script>
<!-- jQuery  -->

<script src="<?php echo base_url();?>assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables/dataTables.responsive.min.js"></script>

<script src="<?php echo base_url();?>assets/js/waves.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.slimscroll.js"></script>
<!-- Counter Up  -->
<script src="<?php echo base_url();?>assets/plugins/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/counterup/jquery.counterup.min.js"></script>
<!-- App js -->
<script src="<?php echo base_url();?>assets/js/jquery.core.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.app.js"></script>
<?php if(get_option('dropbox_app_key') != ''){ ?>
<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?php echo get_option('dropbox_app_key'); ?>"></script>
<?php } ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.js'); ?>"></script>
<?php if(file_exists(FCPATH.'assets/plugins/fullcalendar/locale/'.$locale.'.js')){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/locale/'.$locale.'.js'); ?>"></script>
<?php } ?>
<?php echo app_script(template_assets_path().'/js','clients.js'); ?>
<?php } ?>

		<script>
		
			$(document).ready(function () {
				$('#datatable').dataTable();
			});

			$('.opn_annouce_pop').click(function(){
				announ_id = $(this).attr("id");
				$.ajax({
					type: "POST",
					url: "<?php echo base_url();?>clients/open_announcement",
					data: { id : announ_id},
					success : function(data){
						data = JSON.parse(data);
						$('.app_announce_ment').html(data[0].message);
						$('#announce_modal_md').modal();
						annNotifUpdt();
					}            
				});
			});

				function annNotifUpdt()
				{
					$.ajax({
						type: "POST",
						url: "<?php echo base_url();?>clients/notificationRefresh",
						data: { id : 'static'},
						success : function(data){
							data = JSON.parse(data);
							$('.countr-zro').text(data[0]);
						}            
					});
				}
			
			$('.clearanouncement').click(function(){
				announ_id = $(this).attr("id");
				$.ajax({
					type: "POST",
					url: "<?php echo base_url();?>clients/clear_announcement",
					data: { id : announ_id},
					success : function(data){
						data = JSON.parse(data);
						console.log(data[0]);
						if(data[0] == 'save')
						{
							$('.announc_clr_rmv').html('');
							$('.countr-zro').text(0);
						}
						else
						{
							
						}
					}            
				});
			});
			
			
			$('#load_more').click(function(){
				filesId = $(this).val();
				$.ajax({
					type: "POST",
					url: "<?php echo base_url();?>clients/getmorefile",
					data: { filesId : filesId},
					success : function(data){
						var result = JSON.parse(data);
						if(result.status == 'success')
						{
							$('#loadfiles').append(result.data);
							$('#load_more').val(result.id);
						}
						else
						{
							$('#load_more').attr('disabled','disabled');
						}
					}            
				});
			});
			

			// $('.zindex_div').addClass("addzindex_div");
			// $('.zindex_div').click(function(){
				// $('#company_modal_img').modal();
			// });
			// $('#company_modal_img').on('hidden.bs.modal', function () {
				// $('.zindex_div').removeClass("removezindex_div");
				// $('.zindex_div').addClass("addzindex_div");
			// });
			
			// $('#company_modal_img').on('show.bs.modal', function() {
				// $('.zindex_div').removeClass("addzindex_div");
				// $('.zindex_div').addClass("removezindex_div");
			// });
			
			$('#select_business').on('change', function() {
				$('#companyvalueid').val($(this).val());
				$('#existingfield-error').css('display','none');
				//$('.dropzone').trigger('click');
			});

function statusUpdate(e){
    var getfieldId = '#statusUpdate_'+e; 
    var status = $(getfieldId).attr('data-status'); // retrieve the hid by data attr.
    $.ajax({
        type: "POST",
        url: "<?php echo base_url();?>clients/read_announcement",
        data: { id : e, status : status }, // pass it as POST parameter
          success : function(data){
				data = JSON.parse(data);
				console.log(data);
				annNotifUpdt();
            if(data.toString() == "save"){
              $(getfieldId).attr("data-status", "1");
              $(getfieldId).html("Read");
              $(getfieldId).removeClass("badge badge-danger");
              $(getfieldId).addClass("badge badge-success");
            }else{
              //$(getfieldId).attr("data-status", "0");
              //$(getfieldId).html("Not Read");
              //$(getfieldId).removeClass("badge badge-success");
              //$(getfieldId).addClass("badge badge-danger");
            }
        }            
    });
  }
		</script>
		<?php
		// DONT REMOVE THIS LINE
		do_action('customers_after_js_scripts_load');
		?>
		<?php
		$alertclass = "";
		if($this->session->flashdata('message-success')){
			$alertclass = "success";
		} else if ($this->session->flashdata('message-warning')){
			$alertclass = "warning";
		} else if ($this->session->flashdata('message-info')){
			$alertclass = "info";
		} else if ($this->session->flashdata('message-danger')){
			$alertclass = "danger";
		}
		if($alertclass != ''){
			$alert_message = '';
			$alert = $this->session->flashdata('message-'.$alertclass);
			if(is_array($alert)){
				foreach($alert as $alert_data){
					$alert_message.= '<span>'.$alert_data . '</span><br />';
				}
			} else {
				$alert_message .= $alert;
		}}
			?>
		<script>
			$(function(){
				//alert_float('<?php if(isset($alertclass)){ echo $alertclass;} ?>','<?php if(isset($alert_message)){ echo $alert_message;} ?>');
			});
		</script>
