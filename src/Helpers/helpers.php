<?php

namespace CloudMyn\SpellChecker\Helpers;


if (function_exists('CloudMyn\SpellChecker\Helpers\getWordListDic') === false) {
    function getWordListDic(): string
    {
        $s = DIRECTORY_SEPARATOR;
        return  __DIR__ . "{$s}..{$s}..{$s}wordlist{$s}";
    }
}

if (function_exists('CloudMyn\SpellChecker\Helpers\getDicDirectory') === false) {
    function getDicDirectory(?string $file_name = null): string
    {
        $s = DIRECTORY_SEPARATOR;
        return  __DIR__ . "{$s}..{$s}..{$s}dic{$s}{$file_name}";
    }
}


if (function_exists('CloudMyn\SpellChecker\Helpers\strContains') === false) {
    function strContains(string $find, string $word): bool
    {
        $result = preg_match("/{$find}/", $word);
        return $result >= 1 ? true : false;
    }
}
