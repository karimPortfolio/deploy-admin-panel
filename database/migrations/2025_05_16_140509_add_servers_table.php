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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('instance_id')->nullable();
            $table->string('image_id')->nullable();
            $table->string('name');
            $table->string('instance_type');
            $table->string('status')->nullable();
            $table->string('private_ip_address');
            $table->string('public_ip_address');
            $table->foreignId('ssh_key_id')->nullable()->constrained();
            $table->foreignId('security_group_id')->nullable()->constrained('security_groups');
            $table->string('vpc_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
