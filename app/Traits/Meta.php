<?php 
namespace App\Traits;

use Exception;

/**
 * Meta Trait
 * 
 * @package MyTonic
 * @author Saiful Alam
 * @version 1.0.0
 */
trait Meta
{
	/**
	 * Get the meta value by key
	 * 
	 * @param string $key
	 * @param string|mixed $fallback
	 * @param boolean $toArray if json
	 * @return string|mixed 
	 */
    public function getMeta($key, $fallback = null, $toArray = false)
    {
    	$this->checkMetaMethodIsDefinedOrNot();
        $meta = $this->meta->where('meta_key', $key)->first();
        if( $meta ){
            return $this->filterMetaValue($meta->meta_value) ?? $fallback;
        }
        return $fallback;
    }

	/**
	 * Check the meta is available or not
	 * 
	 * @param string $key
	 * @return boolean 
	 */
    public function hasMeta($name)
    {
    	$this->checkMetaMethodIsDefinedOrNot();
        return $this->meta->where('meta_key', $name)->count() ? true : false;
    }

	/**
	 * Remove meta from meta table
	 * 
	 * @param string|array $name
	 * @return boolean
	 */
    public function removeMeta($name)
    {
    	$this->checkMetaMethodIsDefinedOrNot();
        $metaKeys = is_array($name) ? $name : [$name];
        return $this->meta()->whereIn('meta_key', $metaKeys)->delete();
    }

	/**
	 * Set the meta value by key
	 * 
	 * @param string|array $name
	 * @param string|mixed $value
	 * @return string|mixed 
	 */
    public function setMeta($name, $value = null, $extra = [])
    {
    	$this->checkMetaMethodIsDefinedOrNot();
        if( is_array($value) ){
            $value = json_encode($value);
        }
        $mergedValues = array_merge(['meta_value' => $value], $extra);
        $meta = $this->meta()
    			->updateOrCreate([
                    'meta_key'=> $name
                ], $mergedValues);
        if( is_array($value) ){
            return json_decode($meta->meta_value, true);
        }
        return $meta->meta_value ?? $value;
    }

    /**
     * Get the all meta by their key => value
     * 
     * @return array|mixed 
     */
    public function getSerializedMeta()
    {
        $data = [];
        foreach ($this->meta as $item) {
            $data[$item->meta_key] = $this->filterMetaValue($item->meta_value);
        }
        return $data;
    }

    /**
     * Check is the meta method is declared or not
     * 
     * @return boolean|exception
     */
    public function checkMetaMethodIsDefinedOrNot()
    {
    	if( method_exists($this, 'meta') ){
    		return true;
    	}else{
    		debug(new Exception("meta() method is not defined!"));
    	}
    }

    /**
     * Get Meta Value after filter
     * 
     * @param string $meta_value
     * @return string|boolean|mixed
     */
    public function filterMetaValue($value)
    {
        if( is_null($value) ) return $value;
        $bool = filter_var($value, 
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE);
        if( $bool === null ){
            $toJson = json_decode($value, 1);
            if(json_last_error() === JSON_ERROR_NONE && is_array($toJson)){
                return array_map(function($item){ 
                    return is_string($item) ? trim($item) : $item; 
                }, $toJson);
            }else{
                return $value;
            }
        }
        return $bool;
    }
}