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
     * analytics.js seems to randomly use /r/ in the Measurement Protocol endpoint. That's why
     * we register them both.
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
                        'permission_callback' => array($this, 'send_data_permissions_check')
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
    public function send_data_permissions_check($request)
    {
        return true;
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @throws Exception
     */
    public function send_data($request)
    {
        $params = $request->get_params();
        $query  = '?' . http_build_query($params);
        try {
            $response = wp_remote_get(CAOS_GA_URL . '/r/collect' . $query);
        } catch (\Exception $error) {
            throw new Exception($error->getMessage());
        }

        return $response;
    }
}
