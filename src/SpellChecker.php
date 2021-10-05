<?php

namespace CloudMyn\SpellChecker;

use CloudMyn\MetaSearch\Utils\Soundex;
use CloudMyn\SpellChecker\Exceptions\DictionaryException;

use function CloudMyn\SpellChecker\Helpers\getCustomDicPath;
use function CloudMyn\SpellChecker\Helpers\getDicDirectory;

class SpellChecker
{

    /**
     *  Metode untuk mengecek kata dalam bahasa indonesia
     *
     *  @param  string  $word
     *  @return array
     */
    public static function checkWordId(string $word)
    {
        $word   =   preg_replace("/[^A-Za-z]/", '', $word);

        if ($word === '' || is_null($word)) return '';

        $soundex        = Soundex::soundexId($word);
        $dic_directory  = getDicDirectory("id_ID.dic");

        return self::check($soundex, $word, $dic_directory);
    }

    /**
     *  Metode untuk mengecek kata dalam bahasa malaysia
     *
     *  @param  string  $word
     *  @return array
     */
    public static function checkWordMy(string $word)
    {
        $word   =   preg_replace("/[^A-Za-z]/", '', $word);

        if ($word === '' || is_null($word)) return '';

        $soundex        = Soundex::soundexId($word);
        $dic_directory  = getDicDirectory("my_MY.dic");

        return self::check($soundex, $word, $dic_directory);
    }

    /**
     *  Metode untuk mengecek kata dalam bahasa malaysia
     *
     *  @param  string  $word
     *  @return array
     */
    public static function checkWordEn(string $word)
    {
        $word   =   preg_replace("/[^A-Za-z]/", '', $word);

        if ($word === '' || is_null($word)) return '';

        $soundex        = Soundex::soundexId($word);
        $dic_directory  = getDicDirectory("en_US.dic");

        return self::check($soundex, $word, $dic_directory);
    }

    /**
     *  Fungsi untuk mengecek pengucapan suatu kata
     *
     *  @param  string  $lang
     *  @param  string  $word
     */
    protected static function check(string $soundex, string $word, string $file_dic)
    {
        if (!file_exists($file_dic))
            throw new DictionaryException("File Dictionary not found! at : $file_dic");


        $matches        =   [];
        $suggestions    =   [];

        self::searchInDic($file_dic, $word, $soundex, $matches, $suggestions);

        self::searchInCustomDic($word, $soundex, $matches, $suggestions);

        $data['matches']        =   $matches;
        $data['suggestions']    =   $suggestions;
        $data['typo']           =   count($matches) === 0 ? true : false;

        return $data;
    }

    /**
     *  Metode untuk mencari kata dalam kamus dasar
     *
     *  @param  string  $file_dic
     *  @param  string  $word
     *  @param  string  $soundex
     *  @param  array   $matches
     *  @param  array   $suggestions
     *
     *  @return bool
     */
    private static function searchInDic(string $file_dic, string $word, string $soundex, &$matches, &$suggestions): bool
    {
        $file = fopen($file_dic, 'r');

        if (!$file) return false;

        self::scanLine($file, $word, $soundex, $matches, $suggestions);

        fclose($file);

        return true;
    }


    /**
     *  Metode untuk mencari kata dalam kamus kostum
     *
     *  @param  string  $word
     *  @param  string  $soundex
     *  @param  array   $matches
     *  @param  array   $suggestions
     *
     *  @return bool
     */
    private static function searchInCustomDic(string $word, string $soundex, array &$matches, array &$suggestions): bool
    {
        $custom_dic = getCustomDicPath();

        $file = fopen($custom_dic, 'r');

        if (!$file) return false;

        self::scanLine($file, $word, $soundex, $matches, $suggestions);

        fclose($file);

        return true;
    }

    /**
     *  Metode untuk men-scan baris didalam kamus
     *
     *  @param  resource    $file
     *  @param  string      $word
     *  @param  string      $soundex
     *  @param  array       $matches
     *  @param  array       $suggestions
     *
     *  @return void
     */
    private static function scanLine($file, string $word, string $soundex, &$matches, &$suggestions)
    {
        $match_percentage = config('spellchecker.match_percentage', 95);
        $sggest_percentage = config('spellchecker.suggest_percentage', 75);

        while (!feof($file)) {

            try {

                $line   =   fgets($file);

                $line   =   trim($line);

                if ($line === "" || is_null($line)) continue;

                $line   =   explode('::', $line);

                $actual_word    =   count($line) >= 1 ? $line[0] : null;
                $mtaphon_word   =   count($line) >= 2 ? $line[1] : null;
                $soundex_word   =   count($line) >= 3 ? $line[2] : null;

                if (is_null($actual_word) && is_null($mtaphon_word) && is_null($soundex_word))
                    continue;

                if ($soundex_word === $soundex) {
                    similar_text($actual_word, $word, $pecent);
                    if ($pecent >= $match_percentage) $matches[] = $actual_word;
                    else if ($pecent >= $sggest_percentage) $suggestions[] = $actual_word;
                }

                // ...
            } catch (\Throwable $th) {

                report($th);
            }

            // ...
        }
    }
}
