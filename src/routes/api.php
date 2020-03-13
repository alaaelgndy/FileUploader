<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Elgndy\FileUploader\Controllers'], function () {
    Route::group(['middleware' => config('elgndy_media.uploader_middlewares', ['api'])], function () {
        Route::post(config('elgndy_media.uploader_route', 'upload-media'), 'FileUploaderController@store');
    });
});
