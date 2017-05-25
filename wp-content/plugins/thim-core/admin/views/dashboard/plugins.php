<?php
wp_update_plugins();

$plugins  = Thim_Plugins_Manager::get_all_plugins();
$add_ons  = Thim_Plugins_Manager::get_all_add_ons();
$writable = Thim_Plugins_Manager::get_permission();
?>

<?php
do_action( 'thim_dashboard_registration_box' );
?>

<div class="wrap plugin-tab">
    <h2 class="screen-reader-text"><?php esc_html_e( 'Filter plugins list', 'thim-core' ); ?></h2>
    <div class="wp-filter">
        <ul class="filter-links">
            <li class="all" data-filter="*"><a href="#all" class="current"><?php esc_html_e( 'All (__)', 'thim-core' ); ?></a></li>
            <li class="required" data-filter=".required"><a href="#required" class=""><?php esc_html_e( 'Required (__)', 'thim-core' ); ?></a></li>
            <li class="recommended" data-filter=".recommended"><a href="#recommended"><?php esc_html_e( 'Recommended (__)', 'thim-core' ); ?></a></li>
			<?php if ( count( $add_ons ) ): ?>
                <li class="add-ons" data-filter=".add-on"><a href="#add-ons"><?php printf( __( 'Add-ons (%s)', 'thim-core' ), count( $add_ons ) ); ?></a></li>
			<?php endif; ?>
            <li class="updates" data-filter=".can-update"><a href="#updates"><?php esc_html_e( 'Update Available (__)', 'thim-core' ); ?></a></li>
        </ul>

        <div class="search-form search-plugins">
            <input type="hidden" name="tab" value="search">
            <label><span class="screen-reader-text"><?php esc_html_e( 'Search plugins', 'thim-core' ); ?></span>
                <input type="search" name="s" value="" class="wp-filter-search" placeholder="<?php esc_attr_e( 'Search plugins', 'thim-core' ); ?>"
                       aria-describedby="live-search-desc">
            </label>
            <input type="submit" id="search-submit" class="button hide-if-js" value="<?php esc_attr_e( 'Search Plugins', 'thim-core' ); ?>"></div>
    </div>
    <br class="clear">

    <div id="plugin-filter">
        <div class="list-plugins">
			<?php foreach ( $plugins as $plugin_data ):
				$slug = $plugin_data['slug'];

				$plugin = new Thim_Plugin();
				$plugin->set_args( $plugin_data );
				$status      = $plugin->get_status();
				$is_wporg    = $plugin->is_wporg();
				$plugin_info = $plugin->get_info();
				$can_update  = $plugin->can_update();

				$plugin_icon = THIM_CORE_ADMIN_URI . '/assets/images/logo.svg';
				if ( $plugin->get_icon() ) {
					$plugin_icon = $plugin->get_icon();
				} elseif ( $is_wporg ) {
					$plugin_icon = 'https://ps.w.org/' . $plugin->get_slug() . '/assets/icon-128x128.png';
				}

				$plugin_classes = $plugin_data['required'] ? 'required' : 'recommended';
				if ( $plugin->is_add_on() ) {
					$plugin_classes = 'add-on';
				}
				$plugin_classes .= ' plugin-card-' . $slug;

				$version = $plugin->get_require_version();
				$version = $version ? $version : $plugin->get_current_version();

				$plugin_classes .= $can_update ? ' can-update' : '';
				?>
                <div class="plugin-card <?php esc_attr_e( $plugin_classes ); ?>" id="plugin-<?php echo esc_attr( $slug ); ?>">
                    <div class="plugin-card-top">
                        <div class="name column-name">
                            <h3>
								<?php echo $plugin_data['name']; ?>
                                <div class="open-plugin-details-modal">
                                    <img src="<?php echo esc_url( $plugin_icon ); ?>" class="plugin-icon" alt="<?php echo esc_attr( $plugin_data['name'] ); ?>">
                                </div>
                            </h3>
                        </div>
                        <div class="action-links">
                            <ul class="plugin-action-buttons" data-slug="<?php echo esc_attr( $plugin->get_slug() ); ?>">
                                <li>
									<?php if ( $can_update ) : ?>
                                        <button type="button" class="button" data-action="update" <?php disabled( $writable, false ); ?> ><?php esc_html_e( 'Update', 'thim-core' ); ?></button>
									<?php elseif ( $status == 'not_installed' ): ?>
                                        <button type="button" class="button" data-action="install" <?php disabled( $writable, false ); ?> ><?php esc_html_e( 'Install Now', 'thim-core' ); ?></button>
									<?php elseif ( $status == 'inactive' ): ?>
                                        <button type="button" class="button" data-action="activate"><?php esc_html_e( 'Activate', 'thim-core' ); ?></button>
									<?php else: ?>
                                        <button type="button" class="button" data-action="deactivate"><?php esc_html_e( 'Deactivate', 'thim-core' ); ?></button>
									<?php endif; ?>
                                </li>
                            </ul>
                        </div>
                        <div class="desc column-description">
                            <p><?php echo $plugin->get_description(); ?></p>
                        </div>
                    </div>
                    <div class="plugin-card-bottom">
                        <div class="column-downloaded"><?php printf( __( 'Version: %s', 'thim-core' ), $version ); ?></div>

						<?php if ( $plugin->is_required() ): ?>
                            <div class="column-updated"><span class="plugin-required"><?php esc_html_e( 'Required', 'thim-core' ) ?></span></div>
						<?php endif; ?>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
    </div>

    <span class="spinner"></span>
</div>