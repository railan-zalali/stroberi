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
    public function up(): void
    {
        // Create jobs table for queue processing
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index()->comment('Queue name');
            $table->longText('payload')->comment('Job data');
            $table->unsignedTinyInteger('attempts')->comment('Number of attempts');
            $table->unsignedInteger('reserved_at')->nullable()->comment('When job was reserved');
            $table->unsignedInteger('available_at')->comment('When job becomes available');
            $table->unsignedInteger('created_at')->comment('When job was created');
        });

        // Create job batches table for batch processing
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs')->comment('Total jobs in batch');
            $table->integer('pending_jobs')->comment('Pending jobs count');
            $table->integer('failed_jobs')->comment('Failed jobs count');
            $table->longText('failed_job_ids')->comment('IDs of failed jobs');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // Create failed jobs table for tracking failed jobs
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique()->comment('Unique identifier');
            $table->text('connection')->comment('Connection name');
            $table->text('queue')->comment('Queue name');
            $table->longText('payload')->comment('Job data');
            $table->longText('exception')->comment('Exception details');
            $table->timestamp('failed_at')->useCurrent()->comment('When job failed');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
