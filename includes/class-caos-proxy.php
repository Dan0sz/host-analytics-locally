<?php
/**
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 */

class CAOS_Proxy extends WP_REST_Controller
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

        /**
         * linkid.js seems to be causing issues.
         */
        foreach (self::CAOS_PLUGIN_ENDPOINTS as $endpoint) {
            register_rest_route(
                $this->namespace,
                '/' . $this->rest_base . $endpoint,
                array(
                    array(
                        'methods' => WP_REST_Server::READABLE,
                        'callback' => array($this, 'file_download'),
                        'permission_callback' => array($this, 'permissions_check')
                    ),
                    'schema' => null,
                )
            );
        }
    }

    /**
     * @param $request
     *
     * @return bool
     */
    public function permissions_check()
    {
        return true;
    }

    /**
     * The uip-parameter is added to the query, to keep the location data accurate.
     *
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @throws Exception
     */
    public function send_data($request)
    {
        $params  = $request->get_params();
        $ip      = $this->get_user_ip_address();
        $paramIp = array('uip' => $ip);
        $query   = '?' . http_build_query($params + $paramIp);
        $url     = CAOS_GA_URL . '/r/collect' . $query;
        try {
            $response = wp_remote_get(
                $url,
                array(
                    'headers' => array(
                        'X-Forwarded-For' => $this->get_user_ip_address()
                    )
                )
            );
        } catch (\Exception $error) {
            throw new Exception($error->getMessage());
        }

        return $response;
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
     * When Enhanced Ecommerce features are used, we need to redirect the requests to download the
     * plugins. Sadly, these redirects will be caught by Ad Blockers.
     *
     * @param $request
     */
    public function file_download($request)
    {
        $endpoint = array_filter(self::CAOS_PLUGIN_ENDPOINTS, function($value) use ($request) {
            return strpos($request->get_route(), $value) !== false;
        });
        $file = CAOS_GA_URL . $endpoint[0];

        // Force the download
        header("Location: $file");
    }
}
