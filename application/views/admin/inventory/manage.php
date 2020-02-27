<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
           <?php do_action('before_items_page_content');?>
           <div class="_buttons">
       <?php if(getAdmin() || has_permission('inventory', '', 'create')) { ?>
            <a href="#" class="btn btn-info pull-left" onclick="add_inventory()" data-toggle="modal" data-target="#sales_inventory_modal"><?php echo _l('New Inventory'); ?></a>
       <?php } if(getAdmin() || has_permission('inventory', '', 'view') && has_permission('inventory', '', 'create') && has_permission('inventory', '', 'edit') && has_permission('inventory', '', 'delete') ){ ?>
            <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#type_of_hardware"><?php echo _l('Types of Hardware'); ?></a>
            <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#status"><?php echo _l('Status'); ?></a>
            <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#owner"><?php echo _l('Owner'); ?></a>
            <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#origin"><?php echo _l('Origin'); ?></a>
      <?php } ?>
          </div>
          <div class="clearfix"></div>
          <hr class="hr-panel-heading" />
          <div>
            <div class="col-md-1 " style= "padding-right:0px; padding-left:0px;">
              <h5 class="bold"><?php echo _l('filter_by_customers'); ?> :</h5>
            </div>
            <div class="col-md-4 ">
                <?php 
                  $selected = ''; ?>
                <?php echo render_select('customers[]',$cust,array('userid','company'),'',$selected,array('multiple'=>true,'data-actions-box'=>true, 'selected'=>true),array(),'','customer_inventory',false);?> 
            </div>
            <div class="col-md-1 ">
              <h5 class="bold"><?php echo _l('filter_by_status'); ?> :</h5>
            </div>
            <div class="col-md-4 ">
              <?php
              // print_r($statuses); exit; 
              // $selected[] = '';
              $selected = array(); 
                foreach($statuses as $key => $status) {
                      $selected[] = $status['id'];
                    }    
                echo render_select('statuses[]',$statuses,array('id','value'),'',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','status_inventory',false);?>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="clearfix"></div>
      <?php 
      if (getAdmin() || has_permission('inventory', '', 'edit') || has_permission('inventory', '', 'delete') ) 
      {
        $table_data = array(
          _l('serial_number'),
          _l('type_Of_hardware'),
          _l('account'),
          _l('notes'),
          _l('Last Activity'),
          _l('status'),
          _l('date_in'),
          _l("origin"),
          _l("who_owns_the_equipment"),
          _l("actions"));
        render_datatable($table_data,'sale-inventory-items');
      }
      else
      {
        $table_data = array(
          _l('serial_number'),
          _l('type_Of_hardware'),
          _l('account'),
          _l('notes'),
          _l('status'),
          _l('date_in'),
          _l("origin"),
          _l("who_owns_the_equipment"));
        render_datatable($table_data,'sale-inventory-items'); 
      }
            
      ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('admin/inventory/add_inventory'); ?>
<?php //types of hardware?>
<div class="modal fade" id="type_of_hardware" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('hardware_name'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('inventory','','create')){ ?>
        <div class="input-group">
      <input type="hidden" value="inventoryGroup" id="action">
      <input type="hidden" value="hardware" id="inv_group_type">
          <input type="text" name="name" id="name" class="form-control" placeholder="<?php echo _l('hardware_name'); ?>">
          <span class="input-group-btn">
            <button class="btn btn-info p9 btn-inv-action" type="button"><?php echo _l('add_new'); ?></button>
          </span>
        </div>
        <hr />
        <?php } ?>
        <div class="row">
         <div class="container-fluid">
          <table class="table dt-table table-items-groups" data-order-col="0" data-order-type="asc">
            <thead>
              <tr>
                <th><?php echo _l('hardware_name'); ?></th>
                <th><?php echo _l('option'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($values as $value){ 
        if($value->group_type == 'hardware'){?>
              <tr data-group-row-id="<?php echo $value->id;?>">
                <td data-order="<?php echo $value->value;?>">
                  <span class="group_name_plain_text"><?php echo $value->value;?></span>
                  <div class="group_edit hide">
                   <div class="input-group">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-info p7 update-inventory-group" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('inventory','','edit')){ ?><button type="button" class="btn btn-default btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('inventory','','delete')){ ?><a href="<?php echo admin_url('inventory/delete_group/'.$value->id); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
              </tr>
              <?php } }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    </div>
  </div>
</div>
</div>
<?//types of hardware end?>
<?//owner start?>

<div class="modal fade" id="owner" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('owner_name') ?>
        </h4>
      </div>
      <div class="modal-body">

        <?php if(has_permission('inventory','','create')){ ?>
        <div class="input-group">
     <input type="hidden" value="inventoryGroup" id="action">
      <input type="hidden" value="owner" id="inv_group_type">
    
    
          <input type="text" name="name" id="name" class="form-control" placeholder="<?php echo _l('owner_name'); ?>">
          <span class="input-group-btn">
            <button class="btn btn-info p9 btn-inv-action" type="button"><?php echo _l('add_new'); ?></button>
          </span>
        </div>
        <hr />
        <?php } ?>
        <div class="row">
         <div class="container-fluid">
          <table class="table dt-table table-items-groups" data-order-col="0" data-order-type="asc">
            <thead>
              <tr>
                <th><?php echo _l('owner_name'); ?></th>
                <th><?php echo _l('option'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($values as $value){ 
        if($value->group_type == 'owner'){?>
              <tr data-group-row-id="<?php echo $value->id; ?>">
                <td data-order="<?php echo $value->value; ?>">
                  <span class="group_name_plain_text"><?php echo $value->value; ?></span>
                  <div class="group_edit hide">
                   <div class="input-group">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-info p7 update-inventory-group" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('inventory','','edit')){ ?><button type="button" class="btn btn-default btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('inventory','','delete')){ ?><a href="<?php echo admin_url('inventory/delete_group/'.$value->id); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
              </tr>
              <?php } } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    </div>
  </div>
</div>
</div>
<?// owner end?>
<?//status start?>
<div class="modal fade" id="status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('status_name') ?>
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('inventory','','create')){ ?>
        <div class="input-group">
      <input type="hidden" value="inventoryGroup" id="action">
      <input type="hidden" value="status" id="inv_group_type">
      
          <input type="text" name="name" id="name" class="form-control" placeholder="<?php echo _l('status_name'); ?>">
          <span class="input-group-btn">
            <button class="btn btn-info p9 btn-inv-action" type="button"><?php echo _l('add_new'); ?></button>
          </span>
        </div>
        <hr />
        <?php } ?>
    <div class="row">
         <div class="container-fluid">
          <table class="table dt-table table-items-groups" data-order-col="0" data-order-type="asc">
            <thead>
              <tr>
                <th><?php echo _l('status_name'); ?></th>
                <th><?php echo _l('option'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($values as $value){ 
        if($value->group_type == 'status'){?>
              <tr data-group-row-id="<?php echo $value->id; ?>">
                <td data-order="<?php echo $value->value; ?>">
                  <span class="group_name_plain_text"><?php echo $value->value; ?></span>
                  <div class="group_edit hide">
                   <div class="input-group">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-info p7 update-inventory-group" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('inventory','','edit')){ ?><button type="button" class="btn btn-default btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('inventory','','delete')){ ?><a href="<?php echo admin_url('inventory/delete_group/'.$value->id); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
              </tr>
              <?php } }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    </div>
  </div>
</div>
</div>
<?// status end?>
<?//origin start?>
<div class="modal fade" id="origin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('origin_name') ?>
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('inventory','','create')){ ?>
        <div class="input-group">
       <input type="hidden" value="inventoryGroup" id="action">
      <input type="hidden" value="origin" id="inv_group_type">
      
    <input type="text" name="name" id="name" class="form-control" placeholder="<?php echo _l('origin_name'); ?>">
          <span class="input-group-btn">
            <button class="btn btn-info p9 btn-inv-action" type="button"><?php echo _l('add_new'); ?></button>
          </span>
        </div>
        <hr />
        <?php } ?>
    <div class="row">
         <div class="container-fluid">
          <table class="table dt-table table-items-groups" data-order-col="0" data-order-type="asc">
            <thead>
              <tr>
                <th><?php echo _l('origin_name'); ?></th>
                <th><?php echo _l('option'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($values as $value){ 
        if($value->group_type == 'origin'){?>
              <tr data-group-row-id="<?php echo $value->id; ?>">
                <td data-order="<?php echo $value->value; ?>">
                  <span class="group_name_plain_text"><?php echo $value->value; ?></span>
                  <div class="group_edit hide">
                   <div class="input-group">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-info p7 update-inventory-group" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('inventory','','edit')){ ?><button type="button" class="btn btn-default btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('inventory','','delete')){ ?><a href="<?php echo admin_url('inventory/delete_group/'.$value->id); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
              </tr>
              <?php } }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    </div>
  </div>
</div>
</div>
<?// status end?>
<?//origin start?>
<?php init_tail(); ?> 

<script>
  var values ='';
    //**** umer farooq chattha Start****//
  var validation_input, validation_textarea, validation_select = 0;
  function validate_form(){
    var required_field = '';
    $("input[type=text]").each(function(){
      if($(this).attr('data-fieldto') == 'inventory')
      {
        $(this).next().remove();
        required_field = $(this).attr('data-custom-field-required');
        if($(this).val() == ''){
          if(required_field){
            $(this).parent().addClass('has-error');
            $(this).after('<p id="'+ $(this).attr('id') +'" class="text-danger">This field is required.</p>');
            validation_input = 1;
          }
        }else{
          validation_input = 0;
          $(this).parent().removeClass('has-error');
          $(this).next().remove();
        }     
      }
    });
    
    $(".chk").each(function(key, value){
      var x = 0;
      var req_field_flg = 0;
      $(value).next().find(".errorMessage").remove();
      var chkId = $(value).find('.custom_field_checkbox');
      console.log($(chkId));
      var csChk = $(chkId).attr('data-fieldid');
      $("input[data-fieldid="+csChk+"]").each(function(key, val){
        required_field = $(this).attr('data-custom-field-required');
        if(required_field == 1){
          req_field_flg = 1;
          if($(this).is(':checked')){
            x = x+1;
          }
        }
      });
      if(x < 1 && req_field_flg == 1){
        x = 0;
        required_field = '';
        validation_input = 1;
        $("input[data-fieldid="+csChk+"]").parent().parent().addClass('has-error');
        $("input[data-fieldid="+csChk+"]").parent().parent().after('<p id="'+ $(this).attr('id') +'" class="text-danger errorMessage">This field is required.</p>');
        req_field_flg = 0;
       }else{
        x = 0;
        required_field = '';
        validation_input = 0;
        req_field_flg = '';
        console.log('Hi');
        $("input[data-fieldid="+csChk+"]").parent().parent().removeClass('has-error');
        $("input[data-fieldid="+csChk+"]").parent().siblings().find(".errorMessage").remove();
      }
    });
    
    $("textarea").each(function() {
      if ($(this).attr('data-custom-field-required'))
      {
        $(this).next().remove();
        if($(this).val() == ''){
          $(this).parent().addClass('has-error');
          $(this).after('<p id="'+ $(this).attr('id') +'" class="text-danger">This field is required.</p>');
          validation_textarea = 1;
        }else{
          validation_textarea = 0;
          $(this).parent().removeClass('has-error');
          $(this).next().remove();
        } 
      }
    });
    
    $("select").each(function() {
      if ($(this).attr('data-custom-field-required'))
      {
        $(this).next().remove();
        if($(this).change().val() == ''){
          $(this).parent().parent().addClass('has-error');
          $(this).after('<p id="'+ $(this).attr('id') +'" class="text-danger">This field is required.</p>');
          validation_select = 1;
        }else{
          validation_select = 0;
          $(this).parent().parent().removeClass('has-error');
          $(this).next().remove();
        }
      }
    });
    return false;
  }
  
    function add_validate_form(){
    var required_field = '';
    $("input[type=text]").each(function(){
      if($(this).attr('data-fieldto') == 'inventory')
      {
        required_field = $(this).attr('data-custom-field-required');
        if(required_field){
          $(this).prev().prepend('<small class="req text-danger">* </small>');
        }       
      }
    });
    
    $("input[type=checkbox]").each(function(){
      if($(this).attr('data-fieldto') == 'inventory')
      {
        required_field = $(this).attr('data-custom-field-required');
        if(required_field){
          $(this).parent().prev().prepend('<small class="req text-danger">* </small>');
        }       
      }
    });
    
    $( "select" ).each(function() {
      if ($(this).attr('data-custom-field-required'))
      {
        $(this).parent().prev().prepend('<small class="req text-danger">* </small>');
      }
    });
    
    $( "textarea" ).each(function() {
      if ($(this).attr('data-custom-field-required'))
      {
        $(this).prev().prepend('<small class="req text-danger">* </small>');
      }
    });
    
  }
  
    function add_inventory()
    {
    add_validate_form();
        $("#inventory_id").val(0);
        $("#account123").val('').change();
        //$("select[name='type_of_hardware']").val(dt[0].type_of_hardware).change();
        $("#serial_number").val('');
        $("select[name='type_of_hardware']").val('').change();
        $("input[name='date_in']").val('');
        $("select[name='status']").val('').change();
        $("select[name='origin']").val('').change();
        $("img.image").hide();
        $("img.image").attr('src','');
        $("select[name='equipment_owner']").val('').change();
        $("input[name='manufacturer']").val('');
        $("select[name='warranty_expiration_date']").val('').change();
        $("#description").val('');
        $("input[name='inventory_id']").val('');
        $("input[name='inventory_id']").val('');
        $("input[name='inventory_id']").val('');
    }
    
    function umer(a)
    {
        if(a==10)
        {
            $("#exp_no").val('');
            $("#exp_type").val(1);
            $("div#expiration_fields").show();
        }else
        {
            $("div#expiration_fields").hide();
        }
    }
   
    //***umer farooq chattha*****//
    function edit_inventory(e)
    {
    add_validate_form();
    $(".modal-title .add-title").text('Edit Inventory');
        $.ajax({
            type:"post",
            url:'<?php echo base_url(); ?>admin/inventory/for_model',
            data:{"inventory_id":e},
            dataType:'json',
            success:function(dt){
        console.log(dt);
        setTimeout(function(){
        if(dt.inventory_custom_res.length > 0)
        {
          for(var r=0; r<dt.inventory_custom_res.length; r++)
          {
            var class_chkbox = $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').attr('class');
            var custom_mlt_slc_cls = $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').hasClass('custom-field-multi-select');
            
            if(class_chkbox != "custom_field_checkbox"){
              $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').val(dt.inventory_custom_res[r].value).change();
            }
            
            if(custom_mlt_slc_cls)
            {
              
              split_mltbx_ = dt.inventory_custom_res[r].value.split(",");
                  
              opt_slec_mulit = 0;
              $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').val(dt.inventory_custom_res[r].value).siblings().find('ul.dropdown-menu li').each(function(){
                
                currnt_sp_mlt = $(this).find('span').text();
                
                cstm_slctd_multibx = $(this);
                cstm_slctd_multibx_anc_atr = $(this).find('a');
                cstm_slctd_multibx_parent = $(this).parent().parent().prev().find('span:first');
                cstm_slctd_multibx_parent_btn = $(this).parent().parent().prev();
                  
                $.each(split_mltbx_,function(i){                  
                  if(currnt_sp_mlt == split_mltbx_[i].trim()){
                    
                    values += split_mltbx_[i]+',';
                    cstm_slctd_multibx_anc_atr.attr('aria-selected',true);
                    cstm_slctd_multibx.addClass('selected');
                    if(cstm_slctd_multibx.attr('data-original-index') == '0')
                    {
                      cstm_slctd_multibx.removeClass('selected');
                    }
                    
                    $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']  option:eq('+opt_slec_mulit+')').attr('selected','selected');
                  }                   
                });
                opt_slec_mulit++;
              });
              values = values.slice(0,-1);
              cstm_slctd_multibx_parent.text(values);
              cstm_slctd_multibx_parent_btn.attr('title',values);
            }
                        
            if(class_chkbox == "custom_field_checkbox"){

              split_checkbx_ = dt.inventory_custom_res[r].value.split(",");
              $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').each(function(){
                currnt_bo_chk = $(this).val();
                currnt_bo_chk_id = $(this).attr('id');
                
                $.each(split_checkbx_,function(i){                  
                  if(currnt_bo_chk == split_checkbx_[i].trim()){
                    $('#'+currnt_bo_chk_id).prop('checked',true);
                  }
                });               
              });
            }
          } 
        }       
        
                $("#inventory_id").val(dt.inventory_res.inventory_id);
                $("#account123").val(dt.inventory_res.account).change();
                $("select[name='type_of_hardware']").val(dt.inventory_res.type_of_hardware).change();
                $("input[name='serial_number']").val(dt.inventory_res.serial_number);
                $("select[name='type_of_hardware']").val(dt.inventory_res.type_of_hardware).change();
                $("input[name='date_in']").val(dt.inventory_res.date_in);
                $("select[name='status']").val(dt.inventory_res.status).change();
                $("select[name='origin']").val(dt.inventory_res.origin).change();
                $("img.image").show();
                $("img.image").attr('src',dt.inventory_res.image);
                $("select[name='equipment_owner']").val(dt.inventory_res.equipment_owner).change();
                $("input[name='manufacturer']").val(dt.inventory_res.manufacturer);
                $("select[name='warranty_expiration_date']").val(dt.inventory_res.warranty_expiration_date).change();
                if(dt.inventory_res.warranty_expiration_date==10)
                {
                    $("#exp_no").val(dt.inventory_res.custome_nu);
                    $("#exp_type").val(dt.inventory_res.custome_type);
                    $("div#expiration_fields").show();
                }
                $("#description").val(dt.inventory_res.description);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
        },1000);
            }
        });
    }
    //**** umer farooq chattha End****//
 $(function(){    
    var not_sortable_items;    
    not_sortable_items = [($('.table-invoice-items').find('th').length - 1)];    
    not_sortable_items = [($('.table-sale-inventory-items').find('th').length - 1)];    
    initDataTable('.table-sale-inventory-items', admin_url+'inventory/table', not_sortable_items, not_sortable_items,'undefined',[0,'ASC']);    
    if(get_url_param('groups_modal'))
    {       
      // Set time out user to see the message       
      setTimeout(function(){         
      $('#groups').modal('show');       
      },1000);    
    }

     $('.btn-inv-action').on('click',function(){
      var action = $(this).parent().parent().find('#action').val();
      var inv_group_type = $(this).parent().parent().find('#inv_group_type').val();
    var name = $(this).parent().parent().find('#name').val();
      if(name != ''){
          $.post(admin_url+'inventory/'+action,{name:name,inv_group_type:inv_group_type}).done(function(){
      window.location.href = admin_url+'inventory?inventory_modal=true';
         });
      }
    });


    $('body').on('click','.edit-item-group',function(){
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
    });

    $('body').on('click','.update-inventory-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'inventory/update_group/'+group_id,{name:name}).done(function(){
         window.location.href = admin_url+'inventory';
       });
      }
    });
  });  
</script>
</body>
</html>
