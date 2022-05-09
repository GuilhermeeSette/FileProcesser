<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('insert_key')->unique();
			$table->string('name');
			$table->string('address');
			$table->boolean('checked')->nullable();
			$table->text('description')->nullable();
			$table->string('interest')->nullable();
			$table->date('date_of_birth')->nullable();
			$table->string('email');
			$table->string('account');
			$table->timestamps();
			$table->index('insert_key');
		});
		
		Schema::create('credit_cards', function(Blueprint $table){
			$table->bigIncrements('id');
			$table->unsignedBigInteger('client_id');
			$table->string('type');
			$table->bigInteger('number');
			$table->string('name');
			$table->string('expirationDate');
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('credit_cards');
    }
}
