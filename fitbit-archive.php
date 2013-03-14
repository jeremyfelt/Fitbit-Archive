<?php
/*
Plugin Name: Fitbit Archive
Plugin URI: http://fitbit.com/jeremyfelt/fitbit-archive/
Description:
Version: 0.1
Author: Jeremy Felt
Author URI: http://jeremyfelt.com
License: GPL2
*/

/*  Copyright 2013 Jeremy Felt (email: jeremy.felt@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include __DIR__ . '/includes/fitbitphp.php';
include __DIR__ . '/includes/config.php';

class Fitbit_Archive_Foghlaim {

	/**
	 * @var bool|FitBitPHP
	 */
	var $fitbit = false;

	/**
	 * Consumer key provided for custom app via Fitbit
	 *
	 * @var string
	 */
	var $consumer_key = FAF_CONSUMER_KEY;

	/**
	 * Consumer key provided for custom app via Fitbit
	 *
	 * @var string
	 */
	var $consumer_secret = FAF_CONSUMER_SECRET;

	/**
	 * Personal oauth token after callback routine
	 *
	 * @todo - make this dynamic
	 *
	 * @var string
	 */
	var $oauth_token = FAF_OAUTH_TOKEN;

	/**
	 * Personal oauth secret after callback routine
	 *
	 * @todo - make this dynamic
	 *
	 * @var string
	 */
	var $oauth_secret = FAF_OAUTH_SECRET;

	/**
	 * Maintains the single instance of this
	 *
	 * @var bool|Fitbit_Archive_Foghlaim
	 */
	private static $instance = false;

	/**
	 * Private, lonely, empty constructor
	 */
	private function __construct() {}

	/**
	 * Handle requests for the instance.
	 *
	 * @return bool|Fitbit_Archive_Foghlaim
	 */
	public static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new Fitbit_Archive_Foghlaim();
		return self::$instance;
	}

	private function new_fitbit() {
		if ( false === $this->fitbit )
			$this->fitbit = new FitBitPHP( $this->consumer_key, $this->consumer_secret );
	}

	private function auth_fitbit() {
		$this->new_fitbit();
		// @todo still thinking about this URL
		$this->fitbit->initSession( admin_url( 'fitbit-archive-callback' ) );
	}

	private function handle_callback() {
		// expect admin_url from auth_fitibt, $_GET['oauth_token'] and $_GET['oauth_verifier']
		$this->auth_fitbit();
		$oauth_token = $this->fitbit->getOAuthToken();
		$oauth_secret = $this->fitbit->getOAuthSecret();

		if ( empty( $oauth_token ) || empty( $oauth_secret ) )
			wp_die( 'Empty callback data received from OAuth', 'Invalid Fitbit Authentication' );

		$user_id = get_current_user_id();
		update_user_meta( $user_id, $oauth_token, $oauth_secret );
	}

	private function setup_fitbit() {
		$this->new_fitbit();
		$this->fitbit->setOAuthDetails( $this->oauth_token, $this->oauth_secret );
	}

	public function get_profile() {
		$this->new_fitbit();
		$profile_xml = $this->fitbit->getProfile();
		// Do something
	}

	public function get_step_data() {
		$this->setup_fitbit();
		$step_data = $this->fitbit->getTimeSeries( 'steps', '2012-01-01', '2013-03-13' );

		echo '<ul>';
		foreach( $step_data as $date_data ) {
			echo '<li>' . $date_data->dateTime . ' : ' . $date_data->value . '</li>';
		}
		echo '</ul>';
	}
}
$fitbit_archive_foghlaim = Fitbit_Archive_Foghlaim::get_instance();