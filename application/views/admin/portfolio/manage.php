<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			
		<div class = "col-md-12">	
			 <div class="panel_s">
				<div class="panel-body">
					<div class="_buttons">
                            <a href="<?php echo base_url();?>admin/portfolio/portfolio" class="btn btn-info mright5 test pull-left display-block">New Portfolio</a>
							<div class="visible-xs">
								<div class="clearfix"></div>
							</div>          
					</div>
					<div class="clearfix"></div>
					<hr class="hr-panel-heading"> 
					<!-- <table class="table table-bordered roles no-margin">
                           <thead>
                              <tr>
                                 <th class="bold"> No</th>
                                 <th class="text-center bold">Protfolio Name</th>
                                 <th class="text-center bold">Protfolio Type</th>
                                 <th class="text-center bold">Description</th>
                                 <th class="text-center bold">Created at</th>
                                 <th class="text-center text-danger bold">Updated at</th>
								 <th class="text-center bold">Options</th>
                              </tr>
                           </thead>
						   <tbody> -->
								<?php 
								// $i = 1;	
								// foreach($allportfolios as $port){?>
								<!-- <tr>
									<td>#<?php// echo $i?></td>
									<td>
										<a href = "<?php //echo base_url();?>admin/portfolio/portfolio/<?php echo $port['id']?>">
											<?php //echo $port['names']?>
										</a>
									</td>	
									<td><?php //echo $port['portfolio_type']?></td>
									<td><?php// echo substr(strip_tags($port['description']), 0, 30)?>...</td>
									<td><?php //echo date('m-d-Y  h:i A', strtotime($port['createdat']));?></td>
									<td><?php //echo date('m-d-Y  h:i A', strtotime($port['updatedat']));?></td>
									<td> -->
										<!-- <a href = "<?php //echo base_url()?>admin/portfolio/portfolio/<?php //echo $port['id']?>" class = "btn btn-default btn-icon">
											<i class="fa fa-pencil-square-o"></i>
										</a> -->
										<?php //if($port['portfolio_type'] != 'main'){?>
										<!-- <a href = "<?php //echo base_url()?>admin/portfolio/delete_portfolio/<?php echo $port['id']?>" class = "btn btn-danger _delete btn-icon">
											<i class="fa fa-remove"></i>
										</a> -->
										<?php //}?>
								<!-- 	</td>
								</tr> -->
								<?php
								// $i++;
								// }?>
						<!--    </tbody>
					</table> -->
			 
			 			<?php 
                          	render_datatable(array('#',
                            _l('Portfolio Name'),
                            _l('Portfolio Type'),
                            _l('Description'),
                            _l('Created at'),
                            _l('Updated at'),
                            _l('options'),
                            ),'portfolio');  ?>
		</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
		$(function(){
		  initDataTable('.table-portfolio', window.location.href, [1], [1]);
		});
</script>

