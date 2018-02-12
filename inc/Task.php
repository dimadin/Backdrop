<?php

namespace dimadin\WP\Library\Backdrop;

use WP_Temporary;
use WP_Error;

class Task {
	protected $key;
	protected $callback;
	protected $params = array();

	public function __construct( $callback /* , $... */ ) {
		$this->callback = $callback;

		if ( func_num_args() > 1 ) {
			$args = func_get_args();
			$this->params = array_slice( $args, 1 );
		}

		$this->key = $this->get_unique_id();
	}

	public function schedule() {

		if ( $this->is_scheduled() ) {
			return new WP_Error( 'hm_backdrop_scheduled', 'Task is already scheduled to run' );
		}

		$data = array(
			'callback' => $this->callback,
			'params' => $this->params
		);
		WP_Temporary::set( 'hm_backdrop-' . $this->key, $data, 5 * MINUTE_IN_SECONDS );
		add_action( 'shutdown', array( $this, 'spawn_server' ) );

		return true;
	}

	public function is_scheduled() {
		return (bool) $this->get_data();
	}

	public function cancel() {
		if ( ! $this->is_scheduled() ) {
			return new WP_Error( 'hm_backdrop_not_scheduled', 'Task is not scheduled to run' );
		}

		WP_Temporary::delete( 'hm_backdrop-' . $this->key );
		return true;
	}

	public function spawn_server() {
		$server_url = admin_url( 'admin-ajax.php' );
		$data = array(
			'action' => 'hm_backdrop_run',
			'key'    => $this->key,
		);
		$args = array(
			'body' => $data,
			'timeout' => 0.01,
			'blocking' => false,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		);
		wp_remote_post( $server_url, $args );
		return true;
	}

	protected function get_data() {
		return WP_Temporary::get( 'hm_backdrop-' . $this->key );
	}

	protected function get_unique_id() {
		return sha1( serialize( $this->callback ) . serialize( $this->params ) );
	}
}
