<?php
/**
 * @version 0.0.1
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Y_Proofreading\Classes\Controllers\Api\Admin;

use WP_Error;
use WP_Framework_Api\Classes\Controllers\Api\Base;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

/**
 * Class Proofreading
 * @package Y_Proofreading\Classes\Controllers\Api\Admin
 */
class Proofreading extends Base {

	/**
	 * @return string
	 */
	public function get_endpoint() {
		return 'proofreading';
	}

	/**
	 * @return string
	 */
	public function get_call_function_name() {
		return 'proofreading';
	}

	/**
	 * @return string
	 */
	public function get_method() {
		return 'post';
	}

	/**
	 * @return null|string|false
	 */
	public function get_capability() {
		return null;
	}

	/**
	 * @return array
	 */
	public function get_args_setting() {
		return [
			'sentence' => [
				'required'          => true,
				'description'       => 'sentence',
				'validate_callback' => function ( $var ) {
					return $this->validate_string( $var ) && $this->validate_not_empty( $var );
				},
				'sanitize_callback' => function ( $var ) {
					return trim( $var );
				},
			],
		];
	}

	/**
	 * @return bool
	 */
	public function is_only_admin() {
		return true;
	}

	/**
	 * @param WP_REST_Request|array $params
	 *
	 * @return int|WP_Error|WP_REST_Response
	 */
	public function callback( $params ) {
		/** @var \Y_Proofreading\Classes\Models\Proofreading $proofreading */
		$proofreading = \Y_Proofreading\Classes\Models\Proofreading::get_instance( $this->app );

		return new WP_REST_Response( $proofreading->get_result( $params['sentence'] ) );
	}
}
