<?php
/**
 * Custom REST API endpoint
 * 
 * @package     Custom_REST_API\Custom_Endpoint
 */

declare( strict_types = 1 );

namespace Custom_REST_API;

/**
 * Register the routes for the objects of the controller.
 */
class Custom_Endpoint extends \WP_REST_Controller {
	/**
	 * Register the routes for the objects of the controller with custom schema.
	 * wp-json/custom-rest-api/v1/custom-endpoint
	 */
	public function register_routes() {
		$namespace = 'custom-rest-api/v1';
		$base      = 'custom-endpoint';
		
		// register endpoint with custom schema for limit argument.
		register_rest_route( $namespace, '/' . $base, array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_items' ),
			'permission_callback' => array( $this, 'get_items_permissions_check' ),
			'args'                => self::prefix_get_endpoint_args(),
		) );

	}

	/**
	 * Get items
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response
	 */
	public function get_items( $request ) {

		// get limit argument from request.
		$limit = $request->get_param( 'limit' );
		$response = array();

		// if limit is not set, set it to 10.
		if ( ! $limit ) {
			$limit = 10;
		}

		// limit to 100 items.
		if ( $limit > 100 ) {
			$limit = 100;
		}

		for ( $i = 0; $i < $limit; $i++ ) {
			$response[] = array(
				'date' => date( 'Y-m-d' ),
				'time' => gmdate( 'H:i:s' ),
			);
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Get items permissions check
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return bool|\WP_Error
	 */
	public function get_items_permissions_check( $request ) {
		return true;
	}

	/**
	 * Get the argument schema for this example endpoint.
	 */
	public static function prefix_get_endpoint_args() { 

		return array(
			'limit' => array(
					'description'       => __( 'Limit the number of items returned.', 'custom-rest-api' ),
					'type'              => 'integer',
					'default'           => 10,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
				),
		);
	}

	/**
	 * Create item permissions check
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return bool|\WP_Error
	 */
	public function create_item_permissions_check( $request ) {
		return true;
	}
}
