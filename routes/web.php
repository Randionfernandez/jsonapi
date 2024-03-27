<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

// CURIOSIDAD: diferencias entre el hash crc32 y el crc32b usado como parte del cálculo del plainTextToken de Sanctum
Route::get('crc', function(){
    echo "crc32: " . hash("crc32", __FILE__) . "<br/>";
    echo 'crc32b:' . hash("crc32b", __FILE__);
});

