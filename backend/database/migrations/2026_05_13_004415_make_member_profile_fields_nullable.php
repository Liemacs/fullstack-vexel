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
        Schema::table('members', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
            $table->string('role')->nullable()->change();
            $table->string('specialization')->nullable()->change();
            $table->text('bio')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('status')->nullable(false)->change();
            $table->string('role')->nullable(false)->change();
            $table->string('specialization')->nullable(false)->change();
            $table->text('bio')->nullable(false)->change();
        });
    }
};
