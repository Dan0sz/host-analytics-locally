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
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_API_Proxy extends WP_REST_Controller
{
    const CAOS_PROXY_ENDPOINTS = array(
        '/r/collect',
        '/j/collect',
        '/collect'
    );

    const CAOS_PLUGIN_ENDPOINTS = array(
        '/plugins/ua/ec.js',
        '/plugins/ua/linkid.js'
    );

    /** @var string $namespace */
    protected $namespace;

    /** @var string $rest_base */
    protected $rest_base;

    /**
     * CAOS_Proxy constructor.
     */
    public function __construct()
    {
        $this->namespace = 'caos-analytics/v1';
        $this->rest_base = 'proxy';
    }

    /**
     * analytics.js seems to randomly use /r/ and /j/ in the Measurement Protocol endpoint. That's why
     * we register them all.
     */
    public function register_routes()
    {
        foreach (self::CAOS_PROXY_ENDPOINTS as $endpoint) {
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

        foreach (self::CAOS_PLUGIN_ENDPOINTS as $endpoint) {
            register_rest_route(
                $this->namespace,
                '/' . $this->rest_base . $endpoint,
                array(
                    array(
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => array($this, 'set_redirect'),
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
                        'X-Forwarded-For:' => $this->get_user_ip_address()
                    )
                )
            );
        } catch (\Exception $error) {
            throw new Exception($error->getMessage());
        }

        return $response;
    }

    /**
     * If Ecommerce Plugins are used, we need to capture these requests and redirect them to the
     * locally hosted versions, so these requests will also bypass Ad Blockers.
     *
     * @param $request
     */
    public function set_redirect($request)
    {
        $endpoint = array_filter(self::CAOS_PLUGIN_ENDPOINTS, function ($value) use ($request) {
            return strpos($request->get_route(), $value) !== false;
        });

        $endpoint     = reset($endpoint);
        $localFileUrl = content_url() . rtrim(CAOS_OPT_CACHE_DIR, '/') . $endpoint;

        // Set Redirect and die() to force redirect on some servers.
        header("Location: $localFileUrl");
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
}
