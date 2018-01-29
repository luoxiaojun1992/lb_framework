<?php

namespace lb\components\observers;

use lb\components\events\BaseEvent;
use lb\components\listeners\BaseListener;
use lb\components\queues\handlers\EventHandler;
use lb\components\queues\jobs\Job;
use lb\Lb;

class BaseObserver implements ObserverInterface
{
    protected static $event_listeners = [];

    public static function on($event_name, $listener, $data = null)
    {
        static::$event_listeners[] = [$event_name, $listener, $data];
    }

    public static function trigger($event_name, $event = null, $ignoreQueue = false)
    {
        foreach(static::$event_listeners as $event_listener) {
            if ($event_listener[0] == $event_name) {
                $event = $event ?: new BaseEvent();
                if ($data = $event_listener[2]) {
                    $event->setData($data);
                }

                /**
                 * @var BaseListener $listener
                 */
                $listener = $event_listener[1];

                //Async Queue
                if (static::asyncQueue($event_name, $event, $listener, $ignoreQueue)) {
                    continue;
                }

                //Sync Listener
                static::sync($event, $listener);

            }
        }
    }

    protected static function asyncQueue($eventName, $event, $listener, $ignoreQueue)
    {
        if (!($listener instanceof \Closure)) {
            if (!$ignoreQueue && $listener::$useQueue) {
                Lb::app()->queuePush(new Job(EventHandler::class, ['event_name' => $eventName, 'event' => $event]));
                return true;
            }
        }

        return false;
    }

    protected static function sync($event, $listener)
    {
        if ($listener instanceof \Closure) {
            $callableListener = $listener;
        } else {
            $callableListener = [$listener, 'handler'];
        }

        call_user_func_array($callableListener, ['event' => $event]);
    }
}
