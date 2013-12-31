<?php
/**
 * Register Settings
 *
 * @package     Coming_Soon
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2013, Pippin Williamson modification by John Turner
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 *
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0.4
 * @return mixed
 */
function seedcs_get_option( $key = '', $default = false ) {
	global $seedcs_options;
	return isset( $seedcs_options[ $key ] ) ? $seedcs_options[ $key ] : $default;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array  settings
 */
function seedcs_get_settings() {

	$settings = get_option( 'seedcs_settings' );

	if( empty( $settings ) ) {

		$settings = array();

		update_option( 'seedcs_settings', $settings );

	}
	return apply_filters( 'seedcs_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
*/
function seedcs_register_settings() {

	if ( false == get_option( 'seedcs_settings' ) ) {
		add_option( 'seedcs_settings' );
	}

	foreach( seedcs_get_registered_settings() as $tab => $settings ) {

		add_settings_section(
			'seedcs_settings_' . $tab,
			__return_null(),
			'__return_false',
			'seedcs_settings_' . $tab
		);

		foreach ( $settings as $option ) {
			add_settings_field(
				'seedcs_settings[' . $option['id'] . ']',
				$option['name'],
				function_exists( 'seedcs_' . $option['type'] . '_callback' ) ? 'seedcs_' . $option['type'] . '_callback' : 'seedcs_missing_callback',
				'seedcs_settings_' . $tab,
				'seedcs_settings_' . $tab,
				array(
					'id'      => $option['id'],
					'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
					'name'    => $option['name'],
					'section' => $tab,
					'size'    => isset( $option['size'] ) ? $option['size'] : null,
					'options' => isset( $option['options'] ) ? $option['options'] : '',
					'std'     => isset( $option['std'] ) ? $option['std'] : ''
				)
			);
		}

	}

	// Creates our settings in the options table
	register_setting( 'seedcs_settings', 'seedcs_settings', 'seedcs_settings_sanitize' );

}
add_action('admin_init', 'seedcs_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.0.0
 * @return array
*/
function seedcs_get_registered_settings() {

	$pages = get_pages();
	$pages_options = array( 0 => '' ); // Blank option
	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	/**
	 * 'Whitelisted'  settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */
	$seedcs_settings = array(
		/** General Settings */
		'general' => apply_filters( 'seedcs_settings_general',
			array(

				'status' => array(
					'id' => 'status',
					'name' => __( 'Status', 'seedcs' ),
					'desc' => __( 'When you are logged in you will see your normal website. Logged out visitors will see the Coming Soon or Maintenance page. Coming Soon Mode will be available to search engines if your site is not private. Maintenance Mode will notify search engines that the site is unavailable. <a href="'.home_url().'?seedcs_preview=true">Preview</a>', 'seedcs' ),
					'type' => 'select',
					'options' => array('-1'=>'Disabled','1'=>'Coming Soon Mode','2'=>'Maintenance Mode')
				),

				'logo' => array(
					'id' => 'logo',
					'name' => __( 'Logo', 'seedcs' ),
					'desc' => __( 'Upload a logo or enter the url to your image.', 'seedcs' ),
					'type' => 'upload',
				),

				'headline' => array(
					'id' => 'headline',
					'name' => __( 'Headline', 'seedcs' ),
					'desc' => __( 'Enter a headline for your page.', 'seedcs' ),
					'type' => 'text',
				),

				'description' => array(
					'id' => 'description',
					'name' => __( 'Description', 'seedcs' ),
					'desc' => __( 'Enter a descrption for your page.', 'seedcs' ),
					'type' => 'rich_editor',
				),
			)
		),

		/** Design Settings */
		'design' => apply_filters( 'seedcs_settings_design',
			array(

				'background_color' => array(
					'id' => 'background_color',
					'name' => __( 'Background Color', 'seedcs' ),
					'type' => 'color',
				),

			)
		),
		/** Extension Settings */
		'extensions' => apply_filters('seedcs_settings_extensions',
			array()
		),

	);

	return $seedcs_settings;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function seedcs_header_callback( $args ) {
	echo '';
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_checkbox_callback( $args ) {
	global $seedcs_options;

	$checked = isset($seedcs_options[$args['id']]) ? checked(1, $seedcs_options[$args['id']], false) : '';
	$html = '<input type="checkbox" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_multicheck_callback( $args ) {
	global $seedcs_options;

	foreach( $args['options'] as $key => $option ):
		if( isset( $seedcs_options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
		echo '<input name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
		echo '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
	endforeach;
	echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.3.3
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_radio_callback( $args ) {
	global $seedcs_options;

	foreach ( $args['options'] as $key => $option ) :
		$checked = false;

		if ( isset( $seedcs_options[ $args['id'] ] ) && $seedcs_options[ $args['id'] ] == $key )
			$checked = true;
		elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $seedcs_options[ $args['id'] ] ) )
			$checked = true;

		echo '<input name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
		echo '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
	endforeach;

	echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_text_callback( $args ) {
	global $seedcs_options;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $size . '-text" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_textarea_callback( $args ) {
	global $seedcs_options;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<textarea class="large-text" cols="50" rows="5" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.3
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_password_callback( $args ) {
	global $seedcs_options;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="password" class="' . $size . '-text" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.3.1
 * @param array $args Arguments passed by the setting
 * @return void
 */
function seedcs_missing_callback($args) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'seedcs' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_select_callback($args) {
	global $seedcs_options;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<select id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

	foreach ( $args['options'] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_color_select_callback( $args ) {
	global $seedcs_options;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<select id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

	foreach ( $args['options'] as $option => $color ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @global $wp_version WordPress Version
 */
function seedcs_rich_editor_callback( $args ) {
	global $seedcs_options, $wp_version;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
		$html = wp_editor( stripslashes( $value ), 'seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']', array( 'textarea_name' => 'seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']' ) );
	} else {
		$html = '<textarea class="large-text" rows="10" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	}

	$html .= '<br/><label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_upload_callback( $args ) {
	global $seedcs_options;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[$args['id']];
	else
		$value = isset($args['std']) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $size . '-text seedcs_upload_field" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<input type="button" class="seedcs_settings_upload_button button-secondary" value="' . __( 'Upload File', 'seedcs' ) . '"/>';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
	wp_enqueue_media();
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.6
 * @param array $args Arguments passed by the setting
 * @global $seedcs_options Array of all the  Options
 * @return void
 */
function seedcs_color_callback( $args ) {
	global $seedcs_options;

	if ( isset( $seedcs_options[ $args['id'] ] ) )
		$value = $seedcs_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$default = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="seedcs-color-picker" id="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" name="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
	$html .= '<label for="seedcs_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0.8.2
 * @param array $args Arguments passed by the setting
 * @return void
 */
function seedcs_hook_callback( $args ) {
	do_action( 'seedcs_' . $args['id'] );
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 1.0.8.2
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function seedcs_settings_sanitize( $input = array() ) {

	global $seedcs_options;

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$output    = array();
	$settings  = seedcs_get_registered_settings();
	$tab       = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
	$post_data = isset( $_POST[ 'seedcs_settings_' . $tab ] ) ? $_POST[ 'seedcs_settings_' . $tab ] : array();

	$input = apply_filters( 'seedcs_settings_' . $tab . '_sanitize', $post_data );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

		if( $type ) {
			// Field type specific filter
			$output[ $key ] = apply_filters( 'seedcs_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$output[ $key ] = apply_filters( 'seedcs_settings_sanitize', $value, $key );
	}


	// Loop through the whitelist and unset any that are empty for the tab being saved
	if( ! empty( $settings[ $tab ] ) ) {
		foreach( $settings[ $tab ] as $key => $value ) {

			// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
			if( is_numeric( $key ) ) {
				$key = $value['id'];
			}

			if( empty( $_POST[ 'seedcs_settings_' . $tab ][ $key ] ) ) {
				unset( $seedcs_options[ $key ] );
			}

		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $seedcs_options, $output );

	add_settings_error( 'seedcs-notices', '', __( 'Settings Updated', 'seedcs' ), 'updated' );

	return $output;

}

/**
 * Misc Settings Sanitization
 *
 * @since 1.6
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function seedcs_settings_sanitize_misc( $input ) {

	global $seedcs_options;


	return $input;
}
add_filter( 'seedcs_settings_misc_sanitize', 'seedcs_settings_sanitize_misc' );

/**
 * Sanitize text fields
 *
 * @since 1.0.0
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function seedcs_sanitize_text_field( $input ) {
	return trim( $input );
}
add_filter( 'seedcs_settings_sanitize_text', 'seedcs_sanitize_text_field' );

/**
 * Retrieve settings tabs
 *
 * @since 1.0.0
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function seedcs_get_settings_tabs() {

	$settings = seedcs_get_registered_settings();

	$tabs             = array();
	$tabs['general']  = __( 'General', 'seedcs' );
	$tabs['design']      = __( 'Design', 'seedcs' );

	if( ! empty( $settings['extensions'] ) ) {
		$tabs['extensions'] = __( 'Extensions', 'seedcs' );
	}

	return apply_filters( 'seedcs_settings_tabs', $tabs );
}
