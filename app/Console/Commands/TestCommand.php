<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    public $signature = 'tst';

    public $description = 'Test command';

    public function handle() {
        dd(
            @json_decode('asdawdA{', true)
        );
    }
}
