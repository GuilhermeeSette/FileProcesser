<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Client extends Model
{
    protected $fillable = [
		"name",
		"address",
		"checked",
		"description",
		"interest",
		"date_of_birth",
		"email",
		"account",
		"created_at",
		"updated_at"
	];

	public function credit_cards()
    {
        return $this->hasMany('App\CreditCard');
    }

}
