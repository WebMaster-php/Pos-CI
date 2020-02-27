

<?php 
// echo "<pre>"; echo str_replace("Company:","",$versiPos[0]); //print_r(str_replace($versiPos[0],'Company:')); 
// echo "<pre>"; print_r($Report_detial);  exit('ji'); //strpos  ?>
                    
                      
                        <div class="col-sm-12">
                 				<h1 class="text-center"><b><?php echo str_replace("Company:","",$Report_details[0]);?></b></h1>
                 				<h3 class="text-center"><?php echo $Report_details[1]; ?></h3>
                 				<h3 class="text-center"><?php echo $Report_details[2]; ?></h3>
                 			</div>
                 			<div class="report_underline"></div>
                 			<div class="clear10"></div>
                       <pre>
                    <div class = 'versiPospre'>   
                    <table id = "pdf_table" style="margin: 0px; padding: 0px; cellspacing: 0px;"  >   
                       
                       <tr style="margin: 0px; padding: 0px; line-height: 15px; " >
                          <td style="margin: 0px; padding: 0px; display: none"><h1 style="font-family:courier;"><b><?php echo str_replace("Company:","",$Report_details[0])?></b></h1></td>
                       </tr>
                      <tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px; display: none"><h3 style="font-family:courier;"><b><?php echo $Report_details[1]?></b></h3></td></tr>
                      <tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px; display: none"><h3 style="font-family:courier;"><b><?php echo $Report_details[2]?></b></h3></td></tr> 
          				      <?php
					//echo "<pre>"; print_r($versiPos);  	 exit('hhhhheeeeerrrrr'); 
					if(!is_array($versiPos) || $versiPos == '' ){
						echo '<center><h3>Server is busy Try again </h3></center>'; 
					}
					else{
                          foreach ($versiPos as $versipos) {  
                          $ac = array_filter($versipos->ReportTxt);             
                                  for ($i=0; $i <sizeof($versipos->ReportTxt) ; $i++) {  
                                      if($i >=1 ){                
                                          if (trim($versipos->ReportTxt[$i-1]) == '' && trim($versipos->ReportTxt[$i+1]) == '' ) {
                                              
                                              if(trim($versipos->ReportTxt[$i]) != ''){
                                                  // echo '<br>'. '<h5 style="font-weight:bold;font-size: 15px; margin-left : 50px;">' . trim($versipos->ReportTxt[$i]) . '</h5>'  ;
                                                  echo  '<tr style="margin: 0px; padding: 0px; line-height: 15px;" >
                                                          <td style="font-weight:bold;font-size: 15px;">
                                                              <h5 class = "for_pdf" style="font-weight:bold;font-size: 15px; margin : 0px;font-family:courier;">' . trim($versipos->ReportTxt[$i]) . '</h5>
                                                          </td>
                                                        </tr>'  ;
                                              }
                                          } 
                                          elseif(trim($versipos->ReportTxt[$i]) !=''){
                                               echo  '<tr style="margin: 0px; padding: 0px; line-height: 15px;" ><td style="margin: 0px; padding: 0px;"><span class = "for_pdf" style="font-size: 14px; padding : 0; font-family:courier;">' . $versipos->ReportTxt[$i] .  ' </span> <br/> </td></tr>' ;
                                               // echo  '<span style="font-size: 14px; margin-left : 50px;">' . $versipos->ReportTxt[$i] .  '</span>'.  "<br>" ;        
                                          }
                                      }
                                    }      
                                // }
                              }    

							}
                          ?>
                  </table>
                    </div> 
                </pre>

