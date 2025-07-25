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
        Schema::create('memory_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('souvenirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('memory_type_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('memory_points')->default(0);
            $table->timestamps();
        });

        Schema::create('souvenir_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('souvenir_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pseudo')->nullable();
            $table->string('role')->default('member');
            $table->timestamp('joined_at')->useCurrent();
            $table->boolean('can_edit')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'souvenir_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memory_types');
        Schema::dropIfExists('souvenir');
        Schema::dropIfExists('souvenir_users');
    }
};
