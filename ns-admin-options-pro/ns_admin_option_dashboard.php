<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sandbox_theme_display() {
	require_once( plugin_dir_path( __FILE__ ).'inc.php');
?>


    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div>
        <h2>NS Booking Agenda Setting</h2>
        <?php settings_errors(); ?>

		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'how_to_use'; ?>
         
		<h2 class="nav-tab-wrapper">
			<a href="?page=ns-booking-agenda%2Fns-admin-options-pro%2Fns_admin_option_dashboard.php&tab=how_to_use" class="nav-tab <?php echo $active_tab == 'how_to_use' ? 'nav-tab-active' : ''; ?>">How to use</a>
		    <a href="?page=ns-booking-agenda%2Fns-admin-options-pro%2Fns_admin_option_dashboard.php&tab=add_booking_agenda" class="nav-tab <?php echo $active_tab == 'add_booking_agenda' ? 'nav-tab-active' : ''; ?>">Settings</a>
		    <a href="?page=ns-booking-agenda%2Fns-admin-options-pro%2Fns_admin_option_dashboard.php&tab=contact_options" class="nav-tab <?php echo $active_tab == 'contact_options' ? 'nav-tab-active' : ''; ?>">Contact Options</a>
            <a href="?page=ns-booking-agenda%2Fns-admin-options-pro%2Fns_admin_option_dashboard.php&tab=outlook_export" class="nav-tab <?php echo $active_tab == 'outlook_export' ? 'nav-tab-active' : ''; ?>">Outlook Integration</a>
		</h2>
	    <div class="verynsbigbox">
	    	<?php 
	    		/* *** BOX THEME PROMO *** */
				require_once( plugin_dir_path( __FILE__ ).'ns_settings_box_theme_promo.php');

	    		/* *** BOX PREMIUM VERSION *** */
				// require_once( plugin_dir_path( __FILE__ ).'ns_settings_box_pro_version.php');

	    		/* *** BOX NEWSLETTER *** */
				// require_once( plugin_dir_path( __FILE__ ).'ns_settings_box_newsletter.php');
			?>			
		</div>          
        <form method="post" action="options.php">
            <?php
            switch ($active_tab) {
                case 'add_booking_agenda':
                    settings_fields( 'ns_agenda_options_group' );
                    require_once( untrailingslashit( dirname( __FILE__ ) ).'/ns_tab_1_field_group.php');
                    submit_button();
                    break;
                case 'how_to_use':
                    settings_fields( 'ns_how_to_use_options_single_group' );
                    require_once( untrailingslashit( dirname( __FILE__ ) ).'/ns_tab_2_field_group.php');
                    break;
                case 'contact_options':
                    settings_fields( 'ns_agenda_contact_option_group' );
                    require_once( untrailingslashit( dirname( __FILE__ ) ).'/ns_tab_3_field_group.php');  
					submit_button();
                    break;                
                case 'outlook_export':
                    settings_fields( 'ns_export_outlook_options_group' );
                    require_once( untrailingslashit( dirname( __FILE__ ) ).'/ns_tab_4_field_group.php');
					submit_button();
                    break; 
                default:
                    settings_fields( 'ns_cc_options_group' );
                    require_once( untrailingslashit( dirname( __FILE__ ) ).'/ns_tab_1_field_group.php');
                    break;
            }
            ?>
         
            
             
        </form>
         
    </div><!-- /.wrap -->
<?php
} // end sandbox_theme_display

sandbox_theme_display();