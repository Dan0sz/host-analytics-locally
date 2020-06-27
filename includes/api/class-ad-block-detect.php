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

class CAOS_API_AdBlockDetect extends WP_REST_Controller
{
    /** @var array */
    const CAOS_ADBLOCK_DETECT_ENDPOINTS = [
        '/detect'
    ];

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
        $this->rest_base        = 'block';
    }

    /**
     * analytics.js seems to randomly use /r/ and /j/ in the Measurement Protocol endpoint. That's why
     * we register them all.
     */
    public function register_routes()
    {
        foreach (self::CAOS_ADBLOCK_DETECT_ENDPOINTS as $endpoint) {
            register_rest_route(
                $this->namespace,
                '/' . $this->rest_base . $endpoint,
                array(
                    array(
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => [$this, 'send_data'],
                        'permission_callback' => [$this, 'permissions_check']
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
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @throws Exception
     */
    public function send_data($request)
    {
        $params = [
            'v'   => (string) 1,
            't'   => 'event',
            'tid' => CAOS_OPT_TRACKING_ID,
            'cid' => $this->generate_uuid(),
            'ec'  => 'Tracking',
            'ea'  => 'Ad Blocker',
            'el'  => (string) $request->get_param('result') == 0 ? 'Disabled' : 'Enabled'
        ];

        $passThruParams = array(
            'ua' => $request->get_header('user_agent')
        );
        $query          = '?' . http_build_query($params + $passThruParams);
        $url            = CAOS_GA_URL . '/collect' . $query;

        try {
            $response = wp_remote_get(
                $url,
                array(
                    'user-agent' => $request->get_header('user_agent')
                )
            );
        } catch (\Exception $error) {
            throw new Exception($error->getMessage());
        }

        return $response;
    }

    /**
     * Generates a random UUID v4.
     *
     * @param null $data
     *
     * @return string
     * @throws Exception
     */
    private function generate_uuid($data = null)
    {
        $data = $data ?? random_bytes(16);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}