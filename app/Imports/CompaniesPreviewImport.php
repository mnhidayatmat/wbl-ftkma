<?php

namespace App\Imports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class CompaniesPreviewImport implements ToArray, WithHeadingRow
{
    protected $previewData = [];

    /**
     * Transform the imported data into an array with validation results
     */
    public function array(array $array)
    {
        $rowIndex = 0;
        foreach ($array as $row) {
            // Skip if row is not an array
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
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'category' => 'nullable|in:Oil and Gas,Design,Automotive,IT,Manufacturing,Construction,Other',
            'pic_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'company_size' => 'nullable|string|max:50',
        ];

        $validator = Validator::make($row, $rules);

        $errors = [];

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
        }

        // Check for duplicate company name in database
        if (!empty($row['company_name'])) {
            $existingCompany = Company::where('company_name', $row['company_name'])->first();
            if ($existingCompany) {
                $errors[] = "Company '{$row['company_name']}' already exists in database";
            }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    /**
     * Process row data
     */
    protected function processRow(array $row): array
    {
        return [
            'company_name' => $row['company_name'] ?? '',
            'category' => $row['category'] ?? 'Other',
            'pic_name' => $row['pic_name'] ?? '',
            'email' => $row['email'] ?? '',
            'phone' => $row['phone'] ?? '',
            'address' => $row['address'] ?? '',
            'city' => $row['city'] ?? '',
            'state' => $row['state'] ?? '',
            'postcode' => $row['postcode'] ?? '',
            'country' => $row['country'] ?? 'Malaysia',
            'website' => $row['website'] ?? '',
            'industry' => $row['industry'] ?? '',
            'company_size' => $row['company_size'] ?? '',
            'notes' => $row['notes'] ?? '',
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
