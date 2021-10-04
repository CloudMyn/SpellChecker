<?php

namespace CloudMyn\SpellChecker\Console;

use CloudMyn\SpellChecker\Utils\Dictionary;
use Illuminate\Console\Command;

class GenerateDictionary extends Command
{
    protected $signature = 'dictionary:generate';

    protected $description = 'generate the dictionary';

    public function handle()
    {
        $this->info('generating dictionary...');

        Dictionary::generate();

        $this->info('generated dictionary');
    }
}
