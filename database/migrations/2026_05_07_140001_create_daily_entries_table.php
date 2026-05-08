<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sleep_quality')->nullable();
            $table->unsignedTinyInteger('stress_level')->nullable();
            $table->boolean('medication_taken')->default(false);
            $table->string('activity_level')->nullable();
            $table->date('entry_date')->default(now()->toDateString());
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_entries');
    }
};
