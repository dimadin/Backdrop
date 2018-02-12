<?php

namespace dimadin\WP\Library\Backdrop;

class Main {
	const VERSION = '1.0.0';

	public static function init() {
		if ( ! has_action( 'wp_ajax_nopriv_hm_backdrop_run', __NAMESPACE__ . '\Server::spawn' ) ) {
			add_action( 'wp_ajax_nopriv_hm_backdrop_run', __NAMESPACE__ . '\Server::spawn' );
		}
	}
}
