<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="clearfix"></div>

				<table class="table dt-table">
				   <thead>
					   <tr>
						 <th><?php echo _l('client_firstname'); ?></th>
						 <th><?php echo _l('client_lastname'); ?></th>
						 <th><?php echo _l('client_email'); ?></th>
						 <th><?php echo _l('clients_list_company'); ?></th>
						 <th><?php echo _l('client_phonenumber'); ?></th>
						 <th><?php echo _l('contact_position'); ?></th>
						 <th><?php echo _l('clients_list_last_login'); ?></th>
						 <th><?php echo _l('contact_active'); ?></th>
						 <?php if(getAdmin() || has_permission('inventory_items','','delete') || has_permission('inventory_items','','edit')){ ?>
						 <th><?php echo _l('options'); ?></th>
						 <?php } ?>
					  </tr>
				   </thead>
				   <tbody>
					  <?php foreach($client_contacts as $c_admin){ ?>
					  <tr>
						  <td><?php echo '<img src="'.contact_profile_image_url($c_admin['id']).'" class="client-profile-image-small mright5"><a href="#" onclick="contact('.$c_admin['userid'].','.$c_admin['id'].');return false;">'.$c_admin['firstname'].'</a>'; ?></td>
						  <td><?php echo $c_admin['lastname']; ?></td>
						  <td><?php echo '<a href="mailto:'.$c_admin['email'].'">'.$c_admin['email'].'</a>'; ?></td>
						  <td><?php
							$companies = getCompanies($c_admin['email']);
							$comp = '';
							foreach($companies as $company){
								if($company['company'] != ''){
									$comp .= '<a href="'.admin_url('clients/client/'.$company['userid']).'">'.$company['company'].'</a>, ';
								}
							}
							echo rtrim($comp,", ");
						  ?></td>
						  <td>
							<?php echo '<a href="tel:'.$c_admin['phonenumber'].'">'.$c_admin['phonenumber'].'</a>'; ?>
						  </td>
						  <td><?php echo $c_admin['title']; ?></td>
						  <!--<td><?php echo date('m/d/Y', strtotime($c_admin['last_login'])); ?></td>-->
						  <td><?php echo (!empty($c_admin['last_login']) ? '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($c_admin['last_login']).'">' . time_ago($c_admin['last_login']) . '</span>' : ''); ?></td>
						  <!--<td><?php echo $c_admin['active']; ?></td>-->
						  <td><?php echo '<div class="onoffswitch">
								<input type="checkbox" data-switch-url="'.admin_url().'clients/change_contact_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_'.$c_admin['id'].'" data-id="'.$c_admin['id'].'"' . ($c_admin['active'] == 1 ? ' checked': '') . '>
								<label class="onoffswitch-label" for="c_'.$c_admin['id'].'"></label>
							</div>'; ?>
						   </td>
						 <?php if(getAdmin() || has_permission('customers','','edit')){ ?>
						 <td>
							<?php if (getAdmin() || has_permission('customers', '', 'edit')) { ?>
									 <a href="" class="btn btn-default btn-icon" onclick='contact(<?php echo $c_admin['userid'].','.$c_admin['id'];?>);return false;' ><i class="fa fa-pencil-square-o"></i></a>
							<?php } 
							if (getAdmin() || has_permission('customers', '', 'delete')) { 
								if ($c_admin['is_primary'] == 0 || ($c_admin['is_primary'] == 1 && $c_admin['total_contacts'] == 1)) {
							?>
							 <a href="<?php echo base_url().'admin/clients/delete_contact/'.$c_admin['userid'].'/'.$c_admin['id'] ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
								<?php }} ?>
						 </td>
						 <?php } ?>
					  </tr>
					  <?php } ?>
				   </tbody>
				</table>
		  </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/clients/client_js'); ?>
<div id="contact_data"></div>

</body>
</html>
