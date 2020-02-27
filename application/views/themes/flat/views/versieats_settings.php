<?php 
// print_r($merchant_holiday) ;

// print_r( $merchant_holiday['option_value']); 
// exit('kkkkk'); ?>
<?php

// echo "<pre>"; print_r($all); echo "out"; 
// print_r(json_decode($option['option_value'], true)); 
// echo $option['option_value']; 
// exit('jkjjjhdjfhdshs');  
?>

<style>
    body{
        background: #f8f8f8;
    }
.span_padding{
	padding-left : 10px;
	padding-right : 10px;	
}
	
.ghi > .select-placeholder{ 
    width: 38%; 
}
.def button{
    background-color: #fff;
    border: 3px solid #eee;
}
.abc button{
        background-color: #fff;
        border: 3px solid #eee;
}
.pop-main{
        background: #fff;
    padding: 20px 30px;
    margin: 10px 0px;
    width: 100%;
    height: auto;
    float: left;
} 
.interval{width: 70px !important;float: left;}  
.interval2{width: 162px !important;float: left;} 
.form-group.row{width: 100%;} 
.form-group.row span{padding: 0,px 5px;} 
.mb-60{margin-bottom: 30px !important;}
.mb-20{margin-bottom: 20px !important;}
.checkbox label:after, 
.radio label:after {
    content: '';
    display: table;
    clear: both;
}

.checkbox .cr,
.radio .cr {
    position: relative;
    display: inline-block;
    border: 1px solid #a9a9a9;
    border-radius: .25em;
    width: 1.3em;
    height: 1.3em;
    float: left;
    margin-right: .5em;
}

.radio .cr {
    border-radius: 50%;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
    position: absolute;
    font-size: .8em;
    line-height: 0;
    top: 50%;
    left: 20%;
}

.radio .cr .cr-icon {
    margin-left: 0.04em;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
    display: none;
}
.checkbox label:before{display: none !important;}
.checkbox label input[type="checkbox"] + .cr > .cr-icon,
.radio label input[type="radio"] + .cr > .cr-icon {
    transform: scale(3) rotateZ(-20deg);
    opacity: 0;
    transition: all .3s ease-in;
}
.p0{
    padding: 0!important;
}
.sp-txt{
    margin-right: 5px;
    margin-left: 5px;
}
.checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
.radio label input[type="radio"]:checked + .cr > .cr-icon {
    transform: scale(1) rotateZ(0deg);
    opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled + .cr,
.radio label input[type="radio"]:disabled + .cr {
    opacity: .5;
}
.icon_color{
    color: #02c0ce !important;
}.save_btn_submit{
    background: #02c0ce !important;
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
                            <li class="breadcrumb-item active">Versieats</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Popular Settings</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="pop-main">
                    <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <div class="alert alert-success " style=" display: none;" id = "message"><strong>Success! </strong>Merchant setting updated successfully.</div>
                        <form class="form-inline" id="varsi_eat_data" action="versieats_settings">
                            <div class="col-sm-12 row mb-20">
                                <div class=" col-sm-6 col-md-4 p0">
                                    <b>Please Choose location:</b>
                                </div>
                                <div class=" col-sm-6 col-md-5 ">
                                     <?php 
                                  $selected = ''; 
                                    if(count($all_merchant) == 1 ){ $selected = $all_merchant[0]['merchant_id'] ; }
                                    echo render_select('merchants',$all_merchant,array('merchant_id','restaurant_name'),'',$selected,'','',false,'abc');  ?>
                                </div>
                                <div class="clear"></div>
                             </div>
                              <div id = "sub_view"  class=" row mb-21  mb-20 col-sm-12">
                              <div class=" row mb-20  mb-20 col-sm-12">
                                <div class="col-sm-6 col-md-4 p0">
                                    <b>Turn off restaurant</b>
                                </div>
                                <?php 
                                $optns[0] = ''; 
                                    if($option['option_value'] != 'no'){
                                        $optns = json_decode($option['option_value'], true);   
                                    } 
                                ?>
                                <div class=" col-sm-6 col-md-5 " id = "fine">
                                     <select class="form-control abc" name="merchants_turn" id= "merchants_turn"  >
                                        <option value="0" <?php if($option['option_value'] == 'no'){ echo 'selected';}?> >None</option>
                                        <option value="1" <?php if($optns[0] == 1){ echo 'selected';}?>> 1 day</option>
                                        <option value="2" <?php if($optns[0] == 2){ echo 'selected';}?> >1 hour</option>
                                        <option value="3" <?php if($optns[0] == 3){ echo 'selected';}?> >SELECT DATE</option>
                                        <option value="4" <?php if($optns[0] == 4){ echo 'selected';}?> >SELECT DATE RANGE</option>
                                        <option value="5" <?php if($optns[0] == 5){ echo 'selected';}?> >CUSTOM HOURS</option>
                                    </select>

                             <?php 
                                if($optns[0] == 3){ ?> 
                                    <div  id = "apiCustomDateDiv"> 
                                        <label>Select Date:</label> 
                                        <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker" value="<?php echo date('Y-m-d', strtotime($optns[1]) );?>">
                                    </div> 
                                <?php  }
                                if($optns[0] == 4){ ?> 
                                    <div  id = "apiCustomDateDiv"> 
                                        <label>From :</label>  
                                        <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker" value="<?php echo date('Y-m-d', strtotime($optns[1]) );?>"><label>To :</label>  
                                        <input type="date" id = "dateto" name = "dateto" class="form-control datepicker" value="<?php echo date('Y-m-d', strtotime($optns[2]) ); ?>">
                                    </div> 
                                    <?php }

                                if($optns[0] == 5){ 
                                    $t1 = strtotime(  $optns[2] );
                                    $t2 = strtotime(  $optns[1] );
                                    $diff = $t1 - $t2;
                                    $hours = $diff / ( 60 * 60 ); ?>
                                
                                     <div id = "apiCustomDateDiv">
                                        <input size="10" type="number" id = "hours" name = "hours"  placeholder= "Hours" class="form-control" value="<?php echo $hours; ?>"> 
                                    </div>
                                <?php }

                                
                                 
                                //}
                                ?>

                                </div>
                             </div>
                            <div class="row col-sm-12 mb-20">
                                <div class=" col-6 col-sm-6 col-md-4 p0">
                                    <b>Turn off Pickup?</b>
                                </div>
                                <div class=" col-6 col-sm-6 col-md-8">
                                    <div class="checkbox">
                                      <label>
                                        <input type="checkbox" name="turn_off_pickup" value="1"  <?php if($all['turn_off_pickup'] == 1){ echo "checked";}?>>
                                        <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                      </label>
                                    </div>
                                </div>
                             </div>
                            <div class=" row col-sm-12  mb-20">
                                <div class=" col-6 col-xs-6 col-sm-6 col-md-4 p0">
                                    <b>Turn off Delivery?</b>
                                </div>
                                <div class=" col-6 col-sm-6 col-md-8">
                                    <div class="checkbox">
                                      <label>
                                        <input type="checkbox" name="turn_off_delivery" value="1" <?php if($all['turn_off_delivery'] == 1){ echo "checked";}?>>
                                        <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                      </label>
                                    </div>
                                </div>
                             </div>
                             <?php if($all['service'] == 1 || $all['service'] == 2){?>
                            <div class=" row col-sm-12  mb-20">
                                <div class="col-sm-6 col-md-4 p0">
                                    <input type="hidden" name="dd_interval" value="<?php echo $all['dd_interval']?>">
                                    <input type="hidden" name="dp_interval" value="<?php echo $all['dp_interval']?>">
                                    <b>Your Default Pickup interval is<br><?php echo $all['dp_interval']?> Min</b>
                                </div>
                                <div class="form-group col-sm-6 col-md-8 ghi">
                                    <!-- <input type="text" name="pickup_interval_from" class="form-control interval" placeholder="None"> -->
                                    <?php
                                        $delivery_interval_from[0] = array('id' => 0 ,'name' => 'None','portfolio_id' => 1);
                                        $delivery_interval_from[1] = array('id' => 15 ,'name' => '15 Min','portfolio_id' => 1);
                                        $delivery_interval_from[2] = array('id' => 30 ,'name' => '30 Min','portfolio_id' => 1);
                                        $delivery_interval_from[3] = array('id' => 45 ,'name' => '45 Min','portfolio_id' => 1);
                                        $delivery_interval_from[4] = array('id' => 60 ,'name' => '60 Min','portfolio_id' => 1);
                                        $selected_temp_pickup_interval= '';

                                        if(isset($all['temp_pickup_interval'])){
                                            if($all['temp_pickup_interval'] == '' || $all['temp_pickup_interval'] == 0){
                                                $selected_temp_pickup_interval = 0;        
                                            }
                                            else{
                                                $selected_temp_pickup_interval = $all['temp_pickup_interval'] - $all['dp_interval'];           
                                            }
                                        }   
                                    echo render_select('temp_pickup_interval',$delivery_interval_from,array('id','name'),'',$selected_temp_pickup_interval,'','',false,'def', false);  ?>
                                    <span class="sp-txt">For the next</span>
                                    <?php
                                        $delivery_interval_to[0] = array('id' => 0 ,'name' => 'None');
                                        $delivery_interval_to[1] = array('id' => 30 ,'name' => '30 Min');
                                        $delivery_interval_to[2] = array('id' => 60 ,'name' => '60 Min');
                                        $delivery_interval_to[3] = array('id' => 90 ,'name' => '90 Min');
                                        $delivery_interval_to[4] = array('id' => 120 ,'name' => '120 Min');

                                        $selected_temp_pickup_next_time= '';
                                        if(isset($all['temp_pickup_next_time'])){
                                            $selected_temp_pickup_next_time = $all['temp_pickup_next_time'];           
                                        }   
                                    echo render_select('temp_pickup_next_time',$delivery_interval_to,array('id','name'),'',$selected_temp_pickup_next_time,'','',false,'def', false);  ?>
                                </div>
                             </div>
                             <?php }
                             if($all['service'] == 1 || $all['service'] == 3 ){?>
                            <div class="col-sm-12  row  mb-20">
                                <div class="col-sm-6 col-md-4 p0">
                                    <b>Your Default delivery interval is<br><?php echo $all['dd_interval']?> Min</b>
                                </div>
                                <div class="form-group col-sm-6  col-md-8 ghi">
                                    <?php 
                               
                                
                                $selected_temp_delivery_interval= '';
                                
                                if(isset($all['temp_delivery_interval'])){
                                    if($all['temp_delivery_interval'] == '' || $all['temp_delivery_interval'] == 0){
                                        $selected_temp_delivery_interval = 0;        
                                    }
                                    else{
                                        $selected_temp_delivery_interval = $all['temp_delivery_interval'] - $all['dd_interval'] ;           
                                    }
                                }   

                                 echo render_select('temp_delivery_interval',$delivery_interval_from,array('id','name'),'',$selected_temp_delivery_interval,'','',false,'def', false);  ?>
                                    <span class="sp-txt">For the next</span>
                                    <!-- <input type="text" name="delivery_interval_to" class="form-control interval" placeholder="None"> -->
                                    <?php
                                    
                                     $selected_temp_delivery_interval= '';
                                
                                    if(isset($all['temp_delivery_next_time'])){
                                        if($all['temp_delivery_next_time'] == '' || $all['temp_delivery_next_time'] == 0){
                                            $selected_temp_delivery_next_time = 1;        
                                        }
                                        else{
                                            $selected_temp_delivery_next_time = $all['temp_delivery_next_time'];           
                                        }
                                    }   

                                    echo render_select('temp_delivery_next_time',$delivery_interval_to,array('id','name'),'',$selected_temp_delivery_next_time,'','',false,'def', false);  ?>
                                </div>
                             </div>
                         <?php }?>
                                <div class="col-sm-12  row  mb-20">
                                    <div class="col-sm-6 col-md-4 p0">
                                        <b>Holidays: </b>
                                    </div>
                                    <div class="form-group col-sm-6  col-md-8 ghi holiday_list" >
                                        <?php if($merchant_holiday !=''){ $i = 1;
                                            $merchant_holiday_decoded = json_decode($merchant_holiday, true ); ?>
                                                <?php foreach($merchant_holiday_decoded as $holiday){?>
                                                <div class="holiday_row no-gutters">
                                                    <div class="form-group col-sm-12 py-2">
                                                        <input type="date" name="merchant_holiday[]" value="<?php echo $holiday ; ?>" class = "form-control datepicker">
                                                        <a href="javascript:;" class="remove_holiday"><i class="fa fa-minus-square fa-2x px-2 icon_color"></i></a>
                                                    </div>
                                                </div>
                                                <?php $i++; } ?>
                                        <?php  } 
                                        else{?>
                                                <div class="holiday_row no-gutters">
                                                    <div class="form-group col-sm-12 py-2">
                                                    <input type="date" name="merchant_holiday[]" class = "form-control datepicker">
                                                    </div>
                                                </div>

                                        <?php }?>
                                    </div>
                                    <div class="col-sm-6 col-md-4 p0"></div>
                                     <div class="form-group col-sm-6  col-md-8 ghi" >
                                            <div class="holiday_row no-gutters">
                                                <div class="form-group col-sm-12 py-2">
                                                <a href="javascript:;" class="add_new_holiday"><i class="fa fa-plus-square fa-2x  icon_color"></i></a>
                                                </div>
                                            </div>
                                        </div>     
                                </div>
                         </div>
                              <div class="form-group row  mb-20">
                                <div class="form-group col-sm-6 col-md-6">
                                    <input type="button" class="btn btn-success save_btn_submit" id="save" name="save" value="save">
                                </div>
                                
                             </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $('#merchants_turn').change(function(){
        // alert('jijiji');
        // alert(this.id);  
        var option = $(this).val();
        if(option == 3){
            if($('#apiCustomDateDiv').length)
            {
                $('#apiCustomDateDiv').remove();
            $('#merchants_turn').after('<div style = " clear: both; float: left; " id = "apiCustomDateDiv"> Select Date: <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"></div>');
            }
            else{
                $('#merchants_turn').after('<div style = " clear: both; float: left; " id = "apiCustomDateDiv"> Select Date: <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"></div>');   
            }
        }
        else if(option == 4){
            if($('#apiCustomDateDiv').length)
            {
                $('#apiCustomDateDiv').remove();
            $('#merchants_turn').after('<div id = "apiCustomDateDiv"> From : <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"> To : <input type="date" id = "dateto" name = "dateto" class="form-control datepicker"></div>'); 
            }
            else{
                $('#merchants_turn').after('<div id = "apiCustomDateDiv"> From : <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"> To : <input type="date" id = "dateto" name = "dateto" class="form-control datepicker"></div>');   
            }

        }
        else if(option == 5){
            if($('#apiCustomDateDiv').length)
            {
                $('#apiCustomDateDiv').remove();
            $('#merchants_turn').after('<div id = "apiCustomDateDiv"><input size="10" type="number" id = "hours" name = "hours" class="form-control interval2"> <input type="text" size="6" class="form-control interval" placeholder="Hours"></div>'); 
            }
            else{
                 $('#merchants_turn').after('<div id = "apiCustomDateDiv"><input size="10" type="number" id = "hours" name = "hours" placeholder= "Hours" class="form-control"></div>');    
            }

        }
        else{
            $('#apiCustomDateDiv').remove();
        }
    });

    $('#save').click(function(){
        //alert('dfdf');
        if($('#merchants').val() !=''){
            var form_data = $('#varsi_eat_data').serialize();
            $.ajax({
                type:'POST',
                url:'<?php echo base_url() ?>Clients/versieats_settings',
                data:{form_data:form_data},
                success:function(res){
                    $("#message").show();
                    $("#message").fadeOut(5000);
                }
            });    
        }
        
    });

    $('#merchants').change(function(){
        var val = $(this).val();
        $.ajax({
        type:'POST',
        url:'<?php echo base_url()?>clients/get_restaurant_data',
        data:{val:val},
        success:function(res){
            //alert(res); 
        var json = $.parseJSON(res);
        $('#sub_view').empty();
            $('#sub_view').html(json);
            }
            });
});

 $( document ).on( "click", ".add_new_holiday", function() {          
       var t='';
       t+='<div class="holiday_row no-gutters"> <div class="form-group col-sm-12 py-2">';
       t+='<input type="date" name="merchant_holiday[]" class = "form-control datepicker" >';
       t+='<a href="javascript:;" class="remove_holiday"><i class="fa fa-minus-square fa-2x px-2 icon_color"></i></a>';
       t+='</div> </div>';
       $(".holiday_list").append(t);
       // initDate();
   });   
 $( document ).on( "click", ".remove_holiday", function() {       
      var t=$(this).parent();
      t.remove();
   });   

</script>