<h4 class="customer-profile-group-heading"><?php echo _l('saleopp'); ?></h4>
<div class="row">
   <?php
      $_where = '';
      if(!has_permission('sales','','view')){
       $_where = 'id IN (SELECT project_id FROM tblsalemembers WHERE staff_id='.get_staff_user_id().')';
      }
      ?>
   <?php //foreach($saleopp_statuses as $status){ ?>
   <!--<div class="col-md-5ths total-column">
      <div class="panel_s">
         <div class="panel-body">
            <h3 class="text-muted _total">
               <?php //$where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id']. ' AND clientid='.$client->userid; ?>
               <?php //echo total_rows('tblsales',$where); ?>
            </h3>
            <span style="color:<?php //echo $status['color']; ?>"><?php //echo $status['name']; ?></span>
         </div>
      </div>
   </div>-->
   <?php// } ?>
</div>
<?php if(isset($client)){ ?>
<?php if(has_permission('sales','','create')){ ?>
<!--<a href="<?php //echo admin_url('sales/project?customer_id='.$client->userid); ?>" class="btn btn-info mbot25<?php //if($client->active == 0){//echo ' disabled';} ?>"><?php //echo _l('new_project'); ?></a>-->
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client->userid?>">
<a href="#" onclick="init_lead_customer(); return false;" class="btn btn-info mbot25">Sales Opportunity</a>
<?php }
    $table_data = array();
                              $_table_data = array(
                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                '#',
                                _l('lead_company'),
                                //_l('lead_company'),
                                _l('leads_dt_email'),
                                _l('leads_dt_phonenumber'),
                                _l('tags'),
                                _l('leads_dt_assigned'),
                                _l('Followers'),
                                _l('leads_dt_status'),
                                _l('leads_source'),
                                _l('leads_dt_last_contact'),
                                array(
                                 'name'=>_l('leads_dt_datecreated'),
                                 'th_attrs'=>array('class'=>'date-created')
                                 ));
                              foreach($_table_data as $_t){
                               array_push($table_data,$_t);
                              }
                              // $custom_fields = get_custom_fields('leads',array('show_on_table'=>1));
                              // foreach($custom_fields as $field){
                              //  array_push($table_data,$field['name']);
                              // }
                              $table_data = do_action('leads_table_columns',$table_data);
                              $_op = _l('options');
                              array_push($table_data,$_op);

   render_datatable($table_data,'customerleads');
   }
   ?>
