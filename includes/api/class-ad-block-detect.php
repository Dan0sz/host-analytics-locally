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
                        'methods'             => WP_REST_Server::CREATABLE,
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
        parse_str($request->get_body(), $label);

        $params = [
            'v'   => (string) 1,
            't'   => 'event',
            'tid' => CAOS_OPT_TRACKING_ID,
            'cid' => $this->generate_uuid(),
            // Set IP to 0 to guarantee GDPR compliance.
            'uip' => 0,
            'ec'  => 'Tracking',
            'ea'  => 'Ad Blocker',
            'el'  => isset($label['result']) && $label['result'] == 0 ? 'Disabled' : 'Enabled',
            'ev'  => '1',
            'ua'  => $request->get_header('user_agent')
        ];

        $query = '?' . http_build_query($params);
        $url   = CAOS_GA_URL . '/collect' . $query;

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

        if ($request->get_param('result') == 1) {
            $current_value = (int) get_transient(CAOS_Admin_Functions::CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE);
            // Does not expire, but can be safely cleaned by db clean up plugins.
            set_transient(CAOS_Admin_Functions::CAOS_ADMIN_BLOCKED_PAGES_CURRENT_VALUE, ++$current_value);
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

        /**
         * TODO: Maybe the output of this should be a stored to a cookie or session, so ad blocker usage could be bound to client IDs i.e. sessions.
         * 
         * How to achieve this and maintain privacy?
         */
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
