<?php
/**
 * @version 0.0.12
 * @author Technote
 * @since 0.0.7
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'Y_PROOFREADING' ) ) {
	return;
}
/** @var Presenter $instance */
?>
<script>
	( function ( $ ) {
		$( function () {
			$( '#<?php $instance->id(); ?>-content-wrap option' ).mousedown( function ( e ) {
				e.preventDefault();
				$( this ).prop( 'selected', ! $( this ).prop( 'selected' ) );
				return false;
			} );
		} );
	} )( jQuery );
</script>
