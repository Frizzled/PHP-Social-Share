<?
/**
 * Class SocialShare
 * Generate then redirect to a sharing link (to share a URL on a social network) for the following networks: Facebook, Google, Twitter.  
 * Constructor accepts an associative array of required inputs (indexes are url & title) and the social network to generate a sharing link for (class constants provided).
 *
 * https://github.com/Frizzled/PHP-Social-Share
 *
 * Copyright (c) 2014 Vladimir Loscutoff
 * Released under the MIT license
 */
class SocialShare {

	/**
	 * Currently supported social networks
	 */
	const FACEBOOK = 'facebook';
	const TWITTER = 'twitter';
	const GOOGLE = 'google';

	/**
	 * @var bool Are we running in debug mode?
	 */
	protected $debug;

	/**
	 * @var string URL user wishes so share on a social network
	 */
	protected $url;

	/**
	 * @var array Associative array of parameters (expected indexes are url, title, hashtag)
	 */
	protected $vars;

	/**
	 * @var array Social URLs, replace-able variables are: %url$s, %title$s, %hashtags$s
	 */
	protected $socialUrls = array(
		self::FACEBOOK => 'https://www.facebook.com/share.php?src=bm&v=4&i=1407332352&u=%url$s&t=%title$s',
		self::TWITTER => 'http://twitter.com/share?text=%title$s&url=%url$s&hashtags=%hashtags$s',
		self::GOOGLE => 'https://plus.google.com/share?url=%url$s',
	);

	/**
	 * @var array Optional defaults for a social network: variables that can be used but are not required 
	 */
	protected $socialDefaults = array(
		self::TWITTER => array('hashtags'=>''),
	);

	/**
	 * Create and redirect to a sharing link for a social network
	 * @param $social string A social network constant (e.g. FACEBOOK, TWITTER, GOOGLE)
	 * @param $vars array Associative array of parameters (indexes are url, title, hashtags)
	 * @param $debug bool Run in debug mode?
	 */
	function __construct($social, $vars, $debug = FALSE) {
		try {
			$this->setVars($vars, $social, $debug);
			$this->setUrl($social);
			$this->redirect();		
		} catch(Exception $e) {
			if ($this->debug) {
				echo $e->getMessage();
			}
		}
	}

	/**
	 * urlencode then merge the input values with defaults
	 * @param $vars array Associative array of parameters
	 * @param $social string Social network we wish to create a link for
	 * @param $debug bool Debug mode?
	 * @throws Exception
	 */
	protected function setVars($vars, $social, $debug) {
		$this->debug = $debug;
		if (count($vars) == 0) { throw new Exception('Unable to generate URL: No variables to process'); }
		foreach ($vars as $index => $var) {
			$vars[$index] = urlencode($var);
		}
		if (isset($this->socialDefaults[$social])) {
			$vars = array_merge($this->socialDefaults[$social], $vars);
		}
		$this->vars = $vars;
	}

	/**
	 * Set the URL to redirect to
	 * @param $social
	 * @throws Exception
	 */
	protected function setUrl($social) {
		if (!isset($this->socialUrls[$social])) { throw new Exception('no url'); }
		$this->url = $this->vnsprintf($this->socialUrls[$social], $this->vars);
	}

	/**
	 * Send the user to the social network's URL to share their link
	 * @return void
	 */
	protected function redirect() {
		if ($this->debug) {
			echo $this->url;			
		} else {
			header( 'Location: '.$this->url);
		}
	}

	/**
	 * Utility method: vsprintf for associative arrays
	 * @param $format
	 * @param array $data
	 * @return string
	 */
	protected function vnsprintf($format, array $data) {
		preg_match_all( '/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) (?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x', $format, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$offset = 0;
		$keys = array_keys($data);
		foreach ( $match as &$value ) {
			if ( ( $key = array_search( $value[1][0], $keys) ) !== FALSE || ( is_numeric( $value[1][0]) && ( $key = array_search( (int)$value[1][0], $keys) ) !== FALSE ) ) {
				$len = strlen( $value[1][0]);
				$format = substr_replace( $format, 1 + $key, $offset + $value[1][1], $len);
				$offset -= $len - strlen( $key);
			}
		}
		return vsprintf( $format, $data);
	}
		
}
