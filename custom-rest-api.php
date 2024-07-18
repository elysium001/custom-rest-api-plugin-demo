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

function validate_and_log( \WP_REST_Request $request = null) {

	// rest_do_request
	$response = rest_do_request($request);

	$msg = '';
	
	// check if the response has an error.
	if ( is_wp_error( $response ) ) {
		$msg = 'Error: ' . $response->get_error_message();
	} else {
		$data = json_encode( $response->get_data() );

		// check if parameter(s) in request is invalid.
		if ( $response->get_status() === 400 ) {
			$msg = 'Invalid data: ' . $data;
		} else {
			$msg = 'Valid data: ' . $data;
		}
		
	}

    echo "<script>console.log('$msg');</script>";
}

// on init.
add_action(
	'loop_start',
	function () {

		// only run in the is_front_page and is_super_admin.
		if ( ! is_front_page() || ! is_super_admin() ) {
			return;
		}
		
		// Get the schema for the endpoint.
		$request = new WP_REST_Request('GET', '/custom-rest-api/v1/custom-endpoint');

		// Invalid data
		// remove required param limit
		$request->set_param('type', 'missing required param limit');
		validate_and_log($request);

		// Valid data
		$request->set_param('limit', 5);
		$request->set_param('type', 'blank');
		validate_and_log($request);

	
		// Invalid data
		$request->set_param('limit', 'invalid');
		validate_and_log($request);
	}
);
