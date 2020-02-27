<?php//// print_r($default_support); exit; ?>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}
.switch {
background-color: #fff;
border: 1px solid #dfdfdf;
    border-top-color: rgb(223, 223, 223);
    border-right-color: rgb(223, 223, 223);
    border-bottom-color: rgb(223, 223, 223);
    border-left-color: rgb(223, 223, 223);
border-radius: 20px;
cursor: pointer;

vertical-align: middle;

-moz-user-select: none;
-khtml-user-select: none;
-webkit-user-select: none;
-ms-user-select: none;
user-select: none;
box-sizing: content-box;
background-clip: content-box;
}
.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  
}

input:checked + .slider.blue {
  background-color: #2196F3;
}

input:checked + .slider.green {
  background-color: #1bb99a;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            
                            <h4 class="page-title">Real Time Actionable Data (RAD)</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

				
			<div class="alert alert-success" id="success_alert" style="display:none;">Setting  Updated</div>
                <div class="row">
				
                    <div class="col-sm-6">
                        <div class="card-box">
                            <h4 class="header-title m-t-0">Support</h4>
                            <p class="text-muted font-14 m-b-25">
                               Receive notifications whenever we have a support ticket for your business, and choose the way you would like to receive those notifications.
                            </p>
								 <span>SMS</span>
								<label class="switch">
								 
								  <input type="checkbox" name = "sms_alert_support" id = "sms_alert_support" onchange = "setting_sending(this)" 
									<?php foreach($alerts as $al){
										if($al['name'] =="sms_alert_support" && $al['value']==1){echo checked;}
										else{
										}
									};?> />
								  <span class="slider blue round"></span>
								</label>
							
								<span>Email</span>
								<label class="switch"  >
								  <input type="checkbox" name = "email_alert_support" id = "email_alert_support" onchange = "setting_sending(this)" 
										<?php
											if(isset($default_support))
											{
												 echo 'checked';
											}
											else
											{	
												foreach($alerts as $al)
												{
													if($al['name'] =="email_alert_support" && $al['value']==1){echo 'checked';}
													else{}
												}	
											}	
										?>
									/>
								  <span class="slider green round" ></span>
								</label>
							
                        </div>
					</div>


                    <div class="col-sm-6">
                        <div class="card-box">
                            <h4 class="header-title m-t-0">Proposals</h4>
                            <p class="text-muted font-14 m-b-25">
                              Whenever we have a new proposal for you, we’ll send you a notification to your phone or email so you can check it at your own convenience. 
                            </p>
							<span>SMS</span>
							<label class="switch">
							  <input type="checkbox" name = "sms_alert_proposal" id = "sms_alert_proposal" onchange = "setting_sending(this)" 
										<?php foreach($alerts as $al){
									if($al['name'] =="sms_alert_proposal" && $al['value']==1){echo 'checked';}
									else{	}
								;}	?>
									/>
							  <span class="slider blue round"></span> 
							</label>
							<span>Email</span>
							<label class="switch">
							  <input type="checkbox" name = "email_alert_proposal" id = "email_alert_proposal" onchange = "setting_sending(this)" 
									<?php 
									if($default_proposal)
									{
										echo 'checked';
									}
									else
									{
										foreach($alerts as $al)
										{
											if($al['name'] =="email_alert_proposal" && $al['value']==1){echo 'checked';}
											else{	}
										;}
									;}
									?>
								/>
							  <span class="slider green round"></span>
							</label>
						</div>
                    </div>
                </div>
                <!-- end row -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
<script>
function setting_sending (i)
	{
			var id = i.id;
			var value; 
		  if (document.getElementById(id).checked) {
				value = 1;
			} else {
				value = 0;
			}
		 $.ajax({ 
			 type : 'post',
			 url : '<?php echo base_url();?>clients/rad',
			 data: {value : value, name : id},
				success:function(dt){
					$("#success_alert").show();	
					$("#success_alert").fadeOut(3000);
				}		
			
		 });		
		
	}
</script>

       