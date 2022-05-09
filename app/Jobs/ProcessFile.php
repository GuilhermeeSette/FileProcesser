<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessClientsBatch;

class ProcessFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $file;
	protected $timestamp;

	/**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
		$arrFiles = Storage::files('public/clients');
		$timestamp = 0;
		$first_added_file = '';
		foreach ($arrFiles as $filename) {
			$time = Storage::lastModified($filename); //returns unix timestamp
			if($timestamp != 0 && $time > $timestamp){
				continue;
			}else{
				$first_added_file = $filename;
				$timestamp = $time;
			}
		}
		$this->timestamp = $timestamp;
		$this->file = $first_added_file;
    }

    public function handle()
    {
		$file_path = explode('.', $this->file);
		$extension = end($file_path);
		switch($extension){
			case "json":
				$fileContent = Storage::get($this->file);
				$this->createBatches(json_decode($fileContent));
				break;
			default:
		}

	}


	public function createBatches($objects){

		//TODO: Identify the type of the file and call the correct batch process file
		$batches = array_chunk($objects, env('CLIENTS_BATCH_SIZE'), 1);
		foreach($batches as $batch){
			$data = ['timestamp' => $this->timestamp, 'batch' => $batch];
			ProcessClientsBatch::dispatch($data)->onQueue('filesToProcessBatch');
		}
		$filePath = explode('/',$this->file);
		Storage::move($this->file, 'public/clients/processed/'.last($filePath));
	}

}
