<?php /** @noinspection PhpUndefinedFieldInspection */

/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Y_Proofreading\Classes\Models;

use Exception;
use RuntimeException;
use SimpleXMLElement;
use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

/**
 * Class Proofreading
 * @package Y_Proofreading\Classes\Models
 */
class Proofreading implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * @return bool
	 */
	public function is_valid() {
		return ! empty( $this->apply_filters( 'yahoo_client_id' ) );
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	public function get_result( $content ) {
		try {
			if ( ! $this->is_valid() ) {
				throw new Exception( $this->translate( 'Not available' ) );
			}

			$sentence = $this->get_sentence( $content );
			if ( ! $this->apply_filters( 'is_valid_proofreading_cache' ) ) {
				return $this->request( $sentence );
			}

			$hash  = $this->app->utility->create_hash( $sentence, 'proofreading' );
			$cache = $this->cache_get( $hash );
			if ( is_array( $cache ) ) {
				return $cache;
			}

			$result = $this->request( $sentence );
			$this->cache_set( $hash, $result, false, 3600 );

			return $result;
		} catch ( Exception $e ) {
			return [
				'result'  => false,
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * @param string $sentence
	 *
	 * @return array
	 * @throws Exception
	 */
	private function request( $sentence ) {
		$url       = $this->app->get_config( 'yahoo', 'request_url' );
		$client_id = $this->apply_filters( 'yahoo_client_id' );
		$params    = [
			'sentence' => $sentence,
		];
		$no_filter = $this->apply_filters( 'no_filter' );
		if ( $no_filter ) {
			$params['no_filter'] = $no_filter;
		}

		$ch = curl_init( $url );
		curl_setopt_array( $ch, [
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT      => "Yahoo AppID: {$client_id}",
			CURLOPT_POSTFIELDS     => http_build_query( $params ),
		] );
		$results = curl_exec( $ch );
		$errno   = curl_errno( $ch );
		$error   = curl_error( $ch );
		curl_close( $ch );

		if ( CURLE_OK !== $errno ) {
			throw new RuntimeException( $error, $errno );
		}
		if ( false === $results ) {
			throw new Exception( $this->translate( 'Invalid API Response.' ) );
		}

		$results = new SimpleXMLElement( $results );
		if ( $results->Message ) {
			throw new Exception( (string) $results->Message );
		}

		return $this->parse_result( $sentence, $results );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	private function get_sentence( $content ) {
		foreach ( $this->apply_filters( 'remove_block_tags', [ 'pre', 'code', 'blockquote' ] ) as $target ) {
			$content = preg_replace( '#<' . $target . '[^>]*?>[\s\S]*?</' . $target . '>#i', "\n", $content );
		}
		foreach ( $this->apply_filters( 'remove_inline_tags', [ 'rt' ] ) as $target ) {
			$content = preg_replace( '#<' . $target . '[^>]*?>[\s\S]*?</' . $target . '>#i', '', $content );
		}
		foreach ( $this->apply_filters( 'as_block_tags', [ 'li' ] ) as $target ) {
			do {
				$content = preg_replace( '#<' . $target . '[^>]*?>([\s\S]*?)</' . $target . '>#i', "\n$1", $content, -1, $count );
			} while ( $count > 0 );
		}
		$content = wp_strip_all_tags( $content );
		$content = strip_shortcodes( $content );
		$content = htmlspecialchars( $content );
		$content = str_replace( '&amp;nbsp;', '', $content );
		$content = preg_replace( '/&(([a-zA-Z]{2,}[a-zA-Z0-9]*)|(#[0-9]{2,4})|(#x[a-fA-F0-9]{2,4}))?;/', '', $content );
		$content = html_entity_decode( $content );
		$content = str_replace( "\r", "\n", $content );
		$content = preg_replace( [ '/\n{3,}/', '/[ \t]+/' ], [ "\n\n", ' ' ], $content );
		$content = stripslashes( $content );

		return trim( $content );
	}

	/**
	 * @param string $sentence
	 * @param SimpleXMLElement $results
	 *
	 * @return array
	 */
	private function parse_result( $sentence, $results ) {
		$items   = [];
		$index   = 0;
		$hash    = [];
		$summary = [];
		$filters = $this->app->get_config( 'yahoo', 'filter' );
		foreach ( $results->Result as $value ) {
			$start = (int) $value->StartPos;
			$len   = (int) $value->Length;
			if ( '' === (string) $value->Surface ) {
				$surface = mb_substr( $sentence, $start, $len );
			} else {
				$surface = (string) $value->Surface;
			}
			$r = [
				'start'   => $start,
				'end'     => $start + $len,
				'surface' => $surface,
				'word'    => (string) $value->ShitekiWord,
				'info'    => (string) $value->ShitekiInfo,
				'index'   => $this->app->array->get( $filters, (string) $value->ShitekiInfo . '.index', 0 ),
			];
			$h = $this->app->utility->create_hash( $r['index'] . '-' . $r['surface'] . '-' . $r['word'] . '-' . $r['info'], 'proofreading' );
			if ( ! isset( $hash[ $h ] ) ) {
				$hash[ $h ] = $index++;
				$items[]    = [ 'surface' => $r['surface'], 'word' => $r['word'], 'info' => $r['info'], 'index' => $r['index'], 'hash' => $h ];
			}
			$r['item_index'] = $hash[ $h ];
			$summary[]       = $r;
		}

		$fragments = [];
		$end       = null;
		$index     = 0;
		foreach ( array_reverse( $summary ) as $r ) {
			$fragments[] = [
				'text' => mb_substr( $sentence, $r['end'], isset( $end ) ? $end - $r['end'] : null ),
			];
			$fragments[] = [
				'text'       => mb_substr( $sentence, $r['start'], $r['end'] - $r['start'] ),
				'index'      => $r['index'],
				'item_index' => $r['item_index'],
				'info'       => $r['info'],
				'word'       => $r['word'],
				'id'         => 'proofreading-tooltip-' . ( $index++ ),
			];
			$end         = $r['start'];
		}
		if ( $end ) {
			$fragments[] = [
				'text' => mb_substr( $sentence, 0, $end ),
			];
		} else {
			$fragments[] = [
				'text' => $sentence,
			];
		}
		$fragments = array_reverse( $fragments );

		return [
			'result'    => true,
			'sentence'  => $sentence,
			'items'     => $items,
			'fragments' => $fragments,
			'message'   => $this->translate( 'Succeeded' ),
		];
	}

	/**
	 * @return bool
	 */
	public function delete_cache() {
		return $this->cache_clear( null );
	}
}