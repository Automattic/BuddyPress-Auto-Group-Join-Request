<?php
/**
 * BuddyPress Auto Group Join Request
 *
 * @package           BuddyPress-Auto-Group-Join-Request
 * @author            WordPress VIP
 * @copyright         2025-onwards Shared and distributed between contributors.
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Auto Group Join Request
 * Description:       Automatically sends join requests to BuddyPress groups based on any profile field value.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      8.2
 * Author:            WordPress VIP
 * Text Domain:       buddypress-auto-group-join-request
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Automattic\BuddyPressAutoGroupJoinRequest;

defined( 'ABSPATH' ) || exit;

/*
Somewhere in the codebase, we need to add a filter for the BuddyPress Auto Group Join Request plugin
to add a new configuration for the partner group.
	add_filter(
		'bp_auto_group_join_config',
		function( $config ) {
			$config['partner'] = array(
				'profile_field_name' => 'Profile Type',
				'profile_field_value' => 47,
				'group_id' => 2,
			);
		
			return $config;
		}
	);
*/

/**
 * Get the configuration for auto group join requests.
 *
 * @return array Array of configurations, each containing:
 *               - profile_field_name: Name of the xProfile field
 *               - profile_field_value: Value that triggers the join request
 *               - group_id: ID of the group to join
 */
function bp_auto_group_join_get_config() {
	$config = apply_filters( 'bp_auto_group_join_config', array() );
	
	if ( empty( $config ) ) {
		return array();
	}

	// Ensure each config has required keys.
	foreach ( $config as $key => $item ) {
		if ( ! isset( $item['profile_field_name'], $item['profile_field_value'], $item['group_id'] ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'BP Auto Group Join: Invalid configuration for key %s. Missing required fields.', $key ) );
			unset( $config[ $key ] );
		}
	}

	return $config;
}

// Hook into various profile update scenarios.
add_action( 'xprofile_updated_profile', __NAMESPACE__ . '\\bp_auto_group_join_maybe_request', 10, 1 );
add_action( 'bp_core_activated_user', __NAMESPACE__ . '\\bp_auto_group_join_maybe_request', 20, 1 );
add_action( 'bp_after_activation', __NAMESPACE__ . '\\bp_auto_group_join_maybe_request', 20, 1 );
add_action( 'set_user_role', __NAMESPACE__ . '\\bp_auto_group_join_maybe_request', 10, 1 );
add_action( 'user_register', __NAMESPACE__ . '\\bp_auto_group_join_maybe_request', 20, 1 );

/**
 * Core logic to request group join if conditions match.
 *
 * @param int $user_id User ID.
 */
function bp_auto_group_join_maybe_request( $user_id ) {
	if ( ! function_exists( 'groups_send_membership_request' ) || ! function_exists( 'groups_get_group' ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'BP Auto Group Join: BuddyBoss/BuddyPress not loaded' );
		return;
	}

	$configs = bp_auto_group_join_get_config();
	if ( empty( $configs ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'BP Auto Group Join: No configurations found' );
		return;
	}

	foreach ( $configs as $key => $config ) {
		$field_id = xprofile_get_field_id_from_name( $config['profile_field_name'] );
		if ( ! $field_id ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'BP Auto Group Join: Field "%s" not found', $config['profile_field_name'] ) );
			continue;
		}

		$field_value = xprofile_get_field_data( $field_id, $user_id );
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		// error_log( sprintf( 'BP Auto Group Join: Field value for user %d: %s (expected: %s)', $user_id, $field_value, $config['profile_field_value'] ) );
		
		if ( $field_value != $config['profile_field_value'] ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			// error_log( sprintf( 'BP Auto Group Join: Field value %s does not match required value %s', $field_value, $config['profile_field_value'] ) );
			continue;
		}

		$group = groups_get_group( $config['group_id'] );
		if ( empty( $group ) || empty( $group->id ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'BP Auto Group Join: Group with ID %d not found', $config['group_id'] ) );
			continue;
		}

		// Check if already a member or request pending.
		if ( groups_is_user_member( $user_id, $group->id ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'BP Auto Group Join: User %d is already a member of group %d', $user_id, $group->id ) );
			continue;
		}

		// Check for existing membership request using the proper method.
		$request = groups_check_for_membership_request( $user_id, $group->id );
		if ( $request ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'BP Auto Group Join: User %d already has a pending request for group %d', $user_id, $group->id ) );
			continue;
		}

		// Send the membership request.
		$request_id = groups_send_membership_request( $user_id, $group->id );
		if ( ! $request_id ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'BP Auto Group Join: Failed to send membership request for user %d to group %d', $user_id, $group->id ) );
		}
	}
}
