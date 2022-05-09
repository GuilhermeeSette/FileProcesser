<?php


namespace Tests\Unit;


use App\Jobs\ProcessClientsBatch;
use Tests\TestCase;

class ProcessClientsBatchTest extends TestCase
{
    public function testShouldProcessAndCreateClients()
    {
        //Arrange
        $data = json_decode(file_get_contents(storage_path() . "resources/fileJson.json"));

        //Act
        ProcessClientsBatch::dispatch($data)->onQueue('filesToProcessBatch');

        //Assert
        $this->assertEquals(2, Client::all()->count());
    }
}
