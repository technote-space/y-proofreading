<?php
/**
 * WP_Framework_Cron Crons Base
 *
 * @version 0.0.12
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Cron\Classes\Crons;

use WP_Framework_Cron\Traits\Cron;
use WP_Framework_Cron\Traits\Package;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Base
 * @package WP_Framework_Cron\Classes\Crons
 */
abstract class Base implements \WP_Framework_Cron\Interfaces\Cron {

	use Cron, Package;

}
