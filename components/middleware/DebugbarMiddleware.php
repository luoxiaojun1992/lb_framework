<?php

namespace lb\components\middleware;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\StandardDebugBar;
use lb\components\consts\Event;
use lb\components\containers\Config;
use lb\components\db\mysql\Connection;
use lb\components\debugbar\DebugBar;
use lb\components\listeners\LogWriteListener;
use lb\components\listeners\PDOListener;
use lb\components\request\RequestContract;
use lb\components\traits\RateLimit;
use lb\Lb;

class DebugbarMiddleware extends BaseMiddleware
{
    use RateLimit;

    /**
     * @var StandardDebugBar
     */
    protected $debugBar;

    /**
     * @param $params
     * @param $successCallback
     * @param $failureCallback
     * @throws \DebugBar\DebugBarException
     */
    public function runAction($params, $successCallback, $failureCallback)
    {
        $this->debugBar = DebugBar::getInstance();

        $this->addCollectors($params);

        $this->runNextMiddleware();
    }

    /**
     * @throws \DebugBar\DebugBarException
     */
    protected function addCollectors($params)
    {
        //PDO Collector
        $traceablePDO = new TraceablePDO(Connection::component()->write_conn);
        $pdoCollector = new PDOCollector($traceablePDO, new TimeDataCollector(microtime(true)));
        Lb::app()->on(Event::PDO_EVENT, new PDOListener(), $traceablePDO);
        $this->debugBar->addCollector($pdoCollector);

        //Message Collector
        $messageCollector = new MessagesCollector('logs');
        Lb::app()->on(Event::LOG_WRITE_EVENT, new LogWriteListener(), $messageCollector);
        $this->debugBar->addCollector($messageCollector);

        //Config Collector
        /**
         * @var Config $configContainer
         */
        $configContainer = Lb::app()->containers['config'];
        $configCollector = new ConfigCollector($configContainer->iterator()->getCollection());
        $this->debugBar->addCollector($configCollector);

        //CPU Load Avg
        $this->debugBar['messages']->info('CPU Load Avg: ' . implode(',', sys_getloadavg()));
        //PHP PID
        $this->debugBar['messages']->info('PHP PID: ' . getmypid());
        /**
         * @var RequestContract $request
         */
        $request = $params['request'];
        //Server IP
        $this->debugBar['messages']->info('Server IP: ' . $request->getHostAddress());
    }
}
