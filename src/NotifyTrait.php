<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 20/3/2561
 * Time: 23:50 น.
 */

namespace Suilven\Notifier;


trait NotifyTrait
{

    /**
     *
     * @param string $message Message content
     * @param string $channel Channel to send notification to
     * @param string string $level - level to send notication at
     */
    public function notify($message, $channel='default', $level = 'debug')
    {
        switch ($level)
        {
            case 'debug':
                Notify::debug($message, $channel);
                break;
            case 'info':
                Notify::info($message, $channel);
                break;
            case 'notice':
                Notify::notice($message, $channel);
                break;
            case 'warning':
                Notify::warning($message, $channel);
                break;
            case 'error':
                Notify::error($message, $channel);
                break;
            case 'critical':
                Notify::critical($message, $channel);
                break;
            case 'alert':
                Notify::alert($message, $channel);
                break;
            case 'emrgency':
                Notify::emergency($message, $channel);
                break;
            default:
                // do nothing for now
                break;
        }
    }
}
