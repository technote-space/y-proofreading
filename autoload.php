<?php
/**
 * Plugin Name: Y Proofreading
 * Plugin URI:
 * Description: Yahoo! API を使用した校正支援プラグイン
 * Author: Technote
 * Version: 0.2.14
 * Author URI: https://technote.space
 * Text Domain: y-proofreading
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'Y_PROOFREADING' ) ) {
	return;
}

define( 'Y_PROOFREADING', 'Y_Proofreading' );

@require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

WP_Framework::get_instance( Y_PROOFREADING, __FILE__ );
