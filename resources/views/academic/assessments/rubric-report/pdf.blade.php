<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rubric Report - {{ $assessment->assessment_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #003A6C;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            color: #003A6C;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            color: #0084C5;
            font-weight: normal;
        }

        .header p {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .info-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .info-section table {
            width: 100%;
        }

        .info-section td {
            padding: 3px 10px;
            font-size: 10px;
        }

        .info-section .label {
            font-weight: bold;
            color: #666;
            width: 120px;
        }

        .rubric-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .rubric-table th,
        .rubric-table td {
            border: 1px solid #ddd;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
            font-size: 9px;
        }

        .rubric-table th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .rubric-table th.element-header {
            background-color: #003A6C;
            color: white;
            width: 18%;
        }

        .rubric-table th.aware {
            background-color: #dc3545;
            color: white;
            width: 16.4%;
        }

        .rubric-table th.limited {
            background-color: #fd7e14;
            color: white;
            width: 16.4%;
        }

        .rubric-table th.fair {
            background-color: #ffc107;
            color: #333;
            width: 16.4%;
        }

        .rubric-table th.good {
            background-color: #0084C5;
            color: white;
            width: 16.4%;
        }

        .rubric-table th.excellent {
            background-color: #28a745;
            color: white;
            width: 16.4%;
        }

        .rubric-table td.element-cell {
            background-color: #f8f9fa;
        }

        .rubric-table td.aware-cell {
            background-color: #fff5f5;
        }

        .rubric-table td.limited-cell {
            background-color: #fff8f0;
        }

        .rubric-table td.fair-cell {
            background-color: #fffef0;
        }

        .rubric-table td.good-cell {
            background-color: #f0f8ff;
        }

        .rubric-table td.excellent-cell {
            background-color: #f0fff4;
        }

        .element-name {
            font-weight: bold;
            color: #003A6C;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .criteria-keywords {
            font-style: italic;
            color: #666;
            font-size: 8px;
            margin-top: 5px;
        }

        .weight-badge {
            display: inline-block;
            background-color: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            margin-top: 5px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITI MALAYSIA PAHANG AL-SULTAN ABDULLAH</h1>
        <h2>Assessment Rubric Report</h2>
        <p>{{ $courseName }} - {{ $assessment->assessment_name }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td class="label">Course Code:</td>
                <td>{{ $assessment->course_code }}</td>
                <td class="label">Assessment Type:</td>
                <td>{{ $assessment->assessment_type }}</td>
            </tr>
            <tr>
                <td class="label">Assessment Name:</td>
                <td>{{ $assessment->assessment_name }}</td>
                <td class="label">Generated Date:</td>
                <td>{{ now()->format('d M Y, h:i A') }}</td>
            </tr>
        </table>
    </div>

    <table class="rubric-table">
        <thead>
            <tr>
                <th class="element-header">Element</th>
                @foreach($ratingLevels as $level => $info)
                    <th class="{{ strtolower($info['label']) }}">{{ $info['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rubricReport->elements as $element)
                <tr>
                    <td class="element-cell">
                        <div class="element-name">{{ $loop->iteration }}. {{ strtoupper($element->element_name) }}</div>
                        @if($element->criteria_keywords)
                            <div class="criteria-keywords">{{ $element->criteria_keywords }}</div>
                        @endif
                        @if($element->weight_percentage)
                            <div class="weight-badge">Weight: {{ number_format($element->weight_percentage, 1) }}%</div>
                        @endif
                    </td>
                    @foreach($ratingLevels as $level => $info)
                        @php
                            $descriptor = $element->descriptors->where('level', $level)->first();
                        @endphp
                        <td class="{{ strtolower($info['label']) }}-cell">
                            {{ $descriptor->descriptor ?? '-' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated by WBL Management System | UMPSA | {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>
