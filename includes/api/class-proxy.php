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
 * @copyright: (c) 2020 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_API_Proxy extends WP_REST_Controller
{
    /** @var array Default Google Analytics endpoints */
    const CAOS_PROXY_COLLECT_ENDPOINTS = [
        '/r/collect',
        '/j/collect',
        '/collect'
    ];

    /** @var array Enhanced Link Attribution */
    const CAOS_PLUGIN_LINK_ATTR_ENDPOINT = [
        '/plugins/ua/linkid.js'
    ];

    /** @var array $plugin_endpoints */
    private $plugin_endpoints = [];

    /** @var string $namespace */
    protected $namespace;

    /** @var string $rest_base */
    protected $rest_base;

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
                        'methods'             => WP_REST_Server::READABLE,
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
                        'methods'             => WP_REST_Server::READABLE,
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
        $params         = $request->get_params();
        $ip             = $this->get_user_ip_address();

        if (CAOS_OPT_ANONYMIZE_IP) {
            $ip = $this->anonymize_ip($ip);
        }

        $passThruParams = array(
            'uip'        => $ip,
            'ua'         => $request->get_header('user_agent')
        );
        $query          = '?' . http_build_query($params + $passThruParams);
        $url            = CAOS_GA_URL . '/r/collect' . $query;

        try {
            $response = wp_remote_get(
                $url,
                array(
                    'user-agent' => $request->get_header('user_agent'),
                    'headers'    => array(
                        'X-Forwarded-For:' => $ip
                    )
                )
            );
        } catch (\Exception $error) {
            throw new Exception($error->getMessage());
        }

        return $response;
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
        $localFileUrl = content_url() . rtrim(CAOS_OPT_CACHE_DIR, '/') . $endpoint;

        // Set Redirect and die() to force redirect on some servers.
        header("Location: $localFileUrl");
        die();
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
        $localFile = WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR . trim($endpoint, '/');

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
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (is_array(explode(',', $ip))) {
            $ip = explode(',', $ip);

            return $ip[0];
        }

        return $ip;
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
