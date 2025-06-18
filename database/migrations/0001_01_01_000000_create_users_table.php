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
       Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('phone')->nullable(); // Added phone column
        $table->string('address')->nullable(); // Added address column
        $table->string('city')->nullable(); // Added city column
        $table->string('state')->nullable(); // Added state column
        $table->string('country')->nullable(); // Added country column
        $table->string('zip_code')->nullable(); // Added zip_code column
        $table->boolean('active')->default(true);
        $table->boolean('is_verified')->default(false);
        $table->string('avatar')->default('default-avatar.png'); // Default avatar image
        $table->string('user_type')->default(1); // Added user_type column
        $table->timestamp('email_verified_at')->nullable();
       
        $table->rememberToken();
        $table->timestamps();
        $table->softDeletes(); // 👈 Add this line

        $table->index('email');
        $table->index('active');
        $table->index('is_verified');

        });

        

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
