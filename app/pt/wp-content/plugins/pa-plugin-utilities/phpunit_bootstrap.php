<?php

/**
 * Bootstrap the plugin unit testing environment. Customize 'active_plugins'
 * setting below to point to your main plugin file.
 *
 * Requires WordPress Unit Tests (http://unit-test.svn.wordpress.org/trunk/).
 *
 * @package wordpress-plugin-tests
 */

// Add this plugin to WordPress for activation so it can be tested.

global $GLOBALS;

$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array('pa-plugin-utilities/pa-plugin-utilities.php'),
);


// If the wordpress-tests repo location has been customized (and specified
// with WP_TESTS_DIR), use that location. This will most commonly be the case
// when configured for use with Travis CI.

// Otherwise, we'll just assume that this plugin is installed in the WordPress
// SVN external checkout configured in the wordpress-tests repo.

if( false !== getenv( 'WP_TESTS_DIR' ) ) {
	require_once getenv( 'WP_TESTS_DIR' ) . '/tests/phpunit/includes/bootstrap.php';
} else {
//	$wptests_dir = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) .'/wordpress-tests/';
//	set_include_path(get_include_path() . PATH_SEPARATOR . $wptests_dir.'/includes/');

//	require_once 'bootstrap.php';
}