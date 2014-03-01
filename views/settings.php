<div class="wrap">
    <div id="<?php echo $this->plugin->name; ?>-title" class="icon32"></div> 
    <h2 class="wpcube"><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Settings'); ?></h2>
           
    <?php    
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?> 
    
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
    		
    			<!-- Form Start -->
		        <form id="post" name="post" method="post" action="admin.php?page=<?php echo $this->plugin->name; ?>">
		            <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
		                <div class="postbox">
		                    <h3 class="hndle"><?php _e('Merchant Settings', $this->plugin->name); ?></h3>
		                    
		                    <div class="option">
		                    	<p>
		                    		<strong><?php _e('Merchant ID', $this->plugin->name); ?></strong>
		                    		<input type="text" name="<?php echo $this->plugin->name; ?>[merchantID]" value="<?php echo (isset($this->settings['merchantID']) ? $this->settings['merchantID'] : ''); ?>" />
		                    	</p>
		                    	<p class="description"><?php _e('Your ShareASale Merchant ID', $this->plugin->name); ?></p>
		                    </div>
		                    
		                    <div class="option">
			                    <p>
			                    	<input type="submit" name="submit" value="<?php _e('Save', $this->plugin->name); ?>" class="button button-primary" /> 
			                	</p>
			                </div>
		                </div>
		                <!-- /postbox -->
					</div>
					<!-- /normal-sortables -->
			    </form>
			    <!-- /form end -->
    			
    		</div>
    		<!-- /post-body-content -->
    		
    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php require_once($this->plugin->folder.'/_modules/dashboard/views/sidebar-donate.php'); ?>		
    		</div>
    		<!-- /postbox-container -->
    	</div>
	</div>       
</div>