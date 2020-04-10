<?php

return [
    'models_namespace' => 'App\\',

    'temp_path' => 'temp/',

    'uploader_route' => 'upload-media',

    'uploader_middlewares' => ['api'],

    'clean_temp' => 60, // this is the max time for the temp files (in minutes)
];
