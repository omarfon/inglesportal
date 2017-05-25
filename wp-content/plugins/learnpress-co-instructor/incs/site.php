<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'LP_ADDON_CO_INSTRUCTOR_PATH' ) ) {
	die(); // Exit if accessed directly
}

if ( ! class_exists( 'LP_Addon_Site_Co_Instructor' ) ) {

	class LP_Addon_Site_Co_Instructor {
		public function __construct () {
			$this->init_hooks();
		}

		public function init_hooks () {
			add_filter( 'learn_press_course_tabs', array( $this, 'add_course_tab_co_instructors' ), 5 );
		}

		public function add_course_tab_co_instructors ( $tabs ) {
			$tabs['co-instructor'] = array(
				'title'    => __( 'Instructors', 'learnpress-co-instructor' ),
				'priority' => 0,
				'callback' => array( $this, 'add_course_tab_co_instructors_callback' )
			);

			return $tabs;
		}

		public function add_course_tab_co_instructors_callback () {
			$course_id   = learn_press_get_course_id();
			$course      = learn_press_get_course( $course_id );
			$instructors = $this->get_instructors( $course_id );
			$instructors = array_unique( array_merge( array( $course->post->post_author ), $instructors ) );

			return learn_press_get_template( 'default.php', array( 'instructors' => $instructors ), learn_press_template_path() . '/addons/co-instructors/', LP_ADDON_CO_INSTRUCTOR_PATH . '/templates/' );
		}

		public function get_instructors ( $course_id = null ) {
			if ( ! $course_id ) {
				$course_id = learn_press_get_course_id();
			}
			if ( ! $course_id ) {
				return;
			}
			$course      = learn_press_get_course( $course_id );
			$instructors = learn_press_co_instructor_get_instructors( $course_id );

			return $instructors;
		}
	}

}

new LP_Addon_Site_Co_Instructor();
