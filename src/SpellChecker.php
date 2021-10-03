<?php

namespace CloudMyn\SpellChecker;

use CloudMyn\MetaSearch\Utils\Soundex;

class SpellChecker
{
    public function checkWord(string $word)
    {
        Soundex::soundexId($word);
    }
}
