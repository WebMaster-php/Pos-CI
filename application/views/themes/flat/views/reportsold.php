<?php //exit('ji');?>
<!-- <div id="lightwing" > -->
<?php 
 // echo "<pre>"; print_r($reports_data[1]); exit('lp'); ?>
<style>
@media print {
     @page
   		{
		   	size: 8.5in 11in;
  		}
}
	
.export_design{
	position: absolute;
	top: 6px !important;
	border-radius: 10px;  
	box-shadow: none !important;
}

.export_design li a{
	padding-left: 10px !important;
}
.dropdown .btn_export{
	background-color: #fff !important;
	color: blue;
	border:1px solid blue;
}
.clear10{
	height:10px;
	clear: both;
}

h4{
	color: #000;
	font-weight: bold;
}

.table_main thead tr th,
.tr_td_border_complete
{
	border: 2px solid #000 !important;
}

.tr_td_border_complete td
{
	border: 2px solid #000 !important;
}

.table_main thead tr th,
.table_main_discount thead tr th,
.table_main_sales thead tr th,
.table_main_foods thead tr th
{
	font-size: 17px;
	color: #000;
}

.table_main tbody tr td,
.table_main_discount tbody tr td,
.table_main_sales tbody tr td,
.table_main_foods tbody tr td
{
	font-size: 15px;
	color: #000;
	
}

.table_total td:nth-child(3n),
.table_total td:nth-child(4n)
{
	border: 2px solid #000;
}


.table_main_sales td{
	border: 2px solid #000;
}

.table_main_foods tbody tr td{
	border: 2px solid #000 !important;
}

.table_main_foods tbody tr td:last-child
{
	border-right: 2px solid #000;
}

.table_main_foods tbody tr td
{
	border: 0px;
}

.table_main_discount th,
.table_main_discount td,
.table_main_sales th,
.table_main_foods th
{
	border: 2px solid #000 !important;
}

.border-none{
	border: none !important;
}

.total_food_border{
	border: 2px solid #000 !important;
}

.total_food_second_last{
	border-bottom : 2px solid #000 !important;
}

</style>

<style>
.exp .dropdown-menu{
	    border: 2px solid #BDC9D7;
    box-shadow: 0 2px 12px rgba(0,0,0,.175);
}

.exp .dropdown-menu>li>a {
    font-weight: 600;
    font-size: 17px;
    color: #5379AF;
}

.exp .btn-primary{
	    font-weight: 600;
    font-size: 17px;
    color: #5379AF;
    background: #fff;
    border: 2px solid #BDC9D7;
}

.exp .btn-primary:hover, .exp .btn-primary:visited, .exp .btn-primary:active, .exp .btn-primary:focus{
    color: #5379AF;
    background: #fff;
    border: 2px solid #BDC9D7;
    box-shadow: none !important;
	outline: none !important;
}

.exp .btn-primary.active, .exp .btn-primary:active, .open> .exp .dropdown-toggle.btn-primary {
   color: #5379AF !important;
    background: #fff !important;
    border: 2px solid #BDC9D7 !important;
    box-shadow: none !important;
    outline: none !important;
}
.table_main_foods tbody tr:last-child{
	border-top: 2px solid black;
}

.report_underline{
	height: 3px;
    background: #000;
    width: 210px;
    margin: 0 auto;
}

@media only screen and (min-width: 320px) and (max-width: 767px){
	div.dt-buttons{
		    display: contents;
		   /* margin-left: -70px !important;*/
	}
	.btn_export{
		margin-top: 10px;
	}
	.pad_zeroo{
		padding: 0px !important;
	}
	.pull_left_main{
		width: 100%;
	}
	#getReportsubmit{
		width: 100%;
	}
	.sppan_no_left{
		margin-left: 0px !important;
	}
	#getReportVersiPossubmit{
		width : 100%;
	}
	
}



</style>

<div class="wrapper">
	<div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group pull-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                             <li class="breadcrumb-item"><a href="<?php base_url();?>clients/profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
                            <li class="breadcrumb-item active">Legacy Reports</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Legacy Reports</h4>
                </div>
            </div>
        </div>
        <div class="se-pre-con"></div>
	        <!-- end page title end breadcrumb -->
	<div class="row ">
        <div class="col-md-12">
            <div class="card-box">
                
                <ul class="nav nav-pills navtab-bg nav-justified pull-in ">
                    <li class="nav-item">
                        <a href="#info" data-toggle="tab" aria-expanded="false" class="nav-link active">
                            VersiPOS
                        </a>
                    </li>
                    <?php if($flagToShowVerseatsTab == true)  {?>	
                    <li class="nav-item">
                        <a href="#info1" data-toggle="tab" aria-expanded="false" class="nav-link">
                            VersiEATS
                        </a>
                    </li> 
                <?php } ?> 
                </ul>

					<div class="tab-content">
						<div class="tab-pane show active" id="info">
						<!-- Overview -->
							<div class="row pad_zeroo">
						        <div class="col-12 pad_zeroo">
						            <div class="card-box pad_zeroo">
						                <div class="col-12 pad_zeroo" >
								            <div class="card-box pad_zeroo">  
								            	<div class="pull-left pull_left_main">
						           					<form id = "getReportVersiPos" name = "getReportVersiPos" method="POST">
								           				<div class = "dt-buttons btn-group customadd" pull-left>
							           							<!-- </span> -->
							           							<span  class="sppan_no_left" style="margin-left:  5%">
							           							<?php
							           							// print_r($versiPos_merchants); 
							           								echo render_select('restaurant_name_versiPos',$versiPos_merchants,array('client_merchant_versiPos','merchant_name_versiPos'),'',$versiPos_merchants[0]['merchant_versiPos'],array(),array(),'','',false); 
							           							 ?>
								           						</span>
								           						<span class="sppan_no_left" style="margin-left:  5%; ">
								           							<a href="#" id = "getReportVersiPossubmit" class="btn btn-primary waves-light waves-effect">Run Report</a>
								           						</span>
								           				</div>
						           					</form>
						           				</div>
						           				<div class="dt-buttons btn-group pull-right">
						          					  <div class="dropdown exp">
													    <button class="btn btn-default dropdown btn_export" type="button" id="menu1" data-toggle="dropdown">EXPORT</button>
													    <ul class="dropdown-menu export_design" role="menu" aria-labelledby="menu1">
													      <li role=""><a   id = "versiPos_pdf"  role="menuitem" tabindex="-1" href="#">PDF</a></li>
													      <li role=""><a  id = "versiPos_print" class = "print" role="menuitem" tabindex="-1" href="#">Print</a></li>
													    </ul>
													  </div>
						          				</div>  	  
								            </div><!-- end card-box -->
								            <div class="clear10"></div>
						           			<?php if($versiPos){?>
											<div  class = "versi" id = "content-for-export-versiPos" style="height: 50%" >
 						           				<div class="col-sm-12">
							           				<h1 class="text-center"><b><?php echo $versiPos[0]?></b></h1>
							           				<h3 class="text-center"><?php echo $versiPos[1]; ?></h3>
							           				<h3 class="text-center"><?php echo $versiPos[2]; ?></h3>
							           			</div>
							           			<div class="report_underline"></div>
							           			<div class="clearfix10"></div>
							           			
												<pre>
													<div class = 'versiPospre'>
												<table id = "pdf_table" style="margin: 0px; padding: 0px; cellspacing: 0px;">		
														<tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px; display: none"><h1><b><?php echo $versiPos[0]?></b></h1></td></tr>
														<tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px; display: none"><h3><b><?php echo $versiPos[1]?></b></h3></td></tr>
														<tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px; display: none"><h3><b><?php echo $versiPos[2]?></b></h3></td></tr>
													<?php
														for ($i=0; $i <sizeof($versiPos) ; $i++) {  
															if($i >=4 ){		        
																	if (trim($versiPos[$i-1]) == '' && trim($versiPos[$i+1]) == '' ) {
																		if(trim($versiPos[$i]) != ''){
																			// echo  '<h5 class = "for_pdf" style="font-weight:bold;font-size: 15px; margin-left : 50px;">' . trim($versiPos[$i]) . '</h5>'  ;
																			echo  '<tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="font-weight:bold;font-size: 15px;">
																			<h5 class = "for_pdf" style="font-weight:bold;font-size: 15px; margin : 0px;">' . trim($versiPos[$i]) . '</h5></td></tr>'  ;
																		}
																	} 
																	else{
																		 echo  '<tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px;"><span class = "for_pdf" style="font-size: 14px; padding : 0;">' . $versiPos[$i] .  ' </span> <br/> </td></tr>' ;        
																		// echo  '<span class = "for_pdf" style="font-size: 14px; margin-left: 50px; padding : 0;">' . $versiPos[$i] .  ' </span> <br/>' ;        
																	}
																}
														} ?>
															<!-- </p> -->
															</table>
													</div>
												</pre>

 						           			 </div>
											<?php }
											else{?>
												<center>
													<h3>No Reports Available</h3>
												</center>
											<?php }?>
						        		</div>
						                <!-- end row -->
						            </div>
						        </div>
						    </div>
						    <!-- end row -->
						</div>
						<?php if($flagToShowVerseatsTab == true ){?> 
						<div class="tab-pane" id="info1">
						<div id = "ri">	
						<!-- Overview -->
						    <div class="row pad_zeroo">
						        <div class="col-12">
						            <div class="card-box pad_zeroo">
						                <div class="col-12 pad_zeroo">
						            		<div class="card-box pad_zeroo">
						          				
						          				<div class="pull-left pull_left_main">
						           					<form id = "getReport" name = "getReport" method="POST">
								           				<div class = "dt-buttons btn-group customadd" pull-left>
								           					<?php 
								           						$date_data[0] = array('report_id' => 1 ,'name' => 'Today','portfolio_id' => 1,);
								           						$date_data[1] = array('report_id' => 2 ,'name' => 'YESTERDAY','portfolio_id' => 1,);
								           						$date_data[2] = array('report_id' => 7 ,'name' => 'LAST 7 DAYS','portfolio_id' => 1,);
								           						$date_data[3] = array('report_id' => 30 ,'name' => 'LAST 30 DAYS ','portfolio_id' => 1,);
								           						// $date_data[4] = array('report_id' => 0 ,'name' => 'CUSTOM ','portfolio_id' => 1,);
								           						$date_data[4] = array('report_id' => 5 ,'name' => 'CUSTOM','portfolio_id' => 1,); ?>
							           						    
							           						    <!-- <span style="margin-right:  5%; " class="sppan_no_left"> -->
							           							<?php
							           								echo render_select('day',$date_data,array('report_id','name'),'',$report_id,array(),array(),'','',false); 
							           							?>
							           							<!-- </span> -->
							           							<span  class="sppan_no_left" style="margin-left:  5%">
							           							<?php
							           							// print_r($all_related_merchant); 
							           								echo render_select('restaurant_name',$all_related_merchant,array('merchant_id','restaurant_name'),'',$merchant[0]['merchant_id'],array(),array(),'','',false); 
							           							 ?>
								           						</span>
								           						<span class="sppan_no_left" style="margin-left:  5%; ">
								           							<!-- <input type="button" id = "getReportsubmit" class ="btn btn-primary waves-light waves-effect" name="getReportsubmit" value = "Run Report"> -->
								           							<!-- <button  id = "getReportsubmit"; class="btn btn-primary waves-light waves-effect" >Run Report</button>  -->
								           							<a href="#" id = "getReportsubmit" class="btn btn-primary waves-light waves-effect">Run Report</a>
								           						</span>
								           				</div>
						           					</form>
						           				</div>
						           				<div class="dt-buttons btn-group pull-right">
						          					  <div class="dropdown exp">
													    <button class="btn btn-default dropdown btn_export" type="button" id="menu1" data-toggle="dropdown">EXPORT</button>
													    <ul class="dropdown-menu export_design" role="menu" aria-labelledby="menu1">
													      <li role=""><a id = "execl" role="menuitem" tabindex="-1" href="#">Execl</a></li>
													      <li role=""><a id = "csv" role="menuitem" tabindex="-1" href="#">CSV</a></li>
													      <li role=""><a class = "pdf" id = "VersiEATS_pdf" role="menuitem" tabindex="-1" href="#">PDF</a></li>
													      <li role=""><a class = "print" id = "versiPos_print" role="menuitem" tabindex="-1" href="#">Print</a></li>
													    </ul>
													  </div>
						          				</div>  	
						           			</div>
						           			<div class="clear10"></div>
						           			<div  id = "content-for-export" >
						           			<!-- <div class="se-pre-con" id= 'ajaxcal'></div> -->
						           			<div class="col-sm-12">
						           				<h1 class="text-center"><b>Company: <?php echo $merchant[0]['restaurant_name']?></b></h1>
						           				<h3 class="text-center">Run Time: <?php echo date('m/d/Y h:i:A', strtotime($runtime)); ?></h3>
						           				<h3 class="text-center">Reporting On: <?php echo date('m/d/Y ', strtotime($reportingon)); ?></h3>
						           				
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
															<?php //echo "<pre>"; print_r($reports_data[0]); exit('k');
																$tol = 0; 
																foreach ($reports_data[0] as $rd){  
																	if($rd[0] !='Payment Tips'){
																	$tol =  $tol+$rd['SUM(total_w_tax)']; }
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
																				echo round($rd['SUM(total_w_tax)'], 2);
																				} 
																			else{ echo '00.00';}
																		?>
																		</span>
																	</td>
																</tr>
															<?php } ?>
															<tr class="table_total">
																<td></td>
																<td></td>
																<td>Total</td>
																<td><span class="pull-left">$</span> <span class="pull-right"><?php echo round($tol , 2)?></span></td>
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
															<?php 
															echo ""; 
															$total_sales = 0; $total_qty = 0; 
															foreach($reports_data[1] as $rsales){
																$total_sales += $rsales['price'] ;
																$total_qty += $rsales['qty'];
																$tprice += $rsales['price'] +$rsales['tax']/100*$rsales['price'];
																$total_tax_by_cat = 0;
																}?>
															
															<?php foreach($reports_data[1] as $rsales){?>
																<tr>
																	<td class="text-left-padding">*<?php echo $rsales['name']?></td>
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
																	<td> <?php echo round($rsales['price']/ $total_sales * 100, 2);?>%</td>
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
																<th width="%">Category</th>
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
																			// $tcqty += $rt['qty'] ; ?><br>
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
																<?php 
																// echo "<pre>"; print_r($reports_data[3]); exit('view');  
																foreach ($reports_data[3] as $hr) {?>
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
							    </div>
							</div> <!-- end row -->
						</div> <!-- end of tab -->
                	</div>
            	
            	</div>
            	</div>
            	<?php ;} ?>
            	</div>
        	</div> <!-- end col -->
    	</div>
	</div>
</div>

<!-- End wrapper -->

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                2018 © VersiPOS
            </div>
        </div>
    </div>
</footer>
    <!-- End Footer -->
<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<!-- <script src="<?php //echo base_url();?>assets/plugins/datatables/datatables.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.debug.js"></script>
<!-- D:\xampp\htdocs\versipos\assets\plugins\datatables\datatables.js -->
<!-- <script src="http://www.csvscript.com/dev/html5csv.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
<script >

// $(window).load(function() {
// 		// Animate loader off screen
// 		$(".se-pre-con").fadeOut("slow");
// 	});
//execl
	$("body").on("click", "#execl", function (e){
  	let file = new Blob([$('#content-for-export').html()], {type:"application/vnd.ms-excel"});
	let url = URL.createObjectURL(file);
	let a = $("<a />", {
 	 href: url,
  	download: "filename.xls"}).appendTo("body").get(0).click();
  	e.preventDefault();
	});

//csv
// $("body").on("click", "#csv", function () {
//  alert('j'); 
// let file = new Blob([$('#content-for-export').html()], {type:"text/csv"});
// 	let url = URL.createObjectURL(file);
// 	let a = $("<a />", {
//  	 href: url,
//   	download: "filename.csv"}).appendTo("body").get(0).click();
//   	e.preventDefault();
// });
function download_csv(csv, filename) {
    var csvFile;
    var downloadLink;

    // CSV FILE
    csvFile = new Blob([csv], {type: "text/csv"});

    // Download link
    downloadLink = document.createElement("a");

    // File name
    downloadLink.download = filename;

    // We have to create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);

    // Make sure that the link is not displayed
    downloadLink.style.display = "none";

    // Add the link to your DOM
    document.body.appendChild(downloadLink);

    // Lanzamos
    downloadLink.click();
}

function export_table_to_csv(html, filename) {


	var csv = [];
	var rows = document.querySelectorAll("table tr");
	
    for (var i = 0; i < rows.length; i++) {
    	// var a = $('h4').text();
    	// alert(a);
		var row = [], cols = rows[i].querySelectorAll("td, th");
		
        for (var j = 0; j < cols.length; j++) 
            row.push(cols[j].innerText);
        
		csv.push(row.join(","));		
	}

    // Download CSV
    download_csv(csv.join("\n"), filename);
}



document.querySelector("#csv").addEventListener("click", function () {
    var html = document.querySelector("#content-for-export").outerHTML;
	export_table_to_csv(html, "Report.csv");
});

	var doc = new jsPDF('p', 'pt', 'letter');;
	var specialElementHandlers = {
		'#editor': function (element, renderer) {
		// '#ignoreContent': function (element, renderer){
			return true;
		}
};

	

$('#versiPos_pdf').click(function () {
	var wit = $('#content-for-export-versiPos').html(); 

	 var pdf = new jsPDF('p', 'pt', 'letter');
    source = $('#pdf_table')[0];
    specialElementHandlers = {
        '#editor': function (element, renderer) {
            return true
        }
    };
    margins = {
        top: 10,
        bottom: 60,
        left: 50,
        width: 700
    };
    pdf.fromHTML(
    source, 
    margins.left, 
    margins.top, { 
        'width': margins.width, 
        'elementHandlers': specialElementHandlers
    },
    function (dispose) {
        pdf.save('versiPos lagacy Report.pdf');
    }, margins);
});
	
	

//pdf
	
$("body").on("click", ".pdf", function () {
	var doc = new jsPDF();
	var specialElementHandlers = {
		'#ignoreContent': function (element, renderer){
			return true; 
		}
	};
		var val ; 
		if(this.id == 'versiPos_pdf'){
			// val ='#content-for-export-versiPos' ; 
			//val = $('#content-for-export-versiPos').html()
		}
		else{
			val ='#content-for-export' ; 	
		}
            html2canvas($(val), {
                onrendered: function (canvas) {
                    var data = canvas.toDataURL();
                    var docDefinition = {
                        content: [{
                            image: data,
                            width: 500
                        }]
                    };
                    pdfMake.createPdf(docDefinition).download("Report.pdf");
                }
            });
        });
//print
$('.print').click(function () {   
 if(this.id == 'versiPos_pdf'){
		val ='#content-for-export-versiPos' ;
	    $(".versiPospre").unwrap();

	}
	else{
		val ='#content-for-export' ; 	
	}
   // customadd exp pull-in
$(".versiPospre").unwrap();
  $('.customadd').hide();
  $('.pull-in').hide();
  $('.exp').hide();

   window.print();
   $('.customadd').show();
   $('.pull-in').show();
   $('.exp').show();
   $(".versiPospre").wrap("<pre></pre>");
});

// $('#pdf').click(function () {   
//    alert('inn'); 
//     doc.fromHTML($('#content-for-export').html(), 15, 15, {
//         'width': 170,
//             'elementHandlers': specialElementHandlers
//     });
//     doc.save('sample-file.pdf');
// });

// onchange funciton to open date fields;
$( "#day" ).change(function() {
 	if($(this).val() == 5){
		$('#day').after('<div id = "customDateDiv"> From : <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"> To : <input type="date" id = "dateto" name = "dateto" class="form-control datepicker"></div>');
 		// alert($(this).val());
 		// alert( "Handler for .change() called." );	
 	}  
 	else{
 		$("#customDateDiv").remove();
 		// $("#dateto").remove();
 	}
  

});

//onclick function to get runed report; 
$("#getReportsubmit").click(function(){
	var formdata = $('#getReport').serialize();
	var run = 'get_reports_data'; 
	$.ajax({
		type:'POST',
		url: '<?php echo base_url();?>clients/reports',
		data:{formdata:formdata, run: run},
		success:function(data){
			var data = $.parseJSON(data);
			$("#content-for-export").html(data);
			$("#ajaxcal").fadeOut("slow");
		}
	});
  // alert("The paragraph was clicked.");
});
	
	
	
$("#getReportVersiPossubmit").click(function(){
	var formdata = $('#getReportVersiPos').serialize();
	$.ajax({
		type:'POST',
		url: '<?php echo base_url();?>clients/get_versiPos_data',
		data:{formdata},
		success:function(data1){
			// alert(data1); 
			var data1 = $.parseJSON(data1);
			
			$("#content-for-export-versiPos").html(data1);
			// // alert(data);
			// $("#ajaxcal").fadeOut("slow");
		}
	});
});

</script>
</div>

<!-- <script type="text/javascript">
        function demoFromHTML() {
            var pdf = new jsPDF('p', 'pt', 'letter');
            // source can be HTML-formatted string, or a reference
            // to an actual DOM element from which the text will be scraped.
            source = $('#customers')[0];

            // we support special element handlers. Register them with jQuery-style 
            // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
            // There is no support for any other type of selectors 
            // (class, of compound) at this time.
            specialElementHandlers = {
                // element with id of "bypass" - jQuery style selector
                '#bypassme': function(element, renderer) {
                    // true = "handled elsewhere, bypass text extraction"
                    return true
                }
            };
            margins = {
                top: 80,
                bottom: 60,
                left: 40,
                width: 522
            };
            // all coords and widths are in jsPDF instance's declared units
            // 'inches' in this case
            pdf.fromHTML(
                    source, // HTML string or DOM elem ref.
                    margins.left, // x coord
                    margins.top, {// y coord
                        'width': margins.width, // max width of content on PDF
                        'elementHandlers': specialElementHandlers
                    },
            function(dispose) {
                // dispose: object with X, Y of the last line add to the PDF 
                //          this allow the insertion of new lines after html
                pdf.save('Test.pdf');
            }
            , margins);
        }
    </script> -->







