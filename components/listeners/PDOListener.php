<?php

namespace lb\components\listeners;

use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PDO\TracedStatement;
use lb\components\events\BaseEvent;
use lb\components\traits\Singleton;

class PDOListener extends BaseListener
{
    use Singleton;

    protected $context;

    public function handler(BaseEvent $event)
    {
        parent::handler($event);

        /**
 * @var TraceablePDO $traceablePDO 
*/
        $traceablePDO = $event->getData();
        $tracedStatement = new TracedStatement($event->getStatement(), $event->getBindings());
        $tracedStatement->start($event->getStartTime(), $event->getStartMemory());
        $tracedStatement->end(
            null, $event->getPdoStatement()->rowCount(), $event->getEndTime(), $event->getEndMemory()
        );
        $traceablePDO->addExecutedStatement($tracedStatement);
    }
}
