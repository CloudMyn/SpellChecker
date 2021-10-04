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
     *  Daftar kamus bahasa yang tersedia
     */
    'dictionaries'   =>  ['en_US', 'id_ID', 'my_MY'],

    /**
     *  Array ini memunkinkan anda menambahkan kata baru
     *  ke dalam mesin pengecekan
     *
     *  struktur kata yang di masukkan harus memiliki format dibwh ini
     *  'kata::metaphone::soundex'
     */
    'costumes'  =>  [
        [
            'pukis',
            metaphone('pukis'),
            Soundex::soundexId('pukis'),
        ],
        [
            'kultivantod',
            metaphone('kultivantod'),
            Soundex::soundexId('kultivantod'),
        ],
    ],

];
