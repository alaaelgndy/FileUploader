<?php

use Illuminate\Support\Facades\Route;

Route::group(
    ['namespace' => 'Elgndy\FileUploader\Controllers', 'middleware' => 'api'], function () {
        Route::post('upload-media', 'FileUploaderController@store');
        Route::post('move-media', 'FileUploaderController@move');
    }
);
