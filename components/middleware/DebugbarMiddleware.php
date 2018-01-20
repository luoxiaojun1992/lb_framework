<?php

namespace lb\components\middleware;

use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\StandardDebugBar;
use lb\components\consts\Event;
use lb\components\db\mysql\Connection;
use lb\components\listeners\LogWriteListener;
use lb\components\listeners\PDOListener;
use lb\components\traits\RateLimit;
use lb\Lb;

class DebugbarMiddleware extends BaseMiddleware
{
    use RateLimit;

    /**
     * @param $params
     * @param $successCallback
     * @param $failureCallback
     * @throws \DebugBar\DebugBarException
     */
    public function runAction($params, $successCallback, $failureCallback)
    {
        Lb::app()->getDIContainer()->set('debugbar', new StandardDebugBar());

        $this->addCollectors();

        $this->runNextMiddleware();
    }

    /**
     * @throws \DebugBar\DebugBarException
     */
    protected function addCollectors()
    {
        /**
         * @var StandardDebugBar $debugBar
         */
        $debugBar = Lb::app()->getDIContainer()->get('debugbar');

        //PDO Collector
        $traceablePDO = new TraceablePDO(Connection::component()->write_conn);
        $pdoCollector = new PDOCollector($traceablePDO, new TimeDataCollector(microtime(true)));
        Lb::app()->on(Event::PDO_EVENT, new PDOListener(), $traceablePDO);
        $debugBar->addCollector($pdoCollector);

        //Message Collector
        $messageCollector = new MessagesCollector('logs');
        Lb::app()->on(Event::LOG_WRITE_EVENT, new LogWriteListener(), $messageCollector);
        $debugBar->addCollector($messageCollector);
    }
}
