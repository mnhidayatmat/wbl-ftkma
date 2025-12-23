<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Application Letter (SAL)</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>STUDENT APPLICATION LETTER (SAL)</h1>
    </div>

    <div class="date">
        Date: {{ now()->format('d F Y') }}
    </div>

    <div class="content">
        <p>To Whom It May Concern,</p>
        
        <p>This is to certify that <strong>{{ $student->name }}</strong> (Student ID: <strong>{{ $student->matric_no }}</strong>) 
        is a student of the Faculty of {{ $student->programme ?? 'Engineering' }}, 
        Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA).</p>
        
        <p>The student is currently enrolled in the Work-Based Learning (WBL) program and is seeking placement 
        for a period of <strong>{{ $wblDuration }}</strong>.</p>
        
        <p>We kindly request your consideration for the student's application for industrial training placement 
        at your esteemed organization.</p>
        
        <p>Should you require any further information, please do not hesitate to contact us.</p>
        
        <p>Thank you for your consideration.</p>
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

