<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;

class GenerateSessionCommand extends Command
{

    public $signature = 'generate:session';

    public $description = 'Generate session';

    public function handle() {
        $session = new Session();
        $session->save();

        dd($session->toArray());
    }

}
