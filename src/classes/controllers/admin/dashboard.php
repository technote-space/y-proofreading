<?php
/**
 * @version 0.0.2
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Y_Proofreading\Classes\Controllers\Admin;

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Y_Proofreading\Classes\Controllers\Admin
 */
class Dashboard extends \WP_Framework_Admin\Classes\Controllers\Admin\Base {

	use \WP_Framework_Admin\Traits\Dashboard;

	/**
	 * @return array
	 */
	protected function get_setting_list() {
		return [
			'yahoo_client_id',
			'no_filter' => [
				'form'    => 'multi_select',
				'options' => $this->app->array->combine( $this->app->array->map( $this->app->get_config( 'yahoo', 'filter' ), function ( $value, $key ) {
					$value['display'] = "{$key} （{$value['description']}）";

					return $value;
				} ), 'index', 'display' ),
			],
			'use_admin_ajax',
		];
	}
}
