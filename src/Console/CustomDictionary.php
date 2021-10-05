<?php

namespace CloudMyn\SpellChecker\Console;

use CloudMyn\SpellChecker\Utils\Dictionary;
use Illuminate\Console\Command;

class CustomDictionary extends Command
{
    protected $signature = 'custom:generate';

    protected $description = 'generate the custom dictionary';

    public function handle()
    {
        $this->info('generating custom dictionary...');

        Dictionary::generateCustom();

        $this->info('generated custom dictionary.');
    }
}
