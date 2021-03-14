<?php

namespace App;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Sender
{
    public function __construct()
    {
        $run = (new App\SmsBuilder('smscsim.melroselabs.com', '2775', '789695', 'a85d40', 10000))->sendMessage();

        $process = new Process([$run]);
        $process->run();
        
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        echo $process->getOutput();
    }
}

