<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->title }}</title>
    <style>
        @page {
            margin: {{ $template->settings['margin_top'] ?? '25' }}mm {{ $template->settings['margin_right'] ?? '25' }}mm {{ $template->settings['margin_bottom'] ?? '25' }}mm {{ $template->settings['margin_left'] ?? '25' }}mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: {{ $template->settings['font_size'] ?? '12' }}pt;
            line-height: {{ $template->settings['line_height'] ?? '1.6' }};
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header .subtitle {
            font-size: 12pt;
            color: #444;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
        }
        .salutation {
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
            text-align: justify;
        }
        .content p {
            margin-bottom: 15px;
        }
        .closing {
            margin-top: 30px;
        }
        .signature {
            margin-top: 50px;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
        .signatory-name {
            font-weight: bold;
        }
        .signatory-title {
            color: #333;
        }
        .signatory-dept {
            color: #666;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    @if($template->settings['show_logo'] ?? true)
    <div class="header">
        @if(file_exists(public_path('images/umpsa-logo.png')))
            <img src="{{ public_path('images/umpsa-logo.png') }}" alt="UMPSA Logo">
        @endif
        <h1>{{ $template->title }}</h1>
        @if($template->subtitle)
            <p class="subtitle">{{ $template->subtitle }}</p>
        @endif
    </div>
    @else
    <div class="header">
        <h1>{{ $template->title }}</h1>
        @if($template->subtitle)
            <p class="subtitle">{{ $template->subtitle }}</p>
        @endif
    </div>
    @endif

    @if($template->settings['show_date'] ?? true)
    <div class="date">
        Date: {{ now()->format($template->settings['date_format'] ?? 'd F Y') }}
    </div>
    @endif

    @if($template->salutation)
    <div class="salutation">
        <p>{{ $template->salutation }}</p>
    </div>
    @endif

    <div class="content">
        @php
            // Replace template variables with actual student data
            $content = $template->body_content;
            $replacements = [
                '{{student_name}}' => $student->name ?? '',
                '{{student_matric}}' => $student->matric_no ?? '',
                '{{student_ic}}' => $student->ic_no ?? '',
                '{{student_faculty}}' => $student->programme ?? 'Engineering',
                '{{student_email}}' => $student->user->email ?? '',
                '{{student_phone}}' => $student->phone ?? '',
                '{{wbl_duration}}' => $wblDuration ?? '6 months',
                '{{current_date}}' => now()->format($template->settings['date_format'] ?? 'd F Y'),
                '{{group_name}}' => $student->group->name ?? '',
            ];

            foreach ($replacements as $key => $value) {
                $content = str_replace($key, $value, $content);
            }

            // Convert line breaks to paragraphs
            $paragraphs = preg_split('/\n\s*\n/', $content);
        @endphp

        @foreach($paragraphs as $paragraph)
            <p>{!! nl2br(trim($paragraph)) !!}</p>
        @endforeach
    </div>

    @if($template->closing_text)
    <div class="closing">
        <p>{{ $template->closing_text }}</p>
    </div>
    @endif

    <div class="signature">
        <div class="signature-line"></div>
        @if($template->signatory_name)
            <p class="signatory-name">{{ $template->signatory_name }}</p>
        @endif
        @if($template->signatory_title)
            <p class="signatory-title">{{ $template->signatory_title }}</p>
        @endif
        @if($template->signatory_department)
            <p class="signatory-dept">{{ $template->signatory_department }}</p>
        @endif
    </div>
</body>
</html>
