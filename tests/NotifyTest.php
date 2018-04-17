<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 17/4/2561
 * Time: 0:46 น.
 */

namespace Suilven\Notifier\Tests;


use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use Suilven\Notifier\NotifyTrait;
use VCR\VCR;

class NotifyTest extends SapphireTest
{
    use NotifyTrait;

    public function setUp()
    {
        parent::setUp();


        $cfg = [
            [
                    'name' => 'default',
                    'url' => 'https://hooks.slack.com/services/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'
                ]
        ];

        Config::nest();
        Config::inst()->update('Suilven\Notifier\Notify', 'slack_webhooks', $cfg);

        VCR::configure()->setCassettePath('vcr');
        VCR::turnOn();
    }

    public function tearDown()
    {
        parent::tearDown();
        VCR::eject();
        VCR::turnOff();
    }

    public function test_notify()
    {
        VCR::insertCassette('vcr/test_notify');
        $this->notify('A comment was made', 'comments', 'debug');
    }
}
