<?php
$body_template = 'boxes/' . $args['template'] . '.php';

$locked = $args['lock'] && ! Thim_Product_Registration::is_active();
?>

<div class="tc-box <?php echo esc_attr( $locked ? 'locked' : '' ) ?>" data-id="<?php echo esc_attr( $args['id'] ) ?>">
    <div class="tc-box-header">
		<?php if ( $args['lock'] ) {
			Thim_Dashboard::get_template( 'partials/box-status.php' );
		} ?>
        <h2 class="box-title"><?php esc_html_e( $args['title'] ) ?></h2>
    </div>

	<?php Thim_Dashboard::get_template( $body_template ) ?>
</div>