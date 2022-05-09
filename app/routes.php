<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'files',
    'middleware' => 'api'
], function () {
    Route::post('/', 'FilesController@files');
});
