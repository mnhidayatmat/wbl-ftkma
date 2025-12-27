<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Recruitment Catalog</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #003A6C;
        }
        .header h1 {
            color: #003A6C;
            font-size: 20pt;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 9pt;
        }
        .filters {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 9pt;
        }
        .filters h3 {
            color: #003A6C;
            font-size: 11pt;
            margin-bottom: 5px;
        }
        .filters ul {
            list-style: none;
            margin-left: 10px;
        }
        .filters li {
            margin-bottom: 3px;
        }
        .student-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 15px;
            page-break-inside: avoid;
            background-color: #fff;
        }
        .student-header {
            border-bottom: 2px solid #003A6C;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .student-name {
            color: #003A6C;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .student-matric {
            color: #666;
            font-size: 10pt;
        }
        .student-info {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 4px 0;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            display: table-cell;
            padding: 4px 0;
            color: #333;
        }
        .skills-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .skills-section h4 {
            color: #003A6C;
            font-size: 10pt;
            margin-bottom: 5px;
        }
        .skills-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .skills-list li {
            display: inline-block;
            background-color: #E3F2FD;
            color: #0084C5;
            padding: 3px 8px;
            margin: 2px;
            border-radius: 3px;
            font-size: 9pt;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }
        .status-approved {
            background-color: #C8E6C9;
            color: #2E7D32;
        }
        .status-pending {
            background-color: #FFF9C4;
            color: #F57F17;
        }
        .status-none {
            background-color: #EEEEEE;
            color: #757575;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Recruitment Catalog</h1>
        <p>UMPSA Work-Based Learning Programme</p>
        <p style="font-size: 8pt; margin-top: 5px;">Generated on {{ now()->format('F d, Y \a\t H:i A') }}</p>
    </div>

    @if(!empty($filters))
        <div class="filters">
            <h3>Applied Filters:</h3>
            <ul>
                @foreach($filters as $key => $value)
                    <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin-bottom: 10px; color: #666; font-size: 9pt;">
        <strong>Total Students:</strong> {{ $students->count() }}
    </div>

    @foreach($students as $student)
        <div class="student-card">
            <div class="student-header">
                <div class="student-name">{{ $student->name }}</div>
                <div class="student-matric">{{ $student->matric_no }} | {{ $student->programme }}</div>
            </div>

            <div class="student-info">
                <div class="info-row">
                    <div class="info-label">CGPA:</div>
                    <div class="info-value">
                        <strong>{{ $student->cgpa ? number_format($student->cgpa, 2) : 'N/A' }}</strong>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Group:</div>
                    <div class="info-value">{{ $student->group->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Mobile:</div>
                    <div class="info-value">{{ $student->mobile_phone ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $student->user?->email ?? 'N/A' }}</div>
                </div>
                @if($student->preferred_industry)
                    <div class="info-row">
                        <div class="info-label">Preferred Industry:</div>
                        <div class="info-value">{{ $student->preferred_industry }}</div>
                    </div>
                @endif
                @if($student->preferred_location)
                    <div class="info-row">
                        <div class="info-label">Preferred Location:</div>
                        <div class="info-value">{{ $student->preferred_location }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Resume Status:</div>
                    <div class="info-value">
                        @if($student->resumeInspection?->status === 'RECOMMENDED')
                            <span class="status-badge status-approved">Approved</span>
                        @elseif($student->resume_pdf_path)
                            <span class="status-badge status-pending">Submitted</span>
                        @else
                            <span class="status-badge status-none">Not Submitted</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Placement Status:</div>
                    <div class="info-value">{{ $student->placementTracking?->status ?? 'Not Started' }}</div>
                </div>
            </div>

            @if($student->skills && count($student->skills) > 0)
                <div class="skills-section">
                    <h4>Skills:</h4>
                    <ul class="skills-list">
                        @foreach($student->skills as $skill)
                            <li>{{ $skill }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($student->interests)
                <div class="skills-section">
                    <h4>Interests:</h4>
                    <p style="font-size: 9pt; color: #555;">{{ $student->interests }}</p>
                </div>
            @endif

            @if($student->background)
                <div class="skills-section">
                    <h4>Background:</h4>
                    <p style="font-size: 9pt; color: #555;">{{ Str::limit($student->background, 200) }}</p>
                </div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>UMPSA Work-Based Learning Management System | Page <span class="page-number"></span></p>
    </div>
</body>
</html>
