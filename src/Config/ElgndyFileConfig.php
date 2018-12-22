<?php 

return [
	
    /*
     * Default Path in FS
     */
    'path' => base_path('public') . '/temp',


    /*
     * Defult Storage 
     */
    'disk' => 'local',


    /*
     * Resizing Option
     * 
     * If you set it true you will need to config resize options
     *
     * look below
     * Bool
     */
    'enable_resize' => 1,


    /*
     * Sizes
     * Array of Models Name
     *
     * Every Model has own Sizes
     *
     * Size is Just string  
     * Width , Height
     *
     * Every file for this model will resized to it's own sizes 
     */
    'sizes' => [
	'users' => [
	    '15,15',
	    '70,100',
	],
    ],


    /*
     * Will use in case you save file and it's model not exist in sizes \^\
     * And 
     * enable_resize = 1
     */
    'default_sizes' => ['100,120'],


    /*
     * Max File size in KB 
     */
    'max_file_size' => 500000,


    /*
     * File extentions 
     */
    'extensions' => ['png', 'jpg' , 'jpg'],

    /*
     * cloud url
     */
    'cloud_url' => url('storage/'),

    /*
     * local url
     */
    'local_url' => url('temp/'),


];
