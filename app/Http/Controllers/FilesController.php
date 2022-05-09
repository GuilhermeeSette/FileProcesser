<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use Illuminate\Http\Request;
use App\Jobs\ProcessFile;

class FilesController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


	public function uploadFile(UploadFileRequest $request){
		ProcessFile::dispatch($request->file)->onQueue('filesToProcess');
	}
}
