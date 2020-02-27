<div class="grab_sale_modla">
<div class="modal fade" id="sales_inventory_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" id="error_by1" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"> <span class="add-title"><?php echo _l('inventory_add'); ?></span> </h4>
      </div>
      <div class="modal-body">
        <div class="row">
        <form enctype="multipart/form-data" id="formPost" method="post">
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
              <div class="form-group"> <?php echo render_input('serial_number','Serial Number'); ?> </div>
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
          <div class = "col-md-12">
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
          <div class = "col-md-12" style="margin-bottom: 4px; margin-top: 14px;">
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
          <div class = "col-md-12" style="margin-bottom: 4px;">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tax"><?php echo _l('inventory_add_warrantyexpirationdate'); ?></label>
                <select onchange="umer(this.value)" class="selectpicker display-block" name="warranty_expiration_date" >
                  <option value="">Select One</option>
				  <option value="0">No Warranty</option>
                  <option value="1">1 Year</option>
                  <option value="3">3 Year</option>
                  <option value="5">5 Year</option>
                  <option value="10">Custom</option>
                </select>
              </div>
            </div>
          </div>
          <div style="margin-bottom: 4px; display:none;" class="col-md-12" id="expiration_fields">
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
          </div>
          <div class="col-md-12">
            <div class="alert alert-warning affect-warning hide"> <?php echo _l('changing_items_affect_warning'); ?> </div>
            <div class="form-group" app-field-wrapper="file" style="margin-top: 15px;"> <img src="" class="image img-responsive img-rounded" height="150" width="150" style="display:none;" >
              <input id="image"  class="form-control" placeholder="Upload Image" name="image_name" type="file" />
              <input type="hidden" name="img_clone" id="img_clone" value="0"/>
            </div>
            <div> <?php echo render_textarea('description','inventory_add_description'); ?> </div>
          </div>
		  <div class="col-md-12" style="margin-bottom: 15px;">
			<?php echo render_custom_fields('inventory',''); ?>
		  </div>
          <div class="col-md-12">
            <div class="alert alert-warning affect-warning hide" id="alert_msg"> </div>
          </div>
        </form>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="button" onclick="form_submit1('formPost')" id="btnSubmit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
    </div>
  </div>
</div>
</div>
<script>
	var validation_input, validation_textarea, validation_select = 0;
	 function validate_form(){
		var required_field = '';
		$("input").each(function(){
			if($(this).attr('data-fieldto') == 'inventory')
			{
				$(this).next().remove();
				required_field = $(this).attr('data-custom-field-required');
				if($(this).val() == ''){
					$(this).parent().addClass('has-error');
					$(this).after('<p id="'+ $(this).attr('id') +'" class="text-danger">This field is required.</p>');
					validation_input = 1;
				}else{
					validation_input = 0;
					$(this).parent().removeClass('has-error');
					$(this).next().remove();
				}			
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
	
    function form_submit1(e)
    {
		validate_form();
		if(validation_input > 0 || validation_textarea > 0 || validation_select > 0){
			return false;
		}
        var versiEditFormData = new FormData();
        versiEditFormData.append('image_name', $("#image")[0].files[0]);
        versiEditFormData.append('inventory_id', $("#inventory_id").val());
        versiEditFormData.append('account', $("#account123").val());
        versiEditFormData.append('serial_number', $("#serial_number").val());
        versiEditFormData.append('type_of_hardware', $("select[name='type_of_hardware']").val());
        versiEditFormData.append('date_in', $("input[name='date_in']").val());
        versiEditFormData.append('status', $("select[name='status']").val());
        versiEditFormData.append('origin', $("select[name='origin']").val());
        versiEditFormData.append('equipment_owner', $("select[name='equipment_owner']").val());
        versiEditFormData.append('manufacturer', $("input[name='manufacturer']").val());
        versiEditFormData.append('warranty_expiration_date', $("select[name='warranty_expiration_date']").val());

        if($("select[name='warranty_expiration_date']").val()==10)
        {
            versiEditFormData.append('exp_no', $("#exp_no").val());
            versiEditFormData.append('exp_type', $("#exp_type").val());
            $("div#expiration_fields").show();
        }
        versiEditFormData.append('img_clone', $("input[name='img_clone']").val());
        versiEditFormData.append('description', $("#description").val());
        versiEditFormData.append('inventory_id', $("input[name='inventory_id']").val());
        versiEditFormData.append('equipment_owner', $("select[name='equipment_owner']").val());
		$("input[data-fieldid], select[data-fieldid], textarea[data-fieldid], .chk").each(function(key, value){
			var thisObj = $(this);
			if($(this).attr('multiple'))
			{
				versiEditFormData.append('custom_fields[inventory]['+$(this).attr('data-fieldid')+'][]', $("[name='custom_fields[inventory]["+$(this).attr('data-fieldid')+"][]']").val());
			}
			else if($(this).attr('type') != 'checkbox' && $(this).attr('class') != 'form-group chk' )
			{
				versiEditFormData.append('custom_fields[inventory]['+$(this).attr('data-fieldid')+']', $("[name='custom_fields[inventory]["+$(this).attr('data-fieldid')+"]']").val());
			}
			if($(this).attr('class') == 'form-group chk'){
				var ID = $(thisObj).find('input').attr('data-fieldid');
				$(thisObj, '#formPost').each(function(key, value){
					var groupLength = $(value).find('.custom_field_checkbox').length;
					var x = '';
					$(value).find('.custom_field_checkbox').each(function(key, value){
						if($(value).is(':checked')){
							x += $(value).val()+',';
						}
					});
					x = x.slice(0,-1);
					versiEditFormData.append('custom_fields[inventory]['+ID+'][]', x);
				});
			}
		});
        $.ajax({
        url: '<?php echo base_url(); ?>admin/inventory/manage',
        type: 'POST',
        processData: false, // important
        contentType: false, // important
        data: versiEditFormData,
        success:function (data) {
        if(data.toString()=='yes')
        {
            $("div#error_by").hide();
            window.location.href = "<?php  echo base_url(); ?>admin/clients/client/<?php echo $client_id_for_page; ?>?tab=inventory_profile";
        } else {
            document.getElementById('error_by1').scrollIntoView();
            //window.location.hash="div#error_by";
            $("div#error_by").show();
            $("div#error_by").html(data);
        }
        }
        });
    }


/*new ajax = new XMLHttpRequest();
function onchange(){
ajax.onreadystatechange = function(){
ajax.open("POST","add_inventory.php");
ajax.send()	;
}
}*/
// Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
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
</script> 
