<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Confirmation Letter (SCL)</title>
    <style>
        @page {
            margin: 25mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 20px;
        }
        .signature {
            margin-top: 50px;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
        }
        .student-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>STUDENT CONFIRMATION LETTER (SCL)</h1>
    </div>

    <div class="date">
        Date: {{ now()->format('d F Y') }}
    </div>

    <div class="content">
        <p>To Whom It May Concern,</p>
        
        <p>This is to confirm that <strong>{{ $student->name }}</strong> (Student ID: <strong>{{ $student->matric_no }}</strong>) 
        has been accepted for Work-Based Learning (WBL) placement at your organization.</p>
        
        <div class="student-info">
            <p><strong>Student Details:</strong></p>
            <p>Name: {{ $student->name }}</p>
            <p>Student ID: {{ $student->matric_no }}</p>
            <p>Programme: {{ $student->programme ?? 'N/A' }}</p>
            @if($student->academicTutor)
            <p>Academic Tutor (AT): {{ $student->academicTutor->name }}</p>
            @endif
            @if($student->industryCoach)
            <p>Industry Coach (IC): {{ $student->industryCoach->name }}</p>
            @endif
            @if($group)
            <p>Training Period: {{ $group->start_date->format('d F Y') }} to {{ $group->end_date->format('d F Y') }}</p>
            @endif
            @if($student->company)
            <p>Company: {{ $student->company->company_name }}</p>
            @endif
        </div>
        
        <p>The student will commence their industrial training as per the agreed schedule. 
        We appreciate your support in providing this valuable learning opportunity.</p>
        
        <p>Should you require any further information, please do not hesitate to contact us.</p>
        
        <p>Thank you for your cooperation.</p>
    </div>

    <div class="signature">
        <p>Yours sincerely,</p>
        <br><br>
        <p>_________________________</p>
        <p>WBL Coordinator</p>
        <p>Universiti Malaysia Pahang Al-Sultan Abdullah</p>
    </div>
</body>
</html>

