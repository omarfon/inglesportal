<?php

function thim_get_all_plugins_require( $plugins ) {
	$plugins = array(

		array(
			'name'        => 'Visual Composer',
			'slug'        => 'js_composer',
			'source'      => 'https://plugins.thimpress.com/downloads/js_composer.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/js_composer.png',
			'version'     => '5.0.1',
			'description' => 'Drag and drop page builder for WordPress. Take full control over your WordPress site, build any layout you can imagine – no programming knowledge required. By Michael M - WPBakery.com.'
		),
		array(
			'name'        => 'Thim Our Team',
			'slug'        => 'thim-our-team',
			'source'      => 'https://plugins.thimpress.com/downloads/thim-our-team.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/thim-our-team.png',
			'version'     => '1.3.1',
			'description' => 'A plugin that allows you to show off your team members. By ThimPress.',
		),
		array(
			'name'        => 'Thim Testimonials',
			'slug'        => 'thim-testimonials',
			'source'      => 'https://plugins.thimpress.com/downloads/thim-testimonials.zip',
			'icon'        => THIM_URI . 'images/plugins/thim-testimonials.png',
			'required'    => false,
			'version'     => '1.3.1',
			'description' => 'A plugin that allows you to show off your testimonials. By ThimPress.',
		),
		array(
			'name'        => 'Revolution Slider',
			'slug'        => 'revslider',
			'source'      => 'https://plugins.thimpress.com/downloads/revslider.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/revslider.png',
			'version'     => '5.3.1.5',
			'description' => 'Slider Revolution – Premium responsive slider By ThemePunch.',
		),

		array(
			'name'        => 'SiteOrigin Page Builder',
			'slug'        => 'siteorigin-panels',
			'required'    => false,
			'version'     => '2.4.25',
			'description' => 'A drag and drop, responsive page builder that simplifies building your website. By SiteOrigin.',
		),

		array(
			'name'        => 'Black Studio TinyMCE Widget',
			'slug'        => 'black-studio-tinymce-widget',
			'required'    => false,
			'version'     => '2.3.1',
			'description' => 'Adds a new “Visual Editor” widget type based on the native WordPress TinyMCE editor. By Black Studio.',
		),
		array(
			'name'        => 'Contact Form 7',
			'slug'        => 'contact-form-7',
			'required'    => false,
			'version'     => '4.7',
			'description' => 'Just another contact form plugin. Simple but flexible. By Takayuki Miyoshi.',
		),
		array(
			'name'        => 'MailChimp for WordPress',
			'slug'        => 'mailchimp-for-wp',
			'required'    => false,
			'version'     => '4.1.0',
			'description' => 'MailChimp for WordPress by ibericode. Adds various highly effective sign-up methods to your site. By ibericode.',
		),
		array(
			'name'        => 'WooCommerce',
			'slug'        => 'woocommerce',
			'required'    => false,
			'version'     => '2.6.14',
			'description' => 'An e-commerce toolkit that helps you sell anything. Beautifully. By WooThemes.',
		),
		array(
			'name'        => 'bbPress',
			'slug'        => 'bbpress',
			'required'    => false,
			'version'     => '2.5.12',
			'description' => 'bbPress is forum software with a twist from the creators of WordPress. By The bbPress Community.',
		),
		array(
			'name'        => 'Social Login',
			'slug'        => 'miniorange-login-openid',
			'required'    => false,
			'version'     => '5.1',
			'description' => 'Allow your users to login, comment and share with Facebook, Google, Twitter, LinkedIn etc using customizable buttons. By miniOrange.',
		),

		array(
			'name'        => 'Thim Events',
			'slug'        => 'tp-event',
			'source'      => 'https://plugins.thimpress.com/downloads/eduma-plugins/tp-event.2.0.1.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/thim-event.png',
			'version'     => '2.0.1',
			'description' => 'Thim event – countdown By ThimPress.',
		),

		array(
			'name'        => 'Thim Portfolio',
			'slug'        => 'tp-portfolio',
			'source'      => 'https://plugins.thimpress.com/downloads/tp-portfolio.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/thim-portfolio.png',
			'version'     => '1.3',
			'description' => 'A plugin that allows you to show off your portfolio. By ThimPress.',
		),

		array(
			'name'        => 'LearnPress Certificates',
			'slug'        => 'learnpress-certificates',
			'source'      => 'https://plugins.thimpress.com/downloads/eduma-plugins/learnpress-certificates.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/learnpress-certificates.png',
			'version'     => '2.2.5',
			'description' => 'An addon for LearnPress plugin to create certificate for a course By ThimPress.',
            'add-on' => true,
		),

		array(
			'name'        => 'LearnPress Collections',
			'slug'        => 'learnpress-collections',
			'source'      => 'https://plugins.thimpress.com/downloads/eduma-plugins/learnpress-collections.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/learnpress-collections.png',
			'version'     => '2.1.2',
			'description' => 'Collecting related courses into one collection by administrator By ThimPress.',
		),

		array(
			'name'        => 'LearnPress - Paid Memberships Pro',
			'slug'        => 'learnpress-paid-membership-pro',
			'source'      => 'https://plugins.thimpress.com/downloads/eduma-plugins/learnpress-paid-membership-pro.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/learnpress-paidmembership.png',
			'version'     => '2.2.2',
			'description' => 'Paid Membership Pro add-on for LearnPress By ThimPress.',
            'add-on' => true,
		),

		array(
			'name'        => 'LearnPress Co-Instructors',
			'slug'        => 'learnpress-co-instructor',
			'source'      => 'https://plugins.thimpress.com/downloads/eduma-plugins/learnpress-co-instructor.zip',
			'required'    => false,
			'icon'        => THIM_URI . 'images/plugins/learnpress-co-instructor.png',
			'version'     => '2.0',
			'description' => 'Building courses with other instructors By ThimPress.',
            'add-on' => true,
		),

		array(
			'name'        => 'Thim Twitter',
			'slug'        => 'thim-twitter',
			'source'      => 'https://plugins.thimpress.com/downloads/thim-twitter.zip',
			'icon'        => THIM_URI . 'images/plugins/thim-twitter.png',
			'required'    => false,
			'version'     => '1.0.0',
			'description' => 'Thim Twitter plugin helps you get feed on your account easily. By Thimpress.',
            'add-on' => true,
		),

		array(
			'name'        => 'LearnPress',
			'slug'        => 'learnpress',
			'required'    => true,
			'version'     => '2.1.4.2',
			'description' => 'LearnPress is a WordPress complete solution for creating a Learning Management System (LMS). It can help you to create courses, lessons and quizzes. By ThimPress.',
		),

		array(
			'name'        => 'LearnPress Course Review',
			'slug'        => 'learnpress-course-review',
			'required'    => false,
			'version'     => '2.0',
			'description' => 'Adding review for course By ThimPress.',
            'add-on' => true,
		),

        array(
            'name'        => 'LearnPress - WooCommerce Payments',
            'slug'        => 'learnpress-woo-payment',
            'source'      => 'https://plugins.thimpress.com/downloads/eduma-plugins/learnpress-woo-payment.zip',
            'required'    => false,
            'version'     => '2.2.3.1',
            'description' => 'Using the payment system provided by WooCommerce.',
            'add-on' => true,
        ),

        array(
            'name'        => 'Thim Events - WooCommerce Payments',
            'slug'        => 'tp-event-woo-payment',
            'source'      => 'https://plugins.thimpress.com/downloads/eduma-plugins/tp-event-woo-payment.zip',
            'required'    => false,
            'version'     => '2.0',
            'description' => 'Support paying for a booking with the payment methods provided by Woocommerce.',
            'add-on' => true,
        ),

		array(
			'name'        => 'LearnPress Wishlist',
			'slug'        => 'learnpress-wishlist',
			'required'    => false,
			'version'     => '2.0',
			'description' => 'Wishlist feature By ThimPress.',
            'add-on' => true,
		),
		array(
			'name'        => 'LearnPress bbPress',
			'slug'        => 'learnpress-bbpress',
			'required'    => false,
			'version'     => '2.0',
			'description' => 'Using the forum for courses provided by bbPress By ThimPress.',
            'add-on' => true,
		),

//		array(
//			'name'        => 'Paid Memberships Pro',
//			'slug'        => 'paid-memberships-pro',
//			'required'    => false,
//			'version'     => '1.8.13.6',
//			'description' => 'Plugin to Handle Memberships By Stranger Studios.',
//		),

//		array(
//			'name'        => 'Widget Logic',
//			'slug'        => 'widget-logic',
//			'required'    => false,
//			'version'     => '5.7.2',
//			'description' => 'Control widgets with WP’s conditional tags is_home etc By wpchefgadget, alanft.',
//		),
//
		array(
			'name'        => 'Instagram Feed',
			'slug'        => 'instagram-feed',
			'required'    => false,
			'version'     => '1.4.8',
			'description' => 'Display beautifully clean, customizable, and responsive Instagram feeds By Smash Balloon.',
            'add-on' => true,
		),
//
//		array(
//			'name'        => 'WooCommerce Products Filter',
//			'slug'        => 'woocommerce-products-filter',
//			'required'    => false,
//			'version'     => '1.1.6.1',
//			'description' => 'WOOF – WooCommerce Products Filter. Flexible, easy and robust products filter for WooCommerce store site! By realmag777.',
//		),
	);

	return $plugins;
}

add_filter( 'thim_core_get_all_plugins_require', 'thim_get_all_plugins_require' );