<?php

namespace CloudMyn\SpellChecker\Utils;

use CloudMyn\SpellChecker\Exceptions\DictionaryException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use function CloudMyn\SpellChecker\Helpers\getCustomDicPath;
use function CloudMyn\SpellChecker\Helpers\getDicDirectory;
use function CloudMyn\SpellChecker\Helpers\getWordListDic;

class Dictionary
{

    public static function generateFromDatabase(array $tables)
    {
        if (empty($tables)) throw new Exception("'tables' cannot be empty");

        $main_dic = config('spellchecker.main_dic');

        if (empty($main_dic)) new Exception("'main_dic' cannot be empty");


        self::createDicFolder();

        $file_path = getDicDirectory($main_dic);

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $file = fopen($file_path, 'a');

        foreach ($tables as $tableK => $tableV) {

            $data = DB::table($tableK)->get($tableV)->toJson();

            $text = preg_replace("/[^A-Za-z]/", ' ', $data);

            $text = strtolower(trim(preg_replace('/\s+/', ' ', $text)));

            $words = explode(" ", $text);

            foreach ($words as $word) {
                // store the encoded word to new directory
                self::storeDictionary($file, $word);
            }
        }

        // generate costum dic
        self::generateCustom();

        fclose($file);

        $dic_words = [];

        self::scanFile($file_path, function ($line) use (&$dic_words) {
            $key = explode('::', $line)[0];

            if (array_key_exists($key, $dic_words)) {

                $dic_words[$key] = $dic_words[$key] + 1;

                return;
            }

            $dic_words[$key] = 0;
        });

        // hapus file sebelumnya
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $file = fopen($file_path, 'a');

        foreach ($dic_words as $word => $total) {
            // store the encoded word to new directory
            self::storeDictionary($file, $word, $total);
        }
    }

    /**
     *  Metode untuk generate custom kata dalam kalimat
     *
     *  @return bool
     */
    public static function generateCustom(): bool
    {
        // create custom dictionary directory
        $custom_dic     = config('spellchecker.custom_dic', storage_path('spellchecker_custom.dic'));
        $custom_path    = getCustomDicPath();

        $file_read      =   fopen($custom_dic, 'r');
        $file_custom    =   fopen($custom_path, 'a');

        if (!$file_read || !$file_custom) return false;

        while (!feof($file_read)) {
            $line = fgets($file_read);

            self::storeDictionary($file_custom, $line, 0);
        }

        fclose($file_read);

        return true;
    }

    private static function scanFile($file_path, $onScann)
    {
        $file = fopen($file_path, "r");

        while (!feof($file)) {

            $line   =   fgets($file);

            // make sure the line its not emptied
            if ($line === "" or is_null($line)) continue;

            $onScann($line);
        }
    }

    /**
     *  Metode untuk menyimpan kamus yang telah di encode ke dalam file direktori
     *
     *  @param  resource  $file_path
     *  @param  string  $word
     *  @param  string  $lang_dic
     */
    private static function storeDictionary(&$file, string $word, int $total = 0, string $lang_dic = "")
    {
        // remove any non alphabet character from the string
        $word   =   preg_replace("/[^A-Za-z]/", '', $word);

        if ($word === "" || is_null($word)) return;

        $word   =   strtolower($word);

        if (strlen($word) <= 3) return;

        switch ($lang_dic) {
            case self::strContains('my_', $lang_dic):
                $soundex = Soundex::soundexId($word);
                $metaphone = metaphone($word);
                break;
            case self::strContains('id_', $lang_dic):
                $soundex = Soundex::soundexId($word);
                $metaphone = metaphone($word);
                break;
            default:
                $soundex = soundex($word);
                $metaphone = metaphone($word);
        }

        $word   =   [$word, $metaphone, $soundex, $total];
        $word   =   join("::", $word);

        fwrite($file, $word . PHP_EOL);
    }

    /**
     *  Metode untuk mengecek jika sebuah string mengandung sebuah kata
     *
     *  @return bool
     */
    private static function strContains(string $find, string $word): bool
    {
        $result = preg_match("/{$find}/", $word);
        return $result >= 1 ? true : false;
    }

    /**
     *  Metode untuk membuat direktory untuk librari bahasa
     *
     *  @param  string|null $file_name
     *  @return bool true if was created otherwise false
     */
    public static function createDicFolder(?string $file_name = null): bool
    {
        // when $file_name is null this will create this path if doesnt exists
        // X:..\CloudMyn\SpellChecker\src\Helpers\..\..\dic\
        // Otherwise the path gonna be like this
        // X:..\CloudMyn\SpellChecker\src\Helpers\..\..\dic\en_US
        $dic_d  =   getDicDirectory($file_name);

        // check if the given path is exists or not!
        if (!file_exists($dic_d)) {
            mkdir($dic_d, 0777, true);
            return true;
        }

        return false;
    }

    /**
     *  Metode untuk menghapus direktory recursive
     *
     *  @param  string  $dir
     *  @return bool
     */
    private static function deleteDirectory(string $dir): bool
    {
        try {

            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::deleteDirectory("$dir/$file") : unlink("$dir/$file");
            }

            rmdir($dir);

            return true;

            // ...
        } catch (\Throwable $th) {

            report($th);

            return false;
        }
    }
}
