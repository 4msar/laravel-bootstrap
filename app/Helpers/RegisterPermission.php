<?php
namespace App\Helpers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Authorizable;

class RegisterPermission {
	public $gate;
	public function __construct(Gate $gate)
	{
		$this->gate = $gate;
	}

	public function register() : bool
	{
		$this->gate->before(function (Authorizable $user, string $ability) {
			if( $user->isRole('master') ){ return true; }
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }
            return false;
        });
        return true;
	}
}