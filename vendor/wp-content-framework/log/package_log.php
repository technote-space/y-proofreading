<?php
/**
 * WP_Framework Package Log
 *
 * @version 0.0.14
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Package_Log
 * @package WP_Framework
 */
class Package_Log extends Package_Base {

	/**
	 * @return int
	 */
	public function get_priority() {
		return 10;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_view() {
		return true;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_admin() {
		return true;
	}

	/**
	 * @return bool
	 */
	protected function is_valid_cron() {
		return true;
	}

	/**
	 * @return array
	 */
	public function get_configs() {
		return [
			'config',
			'db',
			'filter',
			'map',
			'setting',
		];
	}
}
