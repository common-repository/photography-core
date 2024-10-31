<?php

if ( !class_exists( 'Photography_Core_WPLR_Sync' ) ) {

	class Photography_Core_WPLR_Sync {

		function mfrh_admin_notices() {
			echo '<div class="updated"><p>WP/LR Sync has been detected by Photography Core. Why not creating a mapping to synchronize your collections and folders? You can install the WP/LR Theme Assistant, and click here. The mapping will be automatically created.</p></div>';
		}

		public function __construct() {
			//TODO: We can remove this delete option in the future
			delete_option( 'wplr_hide_posttypes', 0 );

			//TODO: Auto-create the Mapping in the Theme Assistant
			//add_action( 'admin_notices', array( $this, 'mfrh_admin_notices' ) );
		}

	}

}

?>
