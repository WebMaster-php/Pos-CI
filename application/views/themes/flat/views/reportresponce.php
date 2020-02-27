						           			<div id = "content-for-export" >
						           			<!-- <div class="se-pre-con" id= 'ajaxcal'></div> -->
						           			<div>
						           				<h1 class="text-center"><b>Company: <?php echo $merchant[0]['restaurant_name']?></b></h1>
						           				<h3 class="text-center">Run Time: <?php echo date('m/d/Y h:i:A', strtotime($runtime)); ?></h3>
						           				<h3 class="text-center">Reporting On: <?php echo $reportingon; ?></h3>
						           				
						           			</div>
						           			<div class="report_underline"></div>
						           			<div class="clearfix10"></div>
						           			<div>
						           				<h3 class="text-center"><?php echo $r_of_day ; ?></h3>
						           			</div>
						           			<div class="clearfix10"></div>
						           			<div class="clearfix10"></div>
						           			<div class="clearfix10"></div>
						           			<?php //echo "<pre>"; print_r($reports_data); exit('jojojo'); ?> 
						           			<div class = "row">
						           				<div class="col-sm-6">
												<h4>PAYMENTS TOTAL :</h4>
												<div class="table-responsive">
													<table class="table table_main export_orders_table">
														<thead>
															<tr>
																<th width="25%">Qty</th>
																<th width="25%">&nbsp; </th>
																<th width="25%">Payment Type</th>
																<th width="25%">Amount Recvd</th>
															</tr>
														</thead>
														<tbody>
															<tr class="tr_td_border_complete">
																<td></td>
																<td></td>
																<td><b>Gross Total</b></td>
																<?php 
																$gross_total = 0; 
																 //echo "<pre>"; print_r($reports_data[0]); exit('aed');  
																foreach ($reports_data[0] as $gross){  
																 if($gross[0] == 'Cash' ||  $gross[0] == 'Card' ) {
																 	$gross_total += $gross['SUM(total_w_tax)']; } 
																  } ?>
																<td>
																	<span class="pull-left">$</span> 
																	<span class="pull-right"><b><?php echo round($gross_total, 2); ?></b></span>
																</td>
															</tr>
															<?php //echo "<pre>"; print_r($reports_data[0]); exit('k');
																$tol = 0; 
																foreach ($reports_data[0] as $rd){  

																	// if($rd[0] == 'Tax'){
																	// 	echo $rd['SUM(total_w_tax)']; exit('a');  
																	// }
																	if(trim($rd[0]) =='Payment Tips' || trim($rd[0]) =='Tax' || trim($rd[0]) =='Delivery Fee' ){
																	// echo $rd['SUM(total_w_tax)']; 
																	$tol =  $tol + $rd['SUM(total_w_tax)']; 

																}
																// echo $tol; 

																	?>
																<tr class="tr_td_border_complete">
																	<td><?php echo $rd['count(order_id)']?></td>
																	<td></td>
																	<td><?php echo $rd[0]?></td>
																	<td>
																		<span class="pull-left">$</span> 
																		<span class="pull-right">
																		<?php 
																			if($rd['SUM(total_w_tax)']){
																				echo number_format(round($rd['SUM(total_w_tax)'], 2), 2);
																				} 
																			else{ echo '00.00';}
																		?>
																		</span>
																	</td>
																</tr>
															<?php }   ?>
															<tr class="table_total">
																<td></td>
																<td></td>
																<td><b>NetTotal</b></td>
																<td><span class="pull-left">$</span> <span class="pull-right"><b><?php echo round($gross_total - $tol  , 2)?></b></span></td>
															</tr>
														</tbody>
													</table>
												</div>	
												<div class="clearfix"></div>
											</div>	
											<div class="col-sm-6">
											<h4>DISCOUNT TOTAL :</h4>
												<div class="table-responsive">
													<table class="table table_main_discount export_orders_table">
														<thead>
															<tr>
																<th width="10%">Qty</th>
																<th width="60%">Name </th>
																<th width="30%">Amount</th>
															</tr>
														</thead>
														<tbody>
															<?php
															 $totaldiscount = 0;
															 $totaldiscountqty =0;
															 foreach($reports_data[4] as $rdiscount){ ?>
																<tr>
																	<td class="text-right">
																		<?php $totaldiscountqty += $rdiscount['count(voucher_code)'];
																			  echo $rdiscount['count(voucher_code)']
																		 ?>
																	</td>
																	<td class="text-right"><?php echo $rdiscount['voucher_code']?></td>
																	<td>
																		<span class="pull-left">$</span> 
																		<span class="pull-right">
																			<?php $totaldiscount +=$rdiscount['SUM(voucher_amount)'];
																			echo round($rdiscount['SUM(voucher_amount)'], 2) ; ?>
																		</span>
																	</td>
																</tr>
															<?php ;} ?>
														    <tr>
																<td class="text-right"><?php echo round($totaldiscountqty, 2)?></td>
																<td class="text-right">Total</td>
																<td>
																	<span class="pull-left">$</span> 
																	<span class="pull-right">
																		<?php echo round($totaldiscount, 2) ; ?>
																	</span>
																</td>
															</tr>

														</tbody>
													</table>
												</div>	
												<div class="clearfix"></div>
											</div>
											<div class="clear10"></div>
		
											<div class="col-sm-2"></div>
											<div class="col-sm-8">
												<h4>SALES BY CATEGORY :</h4>
												<div class="table-responsive">
													<table class="table table_main_sales">
														<thead>
															<tr>
																<th width="%">Category</th>
																<th width="%">Qty</th>
																<th width="%">Totsales</th>
																<th width="%">Taxable</th>
																<th width="%">NetSales</th>
																<th width="%">Sales %</th>
															</tr>
														</thead>
														<tbody>
															<?php $total_sales = 0; $total_qty = 0; 
															foreach($reports_data[1] as $rsales){
																$total_sales += $rsales['price'] ;
																$total_qty += $rsales['qty'];
																$tprice += $rsales['price'] +$rsales['tax']/100*$rsales['price'];
																$total_tax_by_cat = 0;
																}?>
															
															<?php foreach($reports_data[1] as $rsales){?>
																<tr>
																	<td class="text-left-padding">* <?php echo $rsales['name']?></td>
																	<td><?php echo $rsales['qty']?></td>
																	<td>
																		<span class="pull-left">$</span> 
																		<span class="pull-right">
																			<?php echo round($rsales['price'] + $rsales['tax']/100*$rsales['price'], 2)?>
																		</span>
																	</td>
																	<td>
																		<span class="pull-left">$</span> 
																		<span class="pull-right">
																			<?php 
																				   echo round($rsales['tax']/100*$rsales['price'], 2) ; 
																				   $total_tax_by_cat +=$rsales['tax']/100*$rsales['price'] ?>
																		</span>
																	</td>
																	<td><span class="pull-left">$</span> <span class="pull-right"><?php echo $rsales['price']?></span></td>
																	<td> <?php echo round($rsales['qty']/ $total_qty * 100, 2);?>%</td>
																</tr>		
															<?php }?>
																<tr>
																	<td class="border-none"></td>
																	<td><?php echo $total_qty ;?></td>
																	<td><span class="pull-left">$</span> <span class="pull-right"><?php echo round($tprice, 2) ;?></span></td>
																	<td><span class="pull-left">$</span> <span class="pull-right"><?php echo round($total_tax_by_cat, 2) ;?></span></td>
																	<td>
																		<span class="pull-left">$</span> <span class="pull-right">
																			<?php echo round($tprice - $total_tax_by_cat, 2);?>
																		</span>
																	</td>
																	<td class="border-none"></td>
																</tr>
														</tbody>
													</table>
												</div>	
												<div class="clearfix"></div>
											</div>
											<div class="col-sm-2"></div>
											
											<div class="clear10"></div>
											
											<div class="col-sm-1"></div>
											<div class="col-sm-10">
												<h4>SALES BY FOODS ITEMS BY CATEGORY :</h4>
												<div class="table-responsive">
													<table id = "sal_cat" class="table table_main_foods" >
														<thead>
															<tr>
																<th width="32%">Category</th>
																<th width="%">Item Price</th>
																<th width="%">Qty</th>
																<th width="%">TotSales</th>
																<th width="%">Taxable</th>
																<th width="%">NetSales</th>
																<th width="%">SALES %</th>
															</tr>
														</thead>
														<tbody>
															<?php
																
																$tcp = 0; $tcqty = 0; $total_tax_by_cat_items = 0;
																// echo "<pre>";  print_r($reports_data[2]); exit('by'); 
																foreach($reports_data[2] as $key2 => $ritems){ ?>
															<?php 
															
															
															//foreach($rsalesitems as $key => $ritems){ ?>	
															<tr>
																<td class="text-left-padding">
																	<b>* <?php echo $key2?></b><br>
																		<?php foreach($ritems as  $rt){
																			if($key2 == $rt['cat_name']){ 
																				echo $rt['item_name']; ?><br>
																		<?php } 
																	}?>
																</td>
																<td >
																	&nbsp; <br>
																	<?php foreach($ritems as  $rt){ 
																		if($key2 == $rt['cat_name']){ ?>
																			 <?php
																			 $string = '';
																			 $iprice = json_decode($rt['item_price'] , true);
																			 $v = 1; 
																			 	foreach ($iprice as $key => $ipi) {
																			 		$size_name  = get_size_for_versieats($key); 
																			 		if($size_name){
																			 			if(sizeof($iprice) == $v){
																			 				$string .= '<b> '. $size_name .': ' . '</b>  '  . '$'. ' ' . $ipi ;
																			 			}
																			 			else{
																			 				$string .= '<b> '. $size_name .': ' . '</b>  '  . '$'. ' ' . $ipi .',<br>';	
																			 			}
																			 		}
																			 		else{
																			 			$string .= ' ' . '$'. ' ' . $ipi .',';	
																			 		}	
																			 	$v++; 	
																			 	}
																			 	$stign = trim($string, ',');
																			 echo $stign; 	 
																			 // print_r($ptv); 
																			 // $f = ltrim(rtrim($rt['item_price'], ']'), '['); 
																			 // $t = trim($f , '"');
																			 // echo $t;
																			 // echo round($t); 
																			 //$tcqty += $rt['qty'] ; ?><br>
																		<?php } ?>
																	<?php }?>
																</td>

																<td>
																	&nbsp; <br>
																	<?php 
																	foreach($ritems as  $rt){ 
																		if($key2 == $rt['cat_name']){ ?>
																			 <?php echo $rt['qty']; 
																			 $tcqty += $rt['qty'] ; ?><br>
																		<?php } ?>
																	<?php }?>
																</td>
																<td>
																	&nbsp; <br>
																	<?php foreach($ritems as  $rt){ 
																		if($key2 == $rt['cat_name']){ ?>
																			$ <?php echo round($rt['price'] + $rt['tax']/100*$rt['price'], 2); 
																			   $tcp += $rt['price'] + $rt['tax']/100*$rt['price'];
																				
																			?><br>
																		<?php } ?>
																	<?php }?>
																</td>
																<td>
																	&nbsp; <br>
																	<?php foreach($ritems as  $rt){ 
																		if($key2 == $rt['cat_name']){ ?>
																			$ <?php echo round($rt['tax']/100*$rt['price'], 2); 
																			   $total_tax_by_cat_items += $rt['tax']/100*$rt['price'];
																			?><br>
																		<?php } ?>
																	<?php }?>
																</td>
																<td>
																	&nbsp; <br>
																	<?php foreach($ritems as  $rt){ 
																		if($key2 == $rt['cat_name']){ ?>
																			$ <?php echo round($rt['price'], 2); 
																			   //$tcp += $rt['price'];
																			?><br>
																		<?php } ?>
																	<?php }?>
																	
																</td>
																<td>
																	&nbsp; <br>
																	<?php 

																	//for percentage start 
																$total_percentage_cat= array();
																$total_percentage_cat_all = 0;
																foreach ($reports_data[2] as $key_per => $percentage) {
																	$total_cat_price = 0;
																	foreach ($percentage as $key_per2 => $percentage2) {
																			// $total_cat_price += $percentage2['price'] + $percentage2['tax']/100*$percentage2['price'];
																			$total_cat_price += $percentage2['price'] + $percentage2['tax']/100*$percentage2['price'];
																	}
																	$total_percentage_cat[$key_per] = $total_cat_price ;
																	$total_percentage_cat_all += $total_percentage_cat[$key_per]; 
																}?>
																	
																	<?php foreach($total_percentage_cat as $key_tot_per_cat => $tot_per_cat){ 
																			if($key2 == $key_tot_per_cat){
																				echo round($tot_per_cat/$total_percentage_cat_all*100, 2) .'%'; 
																			}


																		}?>

																</td>
															</tr>
															<?php //} 
															}?>
															<tr>
																<td class="border-none"></td>
																<td class="border-none"></td><!-- $tcp = 0; $tcqty = 0; -->
																<td class="total_food_border"><?php echo round($tcqty, 2); ?></td>
																<td class="total_food_border">$ <?php echo round($tcp, 2)?></td>
																<td class="total_food_border">$ <?php echo round($total_tax_by_cat_items, 2)?></td>
																<td class="total_food_border">$ <?php echo round($tcp - $total_tax_by_cat_items, 2)?></td>
																<td class="border-none"></td>
															</tr>
														</tbody>
														<div class="clearfix"></div>
													</table>
												</div>	
												<div class="clearfix"></div>
											</div>
											<div class="col-sm-1"></div>
											
											<div class="clear10"></div>
											
											<div class="col-sm-12">
												<?php 
												// echo "<pre>"; print_r($reports_data[3]); exit;  ?>
												<h4>HOURLY SALES :</h4>
												<div class="table-responsive">
													<table class="table table_main_discount">
														<thead>
															<tr>
																<th><b>PERIOD</b></th>
																<?php foreach ($reports_data[3] as $hr) {?>
																	<th><b><?php echo $hr['openhoura']; ?></b></th>
																<?php }?>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td># ORD</td>
																<?php foreach ($reports_data[3] as $hr) {?>
																	<td>
																		<?php if($hr['count(order_id)'] != '' && $hr['count(order_id)'] != 0 ){echo $hr['count(order_id)'];} 
																		else { echo '--';}?>	
																	</td>
																<?php }?>
															</tr>
															<tr>
																<td>TOTAL</td>
																<?php foreach ($reports_data[3] as $hr) {?>
																	<td>
																		<?php if($hr['total_w_tax'] != '' && $hr['total_w_tax'] != 0 ){echo round($hr['total_w_tax'], 2);} 
																		else { echo '--';}?>	
																	</td>
																<?php }?>
															</tr>
															<tr>
																<td>NET</td>
																<?php foreach ($reports_data[3] as $hr) {?>
																	<td>
																		<?php if($hr['sub_total'] != '' && $hr['sub_total'] != 0 ){echo round($hr['sub_total'],2);} 
																		else { echo '--';}?>	
																	</td>
																<?php }?>
															</tr>
														</tbody>
													</table>
												</div>	
												<div class="clearfix"></div>
												</div>
											</div>

						           			<div>
						           			<!-- end card-box --> 
						        		</div>
						                <!-- end row -->
							        </div>