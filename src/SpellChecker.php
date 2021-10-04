<?php

namespace CloudMyn\SpellChecker;

use CloudMyn\MetaSearch\Utils\Soundex;
use CloudMyn\SpellChecker\Exceptions\DictionaryException;

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

        $first_char = $word[0];

        $soundex        = Soundex::soundexId($word);
        $dic_directory  = getDicDirectory("id_ID" . DIRECTORY_SEPARATOR . "{$first_char}_id_ID.dic");

        return self::checkWord($soundex, $word, $dic_directory);
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

        $first_char = $word[0];

        $soundex        = Soundex::soundexId($word);
        $dic_directory  = getDicDirectory("my_MY" . DIRECTORY_SEPARATOR . "{$first_char}_my_MY.dic");

        return self::checkWord($soundex, $word, $dic_directory);
    }


    public static function checkWordEn(string $word)
    {
        $word   =   preg_replace("/[^A-Za-z]/", '', $word);

        if ($word === '' || is_null($word)) return '';

        $first_char = $word[0];

        $soundex        = soundex($word);
        $dic_directory  = getDicDirectory("en_US" . DIRECTORY_SEPARATOR . "{$first_char}_en_US.dic");

        return self::checkWord($soundex, $word, $dic_directory);
    }

    /**
     *  Fungsi untuk mengecek pengucapan suatu kata
     *
     *  @param  string  $lang
     *  @param  string  $word
     */
    protected static function checkWord(string $soundex, string $word, string $file_dic)
    {
        if (!file_exists($file_dic))
            throw new DictionaryException("File Dictionary not found! at : $file_dic");

        $match_percentage = config('spellchecker.match_percentage', 95);
        $sggest_percentage = config('spellchecker.suggest_percentage', 75);

        $matches        =   [];
        $suggestions    =   [];

        $file = fopen($file_dic, 'r');

        if (!$file) return;

        while (!feof($file)) {

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
        }

        fclose($file);

        self::findCostumesWord($word, $soundex, $matches, $suggestions);

        $data['matches']        =   $matches;
        $data['suggestions']    =   $suggestions;
        $data['typo']           =   count($matches) === 0 ? true : false;

        return $data;
    }

    private static function findCostumesWord(string $word, string $soundex, array &$matches, array &$suggestions)
    {
        // find in costumes dic
        $costumes = config('spellchecker.costumes', []);

        $match_percentage = config('spellchecker.match_percentage', 95);
        $sggest_percentage = config('spellchecker.suggest_percentage', 75);

        foreach ($costumes as $costume) {
            $c_actual_word    =   count($costume) >= 1 ? $costume[0] : null;
            $c_metaphon_word  =   count($costume) >= 2 ? $costume[1] : null;
            $c_soundex_word   =   count($costume) >= 3 ? $costume[2] : null;

            if ($c_soundex_word === $soundex) {
                similar_text($c_actual_word, $word, $c_percent);
                if ($c_percent >= $match_percentage) $matches[] = $c_actual_word;
                else if ($c_percent >= $sggest_percentage) $suggestions[] = $c_actual_word;
            }
        }
    }
}
