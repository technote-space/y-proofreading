<?php
/**
 * @version 0.0.7
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
			'plugin_icon'   => $this->get_img_url( 'icon-24x24.png' ),
			'api_class'     => $this->get_api_class(),
			'translate'     => $this->get_translate_data( [
				'Y Proofreading',
				'Proofreading',
				'Loading...',
				'Proofreading info',
				'Proofreading contents',
				'Target surface',
				'Candidates of rephrasing',
				'Detail info of indicated word',
				'Item not found',
				'Small',
				'Middle',
				'Large',
				'Size setting',
				'Pin again',
				'Proofread again',
				'Detail info of indicated word',
				'Candidates of rephrasing',
				'In pixel',
				'In percentage',
			] ),
			'size_settings' => $this->get_gutenberg_config( 'size_settings' ),
			'default_size'  => $this->get_gutenberg_config( 'default_size' ),
			'min_width'     => $this->get_gutenberg_config( 'min_width' ),
			'max_width'     => $this->get_gutenberg_config( 'max_width' ),
			'target_media'  => $this->get_gutenberg_config( 'target_media' ),
		];
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	private function get_gutenberg_config( $name ) {
		return $this->apply_filters( $name, $this->app->get_config( 'gutenberg', $name ) );
	}
}