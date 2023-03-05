<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          
            Schema::create('email_validation', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
        $table->string('Token');
        $table->timestamp('created_at')->nullable();
    
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_validation');
        
    }
};
