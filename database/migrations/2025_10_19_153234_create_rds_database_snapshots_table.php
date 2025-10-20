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
        Schema::create('rds_database_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('snapshot_identifier')->unique();
            $table->string('snapshot_arn')->nullable();
            $table->foreignId('rds_database_id')->constrained('rds_databases')->onDelete('cascade');
            $table->string('snapshot_type', )->default('manual'); // manual, automated
            $table->string('status')->default('pending'); // creating, available, deleting, failed
            $table->integer('percent_progress')->default(0); // 0-100
            $table->boolean('encrypted')->default(false);
            $table->string('kms_key_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('snapshot_create_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rds_database_snapshots');
    }
};
