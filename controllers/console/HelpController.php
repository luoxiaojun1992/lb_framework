<?php

namespace lb\components\containers\console;

use lb\controllers\console\ConsoleController;

class HelpController extends ConsoleController
{
    public function index()
    {
        $this->writeln('Building...');
    }
}
