<?php //echo "<pre>"; print_r($proposalsresult); exit;?>
        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                                <ol class="breadcrumb hide-phone p-0 m-0">
                                    <li class="breadcrumb-item"><a href="<?php base_url();?>clients/profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
                                    <li class="breadcrumb-item active">Proposal</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Proposals</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

                <div class="row">
                    <div class="col-12">
                        <div class="card-box">
                            <h4 class="header-title">Manage Proposals</h4>

                            


                            <table class="table table-hover m-0 tickets-list table-actions-bar dt-responsive nowrap" cellspacing="0" width="100%" id="datatable">
                                <thead>
										<tr>
											<th><?php echo _l('proposal') . ' #'; ?></th>
											<th><?php echo _l('proposal_subject'); ?></th>
											<th><?php echo _l('proposal_total'); ?></th>
											<th><?php echo _l('proposal_date'); echo " " ; ?> Sent</th>
											<th><?php echo _l('proposal_open_till'); ?></th>
											<th><?php echo _l('proposal_status'); ?></th>
											<?php
											$custom_fields = get_custom_fields('proposal',array('show_on_client_portal'=>1));
											foreach($custom_fields as $field){ ?>
											<th><?php echo $field['name']; ?></th>
											<?php } ?>
											<th class="hidden-sm">Actions</th>
										  </tr>
									   <!-- <tr>
											<th>Proposal Number</th>
											<th>Subject</th>
											<th>Total</th>
											<th>Date Sent</th>
											<th>Open Till</th>
											<th>Status</th>
											<th class="hidden-sm">Actions</th>
										</tr>-->  
                              </thead>
								            <tbody>
								<?php foreach($proposalsresult as $result){ ?>
								  <?php foreach($result as $proposal){ ?>
								  <tr>
									<td>
									  <a href="<?php echo site_url('viewproposal/'.$proposal['id'].'/'.$proposal['hash']); ?>" style="color: #333333 !important;"><b><?php echo format_proposal_number($proposal['id']); ?></b></a>
									  <td>
										<a  href="<?php echo site_url('viewproposal/'.$proposal['id'].'/'.$proposal['hash']); ?> " style="color: #333333 !important;"><b><?php echo $proposal['subject']; ?></b></a>
										<?php
										if ($proposal['invoice_id'] != NULL) {
										  $invoice = $this->invoices_model->get($proposal['invoice_id']);
										  echo '<br /><a href="' . site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash) . '" target="_blank">' . format_invoice_number($invoice->id) . '</a>';
										} else if ($proposal['estimate_id'] != NULL) {
										  $estimate = $this->estimates_model->get($proposal['estimate_id']);
										  echo '<br /><a href="' . site_url('viewestimate/' . $estimate->id . '/' . $estimate->hash) . '" target="_blank">' . format_estimate_number($estimate->id) . '</a>';
										}
										?>
									  </td>
									  <td data-order="<?php echo $proposal['total']; ?>">
										<?php
										if ($proposal['currency'] != 0) {
										 echo format_money($proposal['total'], $this->currencies_model->get_currency_symbol($proposal['currency']));
									   } else {
										 echo format_money($proposal['total'], $this->currencies_model->get_base_currency($proposal['currency'])->symbol);
									   }
									   ?>
									 </td>
									 <td data-order="<?php echo $proposal['date']; ?>"><?php echo _d($proposal['date']); ?></td>
									 <td data-order="<?php echo $proposal['open_till']; ?>"><?php echo _d($proposal['open_till']); ?></td>
									 <td>   
                                        <?php $decline = format_proposal_status($proposal['status']); 
											if($proposal['status'] == 2){ ?> 
												<span class="badge badge-danger"><?php echo format_proposal_status($proposal['status']); ?></span>
										<?php } else{ ?> 
											<span class="badge badge-success"><?php echo format_proposal_status($proposal['status']); ?></span>
										<?php ;}?>
                                    </td>
									 <?php foreach($custom_fields as $field){ ?>
									 <td><?php echo get_custom_field_value($proposal['id'],$field['id'],'proposal'); ?></td>
									 <?php } ?>
										<td>
											<a href="<?php echo site_url('viewproposal/'.$proposal['id'].'/'.$proposal['hash']); ?>" ><i class="mdi mdi-eye mr-2 text-muted font-18 vertical-middle"></i>View</a>
										</td>
								   </tr>
								<?php } }?>
								 </tbody>	
                                
                            </table>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
		