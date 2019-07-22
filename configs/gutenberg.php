<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

return [
	// size settings
	'size_settings' => [
		'small'      => [
			'title'    => 'Small',
			'unit'     => 'px',
			'default'  => 279,
			'is_fixed' => true,
		],
		'middle'     => [
			'title'    => 'Middle',
			'unit'     => '%',
			'default'  => 35,
			'is_fixed' => true,
		],
		'large'      => [
			'title'    => 'Large',
			'unit'     => '%',
			'default'  => 80,
			'is_fixed' => true,
		],
		'pixel'      => [
			'title'   => 'In pixel',
			'unit'    => 'px',
			'default' => 600,
			'min'     => 279,
		],
		'percentage' => [
			'title'   => 'In percentage',
			'unit'    => '%',
			'default' => 35,
			'min'     => 10,
			'max'     => 100,
		],
	],

	// default size
	'default_size'  => 'middle',

	// min width
	'min_width'     => '279px',

	// max width
	'max_width'     => 'calc(100% - 160px) !important',

	// target media
	'target_media'  => '@media (min-width: 782px)',
];
