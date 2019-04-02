<?php
/**
 * @version 0.0.1
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Y_Proofreading\Classes\Models;

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

/**
 * Class Editor
 * @package Y_Proofreading\Classes\Models
 */
class Editor implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Common\Traits\Package;

	/**
	 * enqueue css for gutenberg
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_block_editor_assets() {
		/** @var Proofreading $proofreading */
		$proofreading = Proofreading::get_instance( $this->app );
		if ( ! $proofreading->is_valid() ) {
			return;
		}

		$handle = 'yproofreading';
		$this->enqueue_style( $handle, 'gutenberg.css', [], $this->app->get_plugin_version() );
		$this->enqueue_script( $handle, 'yproofreading.min.js', [
			'wp-editor',
			'wp-edit-post',
			'wp-data',
			'wp-element',
			'wp-components',
			'wp-plugins',
			'wp-i18n',
			'lodash',
		], $this->app->get_plugin_version() );
		$this->localize_script( $handle, 'yproofreading_params', $this->get_editor_params() );
		$this->app->api->add_use_api_name( 'proofreading' );
	}

	/**
	 * @return array
	 */
	private function get_editor_params() {
		return [
			'plugin_icon' => $this->get_img_url( 'icon-24x24.png' ),
			'api_class'   => $this->get_api_class(),
			'translate'   => [
				'Y Proofreading'                => $this->translate( 'Y Proofreading' ),
				'Proofreading'                  => $this->translate( 'Proofreading' ),
				'Loading...'                    => $this->translate( 'Loading...' ),
				'Proofreading info'             => $this->translate( 'Proofreading info' ),
				'Proofreading contents'         => $this->translate( 'Proofreading contents' ),
				'Target surface'                => $this->translate( 'Target surface' ),
				'Candidates of rephrasing'      => $this->translate( 'Candidates of rephrasing' ),
				'Detail info of indicated word' => $this->translate( 'Detail info of indicated word' ),
				'Item not found'                => $this->translate( 'Item not found' ),
				'Small'                         => $this->translate( 'Small' ),
				'Middle'                        => $this->translate( 'Middle' ),
				'Large'                         => $this->translate( 'Large' ),
				'Size setting'                  => $this->translate( 'Size setting' ),
				'Pin again'                     => $this->translate( 'Pin again' ),
				'Open in modal'                 => $this->translate( 'Open in modal' ),
			],
		];
	}
}