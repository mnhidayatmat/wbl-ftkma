<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>FYP Project Proposal - {{ $proposal->student->matric_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #003A6C;
            padding-bottom: 15px;
        }

        .header-logo {
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #003A6C;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-subtitle {
            font-size: 14px;
            color: #0084C5;
            margin-top: 5px;
        }

        .header-faculty {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        /* Student Info Section */
        .student-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .student-info-header {
            font-size: 12px;
            font-weight: bold;
            color: #003A6C;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #0084C5;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 5px 10px 5px 0;
            font-weight: bold;
            color: #4a5568;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            width: 70%;
            padding: 5px 0;
            color: #1a202c;
        }

        /* Project Title Section */
        .project-title-section {
            background: linear-gradient(135deg, #003A6C 0%, #0084C5 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .project-title-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .project-title-text {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.3;
        }

        /* Proposal Table */
        .proposal-section {
            margin-bottom: 20px;
        }

        .proposal-section-header {
            font-size: 12px;
            font-weight: bold;
            color: #003A6C;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #0084C5;
        }

        .proposal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .proposal-table th {
            background-color: #003A6C;
            color: white;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #003A6C;
        }

        .proposal-table th:first-child {
            width: 5%;
            text-align: center;
        }

        .proposal-table th:nth-child(2),
        .proposal-table th:nth-child(3),
        .proposal-table th:nth-child(4) {
            width: 31.67%;
        }

        .proposal-table td {
            padding: 10px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 10px;
            line-height: 1.5;
        }

        .proposal-table tr:nth-child(even) {
            background-color: #FFFDE7;
        }

        .proposal-table tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .proposal-table td:first-child {
            text-align: center;
            font-weight: bold;
            color: #003A6C;
        }

        /* Status Badge */
        .status-section {
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .status-submitted {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Submission Info */
        .submission-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .submission-grid {
            display: table;
            width: 100%;
        }

        .submission-item {
            display: table-cell;
            width: 33.33%;
            padding: 0 10px;
        }

        .submission-item:first-child {
            padding-left: 0;
        }

        .submission-item:last-child {
            padding-right: 0;
        }

        .submission-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #718096;
            margin-bottom: 3px;
        }

        .submission-value {
            font-size: 11px;
            font-weight: bold;
            color: #1a202c;
        }

        /* Remarks Section */
        .remarks-section {
            background-color: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .remarks-label {
            font-size: 11px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        .remarks-text {
            font-size: 11px;
            color: #78350f;
            line-height: 1.5;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #718096;
        }

        .footer-date {
            margin-top: 5px;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 80%;
            margin: 40px auto 10px;
        }

        .signature-name {
            font-size: 11px;
            font-weight: bold;
            color: #1a202c;
        }

        .signature-title {
            font-size: 10px;
            color: #718096;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">FYP Project Proposal</div>
            <div class="header-subtitle">Final Year Project (Work-Based Learning)</div>
            <div class="header-faculty">Faculty of Mechanical and Automotive Engineering Technology (FTKMA)</div>
        </div>

        <!-- Status Badge -->
        <div class="status-section">
            <span class="status-badge status-{{ $proposal->status }}">
                {{ $proposal->status_label }}
            </span>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="student-info-header">Student Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Student Name</div>
                    <div class="info-value">{{ $proposal->student->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Matric Number</div>
                    <div class="info-value">{{ $proposal->student->matric_no }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Company</div>
                    <div class="info-value">{{ $proposal->student->company->company_name ?? 'Not Assigned' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Industry Coach</div>
                    <div class="info-value">
                        {{ $proposal->student->industryCoach->name ?? 'Not Assigned' }}
                        @if($proposal->student->industryCoach && $proposal->student->industryCoach->position)
                            ({{ $proposal->student->industryCoach->position }})
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Academic Tutor</div>
                    <div class="info-value">{{ $proposal->student->academicTutor->name ?? 'Not Assigned' }}</div>
                </div>
                @if($proposal->student->group)
                <div class="info-row">
                    <div class="info-label">Group</div>
                    <div class="info-value">{{ $proposal->student->group->name ?? '-' }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Project Title -->
        <div class="project-title-section">
            <div class="project-title-label">Project Title</div>
            <div class="project-title-text">{{ $proposal->project_title }}</div>
        </div>

        <!-- Proposal Items Table -->
        <div class="proposal-section">
            <div class="proposal-section-header">Project Details</div>
            <table class="proposal-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Problem Statement</th>
                        <th>Objective</th>
                        <th>Methodology</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposal->proposal_items ?? [] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['problem_statement'] ?? '' }}</td>
                        <td>{{ $item['objective'] ?? '' }}</td>
                        <td>{{ $item['methodology'] ?? '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #718096;">No proposal items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Submission Information -->
        <div class="submission-info">
            <div class="submission-grid">
                <div class="submission-item">
                    <div class="submission-label">Submitted At</div>
                    <div class="submission-value">
                        {{ $proposal->submitted_at ? $proposal->submitted_at->format('d M Y, H:i') : '-' }}
                    </div>
                </div>
                @if($proposal->approved_at)
                <div class="submission-item">
                    <div class="submission-label">Approved At</div>
                    <div class="submission-value">
                        {{ $proposal->approved_at->format('d M Y, H:i') }}
                    </div>
                </div>
                <div class="submission-item">
                    <div class="submission-label">Approved By</div>
                    <div class="submission-value">
                        {{ $proposal->approver->name ?? '-' }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Remarks -->
        @if($proposal->remarks)
        <div class="remarks-section">
            <div class="remarks-label">Remarks</div>
            <div class="remarks-text">{{ $proposal->remarks }}</div>
        </div>
        @endif

        <!-- Signature Section -->
        @if($proposal->status === 'approved')
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $proposal->student->name }}</div>
                <div class="signature-title">Student</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $proposal->approver->name ?? 'Academic Tutor' }}</div>
                <div class="signature-title">Approved By</div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>Work-Based Learning (WBL) Management System</div>
            <div>Faculty of Mechanical and Automotive Engineering Technology (FTKMA), UMPSA</div>
            <div class="footer-date">Generated on {{ now()->format('d M Y, H:i') }}</div>
        </div>
    </div>
</body>
</html>
