<?php
namespace Suilven\Notifier;

/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 16/3/2561
 * Time: 0:02 น.
 */

use Maknz\Slack\Client;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;

class Notify
{

    public function wibble()
    {

    }

    public static function debug($message, $channel = 'development')
    {
        self::sendSlackMessage($message, $channel);
    }

    /**
     * Note to self - Slack Webhook URLs have a default channel, but can be configured to send to any
     * BackTrace class has an error generator
     *
     * Use $client->to($channel)->send($message);
     *
     * @param $message
     * @param $channel
     */
    private static function sendSlackMessage($message, $channel)
    {
        user_error('test');

        $hooksFromConfig = Config::inst()->get('Suilven\Notifier\Notify', 'slack_webhooks');
        $url = null;
        Injector::inst()->get(LoggerInterface::class)->debug('HOOKS ' . print_r($hooksFromConfig, 1));

        foreach($hooksFromConfig as $hook)
        {
            if ($hook['name'] == $channel)
            {
                $url = $hook['url'];
            } elseif (empty($url) && $hook['name'] == 'default') {
                $url = $hook['url'];
            }
        }

        if (empty($url)) {
            Injector::inst()->get(LoggerInterface::class)->debug('The slack channel ' . $channel . ' has no webhook');
            return;
        }

        $client = new Client($url);
        $client->to($channel)->send($message);

    }
}