<?php

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
		$this->fitbit->initSession( 'http://failurepuppy.com/fitbit-test/callback.php' );
	}

	private function handle_callback() {
		$this->new_fitbit();
		$this->fitbit->initSession( 'http://failurepuppy.com/fitbit-test/callback.php' );

		$oauth_token = $this->fitbit->getOAuthToken();
		$oauth_secret = $this->fitbit->getOAuthSecret();

		// Save token data
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