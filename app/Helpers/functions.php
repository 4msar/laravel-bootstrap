<?php 

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


/* @function html_string()  @version v1.0  @since 1.0.0 */
if (!function_exists('html_string')) {
    function html_string($html_string)
    {
        return new HtmlString($html_string);
    }
}

/* @function has_route()  @version v1.0  @since 1.0.0 */
if (!function_exists('has_route')) {
    function has_route($route)
    {
        return Route::has($route);
    }
}

/* @function _date()  @version v1.0  @since 1.0 */
if (!function_exists('_date')) {
    function _date($date, $format = null)
    {
        $date = Carbon::parse($date);
        if(not_empty($format)){
            return $date->format($format);
        }
        return $date;
    }
}

/* @function is_json()  @version v1.0  @since 1.0 */
if (!function_exists('is_json')) {
    function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}


/* @function word_limit()  @version v.1.0  @since 1.0 */
if (!function_exists('word_limit')) {
    /*==== Return some words from a sentence ====*/
    function word_limit($sentence, $limit=10, $end='....'){
        if (!empty($sentence)){
            $words = explode(" ",$sentence);
            if( count($words) <= $limit ) return $sentence;
            $sentence = implode(" ",array_splice($words,0,$limit)) . $end;
        }
        return $sentence;
    }
}

/* @function has_file()  @version v.1.0  @since 1.0 */
if (!function_exists('has_file')) {
    function has_file($file) {
        if(file_exists($file) && is_file($file) && !is_dir($file) ){
            return true;
        }else{
            return false;
        }
    }
}

/* @function save_file()  @version v.1.0  @since 1.0 */
if (!function_exists('save_file')) {
    function save_file($file, $path = '', $name = null) {
        if( ! $file instanceof UploadedFile) return null;
        $ext = $file->extension();
        $file_name = if_null($name, str()->random(20)).'.'.$ext;
        $path = $file->storeAs($path, $file_name, 'public');
        return $path;
    }
}

/* @function delete_file()  @version v.1.0  @since 1.0 */
if (!function_exists('delete_file')) {
    function delete_file($file_path) {
        if( has_file($file_path) ){
            try {
                return unlink($file_path);
            } catch (\Exception $e) {
                info($e->getMessage());
            }
        }
    }
}

/* @function storage_public_path()  @version v.1.0  @since 1.0 */
if (!function_exists('storage_public_path')) {
    function storage_public_path($path = '') {
        return storage_path('app/public').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

/* @function storage_url()  @version v.1.0  @since 1.0 */
if (!function_exists('storage_url')) {
    function storage_url($file, $default = null) {
        if( has_file(storage_path('app/'.$file)) ){
            return Storage::get($file);
        }else{
            return $default;
        }
    }
}

/* @function public_file_url()  @version v.1.0  @since 1.0 */
if (!function_exists('public_file_url')) {
    function public_file_url($file, $default = null) {
        if( has_file(storage_path('app/public/'.$file)) ){
            return asset(Storage::url($file));
        }else{
            return $default;
        }
    }
}

/* @function str()  @version v1.0  @since 1.0.0 */
if (!function_exists('str')) {
    function str()
    {
        return new Str;
    }
}

/* @function debug()  @version v1.0  @since 1.0.0 */
if (!function_exists('debug')) {
    function debug($exception = null)
    {
        $is_debug = config('app.debug') && config('app.env') != 'production';
        if( $exception && $is_debug ){
            throw $exception;
        }
        return $is_debug;
    }
}

/* @function pipeToArray()  @version v1.0  @since 1.0.0 */
if (!function_exists('pipeToArray')) {
    function pipeToArray($string)
    {
        return explode('|', $string);
    }
}

/*================================================*/
/*=========== Settings Related Data ==============*/
/*================================================*/

/* @function site_dependency()  @version v1.0  @since 1.0.0 */
if( ! function_exists('settings') ){
    function settings($name, $fallback = null)
    {
        if( ! application_installed(true) ){ return $fallback; }

        if( is_array($name) && isset($name['key']) ){
            $setting = Setting::updateOrCreate([
                'option_name' => $name['key'] 
            ],[
                'option_value' => $name['value'] ?? null
            ]);
        }elseif(is_string($name)){
            $setting = Setting::where( 'option_name', $name)->first();
        }else{
            return $fallback;
        }
        return $setting->option_value ?? $fallback;
    }
}


/*================================================*/
/*============== Theme Related Data ==============*/
/*================================================*/
/* @function theme_view()  @version v1.0  @since 1.0.0 */
if (!function_exists('theme_view')) {
    function theme_view($view = null, $data = [], $mergeData = [])
    {
        return app('theme')->view($view, $data, $mergeData);
    }
}
/* @function theme_asset()  @version v1.0  @since 1.0.0 */
if (!function_exists('theme_asset')) {
    function theme_asset($path, $theme = false)
    {
        return app('theme')->assetUrl($path, $theme);
    }
}
/* @function theme_layout()  @version v1.0  @since 1.0.0 */
if (!function_exists('theme_layout')) {
    function theme_layout($name, $custom = false)
    {
        return app('theme')->layout($name, $custom);
    }
}
/* @function theme_blade()  @version v1.0  @since 1.0.0 */
if (!function_exists('theme_blade')) {
    function theme_blade($name = null)
    {
        return app('theme')->blade($name);
    }
}