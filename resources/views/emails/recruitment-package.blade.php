<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #003A6C 0%, #0084C5 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .message-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0084C5;
            padding: 15px;
            margin: 20px 0;
        }
        .student-summary {
            background-color: #E3F2FD;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .student-summary h3 {
            color: #003A6C;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-value {
            font-size: 28px;
            font-weight: bold;
            color: #003A6C;
            display: block;
        }
        .summary-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .attachments {
            margin: 20px 0;
        }
        .attachments h3 {
            color: #003A6C;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .attachment-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .attachment-list li {
            padding: 10px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .attachment-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0084C5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Student Recruitment Package</h1>
            <p>UMPSA Work-Based Learning Programme</p>
        </div>

        <div class="email-body">
            <div class="greeting">
                <p>Dear Recruiter,</p>
            </div>

            <p>
                We are pleased to share a curated selection of talented students from the
                <strong>Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)</strong> Work-Based Learning Programme
                who are seeking internship and placement opportunities with <strong>{{ $company->company_name }}</strong>.
            </p>

            @if($customMessage)
                <div class="message-box">
                    <strong>Message from UMPSA:</strong>
                    <p style="margin: 10px 0 0 0;">{{ $customMessage }}</p>
                </div>
            @endif

            <div class="student-summary">
                <h3>Student Package Overview</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-value">{{ $students->count() }}</span>
                        <span class="summary-label">Students</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value">{{ $students->unique('programme')->count() }}</span>
                        <span class="summary-label">Programmes</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-value">{{ $students->avg('cgpa') ? number_format($students->avg('cgpa'), 2) : 'N/A' }}</span>
                        <span class="summary-label">Avg CGPA</span>
                    </div>
                </div>

                @if($students->unique('programme')->count() > 0)
                    <div style="margin-top: 20px;">
                        <strong style="color: #003A6C;">Programmes Included:</strong>
                        <p style="margin: 5px 0; color: #555;">
                            {{ $students->unique('programme')->pluck('programme')->implode(', ') }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="attachments">
                <h3>📎 Attached Documents</h3>
                <ul class="attachment-list">
                    @if($includeExcel)
                        <li>
                            <svg class="attachment-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <strong>student_list.xlsx</strong> - Comprehensive student data in Excel format
                        </li>
                    @endif
                    @if($includePdf)
                        <li>
                            <svg class="attachment-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <strong>student_catalog.pdf</strong> - Detailed student profiles catalog
                        </li>
                    @endif
                    @if($includeResumes)
                        <li>
                            <svg class="attachment-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                            <strong>student_resumes.zip</strong> - Individual student resumes
                        </li>
                    @endif
                </ul>
            </div>

            <div class="divider"></div>

            <p>
                These students have been carefully selected based on their academic performance, skills,
                and alignment with industry requirements. We believe they would be excellent candidates
                for internship and placement opportunities at your organization.
            </p>

            <p>
                Should you require any additional information or wish to schedule interviews,
                please do not hesitate to contact us.
            </p>

            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>UMPSA Work-Based Learning Team</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)</strong></p>
            <p>Work-Based Learning Management System</p>
            <p style="margin-top: 10px; font-size: 11px; color: #999;">
                This email was sent on {{ now()->format('F d, Y \a\t H:i A') }}
            </p>
        </div>
    </div>
</body>
</html>
