<?php /** @noinspection PhpUndefinedFieldInspection */

/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Y_Proofreading\Classes\Models;

use Exception;
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

		$response = wp_remote_post( $url, [
			'user-agent'  => "Yahoo AppID: {$client_id}",
			'body'        => $params,
			'data_format' => 'query',
		] );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		if ( empty( $response['response']['code'] ) || 200 !== $response['response']['code'] ) {
			throw new Exception( $response['response']['message'] );
		}

		$results = new SimpleXMLElement( $response ['body'] );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( $results->Message ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
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
			$count = 0;
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
		$hashes  = [];
		$summary = [];
		$filters = $this->app->get_config( 'yahoo', 'filter' );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		foreach ( $results->Result as $value ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$start = (int) $value->StartPos;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$len = (int) $value->Length;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( '' === (string) $value->Surface ) {
				$surface = mb_substr( $sentence, $start, $len );
			} else {
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$surface = (string) $value->Surface;
			}
			$data = [
				'start'   => $start,
				'end'     => $start + $len,
				'surface' => $surface,
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				'word'    => (string) $value->ShitekiWord,
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				'info'    => (string) $value->ShitekiInfo,
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				'index'   => $this->app->array->get( $filters, (string) $value->ShitekiInfo . '.index', 0 ),
			];
			$hash = $this->app->utility->create_hash( $data['index'] . '-' . $data['surface'] . '-' . $data['word'] . '-' . $data['info'], 'proofreading' );
			if ( ! isset( $hashes[ $hash ] ) ) {
				$hashes[ $hash ] = $index++;
				$items[]         = [
					'surface' => $data['surface'],
					'word'    => $data['word'],
					'info'    => $data['info'],
					'index'   => $data['index'],
					'hash'    => $hash,
				];
			}
			$data['item_index'] = $hashes[ $hash ];
			$summary[]          = $data;
		}

		$fragments = [];
		$end       = null;
		$index     = 0;
		foreach ( array_reverse( $summary ) as $data ) {
			$fragments[] = [
				'text' => mb_substr( $sentence, $data['end'], isset( $end ) ? $end - $data['end'] : null ),
			];
			$fragments[] = [
				'text'       => mb_substr( $sentence, $data['start'], $data['end'] - $data['start'] ),
				'index'      => $data['index'],
				'item_index' => $data['item_index'],
				'info'       => $data['info'],
				'word'       => $data['word'],
				'id'         => 'proofreading-tooltip-' . ( $index++ ),
			];
			$end         = $data['start'];
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
