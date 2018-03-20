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
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use Suilven\Notifier\Jobs\NotifyViaSlackJob;
use Symbiote\QueuedJobs\Services\QueuedJobService;

class Notify
{
    const LEVELS = [
      'DEBUG' => 1,
      'INFO' => 2,
      'NOTICE' => 3,
      'WARNING' => 4,
      'ERROR' => 5,
      'CRITICAL' => 6,
      'ALERT' => 7,
      'EMERGENCY' => 8,
    ];

    /**
     * @var bool|string if this is set to a channel name, the channel will be set to this value.  This allows likes of
     *      all dev Slack activity to go to one channel, but for them to be separated in production
     */
    private static $channel_override = false;


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
        // do not send Slack messages in test mode for now
        // @todo Trap these for testing
        if (Director::isTest()) {
            return;
        }

        $hooksFromConfig = Config::inst()->get('Suilven\Notifier\Notify', 'slack_webhooks');
        error_log('HOOKS: ' . print_r($hooksFromConfig, 1));

        // optionally override the channel
        $channelOverride = Config::inst()->get('Suilven\Notifier\Notify', 'channel_override');
        if (!empty($channelOverride)) {
            $channel = $channelOverride;
            error_log('>>>>>>>> Overriden channel ' . $channelOverride);
        }

        // get the appropriate webhook
        $url = null;
        foreach($hooksFromConfig as $hook)
        {
            // stick with the default webhook but allow overriding on a per channel basis
            if ($hook['name'] == $channel)
            {
                $url = $hook['url'];
            } elseif (empty($url) && $hook['name'] == 'default') {
                $url = $hook['url'];
            }
        }

        // Log an error if Slack config misconfigured
        if (empty($url)) {
            Injector::inst()->get(LoggerInterface::class)->error('The slack channel ' . $channel . ' has no webhook');
            return;
        }

        // create job
        error_log("NotifyViaSlackJob({$url}, {$message}, {$channel})");
        $job = new NotifyViaSlackJob();
        $job->webhookURL = $url;
        $job->message = $message;
        $job->channel = $channel;

        error_log('Placing job on queue');

        // place on queue
        singleton(QueuedJobService::class)->queueJob($job);
    }
}
