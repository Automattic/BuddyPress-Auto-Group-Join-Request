# BuddyPress Auto Group Join Request

**Contributors:** garyj  
**Tags:** BuddyPress, BuddyBoss, groups, automatic join requests, profile fields  
**Requires at least:** 6.6  
**Tested up to:** 6.9  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Automatically sends join requests to BuddyPress groups based on any profile field value.

## Description

This plugin automatically sends join requests to BuddyPress groups when users have specific profile field values. It's particularly useful for:

* Automatically adding users to relevant groups based on any profile field (name, location, interests, profile type, etc.)
* Streamlining the group membership process
* Ensuring users are connected to the right communities based on their profile information

The plugin integrates with BuddyPress/BuddyBoss's profile fields and group management to provide a seamless user experience.

## Features

* Automatic group join requests based on any profile field value
* Configurable profile-field-to-group mapping
* Handles multiple group configurations
* Prevents duplicate requests
* Logs all actions for troubleshooting

## Installation

1. Upload the `buddypress-auto-group-join-request` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the profile-field-to-group mapping (see Configuration section below)

## Configuration

The plugin uses a simple array-based configuration for mapping profile field values to groups. Add the following code to your theme's `functions.php` file or in a custom plugin:

~~~php
add_filter(
    'bp_auto_group_join_config',
    function( $config ) {
        // Example 1: Add users with profile type "Partner" to group 2
        $config['partners'] = array(
            'profile_field_name' => 'Profile Type',
            'profile_field_value' => 47,
            'group_id' => 2,
        );

        // Example 2: Add users named "Dave" to group 3
        $config['daves'] = array(
            'profile_field_name' => 'Name',
            'profile_field_value' => 'Dave',
            'group_id' => 3,
        );
        
        return $config;
    }
);
~~~

### Finding the Required Values

1. **Profile Field Name**: 
   - Go to BuddyPress → Settings → Profile Fields
   - Find the field you want to use
   - The name is exactly as shown in the admin interface

2. **Profile Field Value**:
   - In the same Profile Fields section
   - For dropdown/radio/checkbox fields, the value is the ID of the option
   - For text fields, use the exact text value
   - For name fields, use the exact name as it appears in the profile

3. **Group ID**:
   - Go to the group's page in wp-admin
   - Look at the URL - it will contain something like `group_id=123`
   - The number is your group ID

You can add multiple configurations by adding more entries to the `$config` array:

~~~php
$config['partner'] = array(
    'profile_field_name' => 'Profile Type',
    'profile_field_value' => 47,
    'group_id' => 2,
);
$config['student'] = array(
    'profile_field_name' => 'Profile Type',
    'profile_field_value' => 48,
    'group_id' => 3,
);
$config['daves'] = array(
    'profile_field_name' => 'Name',
    'profile_field_value' => 'Dave',
    'group_id' => 4,
);
~~~

## Requirements

* WordPress 6.6 or higher
* BuddyPress or BuddyBoss Platform
* Member types and profile fields configured in BuddyPress/BuddyBoss
* Groups created in BuddyPress/BuddyBoss

## Troubleshooting

The plugin logs all actions to the WordPress error log. If something isn't working as expected:

1. Check your WordPress error log
2. Verify the profile field name matches exactly
3. Confirm the profile field value is correct
4. Ensure the group ID exists
5. Check that the user isn't already a member or has a pending request

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a complete list of changes.

## License

This plugin is licensed under the GPLv2 or later. See [LICENSE](LICENSE) for details.
