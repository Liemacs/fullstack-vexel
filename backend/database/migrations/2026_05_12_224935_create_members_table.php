<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('call_sign')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('status');
            $table->string('role');
            $table->string('specialization');
            $table->string('image')->nullable();
            $table->text('quote')->nullable();
            $table->text('bio');
            $table->json('skills')->default('[]');
            $table->json('gear')->default('[]');
            $table->text('character')->nullable();
            $table->text('vexel_history')->nullable();
            $table->json('connections')->default('[]');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
