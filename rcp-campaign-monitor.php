<?php
/**
 * Plugin Name: Restrict Content Pro - Campaign Monitor
 * Plugin URL: https://restrictcontentpro.com/downloads/campaign-monitor/
 * Description: Include a Campaign Monitor signup option with your Restrict Content Pro registration form
 * Version: 1.2.3
 * Author: iThemes
 * Author URI: https://ithemes.com/
 * Contributors: jthillithemes, layotte, ithemes
 * Text Domain: rcp-campaign-monitor
 * iThemes Package: restrict-content-pro-campaign-monitor
 */

$rcp_cm_options = get_option( 'rcp_cm_settings' );

/**
 * Load plugin text domain for translations
 *
 * @since 1.0.3
 * @return void
 */
function rcp_cm_load_textdomain() {

	// Set filter for plugin's languages directory
	$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$lang_dir = apply_filters( 'rcp_campaign_monitor_languages_directory', $lang_dir );

	// Load the translations
	load_plugin_textdomain( 'rcp-campaign-monitor', false, $lang_dir );

}
add_action( 'init', 'rcp_cm_load_textdomain' );

/**
 * Maybe load deprecated action hooks.
 * We put this in `plugins_loaded` to prevent the "Extra" column from loading unnecessarily.
 *
 * @since 1.1
 */
add_action( 'plugins_loaded', function() {
	if ( ! function_exists( 'rcp_get_membership' ) ) {
		add_action( 'rcp_set_status', 'rcp_cm_add_to_list', 10, 4 );

		add_action( 'rcp_members_page_table_header', 'rcp_add_cm_table_column_header_and_footer' );
		add_action( 'rcp_members_page_table_footer', 'rcp_add_cm_table_column_header_and_footer' );
		add_action( 'rcp_members_page_table_column', 'rcp_add_cm_table_column_content' );
	}
} );

/**
 * Add settings page
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_settings_menu() {
	add_submenu_page( 'rcp-members', __( 'Restrict Content Pro Campaign Monitor Settings', 'rcp-campaign-monitor' ), __( 'Campaign Monitor', 'rcp-campaign-monitor' ), 'manage_options', 'rcp-campaign-monitor', 'rcp_cm_settings_page' );
}

add_action( 'admin_menu', 'rcp_cm_settings_menu', 100 );

/**
 * Register the plugin settings
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_register_settings() {

	// register the settings section

	add_settings_section(
		'rcp_cm_settings',
		__( 'Campaign Monitor Settings', 'rcp-campaign-monitor' ),
		'rcp_cm_description_callback',
		'rcp_cm_settings'
	);

	// register the settings fields

	add_settings_field(
		'rcp_cm_settings[cm_api]',
		__( 'API Key', 'rcp-campaign-monitor' ),
		'rcp_cm_text_callback',
		'rcp_cm_settings',
		'rcp_cm_settings',
		array(
			'id'      => 'cm_api',
			'desc'    => __( 'Enter your Campaign Monitor API key to enable a newsletter signup option with the registration form.', 'rcp-campaign-monitor' ),
			'name'    => __( 'API Key', 'rcp-campaign-monitor' ),
			'options' => null,
		)
	);
	add_settings_field(
		'rcp_cm_settings[cm_client]',
		__( 'Client ID', 'rcp-campaign-monitor' ),
		'rcp_cm_text_callback',
		'rcp_cm_settings',
		'rcp_cm_settings',
		array(
			'id'      => 'cm_client',
			'desc'    => __( 'Enter the ID of the client to use. The ID can be found in the Client Settings page of the client.', 'rcp-campaign-monitor' ),
			'name'    => __( 'Client ID', 'rcp-campaign-monitor' ),
			'options' => null,
		)
	);
	add_settings_field(
		'rcp_cm_settings[cm_list]',
		__( 'List', 'rcp-campaign-monitor' ),
		'rcp_cm_select_callback',
		'rcp_cm_settings',
		'rcp_cm_settings',
		array(
			'id'      => 'cm_list',
			'desc'    => __( 'Choose the list to subscribe users to.', 'rcp-campaign-monitor' ),
			'name'    => __( 'List', 'rcp-campaign-monitor' ),
			'options' => rcp_cm_get_lists(),
		)
	);
	add_settings_field(
		'rcp_cm_settings[cm_label]',
		__( 'Form Label', 'rcp-campaign-monitor' ),
		'rcp_cm_text_callback',
		'rcp_cm_settings',
		'rcp_cm_settings',
		array(
			'id'      => 'cm_label',
			'desc'    => __( 'Enter the label to be shown on the "Signup for Newsletter" checkbox', 'rcp-campaign-monitor' ),
			'name'    => __( 'Form Label', 'rcp-campaign-monitor' ),
			'options' => null,
		)
	);
	add_settings_field(
		'rcp_cm_settings[default_checkbox_state]',
		__( 'Opt-In Checkbox Default State', 'rcp-campaign-monitor' ),
		'rcp_cm_select_callback',
		'rcp_cm_settings',
		'rcp_cm_settings',
		array(
			'id'      => 'default_checkbox_state',
			'desc'    => __( 'Choose whether you want the opt-in on the registration form checked or unchecked by default.', 'rcp-campaign-monitor' ),
			'name'    => __( 'Opt-In Checkbox Default State', 'rcp-campaign-monitor' ),
			'options' => array(
				'checked'   => __( 'Checked', 'rcp-campaign-monitor' ),
				'unchecked' => __( 'Unchecked', 'rcp-campaign-monitor' )
			)
		)
	);


	// create whitelist of options
	register_setting( 'rcp_cm_settings', 'rcp_cm_settings' );
}

add_action( 'admin_init', 'rcp_cm_register_settings', 100 );

/**
 * Description callback
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_description_callback() {
	echo __( 'Configure the settings below', 'rcp-campaign-monitor' );
}

/**
 * Text field callback
 *
 * @param array $args
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_text_callback( $args ) {

	global $rcp_cm_options;
    $html = '';

	if ( $args['id'] !== 'cm_label') {
		$value = $rcp_cm_options[ $args['id'] ];
		$type = !empty( $rcp_cm_options[ $args['id'] ] ) ? 'password' : 'text';
		$html  = '<input 
		    type="' . $type . '" 
		    class="regular-text" 
		    id="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']" 
		    name="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']" 
		    value="' . esc_attr( $value ) . '"
        ';

		if ( $args['id'] === 'cm_api' ) {
			$html .= 'placeholder="' . __( 'Enter your API Key', 'rcp-campaign-monitor' ) . '" />';
        } elseif ( $args['id'] === 'cm_client' ) {
			$html .= 'placeholder="' . __( 'Enter your Client ID', 'rcp-campaign-monitor' ) . '" />';
        }

		if ( $type === 'password' ) {
		    $html .= "<button type='button' class='button button-secondary wp-hide-pw hide-if-no-js'>";
			$html .= '<span toggle="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']"" class="dashicons dashicons-hidden toggle-password"></span>';
			$html .= "</button>";


		}
		$html  .= '<div class="description"><label for="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']"> ' . $args['desc'] . '</label></div>';
    } else {
		$value = ! empty( $rcp_cm_options[ $args['id'] ] ) ? $rcp_cm_options[ $args['id'] ] : __( 'Sign up for our mailing list', 'rcp-campaign-monitor' );
		$html  = '<input type="text" class="regular-text" id="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']" name="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';
		$html  .= '<div class="description"><label for="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']"> ' . $args['desc'] . '</label></div>';
    }

	echo $html;

}

/**
 * Select field callback
 *
 * @param array $args
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_select_callback( $args ) {

	global $rcp_cm_options;

	$value = isset( $rcp_cm_options[ $args['id'] ] ) ? $rcp_cm_options[ $args['id'] ] : '';
	$html  = '<select id="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']" name="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']"/>';
	foreach ( $args['options'] as $option => $name ) {
		$html .= '<option value="' . esc_attr( $option ) . '" ' . selected( $option, $value, false ) . '>' . esc_html( $name ) . '</option>';
	}
	$html .= '</select>';
	$html .= '<div class="description"><label for="rcp_cm_settings[' . esc_attr( $args['id'] ) . ']"> ' . $args['desc'] . '</label></div>';

	echo $html;

}

/**
 * Render settings page
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_settings_page() {

	global $rcp_cm_options;

	?>
	<div class="wrap">

		<?php settings_errors( 'rcp_cm_settings' ); ?>

		<form method="post" action="options.php" class="rcp_options_form">

			<?php
			settings_fields( 'rcp_cm_settings' );
			do_settings_sections( 'rcp_cm_settings' );
			?>
			<?php submit_button( __( 'Save Options', 'rcp-campaign-monitor' ) ); ?>

		</form>
	</div><!--end .wrap-->
	<?php
}

/**
 * Get an array of all Campaign Monitor subscription lists
 *
 * @since 1.0
 * @return array
 */
function rcp_cm_get_lists() {

	global $rcp_cm_options;

	if ( strlen( trim( $rcp_cm_options['cm_api'] ) ) > 0 && strlen( trim( $rcp_cm_options['cm_client'] ) ) > 0 ) {

		$lists = array();

		try {
			if ( ! class_exists( 'CS_REST_Clients' ) ) {
				require_once( dirname( __FILE__ ) . '/vendor/csrest_clients.php' );
			}

			$wrap = new CS_REST_Clients( $rcp_cm_options['cm_client'], $rcp_cm_options['cm_api'] );
		} catch( Exception $e ) {
			if ( function_exists( 'rcp_log' ) ) {
				rcp_log( sprintf( 'Campaign Monitor exception when getting lists: %s', $e->getMessage() ) );
			}

			return array();
		}

		try {
			$result = $wrap->get_lists();
        } catch( Exception $e ) {
		    if ( function_exists( 'rcp_log' ) ) {
		        rcp_log( sprintf( 'Campaign Monitor exception when getting lists: %s', $e->getMessage() ) );
            }
        }

		if ( $result->was_successful() ) {
			if ( empty( $result->response ) ) {
				// No lists in Campaign Monitor.
				return array( __( 'No Campaign Monitor lists found', 'rcp-campaign-monitor' ) );
			} else {
				foreach ( $result->response as $list ) {
					$lists[ $list->ListID ] = $list->Name;
				}
			}

			return $lists;
		}
	}

	return array( __( 'Enter your API key and Client ID above', 'rcp-campaign-monitor' ) );
}

/**
 * Adds an email to the Campaign Monitor subscription list
 *
 * @param string               $email      Email address to add.
 * @param string               $name       Name of the subscriber.
 * @param RCP_Member|false     $member     Deprecated. Member object.
 * @param RCP_Membership|false $membership Membership object.
 *
 * @since 1.0
 * @return bool Whether or not it was added successfully.
 */
function rcp_cm_subscribe_email( $email, $name, $member = false, $membership = false ) {
	global $rcp_cm_options;

	if ( ! strlen( trim( $rcp_cm_options['cm_api'] ) ) > 0 ) {
		return false;
	}

	$list_id = $rcp_cm_options['cm_list'];

	try {
		if ( ! class_exists( 'CS_REST_Subscribers' ) ) {
			require_once( dirname( __FILE__ ) . '/vendor/csrest_subscribers.php' );
		}

		$wrap = new CS_REST_Subscribers( $list_id, $rcp_cm_options['cm_api'] );
	} catch ( Exception $e ) {
		if ( function_exists( 'rcp_log' ) ) {
			rcp_log( sprintf( 'Campaign Monitor exception when subscribing email: %s', $e->getMessage() ) );
		}

		return false;
	}

	$args = array(
		'EmailAddress'      => $email,
		'Name'              => $name,
        'Resubscribe'       => true,
        'ConsentToTrack'    => 'Yes'
	);

	$membership_status = false;
	if ( is_a( $membership, 'RCP_Membership' ) ) {
		$membership_status = $membership->get_status();
	} elseif ( is_a( $member, 'RCP_Member' ) ) {
		$membership_status = $member->get_status();
	}

	rcp_log('RCP Campaign Monitor Membership Status: ' . $membership_status, true);

	if ( function_exists( 'rcp_multiple_memberships_enabled' ) && ! rcp_multiple_memberships_enabled() && ! empty( $membership_status ) ) {
		$custom_field_key = rcp_cm_get_custom_field_key( $rcp_cm_options['cm_list'] );
		rcp_log('RCP Campaign Monitor Custom Field Key: ' . $custom_field_key, true);
		rcp_log('RCP Campaign Monitor Membership Status: ' . $membership_status, true);
		if ( ! empty( $custom_field_key ) ) {
			$args['CustomFields'] = array(
				array(
					'Key'   => $custom_field_key,
					'Value' => $membership_status
				)
			);
		}
	}

	$subscribe = $wrap->add( $args );

	if ( $subscribe->was_successful() ) {
		$user_id = 0;

		if ( is_object( $member ) ) {
			$user_id = $member->ID;
		} elseif ( is_a( $membership, 'RCP_Membership' ) ) {
			$user_id = $membership->get_customer()->get_user_id();
		}

		if ( ! empty( $user_id ) ) {
			$subscribed_lists = get_user_meta( $user_id, 'rcp_campaign_monitor_subscribed_lists', true );

			if ( ! is_array( $subscribed_lists ) ) {
				$subscribed_lists = array();
			}

			if ( ! in_array( $list_id, $subscribed_lists ) ) {
				$subscribed_lists[] = sanitize_text_field( $list_id );

				update_user_meta( $user_id, 'rcp_campaign_monitor_subscribed_lists', $subscribed_lists );
			}
		}

		return true;
	}

	rcp_log( 'RCP Campaign Monitor: Subscribe Failed' );
	return false;
}

/**
 * Update "RCP Status" custom field value when their membership's status changes.
 *
 * @param string $old_status    Previous status, before this update.
 * @param string $new_status    New membership status.
 * @param int    $membership_id ID of the membership.
 *
 * @since 1.1
 * @return void
 */
function rcp_cm_update_subscriber_membership_status( $old_status, $new_status, $membership_id ) {

	global $rcp_cm_options;

	$api_key         = trim( $rcp_cm_options['cm_api'] );
	$default_list_id = $rcp_cm_options['cm_list'];

	if ( empty( $api_key ) || empty( $default_list_id ) ) {
		if ( function_exists( 'rcp_log' ) ) {
			rcp_log( 'Campaign Monitor: Empty API key or list ID. Not editing subscriber status - exiting.' );
		}

		return;
	}

	// Bail if multiple memberships is enabled.
	if ( rcp_multiple_memberships_enabled() ) {
		return;
	}

	$membership = rcp_get_membership( $membership_id );

	if ( empty( $membership ) ) {
		return;
	}

	// If this membership is disabled, we're not going to track it.
	if ( $membership->is_disabled() ) {
		return;
	}

	$user_id = $membership->get_customer()->get_user_id();
	$user    = get_userdata( $user_id );

	if ( empty( $user ) ) {
		return;
	}

	$subscribed_lists = get_user_meta( $user->ID, 'rcp_campaign_monitor_subscribed_lists', true );

	if ( empty( $subscribed_lists ) || ! is_array( $subscribed_lists ) ) {
		$subscribed_lists = array( $default_list_id ); // Backwards compat.
	}

	if ( ! class_exists( 'CS_REST_Subscribers' ) ) {
		require_once( dirname( __FILE__ ) . '/vendor/csrest_subscribers.php' );
	}

	foreach ( $subscribed_lists as $list_id ) {
		if ( function_exists( 'rcp_log' ) ) {
			rcp_log( sprintf( 'Campaign Monitor: Updating RCP Status custom field for user #%d on list ID %s.', $user->ID, $list_id ) );
		}

		$custom_field_key = rcp_cm_get_custom_field_key( $list_id );

		if ( empty( $custom_field_key ) ) {
			if ( function_exists( 'rcp_log' ) ) {
				rcp_log( 'Campaign Monitor: Empty custom field value - exiting.' );
			}

			continue;
		}

		$wrap = new CS_REST_Subscribers( $list_id, array(
			'api_key' => $api_key
		) );

		$result = $wrap->update( $user->user_email, array(
			'CustomFields' => array(
				array(
					'Key'   => $custom_field_key,
					'Value' => $new_status
				)
			),
			'ConsentToTrack'    => 'Yes'
		) );

		if ( $result->was_successful() ) {
			if ( function_exists( 'rcp_log' ) ) {
				rcp_log( 'Campaign Monitor: Successfully updated subscriber.' );
			}
		} else {
			if ( function_exists( 'rcp_log' ) ) {
				rcp_log( sprintf( 'Campaign Monitor: Failed to update subscriber with code %d. Response: %s', $result->http_status_code, var_export( $result->response, true ) ) );
			}
		}
	}

}
add_action( 'rcp_transition_membership_status', 'rcp_cm_update_subscriber_membership_status', 10, 3 );

/**
 * Returns the Campaign Monitor custom field key for RCP Status. It gets created if
 * it's not yet set.
 *
 * @param string $list_id ID of the list to create the key for.
 *
 * @since 1.1
 * @return string
 */
function rcp_cm_get_custom_field_key( $list_id ) {

	global $rcp_cm_options;

	if ( function_exists( 'rcp_log' ) ) {
		rcp_log( sprintf( 'Campaign Monitor: Getting custom field key for list ID %s.', $list_id ) );
	}

	$keys = get_option( 'rcp_campaign_monitor_custom_field_keys' );

	if ( ! is_array( $keys ) ) {
		$keys = array();
	}

	if ( array_key_exists( $list_id, $keys ) ) {
		if ( function_exists( 'rcp_log' ) ) {
			rcp_log( sprintf( 'Campaign Monitor: Key for list ID %s is %s.', $list_id, $keys[ $list_id ] ) );
		}

		return $keys[ $list_id ];
	}

	if ( function_exists( 'rcp_log' ) ) {
		rcp_log( sprintf( 'Campaign Monitor: Creating new custom field for list ID %s.', $list_id ) );
	}

	if ( ! class_exists( 'CS_REST_Lists' ) ) {
		require_once( dirname( __FILE__ ) . '/vendor/csrest_lists.php' );
	}

	$wrap = new CS_REST_Lists( $list_id, array(
			'api_key' => $rcp_cm_options['cm_api']
	) );

	$result = $wrap->create_custom_field( array(
		'FieldName'                 => __( 'RCP Status', 'rcp-campaign-monitor' ),
		'DataType'                  => CS_REST_CUSTOM_FIELD_TYPE_TEXT,
		'VisibleInPreferenceCenter' => false
	) );

	if ( $result->was_successful() ) {
		rcp_log( sprintf( 'Campaign Monitor: Key created successfully: %s.', $result->response ) );

		$keys[ $list_id ] = sanitize_text_field( $result->response );

		update_option( 'rcp_campaign_monitor_custom_field_keys', $keys );

		return $result->response;
	}

	if ( function_exists( 'rcp_log' ) ) {
		rcp_log( sprintf( 'Campaign Monitor: Failed to create custom field. Code: %d; Response: %s', $result->http_status_code, var_export( $result->response, true ) ) );
	}

	return '';

}

/**
 * Displays the Campaign Monitor checkbox on the registration form
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_fields() {
	global $rcp_cm_options;
	$checked = empty( $rcp_cm_options['default_checkbox_state'] ) || 'checked' == $rcp_cm_options['default_checkbox_state'];
	$label   = ! empty( $rcp_cm_options['cm_label'] ) ? $rcp_cm_options['cm_label'] : __( 'Sign up for our mailing list', 'rcp-campaign-monitor' );
	ob_start();
	if ( isset( $rcp_cm_options['cm_api'] ) && strlen( trim( $rcp_cm_options['cm_api'] ) ) > 0 ) { ?>
		<p id="rcp_cm_signup_wrap">
			<input name="rcp_cm_signup" id="rcp_cm_signup" type="checkbox" <?php checked( $checked ); ?>/>
			<label for="rcp_cm_signup"><?php echo $label; ?></label>
		</p>
		<?php
	}
	echo ob_get_clean();
}

add_action( 'rcp_before_registration_submit_field', 'rcp_cm_fields', 100 );

/**
 * Checks whether a user should be signed up for the Campaign Monitor list
 *
 * @param array                $posted              Posted data.
 * @param int                  $user_id             ID of the user registering.
 * @param float                $price               Price of the membership.
 * @param RCP_Customer|false   $customer            Customer object.
 * @param int                  $membership_id       ID of the membership for this registration.
 * @param RCP_Membership|false $previous_membership Previous membership, if this is an upgrade/downgrade.
 * @param string               $registration_type   Type of registration (new, upgrade, downgrade, renewal).
 *
 * @since 1.0
 * @return void
 */
function rcp_cm_check_for_email_signup( $posted, $user_id, $price = 0.00, $payment_id = 0, $customer = false, $membership_id = 0, $previous_membership = false, $registration_type = 'new' ) {
	if ( function_exists( 'rcp_add_membership_meta' ) && ! empty( $membership_id ) ) {
		if ( isset( $posted['rcp_cm_signup'] ) ) {
			rcp_add_membership_meta( $membership_id, 'campaign_monitor_pending_signup', true );
		} else {
			rcp_delete_membership_meta( $membership_id, 'campaign_monitor_pending_signup' );
		}
	} else {
		if ( isset( $posted['rcp_cm_signup'] ) ) {
			update_user_meta( $user_id, 'rcp_pending_cm_signup', true );
		} else {
			delete_user_meta( $user_id, 'rcp_pending_cm_signup' );
		}
	}
}

add_action( 'rcp_form_processing', 'rcp_cm_check_for_email_signup', 10, 8 );

/**
 * Add the user to the Campaign Monitor list when their membership is activated and if they have the flag.
 *
 * @param RCP_Member           $member     Member object.
 * @param RCP_Customer|false   $customer   Customer object.
 * @param RCP_Membership|false $membership Membership object.
 *
 * @since 1.1
 * @return void
 */
function rcp_cm_maybe_add_to_list( $member, $customer = false, $membership = false ) {

	if ( ! is_a( $membership, 'RCP_Membership' ) ) {
		return;
	}

	if ( ! rcp_get_membership_meta( $membership->get_id(), 'campaign_monitor_pending_signup', true ) ) {
		return;
	}

	$user = get_userdata( $membership->get_customer()->get_user_id() );

	if ( empty( $user ) ) {
		return;
	}

	$subscribed = rcp_cm_subscribe_email( $user->user_email, $user->display_name, $user, $membership );

	if ( $subscribed ) {
		update_user_meta( $user->ID, 'rcp_subscribed_to_cm', 'yes' );
		delete_user_meta( $user->ID, 'rcp_pending_cm_signup' );
		rcp_delete_membership_meta( $membership->get_id(), 'campaign_monitor_pending_signup' );
	}

}

add_action( 'rcp_successful_registration', 'rcp_cm_maybe_add_to_list', 10, 3 );

/**
 * Add user to the Campaign Monitor list when their account is activated
 *
 * @deprecated 1.1 In favour of `rcp_cm_maybe_add_to_list()`
 * @see        rcp_cm_maybe_add_to_list()
 *
 * @param string     $status     New status being set.
 * @param int        $user_id    ID of the user.
 * @param string     $old_status Previous status.
 * @param RCP_Member $member     Member object.
 *
 * @since 1.0.1
 * @return void
 */
function rcp_cm_add_to_list( $status, $user_id, $old_status, $member ) {

	/*
	 * Bail on 3.0+
	 * We'll use `rcp_cm_maybe_add_to_list()` instead.
	 */
	if ( function_exists( 'rcp_get_membership' ) ) {
		return;
	}

	if ( ! in_array( $status, array( 'active', 'free' ) ) ) {
		return;
	}

	if ( ! get_user_meta( $user_id, 'rcp_pending_cm_signup', true ) ) {
		return;
	}

	rcp_cm_subscribe_email( $member->user_email, $member->display_name, $member );
	update_user_meta( $user_id, 'rcp_subscribed_to_cm', 'yes' );
	delete_user_meta( $user_id, 'rcp_pending_cm_signup' );

}

/**
 * Add new column header to the "Members" table.
 *
 * @deprecated 1.1 In favour of `rcp_add_cm_membership_column()`
 * @see        rcp_add_cm_membership_column()
 *
 * @since 1.0
 * @return void
 */
function rcp_add_cm_table_column_header_and_footer() {
	if ( function_exists( 'rcp_get_membership' ) ) {
		return;
	}
	echo '<th style="width: 140px;">' . __( 'Newsletter Signup', 'rcp-campaign-monitor' ) . '</th>';
}

/**
 * Add a new column to the "Customers" table.
 *
 * @param array $columns
 *
 * @since 1.1
 * @return array
 */
function rcp_add_cm_customer_column( $columns ) {
	$columns['cm_signup'] = __( 'Newsletter Signup', 'rcp-campaign-monitor' );

	return $columns;
}

add_filter( 'rcp_customers_list_table_columns', 'rcp_add_cm_customer_column' );

/**
 * Display table content saying whether or not the user signed up for the mailing list.
 *
 * @deprecated 1.1 In favour of `rcp_add_cm_customer_column_content()`
 * @see        rcp_add_cm_customer_column_content()
 *
 * @param int $user_id ID of the current member.
 *
 * @since 1.0
 * @return void
 */
function rcp_add_cm_table_column_content( $user_id ) {

	if ( function_exists( 'rcp_get_membership' ) ) {
		return;
	}

	if ( get_user_meta( $user_id, 'rcp_subscribed_to_cm', true ) ) {
		$signed_up = __( 'yes', 'rcp-campaign-monitor' );
	} else {
		$signed_up = __( 'no', 'rcp-campaign-monitor' );
	}

	echo '<td>' . $signed_up . '</td>';
}

/**
 * Display the "Customers" table column content.
 *
 * @param string       $value
 * @param RCP_Customer $customer
 *
 * @since 1.1
 * @return string
 */
function rcp_add_cm_customer_column_content( $value, $customer ) {

	if ( ! is_object( $customer ) ) {
		return $value;
	}

	if ( get_user_meta( $customer->get_user_id(), 'rcp_subscribed_to_cm', true ) ) {
		$value = __( 'yes', 'rcp-campaign-monitor' );
	} else {
		$value = __( 'no', 'rcp-campaign-monitor' );
	}

	return $value;

}

add_filter( 'rcp_customers_list_table_column_cm_signup', 'rcp_add_cm_customer_column_content', 10, 2 );

/**
 * Enqueue the admin JS script for Restrict Content Pro Campaign Monitor
 *
 * @param $hook
 */
function admin_scripts( $hook ) {
    if ( $hook === 'restrict_page_rcp-campaign-monitor' ) {
	    wp_register_script('rcpcm-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', array( 'jquery' ), null, true );
	    wp_enqueue_script( 'rcpcm-admin-js' );
    }
}

add_action( 'admin_enqueue_scripts', 'admin_scripts', 10, 2);

if ( ! function_exists( 'ithemes_rcp_campaign_monitor_updater_register' ) ) {
	function ithemes_rcp_campaign_monitor_updater_register( $updater ) {
		$updater->register( 'REPO', __FILE__ );
	}
	add_action( 'ithemes_updater_register', 'ithemes_rcp_campaign_monitor_updater_register' );

	require( __DIR__ . '/lib/updater/load.php' );
}