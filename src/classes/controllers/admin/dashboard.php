<?php
/**
 * @version 0.0.12
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Y_Proofreading\Classes\Controllers\Admin;

use WP_Framework_Admin\Classes\Controllers\Admin\Base;
use Y_Proofreading\Classes\Models\Proofreading;

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Y_Proofreading\Classes\Controllers\Admin
 */
class Dashboard extends Base {

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

	/**
	 * after update
	 */
	protected function after_update() {
		$this->delete_cache();
	}

	/**
	 * after delete
	 */
	protected function after_delete() {
		$this->delete_cache();
	}

	/**
	 * @return bool
	 */
	private function delete_cache() {
		/** @var Proofreading $proofreading */
		$proofreading = Proofreading::get_instance( $this->app );

		return $proofreading->delete_cache();
	}
}
