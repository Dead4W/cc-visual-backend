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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('web_ide_session_key', 64)->unique();
            $table->string('cc_session_key', 64)->unique();
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->index('web_ide_session_key', 'index_web_ide_session_key');
            $table->index('cc_session_key', 'index_cc_session_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
