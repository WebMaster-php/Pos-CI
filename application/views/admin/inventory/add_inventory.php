<div class="modal fade" id="sales_inventory_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" id="error_by1" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"> <span class="add-title"><?php echo _l('inventory_add'); ?></span> </h4>
      </div>
      <form enctype="multipart/form-data" id="formPost" method="post">
        <div class="modal-body">
        <div class="row">
          <?php // zeeshan's code start?>
          <div class = "col-md-12">
            <div id="error_by" style="display:none;" class="error_by alert alert-danger"></div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="hidden" name="inventory_id" id="inventory_id" value="0"/>
                <!--umer farooq chattha-->
                <label class="control-label" for="tax"><?php echo _l('inventory_add_account'); ?></label>
                <select class="selectpicker display-block" data-live-search="true" data-width="100%" class="ajax-search" id="account123" name="account" data-none-selected-text="<?php echo _l('Please Choose Account'); ?>">
                <option value=""></option>
                <?php foreach($accounts as $account){?>
                <option value="<?php echo $account->userid;?>"><?php echo $account->company;?></option>
                <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group"> <?php echo render_input('serial_number','Serial Number'); ?>
                <div id="custom_fields_items"> </div>
              </div>
            </div>
          </div>
          <div class = "col-md-12">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tax"><?php echo _l('inventory_add_typeofhardware'); ?></label>
                <select class="selectpicker display-block" data-width="100%" name="type_of_hardware">
                  <option value=""></option>
                  <?php foreach($values as $value){ 
if($value->group_type == 'hardware'){?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->value;?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tax"><?php echo _l('inventory_add_datein'); ?></label>
                <div class="input-group date">
                  <input id="duedate" name="date_in" class="form-control datepicker" value="" type="text">
                  <div class="input-group-addon"> <i class="fa fa-calendar calendar-icon"></i> </div>
                </div>
              </div>
            </div>
          </div>
          <div class = "col-md-12" style="margin-bottom: 16px; ">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tax"><?php echo _l('inventory_add_status'); ?></label>
                <select class="selectpicker display-block" data-width="100%" name="status" >
                  <option value=""></option>
                  <?php foreach($values as $value){ 
if($value->group_type == 'status'){?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->value;?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tax"><?php echo _l('inventory_add_origin'); ?></label>
                <select class="selectpicker display-block" data-width="100%" name="origin" data-none-selected-text="<?php echo _l('origin'); ?>">
                  <option value=""></option>
                  <?php foreach($values as $value){ 
if($value->group_type == 'origin'){?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->value;?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>
          </div>
          <div class = "col-md-12" style="margin-bottom: 16px; ">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tax"><?php echo _l('inventory_add_whoownstheequipment'); ?></label>
                <select class="selectpicker display-block" data-width="100%" name="equipment_owner" data-none-selected-text="<?php echo _l("Please Choose"); ?>">
                  <option value=""></option>
                  <?php foreach($values as $value){ 
if($value->group_type == 'owner'){?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->value;?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group"> <?php echo render_input('manufacturer','Manufacturer'); ?>
                <div id="custom_fields_items"> </div>
              </div>
            </div>
          </div>
          <div class = "col-md-12" style="margin-bottom: 6px; ">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tax"><?php echo _l('inventory_add_warrantyexpirationdate'); ?></label>
                <select onchange="umer(this.value)" class="selectpicker display-block form-control" name="warranty_expiration_date" >
                  <option value="">Select One</option>
				  <!-- 31 july start-->
				  <option value="0">No Warranty</option>
				  <!-- 31 july-->	
                  <option value="1">1 Year</option>
                  <option value="3">3 Year</option>
                  <option value="5">5 Year</option>
                  <option value="10">Custom</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-12" style="margin-bottom: 14px; display:none;" id="expiration_fields">
            <div class="col-lg-6">
              <label class="control-label" for="tax">Type Number</label>
              <input type="number" name="exp_no" id="exp_no" class="form-control"/>
            </div>
            <div class="col-lg-6">
              <label class="control-label" for="tax">Select Type</label>
              <select name="exp_type" class="form-control" id="exp_type">
                <option value="1">Year</option>
                <option value="2">Month</option>
                <option value="3">Days</option>
              </select>
            </div>
          </div>
          <div class="col-md-12">
            <div class="col-md-12">
              <label class="control-label" for="tax">Image</label>
              <div class="alert alert-warning affect-warning hide"> <?php echo _l('changing_items_affect_warning'); ?> </div>
              <div class="form-group" app-field-wrapper="file" style="margin-top: 15px;"> <img src="" class="image img-responsive img-rounded" height="150" width="150" style="display:none;" >
                <input id="image"  class="form-control" placeholder="Upload Image" name="image_name" type="file" />
                <input type="hidden" name="img_clone" id="img_clone" value="0"/>
              </div>
              <div> <?php echo render_textarea('description','inventory_add_description'); ?> </div>
            </div>
          </div>
		  
		  <div class="col-md-12" style="margin-bottom: 15px;">
			<div class="col-md-12">
				<?php echo render_custom_fields('inventory',false); ?>
			</div>
		  </div>
		  
		  
          <div class="col-md-12">
            <div class="alert alert-warning affect-warning hide" id="alert_msg"> </div>
          </div> 
		  
		  
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="button" onclick="form_submit(this.form.id)" id="btnSubmit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </form>
    </div>
  </div>
</div>
</div>
</div>
<script>
	
	function form_submit(e)
    {
		var uricont = '<?php echo $this->uri->segment(2);?>';
    var urifunc = '<?php echo $this->uri->segment(3);?>';
    var uriid = '<?php echo $this->uri->segment(4);?>'; 
    		
	$(".errorMessage").remove();	
	validate_form();
	if(validation_input > 0 || validation_textarea > 0 || validation_select > 0){
		return false;
	}
    var form = $('form#formPost');
    var formdata = false;
    $.ajax({type:'post',
    url:'<?php echo base_url(); ?>admin/inventory/manage',
    data:new FormData(form[0]),
    processData:false, 
    cache: false,
    contentType : false,
    success:function (data) {
    if(data.toString()=='yes'){
      // location.reload();
      if(uricont == 'tickets'){
        window.location.href = "<?php  echo base_url(); ?>admin/"+uricont+"/"+urifunc+"/"+uriid;      
      }
      else{
        window.location.href = "<?php  echo base_url(); ?>admin/inventory";    
      }    
    }else{
        document.getElementById('error_by1').scrollIntoView();
        $("div#error_by").show();
        $("div#error_by").html(data);
    }
    }
    });
		
    }

if(typeof(jQuery) != 'undefined'){
init_item_js();
} else {
window.addEventListener('load', function () {
init_item_js();
});
}
// Items add/edit
function manage_invoice_items(form) {
var data = $(form).serialize();

var url = form.action;
$.post(url, data).done(function (response) {
response = JSON.parse(response);
if (response.success == true) {
var item_select = $('#item_select');
if ($("body").find('.accounting-template').length > 0) {
var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
var _option = '<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>';
if (!item_select.hasClass('ajax-search')) {
if (group.length == 0) {
_option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
if (item_select.find('[data-group-id="0"]').length == 0) {
item_select.find('option:first-child').after(_option);
} else {
item_select.find('[data-group-id="0"]').after(_option);
}
} else {
group.prepend(_option);
}
}
if (!item_select.hasClass('ajax-search')) {
item_select.selectpicker('refresh');
} else {

item_select.contents().filter(function () {
return !$(this).is('.newitem') && $(this).is('.newitem-divider');
}).remove();

var clonedItemsAjaxSearchSelect = item_select.clone();
item_select.selectpicker('destroy').remove();
item_select = clonedItemsAjaxSearchSelect;
$("body").find('.items-wrapper').append(clonedItemsAjaxSearchSelect);
init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
}

add_item_to_preview(response.item.itemid);
} else {
// Is general items view
$('.table-invoice-items').DataTable().ajax.reload(null, false);
}
alert_float('success', response.message);
}
$('#sales_item_modal').modal('hide');
}).fail(function (data) {
alert_float('danger', data.responseText);
});
return false;
}
function init_item_js() {
// Add item to preview from the dropdown for invoices estimates
$("body").on('change', 'select[name="item_select"]', function () {
var itemid = $(this).selectpicker('val');
if (itemid != '' && itemid !== 'newitem') {
add_item_to_preview(itemid);
} else if (itemid == 'newitem') {
// New item
$('#sales_item_modal').modal('show');
}
});

// Items modal show action
$("body").on('show.bs.modal', '#sales_item_modal', function (event) {

$('.affect-warning').addClass('hide');

var $itemModal = $('#sales_item_modal');
$('input[name="itemid"]').val('');
$itemModal.find('input').not('input[type="hidden"]').val('');
$itemModal.find('textarea').val('');
$itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
$('select[name="tax2"]').selectpicker('val', '').change();
$('select[name="tax"]').selectpicker('val', '').change();
$itemModal.find('.add-title').removeClass('hide');
$itemModal.find('.edit-title').addClass('hide');

var id = $(event.relatedTarget).data('id');
// If id found get the text from the datatable
if (typeof (id) !== 'undefined') {

$('.affect-warning').removeClass('hide');
$('input[name="itemid"]').val(id);

requestGetJSON('invoice_items/get_item_by_id/' + id).done(function (response) {
$itemModal.find('input[name="description"]').val(response.description);
$itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
$itemModal.find('input[name="rate"]').val(response.rate);
$itemModal.find('input[name="unit"]').val(response.unit);
$('select[name="tax"]').selectpicker('val', response.taxid).change();
$('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
$itemModal.find('#group_id').selectpicker('val', response.group_id);
$.each(response, function (column, value) {
if (column.indexOf('rate_currency_') > -1) {
$itemModal.find('input[name="' + column + '"]').val(value);
}
});

$('#custom_fields_items').html(response.custom_fields_html);

init_selectpicker();
init_color_pickers();
init_datepicker();

$itemModal.find('.add-title').addClass('hide');
$itemModal.find('.edit-title').removeClass('hide');
validate_item_form();
});

}
});

$("body").on("hidden.bs.modal", '#sales_item_modal', function (event) {
$('#item_select').selectpicker('val', '');
});

validate_item_form();
}
function validate_item_form(){
// Set validation for invoice item form
_validate_form($('#invoice_item_form'), {
	description: 'required',
rate: {
required: true,
}
}, manage_invoice_items);
}
// $("body").on("click", "#validate_custom", function (event) {
	// $("form").each(function(){
		// alert($(this).find(':input')); //<-- Should return all input elements in that specific form.
	// });
// });
</script> 
