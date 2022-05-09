<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use App\Client;
use App\CreditCard;

class ProcessClientsBatch implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $batch;
	protected $timestamp;

	const MIN_ACCEPTED_AGE = 18;
	const MAX_ACCEPTED_AGE = 65;

    public function __construct($data)
    {
		$this->batch = $data['batch'];
		$this->timestamp = $data['timestamp'];
    }

    public function handle()
    {
		foreach($this->batch as $key => $item){
			$birthDate = $this->formatDate($item->date_of_birth);
			if($this->checkAcceptedAge($birthDate)){
				if($this->verifyIfAlreadyProcessed($key, $item->timestamp)){
					continue;
				}else{
					$client = new Client;
					$client->insert_key = $key.'_'.$this->timestamp;
					$client->name = $item->name;
					$client->address = $item->address;
					$client->checked = $item->checked;
					$client->description = $item->description;
					$client->interest = $item->interest;
					$client->date_of_birth = !empty($birthDate) ? date('Y-m-d',strtotime($birthDate)) : null;
					$client->email = $item->email;
					$client->account = $item->account;
					$client->save();
					$creditcard = new Creditcard;
					$creditcard->type = $item->credit_card->type;
					$creditcard->number = $item->credit_card->number;
					$creditcard->name = $item->credit_card->name;
					$creditcard->expirationDate = $item->credit_card->expirationDate;
					$client->credit_cards()->save($creditcard);
				}
			}
		}
	}

	private function verifyIfAlreadyProcessed(string $key, string $timestamp)
    {
        return Client::where('insert_key', $key.'_'.$this->timestamp)->first()->exists();
    }

    private function checkAcceptedAge(?string $date){
        if($date == null || empty($date)) return true;

        $age = Carbon::parse($date)->age;
        if($age >= ProcessClientsBatch::MIN_ACCEPTED_AGE && $age <= ProcessClientsBatch::MAX_ACCEPTED_AGE){
            return true;
        }
        return false;
    }

	private function formatDate(?string $date){

		if(!isset($date) || empty($date)){
			return null;
		}
		$date = substr($date, 0, 10);
		if(strpos($date,'/') !== false){
			return Carbon::createFromFormat('d/m/Y', $date);
		}
		return Carbon::createFromFormat('Y-m-d', $date);
	}


}
