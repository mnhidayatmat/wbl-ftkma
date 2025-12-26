<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\WblGroup;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentsPreviewImport implements ToArray, WithHeadingRow
{
    protected $previewData = [];
    protected $rowNumber = 1; // Start at 1 (header is row 0)

    /**
     * Transform the imported data into an array with validation results
     */
    public function array(array $array)
    {
        // The $array parameter contains the rows from the current sheet
        // Each row is already an associative array with column headers as keys

        $rowIndex = 0;
        foreach ($array as $row) {
            // Skip if row is not an array (shouldn't happen, but safety check)
            if (!is_array($row)) {
                continue;
            }

            $rowNumber = $rowIndex + 2; // +2 because Excel is 1-indexed and first row is header

            // Validate the row
            $validationResult = $this->validateRow($row, $rowNumber);

            // Process the row data
            $processedData = $this->processRow($row);

            // Store the preview data with validation status
            $this->previewData[] = [
                'row' => $rowNumber,
                'data' => $processedData,
                'valid' => $validationResult['valid'],
                'errors' => $validationResult['errors'],
            ];

            $rowIndex++;
        }

        return $this->previewData;
    }

    /**
     * Validate a single row
     */
    protected function validateRow(array $row, int $rowNumber): array
    {
        $rules = [
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

        $validator = Validator::make($row, $rules);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        return [
            'valid' => true,
            'errors' => [],
        ];
    }

    /**
     * Process row data (lookup IDs, parse skills, etc.)
     */
    protected function processRow(array $row): array
    {
        // Find group
        $groupName = null;
        $groupId = null;
        if (!empty($row['group'])) {
            $group = WblGroup::where('name', $row['group'])->first();
            $groupName = $row['group'];
            $groupId = $group ? $group->id : null;
        }

        // Find company
        $companyName = null;
        $companyId = null;
        if (!empty($row['company'])) {
            $company = Company::where('company_name', $row['company'])->first();
            $companyName = $row['company'];
            $companyId = $company ? $company->id : null;
        }

        // Parse skills
        $skills = null;
        if (!empty($row['skills'])) {
            if (is_string($row['skills'])) {
                $skills = implode(', ', array_map('trim', explode(',', $row['skills'])));
            } else {
                $skills = $row['skills'];
            }
        }

        return [
            'name' => $row['name'] ?? '',
            'matric_no' => $row['matric_no'] ?? '',
            'programme' => $row['programme'] ?? '',
            'group' => $groupName,
            'group_id' => $groupId,
            'company' => $companyName,
            'company_id' => $companyId,
            'cgpa' => $row['cgpa'] ?? '',
            'ic_number' => $row['ic_number'] ?? '',
            'mobile_phone' => $row['mobile_phone'] ?? '',
            'parent_name' => $row['parent_name'] ?? '',
            'parent_phone_number' => $row['parent_phone_number'] ?? '',
            'next_of_kin' => $row['next_of_kin'] ?? '',
            'next_of_kin_phone_number' => $row['next_of_kin_phone_number'] ?? '',
            'home_address' => $row['home_address'] ?? '',
            'background' => $row['background'] ?? '',
            'skills' => $skills,
            'interests' => $row['interests'] ?? '',
            'preferred_industry' => $row['preferred_industry'] ?? '',
            'preferred_location' => $row['preferred_location'] ?? '',
        ];
    }

    /**
     * Get the preview data
     */
    public function getPreviewData(): array
    {
        return $this->previewData;
    }
}
