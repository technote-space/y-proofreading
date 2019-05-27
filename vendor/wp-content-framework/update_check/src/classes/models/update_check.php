<?php
/**
 * WP_Framework_Update_Check Classes Models Update_Check
 *
 * @version 0.0.5
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Update_Check\Classes\Models;

use /** @noinspection PhpUndefinedClassInspection */
	Puc_v4_Factory;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Update_Check\Traits\Package;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Update_Check
 * @package WP_Framework_Update_Check\Classes\Models
 */
class Update_Check implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook {

	use Singleton, Hook, Package;

	/**
	 * setup update
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_update() {
		$update_info_file_url = $this->app->get_config( 'config', 'update_info_file_url' );
		if ( ! empty( $update_info_file_url ) ) {
			$uri = $this->app->get_plugin_uri();
			if ( ! empty( $uri ) && $this->app->string->starts_with( $uri, 'https://wordpress.org' ) ) {
				$this->app->setting->edit_setting( 'check_update', 'default', false );
			}

			if ( $this->apply_filters( 'check_update' ) ) {
				/** @noinspection PhpUndefinedClassInspection */
				Puc_v4_Factory::buildUpdateChecker(
					$update_info_file_url,
					$this->app->plugin_file,
					$this->app->plugin_name
				);
			}
		} else {
			$this->app->setting->remove_setting( 'check_update' );
		}
	}
}
