<div class="modal fade" id="customer_service_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('customer_service_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('customer_service_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/service',array('id'=>'customer-group-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','customer_service_name'); ?>
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load',function(){
       _validate_form($('#customer-group-modal'), {
        name: 'required'
    }, manage_customer_groups);

       $('#customer_service_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#customer_service_modal .add-title').removeClass('hide');
        $('#customer_service_modal .edit-title').addClass('hide');
        $('#customer_service_modal input[name="id"]').val('');
        $('#customer_service_modal input[name="name"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#customer_service_modal input[name="id"]').val(group_id);
            $('#customer_service_modal .add-title').addClass('hide');
            $('#customer_service_modal .edit-title').removeClass('hide');
            $('#customer_service_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
   });
    function manage_customer_groups(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            console.log(response);
            response = JSON.parse(response);
            if (response.success == true) {
                // console.log(response);
                if($.fn.DataTable.isDataTable('.table-customer-groups')){
                    $('.table-customer-groups').DataTable().ajax.reload();
                }
                if($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
                    var service = $('select[name="services_in[]"]');
                    service.prepend('<option value="'+response.id+'">'+response.name+'</option>');
                    service.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#customer_service_modal').modal('hide');
        });
        return false;
    }

</script>
