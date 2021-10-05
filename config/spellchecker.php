<?php

use CloudMyn\MetaSearch\Utils\Soundex;

return  [

    /**
     *  persentasi kata yang dianggap sesuai dengan kata masukan
     *
     *  jika kecocokan kata melebihi 95%, bisa dipastikan kata
     *  yang dimasukkan itu cocok dengan kata yang didapatkan
     */
    'match_percentage'      =>  95,

    /**
     *  Persentasi kata yang dianggap sebagai saran dari kata masukkan
     *
     *  jika kecocokan kata melebihi 75%, kita bisa memasukanya sebagai saran
     */
    'suggest_percentage'    =>  75,

    /**
     *  Path to the custom dictionary file

     *  untuk aturan penulisan anda harus mengikuti format dibwah ini
     *  ex:
     *  -   abjad
     *  -   adaptasi
     *  -   bacot
     *  setiap kosakata harus dimulia dengan garis baru dan kata
     *  tidak boleh mengandung karakter selain alpabet
     */
    'custom_dic'  =>  storage_path('spellchecker_custom.dic'),

];
