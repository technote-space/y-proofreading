<?php
/**
 * WP_Framework_Log Views Admin Style Logs
 *
 * @version 0.0.16
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var Presenter $instance */
?>
<style>
    #<?php $instance->id();?>-main-contents .summary {
        display: inline-block;
        padding: 4px;
        margin: 5px 0;
        background: #eee;
        border: 1px solid #aaa;
    }

    #<?php $instance->id();?>-main-contents .summary > div {
        padding: 3px;
    }

    #<?php $instance->id();?>-main-contents .summary .total {
        font-size: 1.2em;
        font-weight: bold;
    }
</style>
