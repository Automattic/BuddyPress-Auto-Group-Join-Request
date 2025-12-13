<?php
/**
 * Integration tests for the plugin.
 *
 * @package Automattic\BuddyPressAutoGroupJoinRequest\Tests\Integration
 */

namespace Automattic\BuddyPressAutoGroupJoinRequest\Tests\Integration;

/**
 * Test case for plugin integration.
 */
class PluginTest extends TestCase {

	/**
	 * Test that the plugin is loaded.
	 */
	public function test_plugin_loaded(): void {
		$this->assertTrue(
			function_exists( 'bp_auto_group_join_request_init' )
			|| class_exists( 'BP_Auto_Group_Join_Request' )
		);
	}
}
