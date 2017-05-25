<?php
/*
 * @author leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( $args['event_id'] ) {
	$ids = explode( ',', $args['event_id'] );
	foreach ( $ids as $id ) {
		$event = get_post( $id );
		echo get_the_title( $id );

		$current_time = date( 'Y-m-d H:i' );
		$time         = tp_event_get_time( 'Y-m-d H:i', $event, false ); ?>
        <div class="event-countdown">
			<?php $date = new DateTime( date( 'Y-m-d H:i', strtotime( $time ) ) ); ?>
            <div class="tp_event_counter" data-time="<?php echo esc_attr( $date->format( 'M j, Y H:i:s O' ) ) ?>"></div>
        </div>
		<?php
	}
} else { ?>
    <p class="tp-event-notice error"><?php echo esc_html__( 'Invalid Event ID', 'tp-event' ); ?></p>
	<?php
}