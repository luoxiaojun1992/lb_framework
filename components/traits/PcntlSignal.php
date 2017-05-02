<?php

namespace lb\components\traits;

trait PcntlSignal
{
    public function listenPcntlSignals(Array $signals, $callback)
    {
        declare(ticks=1);
        foreach ($signals as $signal) {
            pcntl_signal($signal, $callback);
        }
    }
}
