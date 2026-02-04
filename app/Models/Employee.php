<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\Place;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'phone',
        'designation',
        'department',
        'date_of_joining',
        'employment_type',
        'status',
        'work_location',
        'branch_id',
        'department_id',
        'designation_id',
        'employment_type_id',
        'shift_id',
        'state_id',
        'city_id',
        'country_id',
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
        'address_line',
        'city',
        'state',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'is_place_allowed',
        'is_tracking',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
        'insurance_valid_till' => 'date',
        'experience_years' => 'decimal:2',
        'is_place_allowed' => 'boolean',
        'is_tracking' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function departmentRelation()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designationRelation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function employmentTypeRelation()
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function shiftRelation()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function stateRelation()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function cityRelation()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function countryRelation()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function places()
    {
        return $this->belongsToMany(Place::class, 'employee_attendance_places')->withTimestamps();
    }
}

