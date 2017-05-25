<?php
global $thim_dashboard;
$theme_data = $thim_dashboard['theme_data'];
$stylesheet = $theme_data['stylesheet'];
$envato_id  = $theme_data['envato_item_id'];

$url_auth_callback = Thim_Product_Registration::get_url_verify_callback();//Back to this site
$return            = isset( $args['return'] ) ? $args['return'] : '';//Back to url was setup
?>

<form action="<?php echo esc_url( Thim_Product_Registration::get_url_auth() ); ?>" method="post">
    <input type="hidden" name="theme" value="<?php echo esc_attr( $stylesheet ); ?>">
    <input type="hidden" name="envato_id" value="<?php echo esc_attr( $envato_id ); ?>">
    <input type="hidden" name="site" value="<?php echo esc_attr( home_url( '/' ) ); ?>">
    <input type="hidden" name="callback" value="<?php echo esc_url( $url_auth_callback ); ?>">
    <input type="hidden" name="return" value="<?php echo esc_url( $return ); ?>">
    <button class="button button-primary tc-button activate-btn tc-run-step" type="submit"><?php esc_html_e( 'Login with Envato', 'thim-core' ); ?></button>
</form>