<?php

namespace lb\controllers\console;

class TinkController extends ConsoleController
{
    public function actionIndex()
    {
        $_SERVER['argv'] = [];

        // And go!
        call_user_func(\Psy\bin());
    }
}
