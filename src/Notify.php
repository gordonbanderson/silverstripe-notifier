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
use Suilven\Notifier\Jobs\NotifyViaSlackJob;
use Symbiote\QueuedJobs\Services\QueuedJobService;

class Notify
{

    private $wibble;


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
        $hooksFromConfig = Config::inst()->get('Suilven\Notifier\Notify', 'slack_webhooks');
        $url = null;

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

        // create job and place on the queue
        error_log("NotifyViaSlackJob({$url}, {$message}, {$channel})");
        $job = new NotifyViaSlackJob();
        $job->webhookURL = $url;
        $job->message = $message;
        $job->channel = $channel;

        $job->setWibble('This is wibble!!!!');

        Injector::inst()->get(LoggerInterface::class)->debug('Created slack job');

        // this works but not queued
        // $job->setup();
        // $job->process();

        singleton(QueuedJobService::class)->queueJob($job);

    }
}
