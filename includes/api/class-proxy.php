<?php
/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/caos/
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_API_Proxy extends WP_REST_Controller
{
	/** @var array Default Google Analytics endpoints */
	const CAOS_PROXY_COLLECT_ENDPOINTS = [
		'/r/collect',
		'/j/collect',
		'/g/collect', // v4 API
		'/collect'
	];

	/** @var array Enhanced Link Attribution */
	const CAOS_PLUGIN_LINK_ATTR_ENDPOINT = [
		'/plugins/ua/linkid.js'
	];

	/** 
	 * Proxy IP Headers used to detect the visitors IP prior to sending the data to Google's Measurement Protocol.
	 * 
	 * @var array 
	 * 
	 * For CloudFlare compatibility HTTP_CF_CONNECTING_IP has been added.
	 * 
	 * @see https://support.cloudflare.com/hc/en-us/articles/200170986-How-does-Cloudflare-handle-HTTP-Request-headers- 
	 */
	const CAOS_PROXY_IP_HEADERS = [
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_FORWARDER_FOR',
		'HTTP_CLIENT_IP',
		'REMOTE_ADDR'
	];

	/** @var array $plugin_endpoints */
	private $plugin_endpoints = [];

	/** @var string $namespace */
	protected $namespace;

	/** @var string $rest_base */
	protected $rest_base;

	/** @var string $plugin_text_domain */
	private $plugin_text_domain = 'host-analyticsjs-local';

	/**
	 * CAOS_Proxy constructor.
	 */
	public function __construct()
	{
		$this->namespace        = 'caos/v1';
		$this->rest_base        = 'proxy';
		$this->plugin_endpoints = apply_filters('caos_stealth_mode_plugin_endpoints', self::CAOS_PLUGIN_LINK_ATTR_ENDPOINT);
	}

	/**
	 * analytics.js seems to randomly use /r/ and /j/ in the Measurement Protocol endpoint. That's why
	 * we register them all.
	 */
	public function register_routes()
	{
		$endpoints = apply_filters('caos_stealth_mode_proxy_endpoints', self::CAOS_PROXY_COLLECT_ENDPOINTS);

		foreach ($endpoints as $endpoint) {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . $endpoint,
				array(
					array(
						'methods'             => 'GET, POST',
						'callback'            => array($this, 'send_data'),
						'permission_callback' => array($this, 'permissions_check')
					),
					'schema' => null,
				)
			);
		}

		foreach ($this->plugin_endpoints as $endpoint) {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . $endpoint,
				array(
					array(
						'methods'             => 'GET',
						'callback'            => array($this, CAOS_OPT_EXT_PLUGIN_HANDLING),
						'permission_callback' => array($this, 'permissions_check')
					),
					'schema' => null,
				)
			);
		}
	}

	/**
	 * @return bool
	 */
	public function permissions_check()
	{
		return true;
	}

	/**
	 * The uip-parameter is added to the query, to preserve the visitor's location.
	 * The ua-parameter is added to the query, to preserve the visitor's User-Agent.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 * @throws Exception
	 */
	public function send_data($request)
	{
		CAOS::debug(__('CAOS API request started.', $this->plugin_text_domain));

		$params = $request->get_params();

		if (CAOS_OPT_SNIPPET_TYPE == 'minimal') {
			parse_str($request->get_body(), $params);
		}

		CAOS::debug(sprintf(__('Parameters: %s', $this->plugin_text_domain), print_r($params, true)));

		$ip                = $this->get_user_ip_address();
		$user_agent        = $request->get_header('user_agent');
		$additional_params = $this->build_additional_params($user_agent, $ip);
		$query             = '?' . http_build_query($params + $additional_params);
		$url               = $this->get_route() . $query;

		$response = wp_remote_get(
			$url,
			[
				'user-agent' => $request->get_header('user_agent'),
				'headers'    => [
					'X-Forwarded-For:' => $ip
				]
			]
		);

		CAOS::debug(sprintf(__('CAOS API request finished: %s - %s', $this->plugin_text_domain), wp_remote_retrieve_response_code($response), wp_remote_retrieve_response_message($response)));

		if (!is_wp_error($response)) {
			return wp_send_json_success(wp_remote_retrieve_body($response));
		}

		return wp_send_json_error(wp_remote_retrieve_response_message($response), wp_remote_retrieve_response_code($response));
	}

	/**
	 * If plugins are used and set_redirect is set, we need to capture these requests and
	 * redirect them to the locally hosted versions, so these requests will also bypass Ad
	 * Blockers.
	 *
	 * @param $request
	 */
	public function set_redirect($request)
	{
		$endpoint = array_filter($this->plugin_endpoints, function ($value) use ($request) {
			return strpos($request->get_route(), $value) !== false;
		});

		$endpoint     = reset($endpoint);
		CAOS::debug(sprintf(__('Endpoint hit: %s', $this->plugin_text_domain), $endpoint));

		$localFileUrl = content_url() . rtrim(CAOS_OPT_CACHE_DIR, '/') . $endpoint;
		CAOS::debug(sprintf(__('Polling %s before redirect.', $this->plugin_text_domain), $localFileUrl));

		if ($this->url_exists($localFileUrl)) {
			CAOS::debug(sprintf(__('%s was found. Redirecting...'), $localFileUrl));
		} else {
			CAOS::debug(sprintf(__('%s was not found on the server.'), $localFileUrl));
		}

		// Set Redirect and die() to force redirect on some servers.
		header("Location: $localFileUrl");
		die();
	}

	/**
	 * @param mixed $url 
	 * @return bool 
	 */
	private function url_exists($url)
	{
		$headers = get_headers($url);

		return stripos($headers[0], '200 OK') !== false;
	}

	/**
	 * If plugins are used and get_file is set, we need to capture these requests and return the
	 * locally hosted versions, so these requests will also bypass Ad Blockers.
	 *
	 * @param $request
	 */
	public function send_file($request)
	{
		$endpoint = array_filter($this->plugin_endpoints, function ($value) use ($request) {
			return strpos($request->get_route(), $value) !== false;
		});

		$endpoint  = reset($endpoint);
		CAOS::debug(sprintf(__('Endpoint hit: %s', $this->plugin_text_domain), $endpoint));

		$localFile = WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR . trim($endpoint, '/');
		CAOS::debug(sprintf(__('Polling %s before sending.', $this->plugin_text_domain), $localFile));

		if (file_exists($localFile)) {
			CAOS::debug(sprintf(__('%s was found. Sending file.'), $localFile));
		} else {
			CAOS::debug(sprintf(__('%s was not found on this server.'), $localFile));
		}

		header('Content-Type: application/javascript');
		header("Content-Transfer-Encoding: Binary");
		header('Content-Length: ' . filesize($localFile));
		flush();
		readfile($localFile);
		die();
	}

	/**
	 * @return string
	 */
	private function get_user_ip_address()
	{
		$ip = '';

		foreach (self::CAOS_PROXY_IP_HEADERS as $header) {
			if ($this->header_exists($header)) {
				CAOS::debug(sprintf(__('HTTP header %s found.', $this->plugin_text_domain), $header));

				$ip = $_SERVER[$header];

				if (is_array(explode(',', $ip))) {
					CAOS::debug(sprintf(__('Multiple IPs detected, using the first one: %s', $this->plugin_text_domain), print_r($ip, true)));

					$ip = explode(',', $ip);

					return $ip[0];
				}

				return $ip;
			}
		}
	}

	/**
	 * Checks if a HTTP header is set and is not empty.
	 * 
	 * @param mixed $global 
	 * @return bool 
	 */
	private function header_exists($global)
	{
		return isset($_SERVER[$global]) && !empty($_SERVER[$global]);
	}

	/**
	 * Builds an array with additional data for Google Analytics' Measurement Protocol:
	 * - (Anonymized) User IP
	 * - User Agent
	 * - GeoID (if CloudFlare's HTTP_CF_IPCOUNTRY header is present, i.e. IP GeoLocation is enabled)
	 * 
	 * @see https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-Cloudflare-IP-Geolocation-do-
	 * @see https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters
	 * 
	 * @param string $user_agent
	 * @param string $ip 
	 * 
	 * @return array 
	 */
	private function build_additional_params($user_agent, $ip)
	{
		if (CAOS_OPT_ANONYMIZE_IP) {
			$ip = $this->anonymize_ip($ip);
		}

		$additional_params = [
			'uip' => $ip,
			'ua'  => $user_agent
		];

		if ($this->header_exists('HTTP_CF_IPCOUNTRY')) {
			$additional_params['geoid'] = $_SERVER['HTTP_CF_IPCOUNTRY'];
		}

		CAOS::debug(sprintf(__('Additional Parameters: %s', $this->plugin_text_domain), print_r($additional_params, true)));

		return $additional_params;
	}

	/**
	 * Get route for Google's Measurement Protocol API.
	 * 
	 * @return string 
	 */
	private function get_route()
	{
		switch (CAOS_OPT_REMOTE_JS_FILE) {
			case 'gtag-v4.js':
				$endpoint = '/g/collect';
				break;
			default:
				$endpoint = '/r/collect';
		}

		return CAOS_GA_URL . $endpoint;
	}

	/**
	 * Anonymize current IP, before sending it to Google to respect the Anonymize IP advanced setting.
	 *
	 * @param $ip
	 *
	 * @return string|string[]|null
	 */
	private function anonymize_ip($ip)
	{
		return preg_replace('/(?<=\.)[^.]*$/u', '0', $ip);
	}
}
