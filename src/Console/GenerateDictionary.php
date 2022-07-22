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

        $tables = config('spellchecker.tables');

        Dictionary::generateFromDatabase($tables);

        $this->info('generated dictionary');
    }
}
