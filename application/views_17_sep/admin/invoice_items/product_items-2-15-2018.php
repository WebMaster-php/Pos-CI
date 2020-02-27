<div class="modal fade" id="sales_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('invoice_item_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('invoice_item_add_headings'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/invoice_items/add_new_products', array('id' => 'invoice_item_form')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                        <?php echo render_input('description', 'invoice_item_add_edit_descriptions'); ?>
                        <?php echo render_textarea('long_description', 'invoice_item_long_description'); ?>
                        <!--                        <div class="form-group">-->
                        <!--                        <label for="rate" class="control-label">-->
                        <!--                            -->
                        <?php //echo _l('invoice_item_add_edit_rate_currency',$base_currency->name . ' <small>('._l('base_currency_string').')</small>'); ?><!--</label>-->
                        <!--                            <input type="number" id="rate" name="rate" class="form-control" value="">-->
                        <!--                        </div>-->
                        <?php
                        //  foreach($currencies as $currency){
                        //     if($currency['isdefault'] == 0 && total_rows('tblclients',array('default_currency'=>$currency['id'])) > 0){ ?>
                        <!--                                <div class="form-group">-->
                        <!--                                    <label for="rate_currency_-->
                        <?php //echo $currency['id']; ?><!--" class="control-label">-->
                        <!--                                        -->
                        <?php //echo _l('invoice_item_add_edit_rate_currency',$currency['name']); ?><!--</label>-->
                        <!--                                        <input type="number" id="rate_currency_-->
                        <?php //echo $currency['id']; ?><!--" name="rate_currency_-->
                        <?php //echo $currency['id']; ?><!--" class="form-control" value="">-->
                        <!--                                    </div>-->
                        <?php //  }
                        //}
                        ?>
                        <?php

                        ?>

                        <!-----start--->
                        <button type="button" class="btn btn-info" id="task_submit_data_add">add data</button>
                        <input type="text" name="task" id="task"><br>
                        <!-----end--->
                        <label class="control-label" for="tax"><?php echo _l('task'); ?></label>
                        <div class="main-but">
                            <div class="form-group one_element">
                                <?php //echo "<pre>"; print_r($tasks);?>
                                <select class="selectpicker display-block task_select" style="width:50%" name="tasks[]"
                                        data-none-selected-text="<?php echo _l('select'); ?>">
                                    <option value=""></option>
                                    <?php
                                    foreach ($tasks as $task) { ?>
                                        <option value="<?php echo $task['id']; ?>"
                                                data-subtext="<?php echo $task['name']; ?>"></option>
                                    <?php } ?>
                                </select>
                                <button type="button" style="height: 36px;" class="fa fa-plus apend_task"></button>
                            </div>

                        </div>

                        <!--<div class="col-md-6">
                         <div class="form-group">
                            <label class="control-label" for="tax2"><?php //echo _l('tax_2'); ?></label>
                            <select class="selectpicker display-block" disabled data-width="100%" name="tax2" data-none-selected-text="<?php //echo _l('no_tax'); ?>">
                                <option value=""></option>
                                <?php //foreach($taxes as $tax){ ?>
                                <option value="<?php //echo $tax['id']; ?>" data-subtext="<?php //echo $tax['name']; ?>"><?php //echo $tax['taxrate']; ?>%</option>
                                <?php //} ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="clearfix mbot15"></div>
                <?php //echo render_input('unit','unit'); ?>
                <div id="custom_fields_items">
                    <?php //echo render_custom_fields('items'); ?>
                </div>-->
                        <?php //echo render_select('group_id',$items_groups,array('id','name'),'item_group'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php //print_r($tasks);exit; ?>
<script>
    var allTasksNames = [];
    var allTasksIds = [];
    <?php
    foreach ($tasks as $task)
    {
    ?>
    allTasksNames.push("<?php echo $task['name'];?>");
    allTasksIds.push(<?php echo $task['id'];?>);
    <?php
    }
    ?>
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if (typeof(jQuery) != 'undefined') {
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
                location.reload();
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
            $itemModal.find('.add-title').removeClass('hide');
            $itemModal.find('.edit-title').addClass('hide');

            var id = $(event.relatedTarget).data('id');

            // If id found get the text from the datatable
            if (typeof (id) !== 'undefined') {

                $('.affect-warning').removeClass('hide');
                $('input[name="itemid"]').val(id);

                requestGetJSON('invoice_items/get_product_by_id/' + id).done(function (response) {
                    $itemModal.find('input[name="description"]').val(response.description);
                    $itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));

                    if (response.productTasks.length) {
                        $('.main-but').empty();
                    }

                    for (K = 0; K < response.productTasks.length; K++) {
                        console.log("response.productTasks");
                        console.log(response.productTasks);

                        var appendedHtml = '<div class="form-group one_element">';
                        var appendedHtml = appendedHtml + '<select class="selectpicker display-block task_select" style="width:50%" name="tasks[]" data-none-selected-text="Select Multiple Tasks"><option value=""></option>';

                        var selectedValue = '';
                        for (i = 0; i < allTasksNames.length; i++) {
                            if (parseInt(response.productTasks[K].task_id) == parseInt(allTasksIds[i])) {
                                console.log("matched");
                                console.log(parseInt(response.productTasks[K].task_id));
                                selectedValue = parseInt(response.productTasks[K].task_id);
                            }

                            appendedHtml = appendedHtml + ' <option value="' + allTasksIds[i] + '" data-subtext="' + allTasksNames[i] + '" >' + allTasksNames[i] + '</option>';
                        }
                        appendedHtml = appendedHtml + '</select><button type="button" style="height: 36px;" class="apend_task fa fa-plus"></button>';
                        appendedHtml = appendedHtml + '<button type="button" style="height: 36px;" onclick="removeTask(this)" class="fa fa-remove"></button></div>';

                        $(".main-but").append(appendedHtml);
                        $itemModal.find('select').last().selectpicker('val', selectedValue);
                    }


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

        ////////////////////////////////////////////////


        $('#task_submit_data_add').on('click', function (e) {
            // alert();
            e.preventDefault();
            var task = $('#task').val();
            // alert(task);

            $.ajax(
                {
                    url: '<?php echo base_url(); ?>admin/projects/insert_task_project',
                    data: {task: task},
                    type: 'POST',
                    success: function (data) {
                       // var myObj = JSON.parse(data);
                        $('#task').val('');
                        alert("Task added successfully!");
                        //console.log();
                        //$('.task_select').append('<option value="'+myObj[0].id+'" selected="selected">'+myObj[0].task_name+'</option>');
                    }
                });
        });


/////////////////////////////////////////////////////
        $("body").on("hidden.bs.modal", '#sales_item_modal', function (event) {
            //$('#item_select').find('select').selectpicker('val', '').selectpicker('destroy');
            $('.selectpicker').selectpicker('destroy');
            $('.main-but').find('.one_element').first().nextAll().remove();
        });

        $('body').on('click', '.apend_task', function () {

            var $itemModal = $('#sales_item_modal');
            var appendedHtml = '<div class="form-group one_element">';
            var appendedHtml = appendedHtml + '<select class="selectpicker display-block task_select" style="width:50%" name="tasks[]" data-none-selected-text="Select Multiple Tasks"><option value=""></option>';
            for (i = 0; i < allTasksNames.length; i++) {
                appendedHtml = appendedHtml + ' <option value="' + allTasksIds[i] + '" data-subtext="' + allTasksNames[i] + '">' + allTasksNames[i] + '</option>';
            }
            appendedHtml = appendedHtml + '</select><button type="button" style="height: 36px;" class="apend_task fa fa-plus"></button>';
            appendedHtml = appendedHtml + '<button type="button" style="height: 36px;" onclick="removeTask(this)" class="fa fa-remove"></button></div>';

            //var select_boxes = $( ".apend_task" ).children().length;

            $(".main-but").append(appendedHtml);
            //$( ".apend_task" ).children('.form-group').last().find('.task_select').attr('name','task'+(select_boxes+1))
            $itemModal.find('select').last().selectpicker('val', '').selectpicker('refresh');
        });
        validate_item_form();
    }
    function removeTask(anchor){
        anchor.closest("div").remove();
    }
    function validate_item_form() {
        // Set validation for invoice item form
        _validate_form($('#invoice_item_form'), {
            description: 'required',
            rate: {
                required: true,
            }
        }, manage_invoice_items);
    }
</script>
