<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                       <div class="clearfix"></div>
                       <div class="clearfix"></div>                    
                        <?php 
                            render_datatable(array('#',
                            _l('Company'),
                            _l('portfolio'),
                            _l('Primary contact'),
                            _l('Primary email'),
                            _l('options'),
                            ),'hidden_items');  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="hidden_items" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="text-align-last: center">Hidden Items</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script>
$(function(){
    initDataTable('.table-hidden_items', window.location.href, [1], [1]);
});

$(document).on("click",".hid", function (e) {
    
    var id = $(this).attr('value');
    $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>admin/hidden_items/hidden_items_popup',
            data:{id : id},
            success:function(data){
                    $('.modal-body').html(data);
            }
        });
    

});
</script>



