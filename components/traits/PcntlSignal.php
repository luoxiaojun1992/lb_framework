<?php

namespace lb\components\traits;

trait PcntlSignal
{
    public function listenPcntlSignals(Array $signals, $callback)
    {
        foreach ($signals as $signal) {
            pcntl_signal($signal, $callback);
        }
    }
}
