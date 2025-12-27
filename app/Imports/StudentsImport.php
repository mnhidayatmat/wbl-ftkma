<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\WblGroup;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;
use Throwable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    protected $errors = [];
    protected $failures = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find or get group_id
        $groupId = null;
        if (!empty($row['group'])) {
            $group = WblGroup::where('name', $row['group'])->first();
            if ($group) {
                $groupId = $group->id;
            }
        }

        // Find or get company_id
        $companyId = null;
        if (!empty($row['company'])) {
            $company = Company::where('company_name', $row['company'])->first();
            if ($company) {
                $companyId = $company->id;
            }
        }

        // Handle skills - can be comma separated or JSON
        $skills = null;
        if (!empty($row['skills'])) {
            if (is_string($row['skills'])) {
                $skills = array_map('trim', explode(',', $row['skills']));
            } else {
                $skills = $row['skills'];
            }
        }

        return new Student([
            'name' => $row['name'] ?? null,
            'matric_no' => $row['matric_no'] ?? null,
            'programme' => $row['programme'] ?? null,
            'group_id' => $groupId,
            'company_id' => $companyId,
            'cgpa' => $row['cgpa'] ?? null,
            'ic_number' => $row['ic_number'] ?? null,
            'mobile_phone' => $row['mobile_phone'] ?? null,
            'parent_name' => $row['parent_name'] ?? null,
            'parent_phone_number' => $row['parent_phone_number'] ?? null,
            'next_of_kin' => $row['next_of_kin'] ?? null,
            'next_of_kin_phone_number' => $row['next_of_kin_phone_number'] ?? null,
            'home_address' => $row['home_address'] ?? null,
            'background' => $row['background'] ?? null,
            'skills' => $skills,
            'interests' => $row['interests'] ?? null,
            'preferred_industry' => $row['preferred_industry'] ?? null,
            'preferred_location' => $row['preferred_location'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'matric_no' => 'required|string|max:50|unique:students,matric_no',
            'programme' => 'nullable|string|max:255',
            'group' => 'nullable|string',
            'company' => 'nullable|string',
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'ic_number' => 'nullable|string|max:20',
            'mobile_phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone_number' => 'nullable|string|max:20',
            'next_of_kin' => 'nullable|string|max:255',
            'next_of_kin_phone_number' => 'nullable|string|max:20',
            'home_address' => 'nullable|string',
            'background' => 'nullable|string',
            'skills' => 'nullable|string',
            'interests' => 'nullable|string',
            'preferred_industry' => 'nullable|string|max:255',
            'preferred_location' => 'nullable|string|max:255',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFailures()
    {
        return $this->failures;
    }
}
