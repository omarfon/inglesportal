<?php
/*
Plugin Name: LearnPress - Co-Instructors
Plugin URI: http://thimpress.com/learnpress
Description: Building courses with other instructors
Author: ThimPress
Version: 2.0.1
Author URI: http://thimpress.com
Tags: learnpress, lms, add-on, co-instructor
Text Domain: learnpress
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'LP_ADDON_CO_INSTRUCTOR_PATH' ) ) {
	define( 'LP_ADDON_CO_INSTRUCTOR_FILE', __FILE__ );
	define( 'LP_ADDON_CO_INSTRUCTOR_PATH', dirname( __FILE__ ) );
	define( 'LP_ADDON_CO_INSTRUCTOR_INCLUDES_PATH', LP_ADDON_CO_INSTRUCTOR_PATH . DIRECTORY_SEPARATOR . 'incs' . DIRECTORY_SEPARATOR );
	define( 'LP_ADDON_CO_INSTRUCTOR_VER', '2.0.1' );
	define( 'LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER', '2.0' );
}

/**
 * Class LP_Addon_Co_Instructor
 */
class LP_Addon_Co_Instructor {
	/**
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * @var
	 */
	public $post_id;
	/**
	 * @var
	 */
	public $user;

	/**
	 * @var
	 */
	public $list_instructors;

	/**
	 * LP_Addon_Co_Instructor constructor.
	 */

	/**
	 * Info tab instructor in frontend
	 */
	public $instructor;

	function __construct () {

		// Prepare data
		$this->user = get_current_user_id();

		$this->include_files();
		add_action( 'wp_before_admin_bar_render', array( $this, 'before_admin_bar_render' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_co_items' ) );
		add_filter( 'learn_press_course_settings_meta_box_args', array( $this, 'add_co_instructor_meta_box' ) );
		add_filter( 'learn_press_valid_quizzes', array( $this, 'co_instructor_valid_quizzes' ) );
		add_filter( 'learn_press_valid_lessons', array( $this, 'co_instructor_valid_lessons' ) );
		add_filter( 'learn_press_valid_courses', array( $this, 'get_available_courses' ) );
		add_action( 'init', array( __CLASS__, 'load_text_domain' ) );
		add_action( 'admin_head-post.php', array( $this, 'process_teacher' ) );

		add_filter( 'learn_press_page_settings', array( $this, 'learn_press_page_settings' ), 10, 2 );
		add_filter( 'learnpress_course_insert_item_args', array( $this, 'course_insert_item_args' ) );
		add_filter( 'learnpress_quiz_insert_item_args', array( $this, 'quiz_insert_question_args' ), 10, 2 );
		add_filter( 'learn_press_user_profile_tabs', array(
			$this,
			'learn_press_add_tab_instructor_in_profile'
		), 15, 2 );

		add_filter( 'learn_press_profile_tab_endpoints', array( $this, 'learn_press_profile_tab_endpoints' ) );
		add_filter( 'learn_press_excerpt_duplicate_post_meta', array(
			$this,
			'learn_press_excerpt_duplicate_post_meta'
		), 10, 3 );

		// All users co-instructors to send mail when this course is enrolled
		add_filter( 'learn_press_user_admin_send_mail_enrolled_course', array(
			$this,
			'learn_press_user_admin_send_mail_enrolled_course'
		), 10, 4 );
		add_action( 'learn_press_before_subject_enrolled_course_admin__settings_fields', array(
			$this,
			'learn_press_before_subject_enrolled_course_admin__settings_fields'
		), 10, 2);

	}


	function include_files () {
		require_once LP_ADDON_CO_INSTRUCTOR_INCLUDES_PATH . 'functions.php';
		is_admin() or require_once LP_ADDON_CO_INSTRUCTOR_INCLUDES_PATH . 'site.php';
	}


	function process_teacher () {
		global $post;
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		$post_id = $post->ID;
		if ( current_user_can( LP_TEACHER_ROLE ) ) {
			if ( $post->post_author == get_current_user_id() ) {
				return;
			}
			$courses = apply_filters( 'learn_press_valid_courses', array() );
			$lessons = apply_filters( 'learn_press_valid_lessons', array() );
			$quizzes = apply_filters( 'learn_press_valid_quizzes', array() );

			if ( in_array( $post_id, $courses ) ) {
				return;
			}
			if ( in_array( $post_id, $lessons ) ) {
				return;
			}
			if ( in_array( $post_id, $quizzes ) ) {
				return;
			} else {
				foreach ( $quizzes as $quiz ) {
					$questions = $this->get_available_question_from_quiz( $quiz );
					if ( in_array( $post_id, $questions ) ) {
						return;
					}
				}
			}
			wp_die( __( 'Sorry! You don\'t have permission to do this action', 'learnpress' ), 403 );
		}
	}

	function co_instructor_valid_lessons () {
		$courses = $this->get_available_courses();

		return $this->get_available_lessons( $courses );
	}

	function co_instructor_valid_quizzes () {
		$courses = $this->get_available_courses();

		return $this->get_available_quizzes( $courses );
	}

	/**
	 * @param $courses
	 *
	 * @return array
	 */
	function get_available_lessons ( $courses ) {
		global $wpdb;

		$query   = $wpdb->prepare(
			"
			SELECT         ID
				FROM            $wpdb->posts
				WHERE           ( post_type = %s OR post_type = %s )
				AND				post_author = %d
			",
			'lpr_lesson', 'lp_lesson', get_current_user_id()
		);
		$lessons = $wpdb->get_col( $query );
		if ( $courses ) {
			foreach ( $courses as $course ) {
				$temp    = $this->get_available_lesson_from_course( $course );
				$lessons = array_unique( array_merge( $lessons, $temp ) );
			}
		}

		return $lessons;
	}

	// get all lessons from course
	function get_available_lesson_from_course ( $course = null ) {

		if ( empty( $course ) ) {
			return array();
		}

		$lp_course = new LP_Course( $course );
		$temps     = array();

		if ( $lessons = $lp_course->get_lessons() ) {
			foreach ( $lessons as $lesson ) {
				$temps[ $lesson->ID ] = absint( $lesson->ID );
			}
		}

		return $temps;
	}

	/**
	 * @param $courses
	 *
	 * @return array
	 */
	function get_available_quizzes ( $courses ) {
		global $wpdb;
		$query   = $wpdb->prepare(
			"
				SELECT         ID
				FROM            $wpdb->posts
				WHERE           ( post_type = %s OR post_type = %s )
				AND				post_author = %d
			", 'lpr_quiz', 'lp_quiz', get_current_user_id()
		);
		$quizzes = $wpdb->get_col( $query );
		if ( $courses ) {
			foreach ( $courses as $course ) {
				// get quizze of course
				$temp    = $this->get_available_quizzes_from_course( $course );
				$quizzes = array_unique( array_merge( $quizzes, $temp ) );
			}
		}

		return $quizzes;
	}


// get all quizzes from course
	function get_available_quizzes_from_course ( $course = null ) {

		if ( empty( $course ) ) {
			return array();
		}

		$lp_course = new LP_Course( $course );
		$temps     = array();

		if ( $quizzes = $lp_course->get_quizzes() ) {
			foreach ( $quizzes as $quiz ) {
				$temps[] = $quiz->ID;
			}
		}

		return $temps;
	}

	function get_available_question_from_quiz ( $quiz = null ) {

		if ( empty( $quiz ) ) {
			return array();
		}

		$lp_quiz = new LP_Quiz( $quiz );
		$temps   = array();

		if ( $questions = $lp_quiz->get_questions() ) {
			foreach ( $questions as $question ) {
				$temps[] = $question->ID;
			}
		}

		return $temps;

	}

	function pre_get_co_items ( $query ) {
		$current_user = wp_get_current_user();
		global $pagenow;

		if ( is_admin() && ( in_array( 'lpr_teacher', $current_user->roles ) || in_array( 'lp_teacher', $current_user->roles ) ) && $pagenow == 'edit.php' ) {
			$post_type = isset( $_REQUEST['post_type'] ) ? sanitize_text_field( $_REQUEST['post_type'] ) : '';
			if ( in_array( $post_type, array(
				'lpr_course',
				'lp_course',
				'lpr_lesson',
				'lp_lesson',
				'lpr_quiz',
				'lp_quiz'
			) ) ) {
				$courses         = $this->get_available_courses();
				$empty_post_type = 'lpr_empty';
				if ( in_array( $post_type, array( 'lpr_course', 'lp_course' ) ) ) {
					if ( count( $courses ) == 0 ) {
						if ( $post_type === 'lp_course' ) {
							$empty_post_type = 'lp_empty';
						}
						$query->set( 'post_type', $empty_post_type );
					} else {
						$query->set( 'post_type', $post_type );
						$query->set( 'post__in', $courses );
					}
					add_filter( 'views_edit-lpr_course', array( $this, 'restrict_co_items' ), 20 );
					add_filter( 'views_edit-lp_course', array( $this, 'restrict_co_items' ), 20 );

					return;
				}
				if ( in_array( $post_type, array( 'lpr_lesson', 'lp_lesson' ) ) ) {
					$lessons = $this->get_available_lessons( $courses );
					if ( count( $lessons ) == 0 ) {
						if ( $post_type === 'lp_lesson' ) {
							$empty_post_type = 'lp_empty';
						}
						$query->set( 'post_type', $empty_post_type );
					} else {
						$query->set( 'post_type', $post_type );
						$query->set( 'post__in', $lessons );
					}
					add_filter( 'views_edit-lpr_lesson', array( $this, 'restrict_co_items' ), 20 );
					add_filter( 'views_edit-lp_lesson', array( $this, 'restrict_co_items' ), 20 );

					return;
				}
				if ( in_array( $post_type, array( 'lpr_quiz', 'lp_quiz' ) ) ) {
					$quizzes = $this->get_available_quizzes( $courses );
					if ( count( $quizzes ) == 0 ) {
						if ( $post_type === 'lp_quiz' ) {
							$empty_post_type = 'lp_empty';
						}
						$query->set( 'post_type', $empty_post_type );
					} else {
						$query->set( 'post_type', $post_type );
						$query->set( 'post__in', $quizzes );
					}
					add_filter( 'views_edit-lpr_quiz', array( $this, 'restrict_co_items' ), 20 );
					add_filter( 'views_edit-lp_quiz', array( $this, 'restrict_co_items' ), 20 );

					return;
				}
			}
		}
	}

	/**
	 * @param $views
	 *
	 * @return mixed
	 */
	function restrict_co_items ( $views ) {

		$post_type = get_query_var( 'post_type' );
		$author    = get_current_user_id();

		$new_views = array(
			'all'        => __( 'All', 'learnpress' ),
			'mine'       => __( 'Mine', 'learnpress' ),
			'publish'    => __( 'Published', 'learnpress' ),
			'private'    => __( 'Private', 'learnpress' ),
			'pending'    => __( 'Pending Review', 'learnpress' ),
			'future'     => __( 'Scheduled', 'learnpress' ),
			'draft'      => __( 'Draft', 'learnpress' ),
			'trash'      => __( 'Trash', 'learnpress' ),
			'co_teacher' => __( 'Co-instructor', 'learnpress' )
		);

		$url = 'edit.php';

		foreach ( $new_views as $view => $name ) {

			$query = array(
				'post_type' => $post_type
			);

			if ( $view == 'all' ) {
				$query['all_posts'] = 1;
				$class              = ( get_query_var( 'all_posts' ) == 1 || ( get_query_var( 'post_status' ) == '' && get_query_var( 'author' ) == '' ) ) ? ' class="current"' : '';

			} elseif ( $view == 'mine' ) {
				$query['author'] = $author;
				$class           = ( get_query_var( 'author' ) == $author ) ? ' class="current"' : '';
			} elseif ( $view == 'co_teacher' ) {
				$query['author'] = - $author;
				$class           = ( get_query_var( 'author' ) == - $author ) ? ' class="current"' : '';

			} else {
				$query['post_status'] = $view;
				$class                = ( get_query_var( 'post_status' ) == $view ) ? ' class="current"' : '';
			}

			$result = new WP_Query( $query );

			if ( $result->found_posts > 0 ) {

				$views[ $view ] = sprintf(
					'<a href="%s"' . $class . '>' . __( $name, 'learnpress' ) . ' <span class="count">(%d)</span></a>',
					esc_url( add_query_arg( $query, $url ) ),
					$result->found_posts
				);

			} else {

				unset( $views[ $view ] );

			}

		}

		return $views;
	}


	/**
	 * @return array
	 */
	function get_available_courses () {
		$return = false;
		if ( ! current_user_can( 'lpr_teacher' ) ) {
			$return = true;
		}

		if ( ! current_user_can( 'lp_teacher' ) ) {
			$return = true;
		}
		if ( $return === false ) {
			return array();
		}
		global $wpdb;

		$query = $wpdb->prepare(
			"
				SELECT DISTINCT p.ID
					FROM				$wpdb->posts AS p
					INNER JOIN 			$wpdb->postmeta AS pm ON p.ID = pm.post_id
					WHERE  				( p.post_author = %d AND ( p.post_type = %s OR p.post_type = %s ) )
					OR 					( ( pm.meta_key = %s OR pm.meta_key = %s ) AND pm.meta_value= %d AND ( p.post_type = %s OR p.post_type = %s ) )
			",
			get_current_user_id(), 'lpr_course', 'lp_course', '_lpr_co_teacher', '_lp_co_teacher', get_current_user_id(), 'lpr_course', 'lp_course'
		);

		return $wpdb->get_col( $query );
	}

	// ADD METABOX CO-INSTRUCTOR IN COURSES
	function add_co_instructor_meta_box ( $meta_box ) {
		$class       = '';
		$post_author = '';
		if ( isset( $_GET['post'] ) && isset( get_post( $_GET['post'] )->post_author ) ) {
			$post_author = get_post( $_GET['post'] )->post_author;
			if ( $post_author != get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				$class = 'hidden';
			}
		}
		$include       = array();
		$users_by_role = get_users( array( 'role' => 'administrator' ) );
		if ( $users_by_role ) {
			foreach ( $users_by_role as $user ) {
				if ( $user->ID != $post_author ) {
					$include[ $user->ID ] = $user->user_login;
				}
			}
		}
		$users_by_role = get_users( array( 'role' => 'lp_teacher' ) );
		if ( $users_by_role ) {
			foreach ( $users_by_role as $user ) {
				if ( $user->ID != $post_author ) {
					$include[ $user->ID ] = $user->user_login;
				}
			}
		}
		$users_by_role = get_users( array( 'role' => 'lpr_teacher' ) );
		if ( $users_by_role ) {
			foreach ( $users_by_role as $user ) {
				if ( $user->ID != $post_author ) {
					$include[ $user->ID ] = $user->user_login;
				}
			}
		}

		$meta_box['fields'][] = array(
			'name'        => __( 'Co-Instructors', 'learnpress' ),
			'id'          => "_lp_co_teacher",
			'desc'        => __( 'Colleagues\'ll work with you', 'learnpress' ),
			'class'       => $class,
			'type'        => 'teacher',
			'multiple'    => true,
			'type'        => 'select_advanced',
			'placeholder' => __( 'Instructor username', 'learnpress' ),
			'options'     => $include
		);

		return $meta_box;
	}

	function course_get_instructors ( $course_id = null ) {
		if ( ! $course_id ) {
			$course_id = get_the_ID();
		}
		// if not isset course id return empty array
		if ( ! $course_id ) {
			return array();
		}

		$co_teacher = array();
		// get list teachers by post meta _lp_co_teacher
		$teachers = get_post_meta( $course_id, '_lp_co_teacher' );
		if ( ! $teachers ) {
			$teachers = get_post_meta( $course_id, '_lpr_co_teacher' );
		}

		foreach ( $teachers as $key => $teacher ) {
			$co_teacher[ $teacher ] = new WP_User( $teacher );
		}

		// return teachers
		return $co_teacher;
	}

	function before_admin_bar_render () {
		// do something with $wp_admin_bar;
		global $post, $wp_admin_bar;

		if ( $post && ! in_array( $post->ID, $this->get_available_courses() ) ) {
			$wp_admin_bar->remove_menu( 'edit' );
		}

		return $wp_admin_bar;
	}

	public function learn_press_page_settings ( $settings, $object ) {

		$instructor_setting = array(
			'title'       => __( 'Instructor', 'learnpress' ),
			'id'          => $object->get_field_name( 'profile_endpoints[profile-instructor]' ),
			'default'     => 'instructor',
			'type'        => 'text',
			'placeholder' => '',
			'desc'        => __( 'This is a slug and should be unique.', 'learnpress' ) . sprintf( ' %s <code>[profile/admin/instructor]</code>', __( 'Example link is', 'learnpress' ) )
		);
		$instructor_setting = apply_filters( 'learn_press_page_settings_item_instructor', $instructor_setting, $settings, $object );

		$new_settings = array();
		foreach ( $settings as $index => $setting ) {
			$new_settings[] = $setting;
			if ( $setting['id'] === $object->get_field_name( 'profile_endpoints[profile-order-details]' ) ) {
				$new_settings[]     = $instructor_setting;
				$instructor_setting = false;
			}
		}

		if ( $instructor_setting ) {
			$new_settings[] = $instructor_setting;
		}

		return $new_settings;

	}

	public function course_insert_item_args ( $args ) {
		$owner               = $this->get_own_user_of_post();
		$args['post_author'] = $owner;

		return $args;
	}

	public function quiz_insert_question_args ( $args, $quiz_id ) {

		$author = get_current_user_id();


		if ( ! empty( $quiz_id ) ) {
			$post   = get_post( $quiz_id );
			$author = $post->post_author;
		}

		if ( ! empty( $author ) ) {
			$args['post_author'] = $author;
		}

		return $args;

	}

	public function learn_press_add_tab_instructor_in_profile ( $tabs, $user ) {

		$tab = apply_filters( 'learn_press_instructor_tab_in_profile', array(
			'title'    => __( 'Instructor', 'learnpress' ),
			'callback' => 'learn_press_profile_tab_instructor_content'
		), $tabs, $user );

		$instructor_endpoint = LP()->settings->get( 'profile_endpoints.profile-instructor' );

		if ( empty( $instructor_endpoint ) || empty( $tab ) ) {
			return $tabs;
		}

		if ( in_array( $instructor_endpoint, array_keys( $tabs ) ) ) {
			return $tabs;
		}

		$instructor = array( $instructor_endpoint => $tab );

		$course_endpoint = LP()->settings->get( 'profile_endpoints.profile-courses' );

		if ( ! empty( $course_endpoint ) ) {

			$pos  = array_search( $course_endpoint, array_keys( $tabs ) ) + 1;
			$tabs = array_slice( $tabs, 0, $pos, true ) + $instructor + array_slice( $tabs, $pos, count( $tabs ) - 1, true );

		} else {
			$tabs = $tabs + $instructor;
		}

		return $tabs;
	}

	public function learn_press_profile_tab_endpoints ( $profile_endpoints ) {

		return $profile_endpoints;

	}

	public function learn_press_excerpt_duplicate_post_meta ( $excerpt, $old_post_id, $new_post_id ) {
		if ( ! in_array( '_lp_co_teacher', $excerpt ) ) {
			$excerpt[] = '_lp_co_teacher';
		}
		return $excerpt;
	}

	public function learn_press_user_admin_send_mail_enrolled_course ( $all_users_id, $course_id, $user_id, $user_course_id ) {

		if ( LP()->settings->get( 'emails_enrolled_course_admin.send_instructors' ) === 'yes' ) {
			$co_instructors = learn_press_co_instructor_get_instructors( $course_id );
			$all_users_id   = array_unique( array_merge( $all_users_id, $co_instructors ) );
		}

		return $all_users_id;
	}

	public function learn_press_before_subject_enrolled_course_admin__settings_fields ( $settings, $settings_class ) {

		?>
		<tr>
			<th scope="row">
				<label for="learn-press-emails-enrolled-course-send-instructors"><?php _e( 'Send Co-Instructors', 'learnpress' ); ?></label></th>
			<td>
				<input type="hidden" name="<?php echo $settings_class->get_field_name( 'emails_enrolled_course_admin[send_instructors]' ); ?>" value="no" />
				<input id="learn-press-emails-enrolled-course-send-instructors" type="checkbox" name="<?php echo $settings_class->get_field_name( 'emails_enrolled_course_admin[send_instructors]' ); ?>" value="yes" <?php checked( $settings->get( 'emails_enrolled_course_admin.send_instructors' ) == 'yes' ); ?> />
			</td>
		</tr>
		<?php
	}

	public function get_own_user_of_post () {

		global $post;
		// Check if current user have permissions admin

		if ( current_user_can( 'administrator' ) && isset( $_REQUEST['_lp_course_author'] ) && ! empty( $_REQUEST['_lp_course_author'] ) ) {
			$this->user = $_REQUEST['_lp_course_author'];
		} else {
			$this->user = $post->post_author;
		}
		$this->user = absint( $this->user );

		return $this->user;
	}

	static function install () {
		$teacher = get_role( 'lp_teacher' );
		if ( $teacher ) {
			$teacher->add_cap( 'edit_others_lp_lessons' );
			$teacher->add_cap( 'edit_others_lp_courses' );
		}
	}

	static function uninstall () {
		$teacher = get_role( 'lp_teacher' );
		if ( $teacher ) {
			$teacher->remove_cap( 'edit_others_lp_lessons' );
			$teacher->remove_cap( 'edit_others_lp_courses' );
		}
	}

	/**
	 * Load text domain
	 */
	static function load_text_domain () {
		if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
			learn_press_load_plugin_text_domain( LP_ADDON_CO_INSTRUCTOR_PATH, true );
		}
	}

	public static function admin_notice () {
		?>
		<div class="error">
			<p><?php printf( __( '<strong>Co-Instructor</strong> addon version %s requires <strong>LearnPress</strong> version %s or higher', 'learnpress' ), LP_ADDON_CO_INSTRUCTOR_VER, LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER ); ?></p>
		</div>
		<?php
	}

	/**
	 * @return LP_Addon_Co_Instructor|null
	 */
	static function instance () {
		if ( ! defined( 'LEARNPRESS_VERSION' ) || ( version_compare( LEARNPRESS_VERSION, LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER, '<' ) ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );

			return false;
		}
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

add_action( 'plugins_loaded', array( 'LP_Addon_Co_Instructor', 'instance' ) );
register_activation_hook( __FILE__, array( 'LP_Addon_Co_Instructor', 'install' ) );
register_deactivation_hook( __FILE__, array( 'LP_Addon_Co_Instructor', 'uninstall' ) );