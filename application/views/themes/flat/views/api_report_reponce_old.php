
	            <div class="col-sm-12">
       				<h1 class="text-center"><b><?php echo $versiPos[0]?></b></h1>
       				<h3 class="text-center"><?php echo $versiPos[1]; ?></h3>
       				<h3 class="text-center"><?php echo $versiPos[2]; ?></h3>
       			</div>
       			<div class="report_underline"></div>
       			<div class="clear10"></div>
       			<pre >
                <div class = 'versiPospre'>   
				<?php
                    for ($i=0; $i <sizeof($versiPos) ; $i++) {  
                        if($i >=4 ){                
                                if (trim($versiPos[$i-1]) == '' && trim($versiPos[$i+1]) == '' ) {
                                    if(trim($versiPos[$i]) != ''){
                                        echo '<br>'. '<h5 style="font-weight:bold;font-size: 15px; margin-left : 50px;">' . trim($versiPos[$i]) . '</h5>'  .'<br>';
                                    }
                                } 
                                else{
                                     echo  '<span style="font-size: 14px; margin-left : 50px;">' . $versiPos[$i] .  '</span>'.  "<br>" ;        
                                }
                            }
                    }
                ?>
                </div> 
                </pre>

