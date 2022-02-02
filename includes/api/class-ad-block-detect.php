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
 * @url      : https://ffw.press/wordpress/caos/
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

    /** 
     * Proxy IP Headers used to detect the visitors IP prior to sending the data to Google's Measurement Protocol.
     * 
     * @var array 
     * 
     * For Cloudflare compatibility HTTP_CF_CONNECTING_IP has been added.
     * 
     * @see https://support.cloudflare.com/hc/en-us/articles/200170986-How-does-Cloudflare-handle-HTTP-Request-headers- 
     */
    const CAOS_PRO_IP_HEADERS = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDER_FOR',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR'
    ];

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
        parse_str($request->get_body(), $params);

        $ip = $this->get_user_ip_address();
        /**
         * Because tracking ad blockers will reveal the user's IP address, we always
         * anonymize it (regardless of the setting) to make sure no privacy laws are violated.
         */
        $ip     = $this->anonymize_ip($ip);
        $result = isset($params['result']) && $params['result'] === "0" ? 'Disabled' : 'Enabled';

        CAOS::debug(sprintf(__('User with IP %s has Ad Blockers %s.', $this->plugin_text_domain), $ip, $result));

        /**
         * Using a clientId allows tracking Ad Blocker users in sessions (as opposed to pageviews)
         * 
         * @since v4.2.0
         */
        $cid = $params['cid'] ?: $this->generate_uuid();

        $params = [
            'v'   => (string) 1,
            't'   => 'event',
            'tid' => CAOS_OPT_TRACKING_ID,
            'cid' => $cid,
            'uip' => $ip,
            'ec'  => 'Tracking',
            'ea'  => 'Ad Blocker',
            'el'  => $result,
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

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @return string
     */
    private function get_user_ip_address()
    {
        $ip = '';

        foreach (self::CAOS_PRO_IP_HEADERS as $header) {
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
     * Anonymize current IP, before sending it to Google to respect the Anonymize IP advanced setting.
     *
     * @param $ip
     *
     * @return string|string[]|null
     */
    private function anonymize_ip($ip)
    {
        $octets = explode('.', $ip);

        if (empty($octets)) {
            return $ip;
        }

        /**
         * @since v2.0.2
         * 
         * Instead of using Regex and str_replace, we're slicing the array parts
         * and rebuilding the ip (implode) to make sure no duplicate values are
         * replaced.
         * 
         * E.g. using str_replace or preg_replace; 192.168.1.1 would result in 092.068.0.0.
         */
        $second_to_last     = array_slice($octets, -2, 1, true);
        $second_to_last_key = array_key_first($second_to_last);

        $second_to_last[$second_to_last_key] = '0';

        $last     = array_slice($octets, -1, 1, true);
        $last_key = array_key_first($last);

        $last[$last_key] = '0';

        /**
         * Replace each octet with the with the 
         */
        $octets = array_replace($octets, $second_to_last, $last);

        return implode('.', $octets);
    }
}
