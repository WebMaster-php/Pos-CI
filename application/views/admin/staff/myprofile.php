
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php if (($staff_p->staffid == get_staff_user_id() || is_admin()) && !$this->input->get('notifications')){ ?>
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body no-padding-bottom">
           <?php $this->load->view('admin/staff/stats'); ?>
         </div>
       </div>
     </div>
     <?php } ?>
     <div class="col-md-5<?php if($this->input->get('notifications')){echo ' hide';} ?>">
      <div class="panel_s">

        <div class="panel-body">
        <h4 class="no-margin">
          <?php echo _l('staff_profile_string'); ?>
        </h4>
       <hr class="hr-panel-heading" />
          <?php 
          // echo "<pre>";  print_r($custom_inventory); exit('ioioi'); 
          if($staff_p->active == 0){ 
             ?>
          <div class="alert alert-danger text-center"><?php echo _l('staff_profile_inactive_account'); ?></div>
          <hr />
          <?php } ?>
          <div class="button-group mtop10 pull-right">
           <?php if(!empty($staff_p->facebook)){ ?>
            <a href="<?php echo $staff_p->facebook; ?>" target="_blank" class="btn btn-default btn-icon"><i class="fa fa-facebook"></i></a>
            <?php } ?>
            <?php if(!empty($staff_p->linkedin)){ ?>
            <a href="<?php echo $staff_p->linkedin; ?>" class="btn btn-default btn-icon"><i class="fa fa-linkedin"></i></a>
            <?php } ?>
            <?php if(!empty($staff_p->skype)){ ?>
            <a href="skype:<?php echo $staff_p->skype; ?>" data-toggle="tooltip" title="<?php echo $staff_p->skype; ?>" target="_blank" class="btn btn-default btn-icon"><i class="fa fa-skype"></i></a>
            <?php } ?>
            <?php if(has_permission('staff','','edit') && has_permission('staff','','view')){ ?>
            <a href="<?php echo admin_url('staff/member/'.$staff_p->staffid); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>
            <?php } ?>
          </div>
          <div class="clearfix"></div>
          <?php if(is_admin($staff_p->staffid)){ ?>
          <p class="pull-right text-info"><?php echo _l('staff_admin_profile'); ?></p>
          <?php } ?>
          <?php echo staff_profile_image($staff_p->staffid,array('staff-profile-image-thumb'),'thumb'); ?>
          <div class="profile mtop20 display-inline-block">
            <h4>
            <?php echo $staff_p->firstname . ' ' . $staff_p->lastname; ?> 
              <?php if($staff_p->last_activity && $staff_p->staffid != get_staff_user_id()){ ?>
              <small> - <?php echo _l('last_active'); ?>:
                <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($staff_p->last_activity); ?>">
                  <?php echo time_ago($staff_p->last_activity); ?>
                </span>
              </small>
            <?php } ?>
            </h4>
            <p class="display-block"><i class="fa fa-envelope"></i> <a href="mailto:<?php echo $staff_p->email; ?>"><?php echo $staff_p->email; ?></a></p>
            <?php if($staff_p->phonenumber != ''){ ?>
            <p><i class="fa fa-phone-square"></i> <?php echo $staff_p->phonenumber; ?></p>
            <?php } 
            // echo "<pre>"; print_r($this->session->all_userdata()); exit('i m in '); 
            ?>
            <form action = "<?php echo base_url();?>admin/staff/staff_setting" method="post">
            <input type="hidden" name="staffid" value = "<?php echo $this->session->userdata('staff_user_id')?>">
            <?php
            if($staff_p->allow_ticket_filters == 1){?>

            <!-- ab -->
            
            <h3 class="no-margin">
            <?php echo _l('Settings'); ?>
            </h3>

            <p><?php echo _l('Defualt Ticket Display Tickets'); ?></p>
            <?php $permis=unserialize($staff_p->ticket_permissions); ?>
            <div class="checkbox checkbox-primary">
            <input type="checkbox"  id="All_checkbox" <?php if(count($permis)==5){ echo "checked"; }?>>
            <label for="All"><?php echo _l('All'); ?></label>
            </div>
            <?php foreach($statuses as $status){ 
            ?>
            <div class="checkbox checkbox-primary">
            <?php if(in_array($status['ticketstatusid'], $permis)){ ?>
            <input type="checkbox" name="ticket_permissions[]" id="mass_delete"  class="second" checked value="<?php echo $status['ticketstatusid']?>">
            <?php 
            }else{?>
            <input type="checkbox" name="ticket_permissions[]" id="mass_delete"  class="second" value="<?php echo $status['ticketstatusid']?>">
            <?php }?>

            <label for="Tickets assigned to me"><?php echo _l($status['name']); ?></label>
            </div>
            <?php }?>

            <!-- <div class="form-group disable_style_d"> -->
              <?php
              // $selected_source = $staff_inventory_notify[0]['source'];
              // echo render_select('source',$sources,array('source_id','source_name'),'Source',$selected_source,array('required'=>'true','disabled'=>'true'), '', '', 'disable_style_d'); 
              ?>
            <!-- </div> -->
            <?php ;}
            if(is_admin()){?>
             <!--  <div class="checkbox checkbox-primary">
                   <input type="checkbox" name="allow_ticket_filters" id="allow_ticket_filters" value= "1" <?php //if($staff_p->allow_ticket_filters == 1){ echo "checked";} ?>/>
                   <label for="allow_ticket_filters">Allow Ticket Filters</label>
              </div> -->
            <?php }?>
            
            <hr>
                     <div class="form-group">
                        <label for="inventory notifications" class="control-label">Inventory notifications</label>
                        <div class="clearfix"></div>
                        <div class="checkbox">
                        <input type="checkbox" value="1" class="form-control notify_inventory_d" name="notify_inventory" <?php if(isset($staff_inventory_notify[0]['notify_inventory']) && $staff_inventory_notify[0]['notify_inventory'] == 1){ echo "checked"; }  ?> >
                        <label for="inventory">Notify if the STATUS has changed to:</label>
                        </div>
                        <?php 
                        $count = 1;
                        $val_count = 0;
                        if(!empty($staff_inventory_notify)) {
                        $data['explode'] = explode(',',$staff_inventory_notify[0]['inventory_status']);
                        $val_count = count($data['explode']);
                        foreach ($data['explode'] as $select)
                        {
                        ?>
                           <div class="row attachments_dd">
                              <div class="attachment">
                              <div class="col-md-8 mbot15">
                              <div class="input-group">
                              <select id="preview<?=$count;?>" name="inventory_status[]" class="form-control inventory_status_d" style="padding-right: 10px; color: #323a45; background-color : #fff;" >
                              <option value="<?php echo (isset($select)) ? $select : ''; ?>" ><?php echo (isset($select)) ? get_staff_value($select) : 'select status'; ?></option>
                              <?php 
                              if(!empty($custom_inventory)) {
                              foreach ($custom_inventory as $inventory){ ?>
                                 <option class="record" value="<?php echo $inventory['id'];?>"><?php echo $inventory['value'];?></option>
                              <?php } } ?>
                              </select>
                                 <span class="input-group-btn" style="padding-left: 10px;">
                                    <button class="btn btn-danger remove_attachment_d attachments_d p8-half" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                 </span>
                              </div>
                              </div>
                              </div>
                           </div>
                        <?php $count++; } } ?>
                        <?php if($val_count < 6) { ?>
                        <div class="row attachments">
                        <div class="attachment">
                        <div class="col-md-8 mbot15">
                        <div class="input-group">
                        <select id="preview" name="inventory_status[]" class="form-control inventory_status_d" style="padding-right: 10px; color: #323a45; background-color : #fff;" >
                        <option value="">select status</option>
                        <?php 
                        if(!empty($custom_inventory)) {
                        foreach ($custom_inventory as $inventory){ ?>
                        <option class="record" value="<?php echo $inventory['id'];?>"><?php echo $inventory['value'];?></option>
                        <?php } } ?>
                        </select>
                        <span class="input-group-btn" style="padding-left: 10px;">
                        <?php if($val_count <= 5) { ?>
                        <button class="btn btn-success add_more_attachments_d attachments_d p8-half" data-ticket="true" type="button"><i class="fa fa-plus"></i></button> 
                        <?php } else { ?>
                        <button class="btn btn-danger remove_attachment_d attachments_d p8-half" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                        <?php } ?>
                        </span>
                        </div>
                        </div>
                        </div>
                        </div>
                        <?php } ?>
                     </div>

            <!-- ab -->


            <?php if(count($staff_departments) > 0) { ?>
            <div class="form-group mtop10">
              <label for="departments" class="control-label"><?php echo _l('staff_profile_departments'); ?></label>
              <div class="clearfix"></div>
              <?php
              foreach($departments as $department){ ?>
              <?php
              foreach ($staff_departments as $staff_department) {
               if($staff_department['departmentid'] == $department['departmentid']){ ?>
               <div class="chip-circle"><?php echo $staff_department['name']; ?></div>
               <?php }
             }
             ?>
             <?php } ?>
           </div>
           <?php } ?>
         </div>
         <!-- save button added according to new requirment-->
         <input type="submit" class = "btn btn-info pull-right"  value="save">
       </form>


          <!-- save button added according to new requirment-->
       
       </div>
     </div>
   </div>
   <?php if ($staff_p->staffid == get_staff_user_id()){ ?>
   <div class="col-md-7<?php if($this->input->get('notifications')){echo ' col-md-offset-2';} ?>">
    <div class="panel_s">
      <div class="panel-body">
       <h4 class="no-margin">
        <?php echo _l('staff_profile_notifications'); ?>

       </h4>
        <a href="#" onclick="mark_all_notifications_as_read_inline(); return false;"><?php echo _l('mark_all_as_read'); ?></a>
       <hr class="hr-panel-heading" />
        <div id="notifications">
        </div>
        <a href="#" class="btn btn-info loader"><?php echo _l('load_more'); ?></a>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>

   var val = 0;
   var addMoreAttachmentsInputKey = 1;
   $("body").on('click', '.add_more_attachments_d', function() { 
   var limit = '<?php echo count($custom_inventory); ?>';
   // alert(limit); 
   val = $('.attachments_d').length + 1;
   // alert(val); 
   if ($(this).hasClass('disabled')) { return false; }
   var total_attachments = $('select[name*="inventory_status"]').length;
   // alert(total_attachments); 
   if ($(this).data('ticket') && total_attachments >= limit) {
   return false;
   }
   var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
   newattachment.find('select').removeAttr('aria-describedby aria-invalid');
   newattachment.find('.has-error').removeClass('has-error');
   newattachment.find('select').attr('id', 'preview' + val);
   newattachment.find('p[id*="error"]').remove();
   newattachment.find('i').removeClass('fa-plus').addClass('fa-minus');

   newattachment.find('button').removeClass('add_more_attachments_d').addClass('remove_attachment_d').removeClass('btn-success').addClass('btn-danger');
   addMoreAttachmentsInputKey++;
   });
   // Remove attachment
   $("body").on('click', '.remove_attachment_d', function() {
   $(this).parents('.attachment').remove();
   /* if($('.attachments_dd').find('.attachments_d').length == 4)
   {  

   } */
   });

   $('body').on('click','.notify_inventory_d',function(){
   if($(this).is(':checked'))
   {
   $('.inventory_status_d').removeAttr('disabled');
   }
   else if($(this).is(':not(:checked)'))
   {
   $('.inventory_status_d').attr('disabled','disabled');
   }
   });


  $(function(){
   var notifications = $('#notifications');
   if(notifications.length > 0){
    var page = 0;
    var total_pages = '<?php echo $total_pages; ?>';
    $('.loader').on('click',function(e){
     e.preventDefault();
     if(page <= total_pages){
      $.post(admin_url + 'staff/notifications',{page:page}).done(function(response){
       response = JSON.parse(response);
       var notifications = '';
       $.each(response,function(i,obj){
        notifications += '<div class="notification-wrapper" data-notification-id="'+obj.id+'">';
        notifications += '<div class="notification-box-all'+(obj.isread_inline == 0 ? ' unread' : '')+'">';
        var link_notification = '';
        var link_class_indicator = '';
        if(obj.link){
         link_notification= ' data-link="'+admin_url+obj.link+'"';
         link_class_indicator = ' notification_link';
       }
       notifications += obj.profile_image;
       notifications +='<div class="media-body'+link_class_indicator+'"'+link_notification+'>';
       notifications += '<div class="description">';
       if(obj.from_fullname){
        notifications += obj.from_fullname + ' - ';
      }
      notifications += obj.description;
      notifications += '</div>';
      notifications += '<small class="text-muted text-right text-has-action" data-toggle="tooltip" data-title="'+obj.full_date+'">' + obj.date + '</small>';
      if(obj.isread_inline == 0){
       notifications += '<a href="#" class="text-muted pull-right not-mark-as-read-inline notification-profile" onclick="set_notification_read_inline('+obj.id+')" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('mark_as_read'); ?>"><small><i class="fa fa-circle-thin" aria-hidden="true"></i></a></small>';
      }
      notifications += '</div>';
      notifications += '</div>';
      notifications += '</div>';
    });

       $('#notifications').append(notifications);
       page++;
     });

      if(page >= total_pages - 1)
      {
       $(".loader").addClass("disabled");
     }
   }
 });

    $('.loader').click();
  }
});

$(document).ready( function() {
$("#All_checkbox").click(function() {
$(".second").prop("checked", $("#All_checkbox").prop("checked"))
}); 
});
  
</script>
</body>
</html>
