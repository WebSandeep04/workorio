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
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('employment_type');
            $table->string('blood_group')->nullable()->after('date_of_birth');
            $table->string('marital_status')->nullable()->after('blood_group');
            $table->string('personal_email')->nullable()->after('email');
            $table->string('spouse_name')->nullable()->after('marital_status');
            $table->unsignedInteger('number_of_dependents')->nullable()->after('spouse_name');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_name');

            $table->string('passport_number')->nullable()->after('personal_email');
            $table->date('passport_expiry')->nullable()->after('passport_number');
            $table->string('aadhaar_number')->nullable()->after('passport_expiry');
            $table->string('pan_number')->nullable()->after('aadhaar_number');

            $table->string('highest_qualification')->nullable()->after('employment_type');
            $table->string('institution_name')->nullable()->after('highest_qualification');
            $table->string('field_of_study')->nullable()->after('institution_name');
            $table->string('graduation_year')->nullable()->after('field_of_study');
            $table->string('grade')->nullable()->after('graduation_year');

            $table->string('previous_employer')->nullable()->after('grade');
            $table->string('previous_job_title')->nullable()->after('previous_employer');
            $table->decimal('experience_years', 5, 2)->nullable()->after('previous_job_title');
            $table->text('skills')->nullable()->after('experience_years');

            $table->string('bank_name')->nullable()->after('work_location');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('ifsc_code')->nullable()->after('bank_account_number');
            $table->string('uan_number')->nullable()->after('ifsc_code');
            $table->string('pf_number')->nullable()->after('uan_number');
            $table->string('esi_number')->nullable()->after('pf_number');

            $table->string('insurance_provider')->nullable()->after('esi_number');
            $table->string('insurance_policy_number')->nullable()->after('insurance_provider');
            $table->date('insurance_valid_till')->nullable()->after('insurance_policy_number');
            $table->text('medical_conditions')->nullable()->after('insurance_valid_till');
            $table->text('allergies')->nullable()->after('medical_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'blood_group',
                'marital_status',
                'personal_email',
                'spouse_name',
                'number_of_dependents',
                'emergency_contact_relation',
                'passport_number',
                'passport_expiry',
                'aadhaar_number',
                'pan_number',
                'highest_qualification',
                'institution_name',
                'field_of_study',
                'graduation_year',
                'grade',
                'previous_employer',
                'previous_job_title',
                'experience_years',
                'skills',
                'bank_name',
                'bank_account_number',
                'ifsc_code',
                'uan_number',
                'pf_number',
                'esi_number',
                'insurance_provider',
                'insurance_policy_number',
                'insurance_valid_till',
                'medical_conditions',
                'allergies',
            ]);
        });
    }
};
