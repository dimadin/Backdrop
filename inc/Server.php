<?php

namespace dimadin\WP\Library\Backdrop;

use WP_Temporary;
use WP_Error;

class Server {
	public function run() {
		if ( empty( $_POST['key'] ) ) {
			return new WP_Error( 'hm_backdrop_no_key', 'No key supplied' );
		}

		$data = WP_Temporary::get( 'hm_backdrop-' . $_POST['key'] );
		if ( empty( $data ) ) {
			return new WP_Error( 'hm_backdrop_invalid_key', 'Supplied key was not valid' );
		}

		$result = call_user_func_array( $data['callback'], $data['params'] );
		WP_Temporary::delete( 'hm_backdrop-' . $_POST['key'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	public static function spawn() {
		$server = new static();
		$server->run();
		exit;
	}
}
