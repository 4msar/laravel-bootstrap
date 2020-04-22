<?php

/*
|--------------------------------------------------------------------------
| Themes Storage Path
|--------------------------------------------------------------------------
|
*/
$theme_path = resource_path('themes');

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),         // register default path
        $theme_path,                    // register themes path
    ],
    /*
    |--------------------------------------------------------------------------
    | Theme View Storage Paths + Assets Path
    |--------------------------------------------------------------------------
    |
    | This is the main path where themes will be load
    | when a theme is active then application check the theme file first 
    | if the file is not found then it will load the default file.
    | When the theme will be active then assets will be copy to the public path.
    |
    */

    'theme_path' => $theme_path,    // get the themes path
    'theme_assets_path' => public_path('themes'),    // get the themes assets path

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
