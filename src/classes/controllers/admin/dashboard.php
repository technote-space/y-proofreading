<?php
/**
 * @version 0.0.2
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Y_Proofreading\Classes\Controllers\Admin;

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Y_Proofreading\Classes\Controllers\Admin
 */
class Dashboard extends \WP_Framework_Admin\Classes\Controllers\Admin\Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return 0;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return 'Dashboard';
	}

	/**
	 * post
	 */
	protected function post_action() {
		if ( $this->app->input->post( 'update' ) ) {
			foreach ( $this->get_setting_list() as $name => $option ) {
				$this->update_setting( $name, $option );
			}
			$this->app->add_message( 'Settings have been updated.', 'setting' );
		} else {
			foreach ( $this->get_setting_list() as $name => $option ) {
				$this->app->option->delete( $this->get_filter_prefix() . $name );
				$this->delete_hook_cache( $name );
			}
			$this->app->add_message( 'Settings have been reset.', 'setting' );
		}
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		$args = [];
		foreach ( $this->get_setting_list() as $name => $option ) {
			$args['settings'][ $name ] = $this->get_view_setting( $name, $option );
		}

		return $args;
	}

	/**
	 * @return array
	 */
	private function get_setting_list() {
		return [
			'yahoo_client_id' => [],
			'no_filter'       => [
				'form'    => 'multi_select',
				'options' => $this->app->array->combine( $this->app->array->map( $this->app->get_config( 'yahoo', 'filter' ), function ( $value, $key ) {
					$value['display'] = "{$key} （{$value['description']}）";

					return $value;
				} ), 'index', 'display' ),
			],
		];
	}

	/**
	 * @param string $name
	 * @param array $option
	 *
	 * @return array
	 */
	private function get_view_setting( $name, $option ) {
		$detail          = $this->app->setting->get_setting( $name, true );
		$detail['id']    = str_replace( '/', '-', $detail['name'] );
		$detail['form']  = $this->app->array->get( $option, 'form', function () use ( $detail ) {
			return $this->get_form_by_type( $this->app->array->get( $detail, 'type', '' ), false );
		} );
		$detail['title'] = $this->translate( $detail['label'] );
		$detail['label'] = $detail['title'];

		if ( $this->app->array->get( $detail, 'type' ) === 'bool' ) {
			if ( $detail['value'] ) {
				$detail['checked'] = true;
			}
			$detail['value'] = 1;
			$detail['label'] = $this->translate( 'Yes' );
		}
		if ( $detail['form'] === 'select' ) {
			$value              = $detail['value'];
			$options            = $this->app->array->get( $option, 'options', [] );
			$detail['selected'] = $value;
			if ( ! isset( $options[ $value ] ) ) {
				$options[ $value ] = $value;
			}
			$detail['options'] = $options;
		}
		if ( $detail['form'] === 'multi_select' ) {
			$value              = $detail['value'];
			$options            = $this->app->array->get( $option, 'options', [] );
			$detail['form']     = 'select';
			$detail['multiple'] = true;
			$detail['selected'] = $this->app->string->explode( $value, $this->get_delimiter( $option ) );
			$detail['options']  = $options;
			$detail['size']     = count( $options );
			$detail['name']     .= '[]';
		}

		return $detail;
	}

	/**
	 * @param string $name
	 * @param array $option
	 *
	 * @return bool
	 */
	private function update_setting( $name, $option ) {
		$detail  = $this->app->setting->get_setting( $name, true );
		$default = null;
		if ( $this->app->array->get( $detail, 'type' ) === 'bool' ) {
			$default = 0;
		}
		if ( $this->app->array->get( $option, 'form' ) === 'multi_select' ) {
			$this->app->input->set_post( $detail['name'], $this->app->string->implode( $this->app->input->post( $detail['name'] ), $this->get_delimiter( $option ) ) );
		}

		return $this->app->option->set_post_value( $detail['name'], $default );
	}

	/**
	 * @param array $option
	 *
	 * @return string
	 */
	private function get_delimiter( $option ) {
		return $this->app->array->get( $option, 'delimiter', ',' );
	}
}
