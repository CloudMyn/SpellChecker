<?php

namespace CloudMyn\SpellChecker\Helpers;


if (function_exists('CloudMyn\SpellChecker\Helpers\getDicDirectory') === false) {
    function getDicDirectory(?string $file_name = null): string
    {
        $s = DIRECTORY_SEPARATOR;
        return  storage_path("spellchecker{$s}dictionaries{$s}{$file_name}");
    }
}


if (function_exists('CloudMyn\SpellChecker\Helpers\getCustomDicPath') === false) {

    /**
     *  Mendapatkan custom dictionary path
     *  jika file tidak ada maka akan dibuat
     *
     *  @return string
     */
    function getCustomDicPath(): string
    {
        $s = DIRECTORY_SEPARATOR;

        $path =  storage_path("{$s}spellchecker{$s}dictionaries{$s}custom_generated.dic");

        if (!file_exists($path)) file_put_contents($path, '');

        return $path;
    }
}


if (function_exists('CloudMyn\SpellChecker\Helpers\strContains') === false) {
    function strContains(string $find, string $word): bool
    {
        $result = preg_match("/{$find}/", $word);
        return $result >= 1 ? true : false;
    }
}

if (function_exists('CloudMyn\SpellChecker\Helpers\arrayContains') === false) {
    function arrayContains(string $word, array $words): bool
    {
        $result = array_search($word, $words);

        return $result === false ? false : true;
    }
}
