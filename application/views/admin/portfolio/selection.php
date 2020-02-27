<?php init_head_2(); ?>
<style>
.center {
    margin: auto;
    width: 50%;
    border: 3px solid green;
    padding: 10px;
}

/**
*@author Bilal Designer
*/

.d-flex a{
	width: 300px;
}

.d-flex i {
    font-size: 50px;
    position: relative;
    top: 40px;
}

.d-flex p{
	font-size: 25px;
    font-weight: bold;
    margin-top: 60px;
} 

.d-flex{
	display: -webkit-inline-box!important;
    display: -ms-inline-flexbox!important;
    display: inline-flex!important;
    margin-right: 30px;
    vertical-align: middle;
}

.top_stats_wrapper{
	height: 200px;
}

.flex_main
{
	margin-top: 180px;
	text-align: center;
}
</style>

<?php
   ob_start();
   ?>
   
<?php
   $top_search_area = ob_get_contents();
   ob_end_clean();
   ?>
<div id="header">
   <div class="hide-menu"><i class="fa fa-bars"></i></div>
   <div id="logo">
      <?php get_company_logo(get_admin_uri().'/') ?>
   </div>
   <nav>
      <div class="small-logo">
         <span class="text-primary">
         <?php get_company_logo(get_admin_uri().'/') ?>
         </span>
      </div>
      <div class="mobile-menu">
         <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
         <i class="fa fa-chevron-down"></i>
         </button>
         <ul class="mobile-icon-menu">
            <?php
               // To prevent not loading the timers twice
               if(is_mobile()){ ?>
            
            <?php } ?>
         </ul>
         <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation" >
            <ul class="nav navbar-nav">
               <li class="header-logout"><a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a></li>
            </ul>
         </div>
      </div>
      <ul class="nav navbar-nav navbar-right"> 
         <?php
            if(!is_mobile()){
              echo $top_search_area;
            } ?>
         <?php do_action('after_render_top_search'); ?>
         <li class="icon header-user-profile" data-toggle="tooltip" title="<?php echo get_staff_full_name(); ?>" data-placement="bottom">
            <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
            <?php echo staff_profile_image($current_user->staffid,array('img','img-responsive','staff-profile-image-small','pull-left')); ?>
            </a>
            <ul class="dropdown-menu animated fadeIn">
               <?php if(get_option('disable_language') == 0){ ?>
               <li class="dropdown-submenu pull-left header-languages">
                  <a href="#" tabindex="-1"><?php echo _l('language'); ?></a>
                  <ul class="dropdown-menu dropdown-menu">
                     <li class="<?php if($current_user->default_language == ""){echo 'active';} ?>"><a href="<?php echo admin_url('staff/change_language'); ?>"><?php echo _l('system_default_string'); ?></a></li>
                     <?php foreach($this->app->get_available_languages() as $user_lang) { ?>
                     <li <?php if($current_user->default_language == $user_lang){echo ' class="active"';} ?>>
                        <a href="<?php echo admin_url('staff/change_language/'.$user_lang); ?>"><?php echo ucfirst($user_lang); ?></a>
                        <?php } ?>
                  </ul>
               </li>
               <?php } ?>
               <li class="header-logout">
                  <a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a>
               </li>
            </ul>
         </li>
         
         
      </ul>
   </nav>
</div>



<div class="container-fluid h-100" style = "background-color:#507ac3 ; width: 100%; height:530px" >
	<div class="row">
		<div class="col-md-12 flex_main">
			<?php if($portfolio =='' && $massage != '') {
				
					echo $massage; 
				
			 } ?>
			
			<?php foreach($portfolio as $p)
			{?>
				<div class="d-flex p-2">
                    <!-- encoding dashboard id using encode helper -->
					<a href = "<?php echo base_url()?>admin/dashboard/dashboard/<?php echo encode_url($p->portfolio_name_id);?>">
		            <div class="top_stats_wrapper">
                      <span><i class="hidden-sm fa fa-briefcase"></i></span>
                     <div class="clearfix"></div>
                      <p class="text-uppercase mtop5<span class="pull-right"><?php echo $p->names;?>
		               </p>
		               <div class="clearfix"></div>
		               
		            </div>
		            </a>
		         </div>

		         
			<?php }
			?>
				
			
			
		</div>
	</div>
</div>

<?php init_tail()?>