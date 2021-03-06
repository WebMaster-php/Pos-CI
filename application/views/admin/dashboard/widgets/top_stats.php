<?php
  /**
  * Author: Ali hamza
  * portfolio changes starting here
  */
  
  $CI =& get_instance();
  $where_temp='';
  if(!null==$CI->session->userdata("portfolio_id")){
      $where_temp .= ' portfolio_id='.$CI->session->userdata("portfolio_id");
  }
  /** changes end here */
?>
<div class="widget relative" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('quick_stats'); ?>">
      <div class="widget-dragger"></div>
      <div class="row">
      <?php
         $initial_column = 'col-lg-3';
         if(!is_staff_member() && (!has_permission('invoices','','view') && !has_permission('invoices','','view_own'))) {
            $initial_column = 'col-lg-6';
         } else if(!is_staff_member() || (!has_permission('invoices','','view') && !has_permission('invoices','','view_own'))) {
            $initial_column = 'col-lg-4';
         }
      ?>
         <?php if(has_permission('invoices','','view') || has_permission('invoices','','view_own')){ ?>
         <div class="col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  // adding $where_temp for portfolio check
                  $total_invoices = total_rows('tblinvoices',$where_temp.' AND status NOT IN (5,6)'.(!has_permission('invoices','','view') ? ' AND addedfrom='.get_staff_user_id() : ''));
                  // adding $where_temp for portfolio check
                  $total_invoices_awaiting_payment = total_rows('tblinvoices',$where_temp.' AND status NOT IN (2,5,6)'.(!has_permission('invoices','','view') ? ' AND addedfrom='.get_staff_user_id() : ''));
                  
                  $percent_total_invoices_awaiting_payment = ($total_invoices > 0 ? number_format(($total_invoices_awaiting_payment * 100) / $total_invoices,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo _l('invoices_awaiting_payment'); ?>
                  <span class="pull-right"><?php echo $total_invoices_awaiting_payment; ?> / <?php echo $total_invoices; ?></span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_total_invoices_awaiting_payment; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_total_invoices_awaiting_payment; ?>">
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         <?php if(is_staff_member()){ ?>         
         <div class="col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $where = '';
                  if(!is_admin()){
                    $where .= '(addedfrom = '.get_staff_user_id().' OR assigned = '.get_staff_user_id().')';
                  }
                  // Junk leads are excluded from total
                  /** Ali hamza 
                   *  portfolio changes 
                   */
                  $total_leads = total_rows('tblleads',($where == '' ? 'junk=0 AND '.$where_temp : $where .= ' AND junk =0 AND '.$where_temp));
                  if($where == ''){
                   $where .= 'status=1';
                  } else {
                   $where .= ' AND status =1';
                  }
                  $total_leads_converted = total_rows('tblleads',$where.' AND '.$where_temp);
                  /** changes end here */
                  $percent_total_leads_converted = ($total_leads > 0 ? number_format(($total_leads_converted * 100) / $total_leads,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-tty"></i> <?php echo _l('leads_converted_to_client'); ?>
                  <span class="pull-right"><?php echo $total_leads_converted; ?> / <?php echo $total_leads; ?></span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_total_leads_converted; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_total_leads_converted; ?>">
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         <div class="col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  $project_status = get_project_status_by_id(2);
                  if(!has_permission('projects','','view')){
                    $_where = 'id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id='.get_staff_user_id().')';
                  }
                  /** Ali hamza 
                   *  portfolio changes 
                   */
                  $totalProjectsWhere = '';
                  if($_where == '') // if default where is empty
                  { 
                    $totalProjectsWhere = $where_temp;
                  }
                  else // else add AND clause
                  { 
                    $totalProjectsWhere = $_where.' AND '.$where_temp;
                  }
                  $total_projects = total_rows('tblprojects',$totalProjectsWhere);
                  $where = ($_where == '' ? '' : $_where.' AND ').'status = 2';
                  
                  $totalProjectsInProgressWhere = '';
                  if($where == '') // if default where is empty
                  {
                    $totalProjectsInProgressWhere = $where_temp;
                  }
                  else // else add AND clause
                  {
                    $totalProjectsInProgressWhere = $where.' AND '.$where_temp;
                  }

                  $total_projects_in_progress = total_rows('tblprojects',$totalProjectsInProgressWhere);
                  /** changes end here */                  
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('projects') . ' ' . $project_status['name']; ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar no-percent-text not-dynamic" style="background:<?php echo $project_status['color']; ?>" role="progressbar" aria-valuenow="<?php echo $percent_in_progress_projects; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_in_progress_projects; ?>">
                  </div>
               </div>
            </div>
         </div>
         <div class="col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  if (!has_permission('tasks', '', 'view')) {
                    $_where = 'tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ')';
                  }

                  /** Ali hamza 
                   *  portfolio changes 
                   */
                  $totalTasksWhere = '';
                  if($_where == '') // if default where is empty
                  { 
                    $totalTasksWhere = $where_temp;
                  }
                  else // else add AND clause
                  { 
                    $totalTasksWhere = $_where.' AND '.$where_temp;
                  }
                  $total_tasks = total_rows('tblstafftasks',$totalTasksWhere);
                  $where = ($_where == '' ? '' : $_where.' AND ').'status != 5';
                  
                  $totalTasksInProgressWhere = '';
                  if($where == '') // if default where is empty
                  {
                    $totalTasksInProgressWhere = $where_temp;
                  }
                  else // else add AND clause
                  {
                    $totalTasksInProgressWhere = $where.' AND '.$where_temp;
                  }

                  $total_not_finished_tasks = total_rows('tblstafftasks',$totalTasksInProgressWhere);
                  /** changes end here */

                  $percent_not_finished_tasks = ($total_tasks > 0 ? number_format(($total_not_finished_tasks * 100) / $total_tasks,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-tasks"></i> <?php echo _l('tasks_not_finished'); ?> <span class="pull-right">
                  <?php echo $total_not_finished_tasks; ?> / <?php echo $total_tasks; ?>
                  </span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_not_finished_tasks; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_finished_tasks; ?>">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
