<?php
/**
 * Plugin Name: Custom REST API
 * Description: Custom REST API examples with schema
 * Version: 1.0
 * Author: aomarserrano
 * 
 * @package     Custom_REST_API
 * @since       1.0.0
 */

declare( strict_types = 1 );

// can't access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// constants for plugin.
define( 'CUSTOM_REST_API_VERSION', '1.0' );
define( 'CUSTOM_REST_API_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOM_REST_API_PATH', plugin_dir_path( __FILE__ ) );

use Custom_REST_API\Custom_Endpoint;

// include the main plugin class.
require_once CUSTOM_REST_API_PATH . 'class-custom-endpoint.php';

// register the routes when the plugin is loaded.
add_action(
	'rest_api_init',
	function () {
		$custom_endpoint = new Custom_Endpoint();
		$custom_endpoint->register_routes();
	} 
);

function validate_against_schema($data, $schema) {
	foreach ($schema as $key => $properties) {
		if (!isset($data[$key])) {
			return false;
		}

		// Check if the type of the data is the same as the schema.
		if (gettype($data[$key]) !== $properties['type']) {
			return false;
		}
	}

	return true;
}

function validate_and_log($data, $schema) {
	// Validate the data against the schema.
    $is_valid = validate_against_schema($data, $schema);

	// Log the data and the validation result for debugging/demo purposes.
    $encoded_data = json_encode($data);
    $msg = $is_valid ? 'Data is valid' : 'Data is invalid';
    echo "<script>console.log($encoded_data, '$msg');</script>";
}

// on init.
add_action(
	'init',
	function () {
		// Get the schema for the endpoint.
		$schema = Custom_Endpoint::prefix_get_endpoint_args();

		// Valid data
		$data = ['limit' => 1];
		validate_and_log($data, $schema);
	
		// Invalid data
		$data = ['limit' => 'one'];
		validate_and_log($data, $schema);
	}
);
