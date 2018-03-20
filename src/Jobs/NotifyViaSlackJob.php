<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 20/3/2561
 * Time: 14:27 น.
 */
namespace Suilven\Notifier\Jobs;

use Maknz\Slack\Client;
use SilverStripe\Core\Injector\Injector;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJob;

class NotifyViaSlackJob extends AbstractQueuedJob implements QueuedJob
{
    /**
     * @var string job type is queued
     */
    private $type = QueuedJob::IMMEDIATE;

    /**
     * Initialise job as immediate with no timing restrictions
     * @param string $webhookURL the URL to use to ping slack
     * @param string $message the message payload
     * @param null|string the channel to send to, blank for the webhook default
     * @param null $type the job type
     */
    public function __construct()
    {
        $this->type = QueuedJob::IMMEDIATE;
        $this->times = array();
    }

    /**
     * @return string
     */
    public function getJobType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return "Slack Sender";
    }

    /**
     * Setup
     */
    public function setup()
    {
        $this->totalSteps = 1;
    }


    /**
     * Send the webhook to slack
     */
    public function process()
    {
        // $this->channel evaluates to blank in the following if, so store it as a variable
        $channel = $this->channel;

        $client = new Client($this->webhookURL);
        if (empty($channel)) {
            $client->send($this->message);
        } else {
            #Send to designated channel
            $client->to($this->channel)->send($this->message);
        }

        // required to terminate the job
        $this->isComplete = true;
        $this->currentStep = 1;
    }
}
