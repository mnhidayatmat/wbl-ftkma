<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'subtitle',
        'salutation',
        'body_content',
        'closing_text',
        'signatory_name',
        'signatory_title',
        'signatory_department',
        'logo_path',
        'word_template_path',
        'word_template_original_name',
        'template_mode',
        'settings',
        'canvas_elements',
        'canvas_width',
        'canvas_height',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'canvas_elements' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who last updated this template.
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get or create the SAL template with default values.
     */
    public static function getSalTemplate(): self
    {
        return self::firstOrCreate(
            ['type' => 'SAL'],
            self::getDefaultSalTemplate()
        );
    }

    /**
     * Get or create the SCL template with default values.
     */
    public static function getSclTemplate(): self
    {
        return self::firstOrCreate(
            ['type' => 'SCL'],
            self::getDefaultSclTemplate()
        );
    }

    /**
     * Get default SAL template values.
     */
    public static function getDefaultSalTemplate(): array
    {
        return [
            'type' => 'SAL',
            'title' => 'STUDENT APPLICATION LETTER (SAL)',
            'subtitle' => 'Work-Based Learning Program',
            'salutation' => 'To Whom It May Concern,',
            'body_content' => 'This is to certify that <strong>{{student_name}}</strong> (Student ID: <strong>{{student_matric}}</strong>) is a student of the Faculty of {{student_faculty}}, Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA).

The student is currently enrolled in the Work-Based Learning (WBL) program and is seeking placement for a period of <strong>{{wbl_duration}}</strong>.

We kindly request your consideration for the student\'s application for industrial training placement at your esteemed organization.

Should you require any further information, please do not hesitate to contact us.

Thank you for your consideration.',
            'closing_text' => 'Yours sincerely,',
            'signatory_name' => '',
            'signatory_title' => 'WBL Coordinator',
            'signatory_department' => 'Universiti Malaysia Pahang Al-Sultan Abdullah',
            'settings' => [
                'font_size' => '12',
                'line_height' => '1.6',
                'margin_top' => '25',
                'margin_bottom' => '25',
                'margin_left' => '25',
                'margin_right' => '25',
                'show_logo' => true,
                'show_date' => true,
                'date_format' => 'd F Y',
            ],
        ];
    }

    /**
     * Get default SCL template values.
     */
    public static function getDefaultSclTemplate(): array
    {
        return [
            'type' => 'SCL',
            'title' => 'STUDENT CONFIRMATION LETTER (SCL)',
            'subtitle' => 'Work-Based Learning Program',
            'salutation' => 'To Whom It May Concern,',
            'body_content' => 'This is to confirm that <strong>{{student_name}}</strong> (Student ID: <strong>{{student_matric}}</strong>) has been successfully placed at <strong>{{company_name}}</strong> for the Work-Based Learning (WBL) program.

The placement period is from <strong>{{start_date}}</strong> to <strong>{{end_date}}</strong>.

We appreciate your support in providing this valuable learning opportunity for our student.

Thank you for your cooperation.',
            'closing_text' => 'Yours sincerely,',
            'signatory_name' => '',
            'signatory_title' => 'WBL Coordinator',
            'signatory_department' => 'Universiti Malaysia Pahang Al-Sultan Abdullah',
            'settings' => [
                'font_size' => '12',
                'line_height' => '1.6',
                'margin_top' => '25',
                'margin_bottom' => '25',
                'margin_left' => '25',
                'margin_right' => '25',
                'show_logo' => true,
                'show_date' => true,
                'date_format' => 'd F Y',
            ],
        ];
    }

    /**
     * Get available template variables for SAL.
     */
    public static function getSalVariables(): array
    {
        return [
            '{{student_name}}' => 'Student\'s full name',
            '{{student_matric}}' => 'Student\'s matric number',
            '{{student_ic}}' => 'Student\'s IC number',
            '{{student_faculty}}' => 'Student\'s faculty',
            '{{student_programme}}' => 'Student\'s programme',
            '{{student_programme_short}}' => 'Student\'s programme short code (BTA/BTD/BTG)',
            '{{student_email}}' => 'Student\'s email',
            '{{student_phone}}' => 'Student\'s phone number',
            '{{wbl_duration}}' => 'WBL training duration',
            '{{current_date}}' => 'Current date',
            '{{group_name}}' => 'WBL group name',
            '{{group_start_date}}' => 'Group start date',
            '{{group_end_date}}' => 'Group end date',
            '{{sal_release_date}}' => 'SAL release/issue date',
            '{{sal_reference_number}}' => 'SAL reference number',
            '{{wbl_coordinator_name}}' => 'WBL Coordinator name (based on programme)',
            '{{wbl_coordinator_email}}' => 'WBL Coordinator email',
            '{{wbl_coordinator_phone}}' => 'WBL Coordinator phone number',
            '{{director_name}}' => 'Director of UMPSA Career Centre name',
            '{{director_signature}}' => 'Director signature image',
            '{{company_name}}' => 'Company name',
            '{{company_address}}' => 'Company address',
            '{{company_pic_name}}' => 'Company person in charge name',
            '{{company_pic_position}}' => 'Company PIC position',
            '{{company_email}}' => 'Company email',
            '{{company_phone}}' => 'Company phone',
        ];
    }

    /**
     * Get available template variables for SCL.
     */
    public static function getSclVariables(): array
    {
        return [
            // Auto-populated - Student Info
            '{{student_name}}' => 'Student\'s full name',
            '{{student_matric}}' => 'Student\'s matric number',
            '{{student_ic}}' => 'Student\'s IC number',
            '{{student_programme}}' => 'Student\'s programme',
            '{{student_programme_short}}' => 'Programme short code (BTA/BTD/BTG)',
            // Auto-populated - Company Info
            '{{company_name}}' => 'Company name (accepted offer)',
            '{{company_address}}' => 'Company address',
            '{{hr_name}}' => 'HR/PIC name from company',
            '{{hr_position}}' => 'HR/PIC position',
            '{{company_email}}' => 'Company email',
            '{{company_phone}}' => 'Company phone',
            // Auto-populated - Dates
            '{{group_start_date}}' => 'WBL start date',
            '{{group_end_date}}' => 'WBL end date',
            '{{accepted_date}}' => 'Offer accepted date',
            '{{current_date}}' => 'Current date',
            // Auto-populated - Supervisors
            '{{academic_tutor_name}}' => 'Academic Tutor (AT) name',
            '{{academic_tutor_email}}' => 'Academic Tutor email',
            '{{academic_tutor_phone}}' => 'Academic Tutor phone',
            '{{industry_coach_name}}' => 'Industry Coach (IC) name',
            '{{industry_coach_email}}' => 'Industry Coach email',
            '{{industry_coach_phone}}' => 'Industry Coach phone',
            // Manual Input
            '{{scl_release_date}}' => 'SCL release/issue date',
            '{{scl_reference_number}}' => 'SCL reference number',
            '{{director_name}}' => 'Director of UMPSA Career Centre',
            '{{director_signature}}' => 'Director signature (image)',
        ];
    }

    /**
     * Replace template variables with actual values.
     */
    public function replaceVariables(array $data): string
    {
        $content = $this->body_content;

        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value ?? '', $content);
        }

        return $content;
    }
}
