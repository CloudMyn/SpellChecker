<?php

namespace CloudMyn\SpellChecker;

use CloudMyn\SpellChecker\Exceptions\DictionaryException;
use CloudMyn\SpellChecker\Utils\Soundex;

use function CloudMyn\SpellChecker\Helpers\arrayContains;
use function CloudMyn\SpellChecker\Helpers\getDicDirectory;

class SpellChecker
{

    public static function checkSentence($sentence)
    {
        $sentence = preg_replace("/[^A-Za-z\s]/", '', $sentence);

        $words  =   explode(' ', $sentence);

        $sentences = [];

        $placeholder = $sentence;

        $res = self::checkWord($words);

        $top_match = [];

        $low_match = [];

        // get top match

        // sort array by its value

        foreach ($res['suggestion'] as $sgk => $sgv) {

            usort($sgv, function ($a, $b) {
                $rdiff = $b['match_percent'] - $a['match_percent'];
                if ($rdiff) return $rdiff;
                return $b['call_freq'] - $a['call_freq'];
            });

            $top_match[$sgk] = array_shift($sgv)['word'];

            foreach ($sgv as $data) {
                $low_match[$data['word']] = $sgk;
            }
        }

        $f_sentence = $placeholder;

        foreach ($top_match as  $word => $sgg_word) {
            $f_sentence = str_replace($word, $sgg_word, $f_sentence);
        }

        foreach ($low_match as $sgg_word => $word) {
            $get_best_match = $top_match[$word];

            $sentences[] = str_replace($get_best_match, $sgg_word, $f_sentence);
        }

        return [
            'actual_sentence'   =>  $sentence,
            'top_match'         =>  $f_sentence,
            'suggested'         =>  $sentences,
            'is_typo'           =>  strtolower($sentence) !== strtolower($f_sentence),
        ];
    }

    /**
     *  Metode untuk mengecek kata dalam bahasa indonesia
     *
     *  @param  array|string  $word
     *  @return array
     */
    public static function checkWord($word)
    {
        $word   =   self::removeNonAlphabetChar($word);

        if ($word === '' || is_null($word)) return '';

        $dic_directory  = getDicDirectory(config('spellchecker.main_dic'));

        if (is_array($word)) {
            return self::check($word, $dic_directory);
        }

        return self::check([$word], $dic_directory);
    }

    /**
     *  Fungsi untuk mengecek pengucapan suatu kata
     *
     *  @param  string  $lang
     *  @param  array  $word
     */
    protected static function check(array $words, string $file_dic)
    {
        if (!file_exists($file_dic))
            throw new DictionaryException("File Dictionary not found! at : $file_dic");


        $matches        =   [];
        $suggestions    =   [];

        self::searchInDic($file_dic, $words, $matches, $suggestions);

        ksort($matches);

        $data['matches']        =   array_values($matches);
        $data['suggestion']     =   $suggestions;

        return $data;
    }

    /**
     *  Metode untuk mencari kata dalam kamus dasar
     *
     *  @param  string  $file_dic
     *  @param  array   $word
     *  @param  array   $matches
     *  @param  array   $suggestions
     *
     *  @return bool
     */
    private static function searchInDic(string $file_dic, array $words, &$matches, &$suggestions): bool
    {
        $file = fopen($file_dic, 'r');

        if (!$file) return false;

        self::scanLine($file, $words, $matches, $suggestions);

        fclose($file);

        return true;
    }

    /**
     *  Metode untuk men-scan baris didalam kamus
     *
     *  @param  resource    $file
     *  @param  array      $word
     *  @param  array       $matches
     *
     *  @return void
     */
    private static function scanLine($file, array $words, &$matches, &$found)
    {
        $match_percentage = config('spellchecker.match_percentage', 95);
        $sggest_percentage = config('spellchecker.suggest_percentage', 75);

        $soundex_words = [];
        $methapone_words = [];

        foreach ($words as $word) {
            $soundex_words[] = Soundex::soundexId($word);
            $methapone_words[] = metaphone($word);
        }

        while (!feof($file)) {

            try {

                $line   =   fgets($file);

                $line   =   trim($line);

                if ($line === "" || is_null($line)) continue;

                $line   =   explode('::', $line);

                $actual_word    =   count($line) >= 1 ? $line[0] : null;
                $mtaphon_word   =   count($line) >= 2 ? $line[1] : null;
                $soundex_word   =   count($line) >= 3 ? $line[2] : null;
                $call_freq      =   count($line) >= 4 ? $line[3] : null;

                if (is_null($actual_word) && is_null($mtaphon_word) && is_null($soundex_word))
                    continue;

                if (arrayContains($soundex_word, $soundex_words)) {

                    foreach ($words as $word) {

                        similar_text($actual_word, $word, $percent);

                        if ($percent >= $match_percentage) $matches[] = $actual_word;

                        if ($percent >= $sggest_percentage) {
                            $found[$word][] = [
                                'word'  =>  $actual_word,
                                'match_percent' => intval($percent),
                                'call_freq' => intval($call_freq),
                            ];

                            // krsort($suggestions);
                        };
                    }
                }

                // ...
            } catch (\Throwable $th) {

                report($th);
            }

            // ...
        }

        // dd($suggestions);
    }

    private static function removeNonAlphabetChar($words)
    {
        return preg_replace("/[^A-Za-z]/", '', $words);
    }
}
