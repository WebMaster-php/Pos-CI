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
                                        <input id="turn_off_pickup" type="checkbox" name="turn_off_pickup" <?php if(isset($all['turn_off_pickup']) && $all['turn_off_pickup'] == 1){ echo "checked";} ?> value="1">
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
                                        <input id="turn_off_delivery" type="checkbox" name="turn_off_delivery" <?php if(isset($all['turn_off_delivery']) && $all['turn_off_delivery'] == 1){ echo "checked";} ?> value="1">
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
                                    <select class="form-control def" name="temp_pickup_interval">
                                        <option value="0" <?php  if($all['temp_pickup_interval'] - $all['dp_interval'] == 0){ echo 'selected';}?> >None</option>
                                        <option value="15" <?php if($all['temp_pickup_interval'] - $all['dp_interval']== 15){ echo 'selected';}?> > 15 Min</option>
                                        <option value="30" <?php if($all['temp_pickup_interval'] - $all['dp_interval'] == 30){ echo 'selected';}?> > 30 Min</option>
                                        <option value="45" <?php if($all['temp_pickup_interval'] - $all['dp_interval']== 45){ echo 'selected';}?> > 45 Min</option>
                                        <option value="60" <?php if($all['temp_pickup_interval'] - $all['dp_interval']== 60){ echo 'selected';}?> > 60 Min</option>
                                    </select>
                                    <span style = "margin-right: 5px; margin-left: 5px;">For the next</span>
                                     <select class="form-control def" name="temp_pickup_next_time">
                                        <option value="0" <?php if($all['temp_pickup_next_time'] == 0){ echo 'selected';}?> >None</option>
                                        <option value="30" <?php if($all['temp_pickup_next_time'] == 30){ echo 'selected';}?> > 30 Min</option>
                                        <option value="60" <?php if($all['temp_pickup_next_time'] == 60){ echo 'selected';}?> > 60 Min</option>
                                        <option value="90" <?php if($all['temp_pickup_next_time'] == 90){ echo 'selected';}?> > 90 Min</option>
                                        <option value="120" <?php if($all['temp_pickup_next_time'] == 120){ echo 'selected';}?> > 120 Min</option>
                                    </select>
                                </div>
                             </div>
                         <?php }
                         if($all['service'] == 1 || $all['service'] == 3 ){?>
                            <div class="col-sm-12  row  mb-20">
                                <div class="col-sm-6 col-md-4 p0">
                                    <b>Your Default delivery interval is<br><?php echo $all['dd_interval']?> Min</b>
                                </div>
                                <div class="form-group col-sm-6  col-md-8 ghi">
                                    <select class="form-control def" name="temp_delivery_interval">
                                        <option value="0" <?php  if($all['temp_delivery_interval'] - $all['dd_interval']== 0){ echo 'selected';}?> >None</option>
                                        <option value="15" <?php if($all['temp_delivery_interval'] - $all['dd_interval']== 15){ echo 'selected';}?> >15 Min</option>
                                        <option value="30" <?php if($all['temp_delivery_interval'] - $all['dd_interval']== 30){ echo 'selected';}?> >30 Min</option>
                                        <option value="45" <?php if($all['temp_delivery_interval'] - $all['dd_interval']== 45){ echo 'selected';}?> >45 Min</option>
                                        <option value="60" <?php if($all['temp_delivery_interval'] - $all['dd_interval']== 60){ echo 'selected';}?> >60 Min</option>
                                    </select>
                                    <span style = "margin-right: 5px; margin-left: 5px;">For the next</span>
                                    <select class="form-control def" name="temp_delivery_next_time">
                                            <option value="0" <?php if($all['temp_delivery_next_time'] == 0){ echo 'selected';}?> >None</option>
                                            <option value="30" <?php if($all['temp_delivery_next_time'] == 30){ echo 'selected';}?> >30 Min</option>
                                            <option value="60" <?php if($all['temp_delivery_next_time'] == 60){ echo 'selected';}?> > 60 Min</option>
                                            <option value="90" <?php if($all['temp_delivery_next_time'] == 90){ echo 'selected';}?> > 90 Min</option>
                                            <option value="120" <?php if($all['temp_delivery_next_time'] == 120){ echo 'selected';}?> >120 Min</option>
                                    </select>

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

<script type="text/javascript">
        $('#merchants_turn').change(function(){
        // alert('jijiji');
        // alert(this.id);  
        var option = $(this).val();
        if(option == 3){
            if($('#apiCustomDateDiv').length)
            {
                $('#apiCustomDateDiv').remove();
            $('#merchants_turn').after('<div style = " clear: both; float: left; " id = "apiCustomDateDiv"><label> Select Date:</label> <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"></div>');
            }
            else{
                $('#merchants_turn').after('<div style = " clear: both; float: left; " id = "apiCustomDateDiv"> <label> Select Date:</label> <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"></div>');   
            }
        }
        else if(option == 4){
            if($('#apiCustomDateDiv').length)
            {
                $('#apiCustomDateDiv').remove();
            $('#merchants_turn').after('<div id = "apiCustomDateDiv"> <label> From :</label> <input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"> <label> To :</label> <input type="date" id = "dateto" name = "dateto" class="form-control datepicker"></div>'); 
            }
            else{
                $('#merchants_turn').after('<div id = "apiCustomDateDiv"> <label> From :</label><input type="date" id = "datefrom" name = "datefrom" class="form-control datepicker"> <label> To :</label> <input type="date" id = "dateto" name = "dateto" class="form-control datepicker"></div>');   
            }

        }
        else if(option == 5){
             if($('#apiCustomDateDiv').length)
            {
                $('#apiCustomDateDiv').remove();
            $('#merchants_turn').after('<div id = "apiCustomDateDiv"><input size="10" type="number" id = "hours" name = "hours" class="form-control " style= " width: 113px !important; float: left;"> <input type="text" size="6" class="form-control interval" placeholder="Hours"></div>'); 
            }
            else{
                 $('#merchants_turn').after('<div id = "apiCustomDateDiv"><input size="10" type="number" id = "hours" name = "hours" placeholder= "Hours" class="form-control style= " width: 113px !important; float: left;""></div>');    
            }

        }
        else{
            $('#apiCustomDateDiv').remove();
        }
    });
</script>
