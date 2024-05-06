<?php

namespace Cleantalk\CraftAntispam\services;

use Craft;
use craft\base\Component;

class CleantalkAntispamService extends Component
{
    public const ENGINE = 'craft-antispam-1.0.4';

    public function init(): void
    {

    }

    public function checkSpam($params = [])
    {
        $apiKey = \Cleantalk\CraftAntispam\CraftAntispam::getInstance()->getSettings()->ctApiKey;

        if ( ! $apiKey ) {
            return true;
        }

        $ct_request = new \Cleantalk\Common\Antispam\CleantalkRequest;

        $ct_request->auth_key        = $apiKey;
        $ct_request->agent           = self::ENGINE;

        $ct_request->sender_ip       = \Cleantalk\Common\Helper\Helper::ipGet('real', false);
        $ct_request->x_forwarded_for = \Cleantalk\Common\Helper\Helper::ipGet('x_forwarded_for', false);
        $ct_request->x_real_ip       = \Cleantalk\Common\Helper\Helper::ipGet('x_real_ip', false);

        // @ToDo implement JS checking
        //$ct_request->js_on           = $this->get_ct_checkjs($_COOKIE);

        // @ToDo implement SUBMIT TIME
        //$ct_request->submit_time     = $this->submit_time_test();

        $ct_request->sender_email = isset($params['email']) ? $params['email'] : '';
        $ct_request->message = isset($params['message']) ? $params['message'] : '';

        $ct_request->event_token = $_POST['ct_bot_detector_event_token'] ?? null;

        $ct                 = new \Cleantalk\Common\Antispam\Cleantalk();
        $ct->server_url     = 'https://moderate.cleantalk.org';

        $result = $ct->isAllowMessage($ct_request);
        $result = json_decode(json_encode($result), true);

        if ($result['allow'] == 0) {
            $ct_die_page = file_get_contents(\Cleantalk\Common\Antispam\Cleantalk::getLockPageFile());

            $message_title = '<b style="color: #49C73B;">Clean</b><b style="color: #349ebf;">Talk.</b> Spam protection';
            $back_script = '<script>setTimeout("history.back()", 5000);</script>';
            $back_link = '';
            if ( isset($_SERVER['HTTP_REFERER']) ) {
                $back_link = '<a href="' . \Cleantalk\Common\Cleaner\Sanitize::cleanUrl(\Cleantalk\Common\Variables\Server::get('HTTP_REFERER')) . '">Back</a>';
            }

            // Translation
            $replaces = array(
                '{MESSAGE_TITLE}' => $message_title,
                '{MESSAGE}'       => $result['comment'],
                '{BACK_LINK}'     => $back_link,
                '{BACK_SCRIPT}'   => $back_script
            );

            foreach ( $replaces as $place_holder => $replace ) {
                $ct_die_page = str_replace($place_holder, $replace, $ct_die_page);
            }
            die($ct_die_page);
        }
        return true;
    }
}
