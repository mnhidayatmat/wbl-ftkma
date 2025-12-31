<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Confirmation Letter (SCL)</title>
    <style>
        @page {
            margin: {{ $template->settings['margin_top'] ?? 25 }}mm {{ $template->settings['margin_right'] ?? 25 }}mm {{ $template->settings['margin_bottom'] ?? 25 }}mm {{ $template->settings['margin_left'] ?? 25 }}mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: {{ $template->settings['font_size'] ?? 12 }}pt;
            line-height: {{ $template->settings['line_height'] ?? 1.6 }};
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
        .signature-image {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 5px;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
        }
        .reference {
            margin-bottom: 20px;
        }
        .student-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
        }
        .company-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #e8f4e8;
        }
        .supervisor-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #e8e8f4;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $template->title ?? 'STUDENT CONFIRMATION LETTER (SCL)' }}</h1>
        @if($template->subtitle)
        <p>{{ $template->subtitle }}</p>
        @endif
    </div>

    @if($sclReferenceNumber)
    <div class="reference">
        <p><strong>Reference No:</strong> {{ $sclReferenceNumber }}</p>
    </div>
    @endif

    <div class="date">
        Date: {{ $sclReleaseDate ?? now()->format('d F Y') }}
    </div>

    <div class="content">
        <p>{{ $template->salutation ?? 'To Whom It May Concern,' }}</p>

        <p>This is to confirm that <strong>{{ $student->name }}</strong> (Student ID: <strong>{{ $student->matric_no }}</strong>)
        has been accepted for Work-Based Learning (WBL) placement.</p>

        <div class="student-info">
            <p><strong>Student Details:</strong></p>
            <p>Name: {{ $student->name }}</p>
            <p>Student ID: {{ $student->matric_no }}</p>
            <p>IC Number: {{ $student->ic_no ?? 'N/A' }}</p>
            <p>Programme: {{ $student->programme ?? 'N/A' }} ({{ $studentProgrammeShort ?? 'N/A' }})</p>
        </div>

        @if($company)
        <div class="company-info">
            <p><strong>Company Details:</strong></p>
            <p>Company Name: {{ $company->company_name }}</p>
            @if($company->address)
            <p>Address: {{ $company->address }}</p>
            @endif
            @if($company->pic_name)
            <p>HR/PIC Name: {{ $company->pic_name }}</p>
            @endif
            @if($company->position)
            <p>Position: {{ $company->position }}</p>
            @endif
            @if($company->email)
            <p>Email: {{ $company->email }}</p>
            @endif
            @if($company->phone)
            <p>Phone: {{ $company->phone }}</p>
            @endif
        </div>
        @endif

        <div class="supervisor-info">
            <p><strong>Supervisor Details:</strong></p>
            @if($academicTutor)
            <p>Academic Tutor (AT): {{ $academicTutor->name }}</p>
            @if($academicTutor->email)
            <p>AT Email: {{ $academicTutor->email }}</p>
            @endif
            @if($academicTutor->phone)
            <p>AT Phone: {{ $academicTutor->phone }}</p>
            @endif
            @endif
            @if($industryCoach)
            <p>Industry Coach (IC): {{ $industryCoach->name }}</p>
            @if($industryCoach->email)
            <p>IC Email: {{ $industryCoach->email }}</p>
            @endif
            @if($industryCoach->phone)
            <p>IC Phone: {{ $industryCoach->phone }}</p>
            @endif
            @endif
        </div>

        @if($group)
        <p><strong>Training Period:</strong> {{ $groupStartDate }} to {{ $groupEndDate }}</p>
        @endif

        @if($acceptedDate)
        <p><strong>Offer Accepted Date:</strong> {{ $acceptedDate }}</p>
        @endif

        <p>The student will commence their industrial training as per the agreed schedule.
        We appreciate your support in providing this valuable learning opportunity.</p>

        <p>Should you require any further information, please do not hesitate to contact us.</p>

        <p>{{ $template->closing_text ?? 'Thank you for your cooperation.' }}</p>
    </div>

    <div class="signature">
        <p>Yours sincerely,</p>
        <br>
        @if($directorSignaturePath && file_exists($directorSignaturePath))
        <img src="{{ $directorSignaturePath }}" alt="Signature" class="signature-image">
        <br>
        @else
        <br><br>
        @endif
        <p><strong>{{ $directorName ?? '_________________________' }}</strong></p>
        <p>{{ $template->signatory_title ?? 'Director of UMPSA Career Centre' }}</p>
        <p>{{ $template->signatory_department ?? 'Universiti Malaysia Pahang Al-Sultan Abdullah' }}</p>
    </div>
</body>
</html>
