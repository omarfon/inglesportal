<?php
global $thim_dashboard;
$theme_data        = $thim_dashboard['theme_data'];
$theme_name        = $theme_data['name'];
$stylesheet        = $theme_data['stylesheet'];
$purchase_link     = $theme_data['purchase_link'];
$url_auth_callback = Thim_Product_Registration::get_url_verify_callback();
?>
<div class="tc-registration-wrapper tc-base-middle">
	<?php if ( ! TP::is_active_network() ):
		$url_activate_network = Thim_Plugins_Manager::get_url_plugin_actions(
			array(
				'slug'          => 'thim-core',
				'plugin-action' => 'activate',
				'network'       => true
			)
		);
		?>
        <div><?php printf( __( 'You need to <a href="%s">network activate</a> Thim Core Plugin to use this feature.', 'thim-core' ), esc_url( $url_activate_network ) ); ?></div>
	<?php else: ?>
        <div class="left">
            <h3 class="title"><?php esc_html_e( "Product registration", 'thim-core' ); ?></h3>
            <div class="sub-title"><?php esc_html_e( "You're almost finished!", 'thim-core' ); ?></div>
        </div>

        <div class="right">
			<?php Thim_Dashboard::get_template( 'partials/button-activate.php' ); ?>
        </div>

		<?php if ( $purchase_link ): ?>
            <div class="purchase">
                <span><?php esc_html_e( "Don't have direct license yet?", 'thim-core' ); ?></span>
                <a href="<?php echo esc_url( $purchase_link ); ?>" target="_blank"><?php printf( __( 'Purchase %s license.', 'thim-core' ), $theme_name ); ?></a>
            </div>
		<?php endif; ?>
	<?php endif; ?>
</div>

