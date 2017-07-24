<?php
/**
 * CoursePress utility functions and definitions
 *
 * @since 3.0
 * @package CoursePress
 */

/**
 * Check if current page is CoursePress admin page.
 *
 * @return bool|string Returns CoursePress screen ID on success or false.
 */
function coursepress_is_admin() {
    global $CoursePress_Admin_Page;


	if ( ! $CoursePress_Admin_Page instanceof CoursePress_Admin_Page ) {
		return false;
	}

    $screen_id = get_current_screen()->id;

    $pattern = '%toplevel_page_|coursepress-pro_page_|coursepress-base_page_|coursepress_page%';
    $id = preg_replace( $pattern, '', $screen_id );


	if ( in_array( $screen_id, $CoursePress_Admin_Page->__get( 'screens' ) ) ) {
		return $id;
	}

    return false;
}

/**
 * Get list of enrollment types.
 *
 * @return array
 */
function coursepress_get_enrollment_types() {
    $enrollment_types = array(
        'manually' => __( 'Manually added', 'cp' ),
        'registered' => __( 'Any registered users', 'cp' ),
        'passcode' => __( 'Any registered users with a pass code', 'cp' ),
        'prerequisite' => __( 'Registered users who completed the prerequisite course(s).', 'cp' ),
    );

    /**
     * Fire to allow additional enrollment types.
     *
     * @since 2.0
     */
    $enrollment_types = apply_filters( 'coursepress_course_enrollment_types', $enrollment_types );

    return $enrollment_types;
}

function coursepress_get_default_enrollment_type() {
    $default = 'registered';

    /**
     * Fire to allow default enrollment type to change.
     *
     * @since 2.0
     */
    $default = apply_filters( 'coursepress_course_enrollment_type_default', $default );

    return $default;
}

/**
 * Get the list of course categories.
 *
 * @return array
 */
function coursepress_get_categories() {
    $terms = get_terms( array( 'taxonomy' => 'course_category', 'hide_empty' => false ) );
    $cats = array();

	if ( ! empty( $terms ) ) {
		foreach ( $terms as $term ) {
			$cats[ $term->term_id ] = $term->name;
		}
	}

    return $cats;
}

/**
 * Get coursepress global setting.
 *
 * @param bool|string $key
 * @param mixed $default
 * @return mixed
 */
function coursepress_get_setting( $key = true, $default = '' ) {
    global $CoursePress_Data_Users;

    $caps = coursepress_get_array_val( $CoursePress_Data_Users->__get( 'capabilities' ), 'instructor' );
    $settings = coursepress_get_option( 'coursepress_settings', array() );

    $defaults = array(
        'general' => array(
        	'details_media_type' => 'default',
	        'details_media_priority' => 'default',
	        'listing_media_type' => 'default',
	        'listing_media_priority' => 'default',
	        'image_width' => 235,
	        'image_height' => 235,
        ),
        'slugs' => array(
        	'course' => 'courses',
	        'course_category' => 'course_category',
	        'units' => 'units',
	        'notifications' => 'notifications',
	        'discussions' => 'discussion',
	        'discussions_new' => 'add_new_discussion',
	        'grades' => 'grades',
	        'workbook' => 'workbook',
	        'login' => 'student-login',
	        'signup' => 'courses-signup',
	        'student_dashboard' => 'courses-dashboard',
	        'student_settings' => 'student-settings',
	        'instructor_profile' => 'instructor',
	        'pages' => array(
	        	'student_dashboard' => 0,
		        'student_settings' => 0,
		        'login' => 0,
	        )
        ),
        'emails' => array(),
        'capabilities' => array(
            'instructor' => $caps,
            'facilitator'=> $caps,
        ),
        'basic_certificate' => array(
            'enabled' => true,
            'use_cp_default' => false,
            'content' => '',
        ),
        'extensions' => array(),
        'marketpress' => array(
            'enabled' => true,
            'redirect' => true,
            'unpaid' => 'change_status',
            'delete' => 'change_status',
        ),
        'woocommerce' => array(
            'enabled' => true,
            'redirect' => true,
            'unpaid' => 'change_status',
            'delete' => 'change_status',
        ),
    );

    if ( ! empty( $settings ) ) {
        // Legacy settings
        $defaults['general']['image_width'] = coursepress_get_array_val( $settings, 'course/image_width' );
        $defaults['general']['image_height'] = coursepress_get_array_val( $settings, 'course/image_height' );
        $defaults['general']['details_media_type'] = coursepress_get_array_val( $settings, 'course/details_media_type' );
        $defaults['general']['details_media_priority'] = coursepress_get_array_val( $settings, 'course/details_media_priority' );
        $defaults['general']['listing_media_type'] = coursepress_get_array_val( $settings, 'course/listing_media_type' );
        $defaults['general']['listing_media_priority'] = coursepress_get_array_val( $settings, 'course/listing_media_priority' );
        $defaults['general']['order_by'] = coursepress_get_array_val( $settings, 'course/order_by' );
        $defaults['general']['order_by_direction'] = coursepress_get_array_val( $settings, 'course/order_by_direction' );
        $defaults['general']['instructor_show_username'] = coursepress_get_array_val( $settings, 'instructor/show_username' );
        $defaults['general']['enrollment_type_default'] = coursepress_get_array_val( $settings, 'course/enrollment_type_default' );
        $defaults['general']['reports_font'] = coursepress_get_array_val( $settings, 'reports/font' );
    }

    /**
     * Fire to allow setting the default settings.
     *
     * @since 3.0
     */
    $defaults = apply_filters( 'coursepress_default_settings', $defaults );
	$settings['general'] = wp_parse_args( $settings['general'], $defaults['general'] );
	$settings['slugs'] = wp_parse_args( $settings['slugs'], $defaults['slugs'] );

    if ( is_bool( $key ) && TRUE === $key ) {
        return $settings;
    }

    return coursepress_get_array_val( $settings, $key, $default );
}

/**
 * Helper function to update CP global settings.
 *
 * @param bool $key
 * @param mixed $value
 *
 * @return mixed
 */
function coursepress_update_setting( $key = true, $value ) {
    $settings = coursepress_get_setting( true );

    if ( ! is_bool( $key )  ) {
        $settings = coursepress_set_array_val( $settings, $key, $value );
    } else {
        $settings = $value;
    }

    coursepress_update_option( 'coursepress_settings', $settings );

    return $settings;
}

/**
 * Get or print the given filename.
 *
 * @param string $filename The relative path of the file.
 * @param array $args Optional arguments to set as variable
 * @param bool $echo Whether to return the result in string or not.
 * @return mixed
 */
function coursepress_render( $filename, $args = array(), $echo = true ) {
    global $CoursePress;

    $path = $CoursePress->plugin_path;
    $filename = $path . $filename . '.php';

    if ( file_exists( $filename ) && is_readable( $filename ) ) {
        if ( ! empty( $args ) ) {
            $args = (array) $args;

            foreach ( $args as $key => $value ) {
                $$key = $value;
            }
        }

		if ( $echo ) {
			include $filename;
        } else {
			ob_start();

            include $filename;

            return ob_get_clean();
        }
        return true;
    }

    return false;
}

/**
 * Get coursepress template or load current theme's custom coursepress template.
 *
 * @param string $name
 * @param string $slug
 *
 * @return mixed
 */
function coursepress_get_template( $name, $slug = '' ) {
    $template = implode( '-', array( $name, $slug ) );

    if ( ! locate_template( $template . '.php' ) ) {
        coursepress_render( 'templates/' . $template );
    }

    return null;
}

/**
 * Helper function to get the value of an dimensional array base on path.
 *
 * @param array $array
 * @param string $key
 * @param mixed $default
 *
 * @return mixed|null|string
 */
function coursepress_get_array_val( $array, $key, $default = '' ) {

	if ( ! is_array( $array ) ) {
		return null;
	}

    $keys = explode( '/', $key );
    $last_key = array_pop( $keys );

	foreach ( $keys as $k ) {
		if ( isset( $array[ $k ] ) ) {
			$array = $array[ $k ];
		}
	}

	if ( isset( $array[ $last_key ] ) ) {
		return $array[ $last_key ];
	}

    return $default;
}

/**
 * Helper function to set an array value base on path.
 *
 * @param $array
 * @param $path
 * @param $value
 *
 * @return array
 */
function coursepress_set_array_val( $array, $path, $value ) {

	if ( ! is_array( $path ) ) {
		$path = explode( '/', $path );
	}

	if ( ! is_array( $array ) ) {
		$array = array();
	}

    $key = array_shift( $path );

	if ( count( $path ) > 0 ) {
		if ( ! isset( $array[ $key ] ) ) {
			$array[ $key ] = array();
		}

		$array[ $key ] = coursepress_set_array_val( $array[ $key ], $path, $value );
	} else {
		$array[ $key ] = $value;
	}

    return $array;
}

/**
 * Helper function to get global option in either single or multi site.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function coursepress_get_option( $key, $default = '' ) {
	if ( is_multisite() ) {
		$value = get_site_option( $key, $default );
	} else {
	    $value = get_option( $key, $default );
    }

    return $value;
}

/**
 * Helper function to update global option in either single or multi site.
 *
 * @param $key
 * @param $value
 */
function coursepress_update_option( $key, $value ) {
    if ( is_multisite() ) {
        update_site_option( $key, $value );
    } else {
        update_option( $key, $value );
    }
}

/**
 * Get CoursePress courses url.
 *
 * @return string
 */
function coursepress_get_url() {
    $slug = coursepress_get_setting( 'slugs/course', 'courses' );

    return trailingslashit( home_url( '/' . $slug ) );
}

/**
 * Check if the given user have comments on the given course, unit or step ID.
 *
 * @param int $student_id
 * @param int $post_id
 *
 * @return bool
 */
function coursepress_user_have_comments( $student_id, $post_id ) {
    $args = array(
        'post_id' => $post_id,
        'user_id' => $student_id,
        'order' => 'ASC',
        'offset' => 0,
        'number' => 1, // We only need one to verify if current user posted a comment.
        'fields' => 'ids',
        'status' => 'all',
    );
    $comments = get_comments( $args );

    return count( $comments ) > 0;
}

/**
 * Get HTML progress wheel.
 *
 * @param array $attr
 *
 * @return string
 */
function coursepress_progress_wheel( $attr = array() ) {
    global $CoursePress_Core;

    $core = $CoursePress_Core;
    $defaults = array(
        'class'                     => '',
        'data-value'                 => 100,
        'data-start-angle'           => '4.7',
        'data-size'                  => 36,
        'data-knob-data-height'      => 40,
        'data-empty-fill'            => 'rgba(0, 0, 0, 0.2)',
        'data-fill-color'            => '#24bde6',
        'data-bg-color'              => '#e0e6eb',
        'data-thickness'             => '6',
        'data-format'                => true,
        'data-style'                 => 'extended',
        'data-animation-start-value' => '1.0',
        'data-knob-data-thickness'   => 0.18,
        'data-knob-text-show'        => true,
        'data-knob-text-color'       => '#222222',
        'data-knob-text-align'       => 'center',
        'data-knob-text-denominator' => '4.5',
    );

    $attr = wp_parse_args( $attr, $defaults );
    $class = array( 'course-progress-disc' );

    if ( ! empty( $attr['class'] ) ) {
        $class[] = $attr['class'];
    }
    $value = $attr['data-value'];

    if ( intval( $value ) > 0 ) {
        $value = intval( $value ) / 100;
        $attr['data-value'] = $value;
    }

    $attr['class'] = implode( ' ', $class );

    return $core->create_html( 'div', $attr );
}

/**
 * Returns breadcrumb.
 *
 * @return null
 */
function coursepress_breadcrumb() {
    global $CoursePress_VirtualPage;

	if ( ! $CoursePress_VirtualPage instanceof CoursePress_VirtualPage ) {
		return null;
	}

    $vp = $CoursePress_VirtualPage;
    $items = $CoursePress_VirtualPage->__get( 'breadcrumb' );

    if ( ! empty( $items ) ) {
        $breadcrumb = '';

        // Make the last item non-clickable
        $last_item = array_pop( $items );

        foreach ( $items as $item ) {
            $attr = array( 'class' => 'course-item' );
            $breadcrumb .= $vp->create_html( 'li', $attr, $item );
        }

        $breadcrumb .= $vp->create_html( 'li', array( 'class' => 'current' ), wp_strip_all_tags( $last_item ) );

        echo $vp->create_html( 'ul', array( 'class' => 'course-breadcrumb' ), $breadcrumb );
    }

    return '';
}

/**
 * Generate HTML block.
 *
 * @param $tag
 * @param array $attributes
 * @param string $content
 *
 * @return null|string
 */
function coursepress_create_html( $tag, $attributes = array(), $content = '' ) {
    global $CoursePress_Core;

	if ( ! $CoursePress_Core instanceof CoursePress_Core ) {
		return null;
	}

    return $CoursePress_Core->create_html( $tag, $attributes, $content );
}

/**
 * Returns true if the WP installation allows user registration.
 *
 * @since  1.0.0
 * @return bool If CoursePress allows user signup.
 */
function coursepress_users_can_register() {
	static $_allow_register = null;

	if ( null === $_allow_register ) {
		if ( is_multisite() ) {
			$_allow_register = users_can_register_signup_filter();
		} else {
			$_allow_register = get_option( 'users_can_register' );
		}

		/**
		 * Filter the return value to allow users to manually enable
		 * CoursePress registration only.
		 *
		 * @since 2.0.0
		 * @var bool $_allow_register
		 */
		$_allow_register = apply_filters( 'coursepress_users_can_register', $_allow_register );
	}

	return $_allow_register;
}

/**
 * Generate alert message.
 *
 * @param string $content Message content.
 * @param string $type    Alert type.
 *
 * @since 3.0.0
 *
 * @return string
 */
function coursepress_alert_message( $content = '', $type = 'info' ) {

	$html = '<div class="cp-alert cp-alert-' . $type . '">';
	$html .= $content;
	$html .= '</div>';

	return $html;
}

function coursepress_convert_hex_color_to_rgb($hex_color, $default)
{
    $color_valid = (boolean) preg_match('/^#[a-f0-9]{6}$/i', $hex_color);
    if($color_valid)
    {
        $values = CP_TCPDF_COLORS::convertHTMLColorToDec($hex_color, CP_TCPDF_COLORS::$spotcolor);
        return array_values($values);
    }

    return $default;
}

function coursepress_download_file( $requested_file ) {
    global $CoursePress;

    ob_start();

    $requested_file_obj = wp_check_filetype( $requested_file );
    $filename = basename( $requested_file );

    /**
     * Filter used to alter header params. E.g. removing 'timeout'.
     */
    $force_download_parameters = apply_filters(
        'coursepress_force_download_parameters',
        array(
            'timeout' => 60,
            'user-agent' => $CoursePress->name . ' / ' . $CoursePress->version . ';',
        )
    );

    $body = wp_remote_retrieve_body( wp_remote_get( $requested_file ), $force_download_parameters );
    if ( empty( $body ) && preg_match( '/^https/', $requested_file ) ) {
        $requested_file = preg_replace( '/^https/', 'http', $requested_file );
        $body = wp_remote_retrieve_body( wp_remote_get( $requested_file ), $force_download_parameters );
    }
    if ( ! empty( $body ) ) {
        header( 'Pragma: public' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Cache-Control: private', false );
        header( 'Content-Type: ' . $requested_file_obj['type'] );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Connection: close' );
        echo $body;
    } else {
        _e( 'Something went wrong.', 'CP_TD' );
    }
    exit();
}

/**
 * Update last activity of the user.
 *
 * This helpfer function can be used to log the latest activity
 * of a student or even any other user.
 *
 * @param string $activity Activity.
 * @param integer $user_id User ID.
 *
 * @since 3.0
 */
function coursepress_log_last_activity( $activity = 'unknown', $user_id = 0 ) {

	// If user id is not given, get current user id.
	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
	if ( empty( $user_id ) ) {
		return;
	}

	// Save only string or numeric values.
	if ( ! is_string( $activity ) && ! is_numeric( $activity ) ) {
		return;
	}

	$allowed_activities = array(
		'course_module_seen',
		'course_seen',
		'course_unit_seen',
		'enrolled',
		'login',
		'module_answered',
	);

	/**
	 * Filter to allow custom activities to log.
	 *
	 * @param array $allowed_activities
	 */
	$allowed_activities = apply_filters( 'coursepress_allowed_activities', $allowed_activities );

	// For other activities, set as unknown.
	if ( ! in_array( $activity, $allowed_activities ) ) {
		$activity = 'unknown';
	}

	update_user_meta( $user_id, 'coursepress_latest_activity_time', time() );
	update_user_meta( $user_id, 'coursepress_latest_activity', $activity );
}

function coursepress_set_cookie( $key, $value, $time ) {
	$key = $key . '_' . COOKIEHASH;
	setcookie( $key, $value, $time, COOKIEPATH, COOKIE_DOMAIN );
}

function coursepress_delete_cookie( $key ) {
	$key = $key . '_' . COOKIEHASH;

	ob_start();
	$content = ob_get_clean();
	setcookie( $key, false, -1, COOKIEPATH, COOKIE_DOMAIN );
}