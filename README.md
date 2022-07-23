# SpellChecker

Spelchecker merupakan library untuk mengecek kesalah ketik(typo) dalam sebuah kata/kalimat

## Usage

untuk menginstall dan setup package ini silahkan jalankan comand dibawah ini

    php artisan spellchecker:install
    
 setelah meng-install silahkan masukan table dari database untuk membuat kamus kata
 dengan berdasarkan kata yang ada di dalam table yang di sertakan
 
 ```PHP
    'tables'    =>  [
        'users' => ['name'],
        'posts' => ['name', 'desc'],
    ],
 ```

untuk men-generasikan kamus dasar silahkan jalankan comand dibawah ini

    php artisan spellchecker:generate
