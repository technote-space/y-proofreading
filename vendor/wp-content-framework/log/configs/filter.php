<?php
/**
 * WP_Framework_Log Configs Filter
 *
 * @version 0.0.15
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'log' => [
		'${prefix}app_initialize'       => [
			'setup_shutdown',
		],
		'${prefix}post_load_admin_page' => [
			'setup_settings',
		],
	],

];