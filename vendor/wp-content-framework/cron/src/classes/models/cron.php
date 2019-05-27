<?php
/**
 * WP_Framework_Cron Classes Models Cron
 *
 * @version 0.0.12
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cron\Classes\Models;

use WP_Framework_Core\Traits\Loader;
use WP_Framework_Cron\Traits\Package;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Cron
 * @package WP_Framework_Cron\Classes\Models
 */
class Cron implements \WP_Framework_Core\Interfaces\Loader {

	use Loader, Package;

	/**
	 * load
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function load() {
		$this->get_class_list();
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\WP_Framework_Cron\Classes\Crons\Base';
	}

	/**
	 * @return array
	 */
	public function get_cron_class_names() {
		$list = $this->get_class_list();

		return array_keys( $list );
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		$namespaces = [ $this->app->define->plugin_namespace . '\\Classes\\Crons\\' ];
		foreach ( $this->app->get_packages() as $package ) {
			foreach ( $package->get_cron_namespaces() as $namespace ) {
				$namespaces[] = $namespace;
			}
		}

		return $namespaces;
	}
}
