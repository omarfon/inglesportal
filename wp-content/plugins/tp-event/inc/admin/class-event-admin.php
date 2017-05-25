<?php

defined( 'ABSPATH' ) || exit();

class TP_Event_Admin {

	public function __construct() {

		$this->_includes();
	}

	private function _includes() {
		include( TP_EVENT_PATH . 'inc/admin/class-event-admin-menu.php' );
		include( TP_EVENT_PATH . 'inc/admin/class-event-admin-assets.php' );
		include( TP_EVENT_PATH . 'inc/admin/class-event-admin-metaboxes.php' );
		include( TP_EVENT_PATH . 'inc/admin/class-event-admin-settings.php' );
		include( TP_EVENT_PATH . 'inc/admin/class-event-admin-users.php' );
	}

}

new TP_Event_Admin();
