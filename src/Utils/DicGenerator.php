<?php

namespace CloudMyn\SpellChecker\Utils;

use function CloudMyn\SpellChecker\Helpers\getWordListDic;

class DicGenerator
{
    public static function generate()
    {
        $wordlists  =   getWordListDic();
        dd($wordlists);
    }
}
