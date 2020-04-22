<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	/*
	 * Disabled the timestamps column
	 */
	public $timestamps = false;
	
	/*
	 * The attributes that are mass assignable.
     *
     * @var array
	 */
    protected $fillable = ['option_name', 'option_value'];

    /**
     * Get Setting data
     * @param string $key The Settings key name
     * @param mixed $fallback Fallback data
     * @param boolean $toArray If json data containes then return as array or not
     * @return mixed array|string
     */
    public static function getValue($key, $fallback = null, $toArray = false)
    {
    	$setting = Setting::where( 'option_name', $key)->first();
        if( !$setting ){ return $fallback; }
        if( is_json($setting->option_value) ){
            return json_decode($setting->option_value, $toArray) ?? $fallback;;
        }
    	return filterValue($setting->option_value) ?? $fallback;
    }

    /**
     * Set Setting data
     * @param string $key The Settings key name
     * @param mixed $value Store data
     * 
     * @return mixed array|string
     */
    public static function setValue($key, $value = null)
    {
        if( is_array($value) ){
            $value = json_encode($value);
        }
        try {
            $setting = Setting::updateOrCreate([
                'option_name' => $key 
            ],[
                'option_value' => $value ?? null
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
