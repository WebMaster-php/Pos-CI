<style>
tickets-list a {
    color: #333333 !important;
    white-space: nowrap !important;
    text-shadow: none !important;
}
</style>
<table class="table table-hover m-0 tickets-list table-actions-bar dt-responsive nowrap" cellspacing="0" width="100%" id="datatable">
                                <thead>
									  <th width="15%">Ticket Number </th>
									  <th width="30%"><?php echo _l('clients_tickets_dt_subject'); ?></th>
									  <th><?php echo _l('clients_tickets_dt_status'); ?></th>
										  <?php
										  $custom_fields = get_custom_fields('tickets',array('show_on_client_portal'=>1));
										  foreach($custom_fields as $field){ ?>
										  <th><?php echo $field['name']; ?></th>
										  <?php } ?>
									  <th>Opened On</th>
									  <th>Last Action</th>
									  <!--<th>Action</th>-->
								</thead>

                                <tbody id="tickesbystatus">
									<?php foreach($tickets as $ticket){ ?>
									  <tr class="<?php if($ticket['clientread'] == 0){echo 'text-danger';} ?>">
										<td data-order="<?php echo $ticket['ticketid']; ?>">
											<span style="color: #333333 !important; cursor:pointer;" > <b><a onclick="show_ticket_status(<?php echo $ticket['ticketid']; ?>); return false;" >#<?php echo $ticket['ticketid']; ?></a></b> </span>
											<!--<a href="#" style="color: #333333 !important;"><b>#<?php// echo $ticket['ticketid']; ?></b></a>-->	
											</td>
											<td><span style="color: #333333 !important; cursor:pointer;"><b><a onclick="show_ticket_status(<?php echo $ticket['ticketid']; ?>); return false;" ><?php echo $ticket['subject']; ?></a></b></span></td>
										 <td>
										   <span class="badge badge-success" style= "background-color:<?php echo $ticket['statuscolor'];?> ">
												<?php echo ticket_status_translate($ticket['ticketstatusid']); ?>
											</span>
										  </td>
										  <td>
											 <?php echo date('m-d-Y', strtotime($ticket['date'])); ?>
										  </td>
										  <td>
											<?php
												if ($ticket['lastreply'] == NULL) {
												 echo _l('client_no_reply');
											   } else {
												 echo _dt($ticket['lastreply']);
											   }
											   ?>
										  </td>
											<!--<td data-order="<?php //echo $ticket['lastreply']; ?>">
												<div class="btn-group dropdown">
													<!-- <a href="javascript: void(0);"   class="table-action-btn dropdown-toggle arrow-none btn btn-light btn-sm" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>	-->
													<!--<a href="javascript: void(0);"   class="table-action-btn dropdown-toggle arrow-none btn btn-light btn-sm" data-toggle="" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item" href="<?php ///echo site_url('clients/ticket/'.$ticket['ticketid']); ?>">
															<i class="mdi mdi-send mr-2 text-muted font-18 vertical-middle"></i>
															Reply
														</a>
													</div>
												</div>
											</td>-->											 
									   </tr>
									   <?php } ?>
                                </tbody>
                            </table>
<!-- Modal -->
<div class="modal fade" id="ticket_history_status_<?php //echo $ticket['ticketid']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog modal-lg" role="document">

<div class="modal-content">
<div id="edit-ticket-message-additional"></div>
<!-- <div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h3><?php //echo _l('Ticket #').$ticket['ticketid'].' '.'|'.' '.$ticket['subject']?></h3>
<h4 class="modal-title" id="myModalLabel"></h4>
</div> -->
<div class="modal-body" id="show_history_data">


</div>

<!-- <div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

</div> -->
</div>

</div>
</div>