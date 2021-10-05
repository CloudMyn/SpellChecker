<?php

namespace CloudMyn\SpellChecker\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function CloudMyn\SpellChecker\Helpers\getWordListDic;

class SpellCheckerInstall extends Command
{
    protected $signature = 'spellchecker:install';

    protected $description = 'install spellchecker package';

    public function handle()
    {
        $this->info('installing package...');

        $config_path = config_path('spellchecker.php');

        $config_exists = file_exists($config_path);

        if ($config_exists && is_file($config_path)) {
            $result = $this->confirm('Konfigurasi telah tersedia!, apakah anda ingin menggantinya?', true);
            if ($result === true) unlink($config_path);
        }

        $this->call('vendor:publish', [
            '--provider' => "CloudMyn\SpellChecker\SpellCheckerServiceProvider",
            '--tag' => "config"
        ]);

        File::copyDirectory(__DIR__ . "/../../wordlist/", getWordListDic());

        $result = $this->confirm('Apakah anda ingin men-generate kosakata dasar! ', true);
        if ($result === true) $this->call('dictionary:generate');

        $this->info('package installed successfuly');
    }
}
