<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $fillable = [ 'meta_key', 'meta_value' ];
    
    public function user()
    {
    	$this->belongsTo(User::class);
    }

    public function getValue()
    {
    	$meta = $this->meta_value;
    	if( is_json($meta) ){
    		return json_decode($meta);
    	}
    	return $meta;
    }
}
