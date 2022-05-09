<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $fillable = [
		"person_id",
		"type",
		"number",
		"name",
		"expirationDate"
	];

	public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
