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
        Schema::create('rds_databases', function (Blueprint $table) {
            $table->id();
            $table->string('db_instance_identifier')->unique();
            $table->string('db_instance_arn')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('engine');
            $table->string('db_instance_class');
            $table->string('db_name')->nullable();
            $table->string('master_username');
            $table->string('master_password_encrypted');
            $table->integer('allocated_storage');
            $table->string('storage_type')->default('gp2');
            $table->boolean('storage_encrypted')->default(false);
            $table->boolean('publicly_accessible')->default(false);
            $table->string('vpc_security_group')->nullable();
            $table->foreign('vpc_security_group')->references('group_id')->on('security_groups')->nullOnDelete();
            $table->string('availability_zone')->nullable();
            $table->boolean('multi_az')->default(false);
            $table->integer('backup_retention_period')->default(7);
            $table->timestamp('instance_create_time')->nullable();
            $table->string('status')->default('pending');
            
            //those fields are available when status becomes 'available'
            $table->string('engine_version')->nullable();
            $table->string('endpoint_address')->nullable();
            $table->integer('endpoint_port')->nullable();
            $table->string('endpoint_hosted_zone_id')->nullable();
            
            //this field will be available after first backup
            $table->timestamp('latest_restorable_time')->nullable();
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rds_databases');
    }
};
