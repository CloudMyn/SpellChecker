<?php

namespace CloudMyn\SpellChecker\Utils;

use CloudMyn\MetaSearch\Utils\Soundex;
use CloudMyn\SpellChecker\Exceptions\DictionaryException;

use function CloudMyn\SpellChecker\Helpers\getDicDirectory;
use function CloudMyn\SpellChecker\Helpers\getWordListDic;

class Dictionary
{
    public static function generate()
    {
        self::createDicFolder();

        $files  =   self::getRawDic();
        $dic_d  =   getDicDirectory();

        if (file_exists($dic_d)) {
            self::recurseRmdir($dic_d);
        }

        foreach ($files as $file_name) {

            $file_path_raw_dic  =   getWordListDic() . $file_name;

            $file   =   fopen($file_path_raw_dic, 'r');

            if (!$file) continue;

            self::readFile($file, $file_name, $dic_d);

            fclose($file);
        }
    }

    /**
     *  Metode untuk menghapus direktory recursive
     *
     *  @param  string  $di
     */
    private static function recurseRmdir(string $dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }

        rmdir($dir);
    }

    private static function readFile($file, string $file_name, string $dic_d)
    {
        $lang_dic   =   explode(".", $file_name);
        $lang_dic   =   is_array($lang_dic) ? $lang_dic[0] : $lang_dic;

        // X:..\CloudMyn\SpellChecker\src\Helpers\..\..\dic\
        $file_path  =   "{$dic_d}{$lang_dic}";

        while (!feof($file)) {

            $line   =   fgets($file);

            // make sure the line its not emptied
            if ($line === "" or is_null($line)) continue;

            // create xsmple: X:..\CloudMyn\SpellChecker\src\Helpers\..\..\dic\en_US
            self::createDicFolder($lang_dic);

            // store the encoded word to new directory
            self::storeDictionary($file_path, $line, $lang_dic);
        }
    }

    /**
     *  Metode untuk menyimpan kamus yang telah di encode ke dalam file direktori
     *
     *  @param  string  $file_path
     *  @param  string  $f_line
     *  @param  string  $lang_dic
     */
    private static function storeDictionary(string $file_path, string $f_line, string $lang_dic)
    {
        // remove any non alphabet character from the string
        $word   =   preg_replace("/[^A-Za-z]/", '', $f_line);

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

        $word   =   [$word, $metaphone, $soundex];
        $word   =   join("::", $word);

        // 'Letter' => 'l' this wll be a prefix of the filename
        $first_letter   =   strtolower($word[0]);
        $file_name      =   "{$first_letter}_$lang_dic.dic";

        // produce something like this x:{$file_path}\dic\en_US\a_en_us.dic
        $file_path      =   $file_path . DIRECTORY_SEPARATOR . $file_name;

        $file = fopen($file_path, 'a');

        fwrite($file, $word . PHP_EOL);

        fclose($file);
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
     *  Metode untuk mendapatkan raw data dari kamus
     *
     *  @return array
     */
    public static function getRawDic(): array
    {
        $wordlists  =   getWordListDic();

        if (!file_exists($wordlists))
            throw new DictionaryException("Dictionaries directory doesnt exists!");

        $directories    =   scandir($wordlists);
        $files  =   [];

        foreach ($directories as $fs) {
            $fpath  =   $wordlists .  $fs;
            if (file_exists($fpath) && is_file($fpath)) $files[] = $fs;
        }

        return $files;
    }
}
