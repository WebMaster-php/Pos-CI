<html>
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reports</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 5px;
  text-align: left;    
}
.reports_wrap{
	width: 100%;
}
.repoorts_heading h2, p{
	text-align: center;
}
.repoorts_heading p{
	margin-top: 5px;
	margin-bottom: 5px;
}
.repoorts_heading h2{
	margin-bottom: 5px;
}
.hr_styling{
	width: 20%;
	color: #000;
	margin-top: 15px;
}
.all_day_report p{
	margin-top:5px;
	margin-bottom: 5px;
}
.tables_section_1{
	float: left;
	width: 47%;
	padding: 15px;

}
.table_1 table{
	width: 70%;
	float: right;
}
.table_2 table{
	width: 70%;
	float: left;
}
.tables_section_1 caption{
	font-size: 18px;
	font-weight: bold;
	text-align: left;
}
.clear20{
	clear: both;
	height: 20px;
}
.sales_by_catagory caption{
	font-size: 18px;
	font-weight: bold;
	text-align: left;
	margin-left: -40px;
}
.sales_by_catagory table{
	width: 60%;
	margin:0 auto;
}
.sales_by_food_catagory table{
	width: 60%;
	margin:0 auto;
}
.sales_by_food_catagory caption{
	font-size: 18px;
	font-weight: bold;
	text-align: left;
	margin-left: -60px;
}
.sales_by_hourly_catagory caption{
	font-size: 18px;
	font-weight: bold;
	text-align: left;
	margin-left: -60px;
}
.sales_by_hourly_catagory table{
	width: 60%;
	margin:0 auto;
}


</style>
</head>
<body>
	<div class="reports_wrap" >
		<div class="repoorts_heading">
			<h2>Company: <?php echo $merchant[0]['restaurant_name']?> </h2>
			<p> Run Time: <?php echo date('m/d/Y h:i:A', strtotime($runtime)); ?></p>
			<p> Reporting On: <?php echo $reportingon; ?></p>
		</div>
		<div class="all_day_report">
			<hr class="hr_styling">
			<p><?php echo $r_of_day ; ?></p>
		</div>
<div class="tables_section_1">
<div class="table_1">
<table style="border-bottom: none; border-left: 0;">
	<caption>PAYMENT TOTALS</caption>
  <tr>
    <th width="2%">QTY</th>
    <th width="8%">&nbsp;</th>
    <th width="8%">Payment Type</th>
    <th width="8%">Amt Recieved</th>
  </tr>
    <?php //echo "<pre>"; print_r($reports_data[0]); exit('k');
        $tol = 0; 
        foreach ($reports_data[0] as $rd){  
        if($rd[0] !='Payment Tips'){
            $tol =  $tol+$rd['SUM(total_w_tax)']; }
        ?>
        <tr>
            <td style="text-align: right;"><?php echo $rd['count(order_id)']?></td>
            <td>&nbsp;</td>
            <td><?php echo $rd[0]?></td>
            <td><span style="margin-right: 15px;"> $ </span>
                <?php 
                    if($rd['SUM(total_w_tax)']){
                        echo round($rd['SUM(total_w_tax)'], 2);
                        } 
                    else{ echo '00.00';} ?>
            </td>
        </tr>
        
    <?php } ?>
   <tr>
   <td style="border: none;"> </td>
   <td style="border:none;"> </td>
    <td>TOTAL</td>
    <td><span style="margin-right: 15px;"> $ </span><?php echo round($tol , 2)?></td>
  </tr>

</table>
</div>
</div>
<div class="tables_section_1">
<div class="table_2">
<table>
	<caption>DISCOUNT TOTALS</caption>
  <tr>
    <th width="2%">QTY</th>
    <th width="15%;" style="text-align: center;">Name</th>
    <th width="8%"  style="text-align: center;">Amount</th>
  </tr>
   <?php
            $totaldiscount = 0;
            $totaldiscountqty =0;
            foreach($reports_data[4] as $rdiscount){ ?>
              <tr>
                <td align="right">
                    <?php $totaldiscountqty += $rdiscount['count(voucher_code)'];
                        echo $rdiscount['count(voucher_code)']?>   
                </td>
                <td><?php echo $rdiscount['voucher_code']?></td>
                <td><span style="float: left;"> $ </span> 
                    <span style="float:  right;"> 
                        <?php $totaldiscount +=$rdiscount['SUM(voucher_amount)'];
                              echo round($rdiscount['SUM(voucher_amount)'], 2) ; ?>
                    </span>
                </td>
              </tr>
         <?php ;} ?>

<tr>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
    <td><span style="float: left;">&nbsp; </span> <span style="float:  right;"> &nbsp; </span></td>
  </tr>
  <tr>
    <td align="right"><?php echo round($totaldiscountqty, 2)?></td>
    <td align="right;">Total</td>
    <td><span style="float: left;">$ </span> <span style="float:  right;"><?php echo round($totaldiscount, 2) ; ?> </span></td>
  </tr>
</table>
</div>
</div>

<div class="clear20"> </div>
<div class="sales_by_catagory">
<table style="border-bottom: none; border-left: 0; border-right:0;">
  <caption>SALES BY CATAGORY</caption>
    <?php $total_sales = 0; $total_qty = 0; 
    foreach($reports_data[1] as $rsales){
        $total_sales += $rsales['price'] ;
        $total_qty += $rsales['qty'];
        $tprice += $rsales['price'] +$rsales['tax']/100*$rsales['price'];
        $total_tax_by_cat = 0;
        }?>
  <tr>
    <th width="15%;" style="text-align: center;">Category</th>
    <th width="5%;" style="text-align: center;">Qty</th>
    <th width="15%;" style="text-align: center;">Total Sales</th>
    <th width="15%;" style="text-align: center;">Taxable</th>
    <th width="15%;" style="text-align: center;">NetSales</th>
    <th width="15%;" style="text-align: center;">Sales%</th>
  </tr>

    <?php foreach($reports_data[1] as $rsales){?>
         <tr>
            <td style="text-align: center;">* <?php echo $rsales['name']?></td>
            <td style="text-align: center;"><?php echo $rsales['qty']?></td>
            <td style="text-align: center;"><?php echo round($rsales['price'] + $rsales['tax']/100*$rsales['price'], 2)?></td>
            <td style="text-align: center;">
               <?php 
               echo round($rsales['tax']/100*$rsales['price'], 2) ; 
               $total_tax_by_cat +=$rsales['tax']/100*$rsales['price'] ?>
            </td>
            <td style="text-align: center;"><?php echo $rsales['price']?></td>
            <td style="text-align: center;"><?php echo round($rsales['qty']/ $total_qty * 100, 2);?>%</td>
          </tr>  
    <?php }?>
    <tr>
    <td style="text-align: center; border:none;">&nbsp;</td>
    <td style="text-align: center;"><?php echo $total_qty ;?></td>
    <td style="text-align: center;">$ <?php echo round($tprice, 2) ;?></td>
    <td style="text-align: center;">$ <?php echo round($total_tax_by_cat, 2) ;?></td>
    <td style="text-align: center;">$ <?php echo round($tprice - $total_tax_by_cat, 2);?></td>
    <td style="text-align: center;border:none;">&nbsp;</td>
  </tr>
 
</table>
</div>

<div class="clear20"> </div>
<div class="sales_by_food_catagory">
<table style="border-bottom: none; border-left: 0; border-right:0;">
  <caption>SALES BY FOOD BY CATAGORY</caption>
  <tr>
    <th width="15%;" style="text-align: center;">Category</th>
    <th width="15%;" style="text-align: center;">Item Price</th>
    <th width="5%;" style="text-align: center;">Qty</th>
    <th width="15%;" style="text-align: center;">Total Sales</th>
    <th width="15%;" style="text-align: center;">Taxable</th>
    <th width="15%;" style="text-align: center;">NetSales</th>
    <th width="15%;" style="text-align: center;">Sales%</th>
  </tr>
    <?php 
        $tcp = 0; $tcqty = 0; $total_tax_by_cat_items = 0;
        // echo "<pre>";  print_r($reports_data[2]); exit('by'); 
        foreach($reports_data[2] as $key2 => $ritems){ ?>
        <tr>
            <td style="text-align: center;">
                <b>* <?php echo $key2?></b><br>
                    <?php foreach($ritems as  $rt){
                        if($key2 == $rt['cat_name']){ 
                            echo $rt['item_name']; ?><br>
                    <?php } 
                }?>
            </td>
            <td style="text-align: center;">
                &nbsp; <br>
                <?php foreach($ritems as  $rt){ 
                    if($key2 == $rt['cat_name']){ ?>
                         $ <?php
                         $f = ltrim(rtrim($rt['item_price'], ']'), '['); 
                         $t = trim($f , '"');
                         // echo round($t); 
                         echo $t;
                         $tcqty += $rt['qty'] ; ?><br>
                    <?php } ?>
                <?php }?>
            </td> 
            <td style="text-align: center;">
                &nbsp; <br>
                <?php 
                foreach($ritems as  $rt){ 
                    if($key2 == $rt['cat_name']){ ?>
                         <?php echo $rt['qty']; 
                         $tcqty += $rt['qty'] ; ?><br>
                    <?php } ?>
                <?php }?>
            </td> 
            <td style="text-align: center;">
                &nbsp; <br>
                <?php foreach($ritems as  $rt){ 
                    if($key2 == $rt['cat_name']){ ?>
                        $ <?php echo round($rt['price'] + $rt['tax']/100*$rt['price'], 2); 
                           $tcp += $rt['price'] + $rt['tax']/100*$rt['price'];
                        ?><br>
                    <?php } ?>
                <?php }?>
            </td>
            <td style="text-align: center;">
                &nbsp; <br>
                <?php foreach($ritems as  $rt){ 
                    if($key2 == $rt['cat_name']){ ?>
                        $ <?php echo round($rt['tax']/100*$rt['price'], 2); 
                           $total_tax_by_cat_items += $rt['tax']/100*$rt['price'];
                        ?><br>
                    <?php } ?>
                <?php }?>
            </td>
            <td style="text-align: center;">
                &nbsp; <br>
                <?php foreach($ritems as  $rt){ 
                    if($key2 == $rt['cat_name']){ ?>
                        $ <?php echo $rt['price']; 
                           $tcp += $rt['price'];
                        ?><br>
                    <?php } ?>
                <?php }?>
                
            <td style="text-align: center;">
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
    <?php 
    }?>
</table>
</div>

<div class="clear20"> </div>
<div class="sales_by_hourly_catagory">
<table style="border-bottom: none; border-left: 0; border-right:0;">
  <caption>HOURLY SALES</caption>
  <tr>
    <th width="15%;" style="text-align: center;">Period</th>
    <?php foreach ($reports_data[3] as $hr) {?>
        <th width="5%;" style="text-align: center;"><?php echo $hr['openhoura']; ?></th>
    <?php }?>
  </tr>
  <tr>
    <td># ORD</td>
    <?php foreach ($reports_data[3] as $hr) {?>
        <td style="text-align: center;">
            <?php if($hr['count(order_id)'] != '' && $hr['count(order_id)'] != 0 ){echo $hr['count(order_id)'];} 
            else { echo '--';}?>    
        </td>
    <?php }?>
  </tr>
   <tr>
    <td>TOTAL</td>
    <?php foreach ($reports_data[3] as $hr) {?>
        <td style="text-align: center;">
            <?php if($hr['total_w_tax'] != '' && $hr['total_w_tax'] != 0 ){echo round($hr['total_w_tax'], 2);} 
            else { echo '--';}?>    
        </td>
    <?php }?>
  </tr>
   <tr>
        <td>NET</td>
        <?php foreach ($reports_data[3] as $hr) {?>
            <td style="text-align: center;">
                <?php if($hr['sub_total'] != '' && $hr['sub_total'] != 0 ){echo round($hr['sub_total'],2);} 
                else { echo '--';}?>    
            </td>
        <?php }?>
  </tr>
    
 
</table>
</div>
<!-- end of reports_wrap -->
</div>
</body>
</html>