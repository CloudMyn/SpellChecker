<?php

namespace CloudMyn\SpellChecker\Helpers;


if (function_exists('CloudMyn\SpellChecker\Helpers\getWordListDic') === false) {
    function getWordListDic(): string
    {
        return  __DIR__ . "/../../wordlist/";
    }
}
