<?php

namespace CloudMyn\SpellChecker\Utils;

class Soundex
{

    public static function soundexId(string $word)
    {
        // Consonant phonetic for bahasa indonesia
        $characters =   [
            ['A', 'I', 'U', 'E', 'O', 'H',],
            ['F', 'V'],
            ['S', 'X', 'Z'],
            ['L'],
            ['R'],
            ['M', 'N'],
            ['B', 'D', 'P', 'T'],
            ['C', 'G', 'J', 'K', 'Q'],
            ['W', 'Y'],
        ];

        return self::soundexAlgo($characters, $word);
    }

    public static function soundexEn(string $word)
    {
        // Consonant phonetic for international english
        $characters =   [
            ['A', 'I', 'U', 'E', 'O', 'H', 'W', 'Y'],
            ['B', 'F', 'P', 'V'],
            ['C', 'G', 'J', 'K', 'Q', 'S', 'X', 'Z'],
            ['D', 'T'],
            ['L'],
            ['M', 'N'],
            ['R'],
        ];

        return self::soundexAlgo($characters, $word);
    }


    protected static function soundexAlgo(array $phonetic, string $word): string
    {
        $word   =   strtoupper($word);

        $word   =   preg_replace("/[^A-Za-z]/", '', $word);

        $word   =   self::removeCloseDuplicated($word);

        if ($word === null || $word === '') return '';

        $first_char =   $word[0];

        $word   =   substr_replace($word, '', 0, 1);

        $new_chars  =   [];
        $prev_val   =   '';

        for ($i = 0; $i < strlen($word); $i++) {
            foreach ($phonetic as $index => $character) {
                if (in_array($word[$i], $character) && $prev_val != $index && $index != 0 && count($new_chars) < 3) {
                    $new_chars[$i]  =   $index;
                    $prev_val   =   $index;
                }
                // Reset $prev_val jika index === '0' or vowel
                else if (in_array($word[$i], $character) && $index == '0' && count($new_chars) < 3) {
                    $prev_val = '';
                }
            }
        }

        array_unshift($new_chars, $first_char);

        while (count($new_chars) < 4) {
            $new_chars[]    =   '0';
        }

        return join('', $new_chars);
    }


    public static function removeCloseDuplicated(string $string): string
    {
        $arr_string     =   str_split($string);
        $prev_string    =   '';
        $new_string     =   '';

        foreach ($arr_string as $s) {
            if ($s !== $prev_string) {
                $new_string .=  $s;
                $prev_string =  $s;
            }
        }

        return $new_string;
    }
}
