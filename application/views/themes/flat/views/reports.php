<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.css">
  
  <!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js"></script>



<?php 
	
// foreach ($list as $list1) {
	// echo "<pre>"; print_r($list); exit('koko');  //if(){}
// }
?>
<style>
.position_ini{
	position: initial !important; 
}
@media screen and (max-width: 767px) {
  	.drop-id .dropdown .dropdown-label {
    	width: 338px !important;
	}
	.drop-id .dropdown.open .dropdown-list {
	    width: 336px  !important;
	}
}


.drop-id .dropdown {
position: relative;
margin-bottom: 20px;
}
.drop-id .dropdown .dropdown-list {
padding: 8px 8px 25px;
background: #fff;
position: absolute;
top: 50px;
left: 0;
right: 0;
border: 1px solid rgba(0, 0, 0, 0.2);
max-height: 300px;
overflow-y: auto;
background: #fff;
display: none;
z-index: 10;
}
.drop-id .dropdown .checkbox {
opacity: 0;
transition: opacity .2s;
}
.drop-id .dropdown .dropdown-label {
display: block;
height: 35px;
width: 200px;
text-overflow: ellipsis;
font-size: 14px;
line-height: 25px;
background: #fff;
border: 1px solid rgba(0, 0, 0, 0.2);
padding: 6px 0px 6px 18px;
cursor: pointer;
/*position: absolute;*/
text-overflow: ellipsis;
overflow: hidden;
font-weight: normal;
white-space: nowrap;
}
.drop-id .dropdown .dropdown-label:before {
content: '▼';
position: absolute;
right: 20px;
top: 50%;
transform: translateY(-50%);
transition: transform .25s;
transform-origin: center center;
}
.drop-id .dropdown.open .dropdown-list {
display: block;
width: 220px;
}
.drop-id .dropdown.open .checkbox {
transition: 2s opacity 2s;
opacity: 1;
}
.drop-id .dropdown.open .dropdown-label:before {
transform: translateY(-50%) rotate(-180deg);
}

.drop-id .checkbox {
margin-bottom: 5px;
}
.drop-id .checkbox:last-child {
margin-bottom: 0;
}
.drop-id .checkbox .checkbox-custom {
display: none;
}
.drop-id .checkbox .checkbox-custom-label {
display: inline-block;
position: relative;
vertical-align: middle;
cursor: pointer;
}
.drop-id .checkbox .checkbox-custom + .checkbox-custom-label:before {
content: '';
background: transparent;
display: inline-block;
vertical-align: middle;
margin-right: 10px;
text-align: center;
width: 12px;
height: 12px;
border: 1px solid rgba(0, 0, 0, 0.3);
border-radius: 2px;
margin-top: -2px;
}
.drop-id .checkbox .checkbox-custom:checked + .checkbox-custom-label:after {
content: '';
position: absolute;
top: 1px;
left: 4px;
height: 10px;
padding: 2px;
transform: rotate(45deg);
text-align: center;
border: solid #000;
border-width: 0 2px 2px 0;
}
.drop-id .checkbox .checkbox-custom-label {
line-height: 16px;
font-size: 16px;
margin-right: 0;
margin-left: 0;
color: black;
}
.rel{
position:relative!important;
}
.drop-id {
display: inline-block;
}
.input-search {
margin-bottom: 15px;
}
.input-search input {
width:100%;
}
.divider {
border-bottom: 1px solid #d6d6d6;
width: 107%;
margin: 5px 0px 10px -7px;
}
.span1 {
margin: 0 10px;
}
.span1 a {
color: #362e2e;
text-decoration: none;
font-weight: bold;
}
.span1 a:hover{
opacity:0.8;
}
.heading{
font-weight: bold;
margin-bottom: 10px;
}
.pl-10{
padding-left:30px;
}
</style>	



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

.loader_div{
  position: fixed;
  top: 0;
  bottom: 0%;
  left: 0;
  right: 0%;
  z-index: 99;
  opacity:0.7;
  display:none;
  background: lightgrey url('<?php echo base_url();?>media/wait.gif') center center no-repeat;
}

</style>
<div id="loader_div" class="loader_div"></div> 
<div class="wrapper">
	<div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group pull-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                             <li class="breadcrumb-item"><a href="<?php base_url();?>clients/profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Reports</h4>
                </div>
            </div>
        </div>
        <div class="se-pre-con"></div>
	        <!-- end page title end breadcrumb -->
	<div class="row ">
		<div class="alert alert-success " style=" display: none;" id = "message"><strong>Success! </strong>Email sent successfully.</div>
        <div class="col-md-12">
            <div class="card-box">
                
                <ul class="nav nav-pills navtab-bg nav-justified pull-in ">
                    <li class="nav-item">
                        <a href="#info" data-toggle="tab" aria-expanded="false" class="nav-link active">
                            Versi Reports
                        </a>
                    </li>
                    <?php if($flagToShowVerseatsTab == true)  {?>	
                    <li class="nav-item">
                        <a href="#info1" data-toggle="tab" aria-expanded="false" class="nav-link">
                            VersiEATS
                        </a>
                    </li> 
                <?php } 
                // echo "<pre>"; print_r($versiPos_merchants); exit('l');  ?> 
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
							           							<span  class="sppan_no_left"  id ="api_day_span" style="margin-left:  0%">
								           							<?php

								           							$date_data_api[0] = array('report_api_id' => 1 ,'name' => 'Today','portfolio_id' => 1,);
									           						$date_data_api[1] = array('report_api_id' => 2 ,'name' => 'YESTERDAY','portfolio_id' => 1,);
									           						$date_data_api[2] = array('report_api_id' => 3 ,'name' => 'LAST 7 DAYS','portfolio_id' => 1,);
									           						$date_data_api[3] = array('report_api_id' => 4 ,'name' => 'LAST 30 DAYS ','portfolio_id' => 1,);
									           						$date_data_api[4] = array('report_api_id' => 5 ,'name' => 'THIS MONTH','portfolio_id' => 1,); 
									           						$date_data_api[5] = array('report_api_id' => 6 ,'name' => 'LAST MONTH','portfolio_id' => 1,); 
									           						$date_data_api[6] = array('report_api_id' => 7 ,'name' => 'SELECT DATE','portfolio_id' => 1,); 
									           						$date_data_api[7] = array('report_api_id' => 8 ,'name' => 'CUSTOM','portfolio_id' => 1,); 

								           						    echo render_select('api_day',$date_data_api,array('report_api_id','name'),'',$report_api_id,array(),array(),'','',false); 
								           							?>
							           							</span>
							           							<span  class="sppan_no_left" style="margin-left:  5%">
							           							<?php
							           								echo render_select('restaurant_name_versiPos',$versiPos_merchants,array('client_merchant_versiPos','merchant_name_versiPos'),'',$versiPos_merchants[0]['merchant_versiPos'],array(),array(),'','',false); error
							           							 ?>
								           						</span>

								           						<!-- <div class="button-group i-nav1 rel mt-2 float-right mb-3">
																	<button type="button" class="btn border p-2 f-15 black btn-default btn-sm dropdown-toggle " data-toggle="dropdown" aria-expanded="false">
																	Download <span class="fa fa-angle-down"></span></button>
																	<ul class="dropdown-menu p-3 left-100" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 45px, 0px);">
																	<div class="d-scroll">
																	<p class="f-15 black bold">Download Completed Specs</p>
																	<li><a href="#" class="small f-13 gry2"><input type="checkbox">&nbsp;&nbsp;&nbsp;All Divisions</a></li>
																	<li><a href="#" class="small"><input type="checkbox">&nbsp;&nbsp;&nbsp;00 - Procurement and Contracting Requirements</a></li>
																	<li><a href="#" class="small"><input type="checkbox">&nbsp;&nbsp;&nbsp;01 - General Requirements </a></li>
																	<li><a href="#" class="small"><input type="checkbox">&nbsp;&nbsp;&nbsp;02 - Existing Conditions</a></li>
																	<li><a href="#" class="small"><input type="checkbox">&nbsp;&nbsp;&nbsp;03 - Concrete</a></li>

																	</div>
																	<a href="#" class="btn-bg1 mt-3 float-right">Download</a>
																	</ul>
																	</div> -->
																	<?php //echo "<pre>"; print_r($list); exit('koko');  ?>
																	<span class="sppan_no_left" style="margin-left:  5%; ">
																<div class="drop-id">
																	<div class="dropdown rel">
																	<label class="dropdown-label abc">Select Options</label>

																	<div class="dropdown-list">
																	<div class="input-search text-center">
																	<input type="text" id = "search_default">
																	<span class="span1"><a href="javascript:void(0)" id = "save_as_default" class = "cl_save_as_default" >Save as default</a></span> | 
																	<span class="span1"><a href="#" id = "clear_default">Clear</a></span> 
																	</div>
																	<div class="alert alert-success" id="success_alert" style="display:none;"> Filters Successfuly Added</div>
																	<div class="heading">Group Report</div>
																	<div class="pl-10" id = "group">
																		<?php 
																			$filters = json_decode($versiPos_default_filter->filters);
																			//echo "<pre>"; print_r($filters); exit('jijij');  
																			$increment = 1;
																		foreach ($list as $list1) {
																			if(trim($list1->Type) == 'group'){?>
																			<div class="checkbox group_search">
																				<input type="checkbox" name="checkboxes1[]" class="check checkbox-custom versi_check" id="checkbox-custom_0<?php echo $increment ?>" value = <?php echo $list1->ReportID ?>  <?php foreach ($filters as $filter) { if($filter == $list1->ReportID){echo "checked"; } }?>/>
																				<label for="checkbox-custom_0<?php echo $increment ?>" class="checkbox-custom-label"><?php echo trim($list1->Name) ?></label>
																			</div>
																		<?php  $increment++;
																			}
																		} ?>
																	</div>
																	<div class="divider"></div>

																	<div class="heading">Preset Report</div>
																		<div class="pl-10" id = "preset">
																			<?php foreach ($list as $list1) {
																				if(trim($list1->Type) == 'preset'){?>
																				<div class="checkbox preset_search">
																					<input type="checkbox" name="checkboxes2[]" class="check checkbox-custom versi_check" id="checkbox-custom_0<?php echo $increment ?>" value = <?php echo $list1->ReportID ?> <?php foreach ($filters as $filter) { if($filter == $list1->ReportID){echo "checked"; } }?>/>
																					<label for="checkbox-custom_0<?php echo $increment ?>" class="checkbox-custom-label"><?php echo trim($list1->Name) ?></label>
																				</div>
																			<?php $increment++; 
																		} } ?>

																		
																		</div>
																	</div>
																	</div>
																	<div class="clearfix"></div>
																	</div>	
																	</span>								           						
																<span class="sppan_no_left" style="margin-left:  5%; ">
								           							<a href="#" id = "getReportVersiPossubmit" class="btn btn-primary waves-light waves-effect">Run Report</a>
								           						</span>
								           				</div>
						           					</form>
						           				</div>
						           				<div class="dt-buttons btn-group pull-right">
						          					  <div class="dropdown exp position_ini">
													    <button class="btn btn-default dropdown btn_export" type="button" id="versipos_menu1" data-toggle="dropdown">EXPORT</button>
													    <ul class="dropdown-menu export_design" role="menu" aria-labelledby="menu1">
													      <li role=""><a   id = "versiPos_pdf"  role="menuitem" tabindex="-1" href="#">PDF</a></li>
													      <li role=""><a  id = "versiPos_print" class = "print" role="menuitem" tabindex="-1" href="#">Print</a></li>
													      <li role=""><a  id = "versiPos_email" class = "email" role="menuitem" tabindex="-1" href="#"  data-toggle="modal" data-target="#myModal">Email</a></li>
													    </ul>
													  </div>
						          				</div>  	  
								            </div><!-- end card-box -->
								            <div class="clear10"></div>
						           			
											<div  class = "versi" id = "content-for-export-versiPos" style="height: 50%" >
 						           				<?php if($versiPos){?>
 						           				<div class="col-sm-12">
							           				<h1 class="text-center"><b><?php echo str_replace("Company:","",$Report_details[0]);?></b></h1>
					                 				<h3 class="text-center"><?php echo $Report_details[1]; ?></h3>
					                 				<h3 class="text-center"><?php echo $Report_details[2]; ?></h3>
							           			</div>
							           			<div class="report_underline"></div>
							           			<div class="clearfix10"></div>
							           			
												<pre>
												<div class = 'versiPospre'>
												<table id = "pdf_table" style="margin: 0px; padding: 0px; cellspacing: 0px;">		
														<tr style="margin: 0px; padding: 0px; line-height: 15px; " >
								                          <td style="margin: 0px; padding: 0px; display: none">
								                          	<h1 ><b><?php echo str_replace("Company:","",$Report_details[0])?></b></h1></td>
								                       </tr>
								                      <tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px; display: none"><h3 ><b><?php echo $Report_details[1]?></b></h3></td></tr>
								                      <tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px; display: none"><h3><b><?php echo $Report_details[2]?></b></h3></td></tr>
													<?php
														foreach ($versiPos as $versipos) {  
									                          $ac = array_filter($versipos->ReportTxt);             
									                                  for ($i=0; $i <sizeof($versipos->ReportTxt) ; $i++) {  
									                                      if($i >=1 ){                
									                                          if (trim($versipos->ReportTxt[$i-1]) == '' && trim($versipos->ReportTxt[$i+1]) == '' ) {
									                                              
									                                              if(trim($versipos->ReportTxt[$i]) != ''){
									                                                  echo  '<tr style="margin: 0px; padding: 0px; line-height: 15px;" >
									                                                          <td style="font-weight:bold;font-size: 15px;">
									                                                              <h5 class = "for_pdf" style="font-weight:bold;font-family:courier; font-size: 18px; margin : 0px;">' . trim($versipos->ReportTxt[$i]) . '</h5>
									                                                          </td>
									                                                        </tr>'  ;
									                                              }
									                                          } 
									                                          elseif(trim($versipos->ReportTxt[$i]) !=''){
									                                               echo  '<tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px;"><span class = "for_pdf" style="font-size: 16px; font-family:courier; padding : 0;">' . $versipos->ReportTxt[$i] .  ' </span> <br/> </td></tr>' ;
									                                          }
									                                      }
									                                    }      
									                              }    
														?>
															</table>
													</div>
												</pre>
											<?php }
												elseif($error){
													echo $error;
												}
											else{?>
												<center>
													<h3>No Reports Available</h3>
												</center>
											<?php }?>
 						           			 </div>
											
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
								           						$date_data[0] = array('report_id' => 1,'name' => 'Today','portfolio_id' => 1);
								           						$date_data[1] = array('report_id' => 2 ,'name' => 'YESTERDAY','portfolio_id' => 1);
								           						$date_data[2] = array('report_id' => 7 ,'name' => 'LAST 7 DAYS','portfolio_id' => 1);
								           						$date_data[3] = array('report_id' => 30 ,'name' => 'LAST 30 DAYS ','portfolio_id' => 1);
								           						// $date_data[4] = array('report_id' => 0 ,'name' => 'CUSTOM ','portfolio_id' => 1);
								           						$date_data[4] = array('report_id' => 5 ,'name' => 'CUSTOM','portfolio_id' => 1); ?>
							           						    
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
						          					  <div class="dropdown exp" style="clear:both;">
													    <button class="btn btn-default dropdown btn_export" type="button" id="menu1" data-toggle="dropdown">EXPORT</button>
													    <ul class="dropdown-menu export_design" role="menu" aria-labelledby="menu1">
													      <li role=""><a id = "execl" role="menuitem" tabindex="-1" href="#">Execl</a></li>
													      <li role=""><a id = "csv" role="menuitem" tabindex="-1" href="#">CSV</a></li>
													      <li role=""><a class = "pdf" id = "VersiEATS_pdf" role="menuitem" tabindex="-1" href="#">PDF</a></li>
													      <li role=""><a class = "print" id = "versiEATS_print" role="menuitem" tabindex="-1" href="#">Print</a></li>
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
															<tr class="tr_td_border_complete">
																<td></td>
																<td></td>
																<td><b>Gross Total</b></td>
																<?php 
																$gross_total = 0; 
																// echo "<pre>"; print_r($reports_data[0]); exit('aed');  
																foreach ($reports_data[0] as $gross){  
																 if($gross[0] == 'Cash' ||  $gross[0] == 'Card' ) {
																 	$gross_total += $gross['SUM(total_w_tax)']; } 
																  } ?>
																<td>
																	<span class="pull-left">$</span> 
																	<span class="pull-right"><b><?php echo number_format(round($gross_total, 2),2); ?></b></span>
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
																				echo number_format(round($rd['SUM(total_w_tax)'], 2),2);
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
																<td><span class="pull-left">$</span> <span class="pull-right"><b><?php echo number_format(round($gross_total - $tol  , 2), 2)?></b> </span></td>
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







<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Email</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
      	<p>Send report to following.</p> 
      	<form id = 'send_email' name = 'send_email'>
	       <span>
	      		<label>To: </label>
	      		<input type="email" name="to" id ="to" class="form-control">
	      		<span id="email_error" style="display: none;" class="text-danger">Please Enter an Email Address.</span>
	      	</span>
	      	<small> You can add comma seperated multiple emails.</small>
<!-- 	      	<span>
		      	<label>CC: </label>
		      	<input type="email" name="cc" id ="cc" class="form-control" >
	        </span> -->
      	</form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" id ='send_email_button'  class="btn btn-success" data-dismiss="modal">Send</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

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
<!--<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<!-- <script src="<?php //echo base_url();?>assets/plugins/datatables/datatables.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.debug.js"></script>
<!-- D:\xampp\htdocs\versipos\assets\plugins\datatables\datatables.js -->
<!-- <script src="http://www.csvscript.com/dev/html5csv.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
<script >

//$( document ).ready(function() {
	 
	//document.getElementById('day').onchange = onChangeDay;
	//function onChangeDay(e){
	
$( "#day" ).change(function() {
	// alert('hereee'); 
 	if($(this).val() == 5){
		
				
		$('#day').after('<div id = "customDateDiv"> From : <input type="text" id = "datefrom1"  name = "datefrom" class="form-control "> To : <input type="text" id = "dateto1" name = "dateto" class="form-control "></div>');	
		$('#datefrom1').datepicker({
			onSelect: function () {
				$('#datefrom').focus();
				
			}
		});
		$('#dateto1').datepicker({
			onSelect: function () {
				$('#dateto').focus();
			}
		});
 	}  
 	else{
 		$("#customDateDiv").remove();
 	}

});	
//} 
//});
	
	
//	$( document ).ready(function() {
	 
//	document.getElementById('api_day').onchange = onChange;
//	function onChange(e){
	
	$( "#api_day" ).change(function() {
	// alert('ko');
 	
	if($(this).val() == 7){
 		if($('#apiCustomDateDiv').length){
 			$("#apiCustomDateDiv").remove(); 
			//  clear: both;
    //float: left;
    //position: absolute;
			$('#api_day').after('<div style = " clear: both; float: left; position: absolute; " id = "apiCustomDateDiv"> Select Date: <input type="text" id = "datefrom" name = "datefrom" class="form-control datepicker"></div>');
					 $('#datefrom11').datepicker({
					  onSelect: function () {
							$('#datefrom').focus();
					}
				  });
 			}
 		else
		{
			$('#api_day').after('<div id = "apiCustomDateDiv"> Select Date: <input type="text" id = "datefrom" name = "datefrom" class="form-control datepicker"></div>');	
					 $('#datefrom').datepicker({
						  onSelect: function () {
								$('#datefrom').focus();
						}
					  });
			}
 		}
 	else if($(this).val() == 8){
			if($('#apiCustomDateDiv').length){
				$("#apiCustomDateDiv").remove();
				$('#api_day').after('<div id = "apiCustomDateDiv"> From : <input type="text" id = "datefrom" name = "datefrom" class="form-control datepicker"> To : <input type="text" id = "dateto" name = "dateto" class="form-control datepicker"></div>');	
				 
				$('#datefrom').datepicker({
						  onSelect: function () {
								$('#datefrom').focus();
						}
					  });
				$('#dateto').datepicker({
						  onSelect: function () {
								$('#dateto').focus();
						}
					  });
			}
			else{
				$('#api_day').after('<div id = "apiCustomDateDiv"> From : <input type="text" id = "datefrom" name = "datefrom" class="form-control datepicker"> To : <input type="text" id = "dateto" name = "dateto" class="form-control datepicker"></div>');	
				 $('#datefrom').datepicker({
						  onSelect: function () {
								$('#datefrom').focus();
						}
					  });
				$('#dateto').datepicker({
						  onSelect: function () {
								$('#dateto').focus();
						}
					  });
			}
		}    
 	else{
 		$("#apiCustomDateDiv").remove();
 	}
  

}); // end api_day
	
//}
	//});
	

	

	
	
	$('#versiPos_pdf').click(function () {
	
	var doc = new jsPDF();
	var specialElementHandlers = {
		'#ignoreContent': function (element, renderer){
			return true; 
		}
	};
		// var val ; 
		// if(this.id == 'versiPos_pdf'){
		// 	// val ='#content-for-export-versiPos' ; 
		// 	//val = $('#content-for-export-versiPos').html()
		// }
		// else{
		// 	val ='#content-for-export' ; 	
		// }
		val ='#versiPospre' ; 	
  //           html2canvas($(val).html(), {
  //               onrendered: function (canvas) {
  //                   var data = canvas.toDataURL();
  //                   var docDefinition = {
  //                       content: [{
  //                           image: data,
  //                           width: 500
  //                       }]
  //                   };
  //                   pdfMake.createPdf(docDefinition).download("Report.pdf");
  //               }
  //           });
   html2canvas(document.getElementById("versiPospre"), {
            onrendered: function(canvas) {

                var imgData = canvas.toDataURL('image/png');
                console.log('Report Image URL: '+imgData);
                var doc = new jsPDF('p', 'mm', [297, 210]); //210mm wide and 297mm high
                
                doc.addImage(imgData, 'PNG', 10, 10);
                doc.save('sample.pdf');
            }
        });
   


		// var html_code = $(".versiPospre").html();
			
				// $.ajax({
				// 	type: 'POST',
				// 	url:'<?php //echo base_url(); ?>clients/report_pdf',
				// 	data: {html_code:html_code},
				// 	success: function(data){
				// 		alert('inn');
				// 	// 	$("#to").val('');
				// 	// $("#message").show();

				// 	// $("#message").fadeOut(5000);
				// 	},
				// });
	//var wit = $('#content-for-export-versiPos').html();

	




	// var wit = $(".versiPospre").html();

	 // var pdf = new jsPDF('p', 'pt', 'letter');
  //   source = $('#pdf_table')[0];
  //   specialElementHandlers = {
  //       '#editor': function (element, renderer) {
  //           return true
  //       }
  //   };



 //    margins = {
 //        top: 10,
 //        bottom: 60,
 //        left: 50,
 //        width: 700
 //    };
	// pdf.setFont('courier');
	// pdf.setFontSize(14);
 //    pdf.fromHTML(
 //    source, 
 //    margins.left, 
 //    margins.top, { 
 //        'width': margins.width, 
 //        'elementHandlers': specialElementHandlers
 //    },
 //    function (dispose) {
 //        pdf.save('versiPos legacy Report.pdf');
 //    }, margins);
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
	
	
$("#search_default").on("keyup click input", function () {
var val = $(this).val();
if (val.length) {
$('.pl-10 .group_search').hide().filter(function () {
return $(this).text().toLowerCase().indexOf(val.toLowerCase()) != -1;
}).show();
$('.pl-10 .preset_search').hide().filter(function () {
return $(this).text().toLowerCase().indexOf(val.toLowerCase()) != -1;
}).show();
}
else {
$('.pl-10 .group_search').show();
$('.pl-10 .preset_search').show();
}
});


$( ".abc" ).click(function() {
			//alert('here'); 
    setTimeout(
  function() {
	 if($(".rel").hasClass("open")){
		 $( ".rel" ).removeClass( "open" );		
		 $( ".rel" ).addClass( "close" );
	 }
	 else{
	 	 $( ".rel" ).addClass( "open" );
		 $( ".rel" ).removeClass( "close" );	
	 }
 
  },
  300);
});
	
$( "#content-for-export-versiPos" ).click(function() {
			//alert('here'); 
   if($(".rel").hasClass("open")){
		 $( ".rel" ).removeClass( "open" );		
		 $( ".rel" ).addClass( "close" );
	 }
});	
	
	
$( "#restaurant_name_versiPos" ).change(function() {
  	var id = $(this).val();  
	//alert($(this).val()); 
	$.ajax({
		type:'POST',
		url: '<?php echo base_url();?>clients/get_versipos_listing',
		data:{id:id},
		success:function(data1){
			var dataa = $.parseJSON(data1);
			// console.log(dataa);
			//alert('j'); 
			var data = dataa['repo_list'];
			var defaults ; 
			if(dataa['defult']){
				if(dataa['defult']['filters'] !=''){
					defaults = $.parseJSON(dataa['defult']['filters']);	
				}
				
			}
			
			// defaults = defaults.split(',');
			//alert(defaults[0]);
			console.log(defaults); 
			$("#group").empty();
			// $(".dropdown-list").append('<div class="input-search text-center"><input type="text"><span class="span1"><a id = "save_as_default" href="javascript:void(0)"  class= "cl_save_as_default">Save as default</a></span> | <span class="span1"><a href="#" id = "clear_default">Clear</a></span></div><div class="alert alert-success" id="success_alert" style="display:none;"> Filters Successfuly Added</div><div class="heading">Group Report</div>')
			// $(".dropdown-list").append('<div class="pl-10">'); 
			var im = 1;

			$.each(data , function(i){
				if(data[i].Type =='group'){
					var ch  = ''; 
					if(defaults){
					$.each(defaults , function(s){
						// alert(data[i].ReportID); 
						// 	alert(defaults);
						if(data[i].ReportID ==  defaults[s]){ 
							// alert(data[i].ReportID); 
							// alert(defaults);
							ch = "checked" ; 
							return false ;
						}
					});
					}
					//alert(ch);
				// if($.inArray(data[i].ReportID, defaults)){  ;alert(data[i].ReportID);
			// }

					 $("#group").append('<div class="checkbox group_search"><input type="checkbox" name="checkboxes1[]" class="check checkbox-custom versi_check" id="checkbox-custom_0'+im+'" value ="'+ $.trim(data[i].ReportID) +'" '+ ch +'  /><label style = "margin-left:18px; font-size:14px;" for="checkbox-custom_0'+ im +'" class="checkbox-custom-label">'+ $.trim(data[i].Name) + '</label></div>'); 
					}	im++;
			}); 
			// $(".dropdown-list").append('</div><div class="divider"></div><div class="heading">Present Report</div><div class="pl-10">')
			$("#preset").empty();
			var imi = 1;
			$.each(data , function(i){
				if(data[i].Type =='preset'){ 
					var chh  = ''; 
					if(defaults){
						$.each(defaults , function(t){
							if(data[i].ReportID ==  defaults[t]){ 
								//alert(data[i].ReportID); 
								chh = "checked" ; 
								return false ;
							}
						});
					}
					 $("#preset").append('<div class="checkbox preset_search"><input type="checkbox" name="checkboxes1[]" class="check checkbox-custom versi_check" id="checkbox-custom_0'+imi+'" value ="'+ $.trim(data[i].ReportID) +'"' + chh +' /><label style ="margin-left:18px; font-size:14px;" for="checkbox-custom_0'+ imi +'" class="checkbox-custom-label">'+ $.trim(data[i].Name) + '</label></div>');
				} imi++ ;  });
			// $(".dropdown-list").append('</div>');
			checkboxDropdown('.dropdown');
		}
	});
});

	$("#save_as_default").click(function(){
		var selected = $( "#restaurant_name_versiPos option:selected" ).val();
		var a = [];
		$('input:checkbox.versi_check').each(function () 
			{
				if(this.checked){	
				 var p = $(this).val();
				 a.push(p);
				}

			}
		 );
// alert(selected); 
	$.ajax({
				type:'post',
				url:'<?php echo base_url()?>clients/versiPos_reports_default_filter',
				data:{'checkbox': a, 'run':'run', 'selected':selected},
				success:function(dt){
					$("#success_alert").show(); 
  					$("#success_alert").fadeOut(5000);	
				}
			});
});
	
$("#getReportVersiPossubmit").click(function(){
	
	jQuery(".loader_div").show();
	
	
	var formdata = $('#getReportVersiPos').serialize();
	$.ajax({
		type:'POST',
		url: '<?php echo base_url();?>clients/get_versiPos_data',
		data:formdata,
		success:function(data1){
			// alert(data1); 
			jQuery(".loader_div").hide();
			var data1 = $.parseJSON(data1);
			
			$("#content-for-export-versiPos").html(data1);
			// // alert(data);
			// $("#ajaxcal").fadeOut("slow");
		}
	});
});
	
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


// $('#pdf').click(function () {   
//    alert('inn'); 
//     doc.fromHTML($('#content-for-export').html(), 15, 15, {
//         'width': 170,
//             'elementHandlers': specialElementHandlers
//     });
//     doc.save('sample-file.pdf');
// });





// $( "#api_day" ).change(function() {
// 	// alert('in'); 
//  // 	alert($(this).val()); 
//  	if($(this).val() == 8){
// 		$('#api_day').after('<div id = "apiCustomDateDiv"> From : <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"> To : <input type="date" id = "dateto" name = "dateto" class="form-control datepicker"></div>');
//  		// alert($(this).val());
//  		// alert( "Handler for .change() called." );	
//  	}  
//  	else{
//  		$("#apiCustomDateDiv").remove();
//  		// $("#dateto").remove();
//  	}
  

// });


//onclick function to get runed report; 
$("#getReportsubmit").click(function(){
//	alert('i m inn'); 
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

<script>

function checkboxDropdown(el) {
var $el = $(el)

function updateStatus(label, result) {
	// alert(label);
if(!result.length) {
label.html('Select Options');
}
};

$el.each(function(i, element) {

var $list = $(this).find('.dropdown-list');
var $label; 

// if($(this).find('.dropdown-label') == ''){
// 	$label = '';
// }
// else{
	$label = $(this).find('.dropdown-label');	
// }

var $checkAll = $(this).find('.check-all'),
$inputs = $(this).find('.check'),
defaultChecked = $(this).find('input[type=checkbox]:checked'),
result = [];

updateStatus($label, result);
if(defaultChecked.length) {
defaultChecked.each(function () {
result.push($(this).next().text());
$label.html(result.join(", "));
});
}

$label.on('click', '.abc' , function(){
$(this).toggleClass('open');
});

$checkAll.on('change', function() {
var checked = $(this).is(':checked');
var checkedText = $(this).next().text();
result = [];
if(checked) {
result.push(checkedText);
$label.html(result);
$inputs.prop('checked', false);
}else{
$label.html(result);
}
updateStatus($label, result);
});

$inputs.on('change', function() {

var checked = $(this).is(':checked');
var checkedText = $(this).next().text();

if($checkAll.is(':checked')) {
result = [];
}
if(checked) {
	if($('.abc').html() === ''){
		result = [];
		$('.abc').html('Select Options');
	}
result.push(checkedText);
$label.html(result.join(", "));
$checkAll.prop('checked', false);
}else{
let index = result.indexOf(checkedText);
if (index >= 0) {
result.splice(index, 1);
}
$label.html(result.join(", "));
}
updateStatus($label, result);
});

$(document).on('click touchstart', function(e){
if(!$(e.target).closest($(this)).length) {
$(this).removeClass('open');
}
});
});
};
checkboxDropdown('.dropdown');



$("#clear_default").click(function(){
	$('.versi_check').prop('checked', false);
	 $('label[class*=abc]').text('');
});
$('#send_email_button').click(function(e){

		var to = $("#to").val();
		if(to != '')
		{
			$("#myModal").prop('data-dismiss','modal');
			var html_code = $(".versiPospre").html();
			
				$.ajax({
					type: 'POST',
					url:'<?php echo base_url(); ?>clients/report_email_send',
					data: {to:to,html_code:html_code},
					success: function(){
					$("#to").val('');
					$("#message").show();

					$("#message").fadeOut(5000);
					},
				});
		}
		else
			{
				$("#email_error").show();
				e.preventDefault();
			} 
});

// var doc = new jsPDF();

// var specialElementHandlers = {
// '#editor': function(element, renderer) {
// return true;
// }
// };

// $('#cmd').click(function() {
// doc.fromHTML($('#content').html(), 15, 15, {
// 'width': 170,
// 'elementHandlers': specialElementHandlers
// });
// doc.save('preliminary-file.pdf');
// });

	
</script> 





