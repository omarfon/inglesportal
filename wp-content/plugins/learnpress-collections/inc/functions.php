<?php
/**
 * learn_press_page_title function.
 *
 * @param  boolean $echo
 *
 * @return string
 */
function learn_press_collections_page_title( $echo = true ) {

	if ( is_search() ) {
		$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'learnpress' ), get_search_query() );

		if ( get_query_var( 'paged' ) )
			$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'learnpress' ), get_query_var( 'paged' ) );

	} elseif ( is_tax() ) {
		$page_title = single_term_title( "", false );
	} else {
		$page_title = __( 'Collections', 'learnpress' );
	}

	$page_title = apply_filters( 'learn_press_page_title', $page_title );

	if ( $echo )
		echo $page_title;
	else
		return $page_title;
}

function learn_press_collections_locate_template( $name ) {
	return learn_press_locate_template( $name, learn_press_template_path() . '/addons/collections/', LP_COLLECTIONS_PATH . '/templates/' );
}

function learn_press_collections_get_template( $name, $args = null ) {
	learn_press_get_template( $name, $args, learn_press_template_path() . '/addons/collections/', LP_COLLECTIONS_PATH . '/templates/' );
}

function learn_press_collections_loop_item_title() {
	learn_press_collections_get_template( 'loop/title.php' );
}

add_action( 'learn_press_collections_loop_item_title', 'learn_press_collections_loop_item_title', 5 );

function learn_press_collections_after_loop_item() {
	learn_press_collections_get_template( 'loop/introduce.php' );
}

add_action( 'learn_press_collections_after_loop_item', 'learn_press_collections_after_loop_item', 5 );

function learn_press_collections_title() {
	learn_press_collections_get_template( 'single-collection/title.php' );
}

add_action( 'learn_press_collections_before_single_summary', 'learn_press_collections_title', 5 );