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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('iso_code', 2)->unique();
            $table->string('currency', 10)->default('NGN');
            $table->string('phone_code', 10)->default('+234');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100)->unique()->index();
            $table->string('iso_code', 10)->nullable()->index();
            $table->string('zone', 50)->index();
            $table->longText('svg_path_d')->nullable();
            $table->integer('grid_col')->nullable();
            $table->integer('grid_row')->nullable();
            $table->decimal('center_latitude', 10, 7)->nullable();
            $table->decimal('center_longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('policy_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->integer('sequence_order')->index();
            $table->string('name', 100);
            $table->string('slug', 100)->index();
            $table->string('color_hex', 7);
            $table->string('text_color_hex', 7);
            $table->string('bg_color_hex', 7);
            $table->string('icon_class', 50);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('policy_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->unique()->constrained('regions')->cascadeOnDelete();
            $table->foreignId('policy_stage_id')->constrained('policy_stages')->cascadeOnDelete();
            $table->boolean('is_stalled')->default(false)->index();
            $table->boolean('is_expired')->default(false)->index();
            $table->text('stalled_reason')->nullable();
            $table->text('expired_reason')->nullable();
            $table->string('year_commenced', 50)->nullable();
            $table->date('data_verified_at')->nullable()->index();
            $table->text('source_notes')->nullable();
            $table->text('objectives_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('policy_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('policy_stage_id')->constrained('policy_stages')->cascadeOnDelete();
            $table->date('transitioned_date')->nullable();
            $table->text('change_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('slug')->unique()->index();
            $table->string('acronym', 50)->nullable()->index();
            $table->string('type', 50)->default('cso')->index();
            $table->string('website_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('funders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('slug')->unique()->index();
            $table->string('acronym', 50)->nullable()->index();
            $table->string('type', 50)->default('philanthropic')->index();
            $table->string('website_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('focus_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique()->index();
            $table->string('slug', 150)->unique()->index();
            $table->string('icon_class', 50)->default('fa-bullseye');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('region_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('role', 50)->default('lead')->index();
            $table->timestamps();

            $table->unique(['region_id', 'organization_id', 'role']);
        });

        Schema::create('region_funder', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('funder_id')->constrained('funders')->cascadeOnDelete();
            $table->string('grant_title')->nullable();
            $table->timestamps();

            $table->unique(['region_id', 'funder_id']);
        });

        Schema::create('region_focus_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('focus_area_id')->constrained('focus_areas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['region_id', 'focus_area_id']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('supporting_org_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('title');
            $table->string('document_type', 50)->default('ctc_enacted')->index();
            $table->integer('year')->nullable()->index();
            $table->string('file_path', 500);
            $table->string('file_name');
            $table->bigInteger('file_size_bytes')->default(0);
            $table->string('mime_type', 100)->default('application/pdf');
            $table->boolean('is_public')->default(true)->index();
            $table->bigInteger('download_count')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auditable_type')->index();
            $table->unsignedBigInteger('auditable_id')->index();
            $table->string('event', 50)->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('filename');
            $table->integer('total_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->json('error_log')->nullable();
            $table->string('status', 50)->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_batches');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('region_focus_area');
        Schema::dropIfExists('region_funder');
        Schema::dropIfExists('region_organization');
        Schema::dropIfExists('focus_areas');
        Schema::dropIfExists('funders');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('policy_status_histories');
        Schema::dropIfExists('policy_statuses');
        Schema::dropIfExists('policy_stages');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('countries');
    }
};
