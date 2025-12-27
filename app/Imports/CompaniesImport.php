<?php

namespace App\Imports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CompaniesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable;

    protected $errors = [];
    protected $failures = [];
    protected $importedCount = 0;

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip if company name is empty
        if (empty($row['company_name'])) {
            return null;
        }

        // Check if company already exists
        $existingCompany = Company::where('company_name', $row['company_name'])->first();

        if ($existingCompany) {
            $this->errors[] = "Company '{$row['company_name']}' already exists (skipped)";
            return null;
        }

        $this->importedCount++;

        return new Company([
            'company_name' => $row['company_name'],
            'category' => $row['category'] ?? 'Other',
            'pic_name' => $row['pic_name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'postcode' => $row['postcode'] ?? null,
            'country' => $row['country'] ?? 'Malaysia',
            'website' => $row['website'] ?? null,
            'industry' => $row['industry'] ?? null,
            'company_size' => $row['company_size'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'category' => 'nullable|in:Oil and Gas,Design,Automotive,IT,Manufacturing,Construction,Other',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'company_name.required' => 'Company name is required',
            'email.email' => 'Invalid email format',
        ];
    }

    /**
     * Handle errors
     */
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        Log::error('Company Import Error: ' . $e->getMessage());
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get failures
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Get imported count
     */
    public function getImportedCount()
    {
        return $this->importedCount;
    }
}
