<?php

//Return if active plugin Thim Core
if ( thim_plugin_active( 'thim-core/thim-core.php' ) ) {
	return;
}

if ( !thim_plugin_active( 'thim-framework/tp-framework.php' ) ) {
	return;
}

require THIM_DIR . 'inc/admin/customize-options.php';
require THIM_DIR . 'inc/widgets/widgets.php';
require THIM_DIR . 'inc/libs/Tax-meta-class/Tax-meta-class.php';
require THIM_DIR . 'inc/libs/custom-export.php';
require THIM_DIR . 'inc/tax-meta.php';