<?php
/**
 * WP_Framework_Cron Interfaces Cron
 *
 * @version 0.0.12
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cron\Interfaces;

use WP_Framework_Common\Interfaces\Uninstall;
use WP_Framework_Core\Interfaces\Hook;
use WP_Framework_Core\Interfaces\Singleton;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Cron
 * @package WP_Framework_Cron\Interfaces
 */
interface Cron extends Singleton, Hook, Uninstall {

	/**
	 * run
	 */
	public function run();

	/**
	 * run now
	 */
	public function run_now();

}
