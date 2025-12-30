@php
    // Check if using canvas elements
    $hasCanvasElements = !empty($template->canvas_elements) && is_array($template->canvas_elements);

    // Get margins
    $marginTop = $template->settings['margins']['top'] ?? $template->settings['margin_top'] ?? 25;
    $marginRight = $template->settings['margins']['right'] ?? $template->settings['margin_right'] ?? 25;
    $marginBottom = $template->settings['margins']['bottom'] ?? $template->settings['margin_bottom'] ?? 25;
    $marginLeft = $template->settings['margins']['left'] ?? $template->settings['margin_left'] ?? 25;

    // Canvas dimensions - A4 at 72 DPI = 595 x 842 points
    // DOMPDF uses 72 DPI, so 1px = 1pt
    $canvasWidth = $template->canvas_width ?? 595;
    $canvasHeight = $template->canvas_height ?? 842;

    // Background color
    $bgColor = $template->settings['background'] ?? '#ffffff';

    // Variable replacements (normal and uppercase versions)
    $studentName = $student->name ?? '';
    $studentMatric = $student->matric_no ?? '';
    $studentIc = $student->ic_no ?? '';
    $studentFaculty = $student->faculty ?? 'Faculty of Manufacturing and Mechatronic Engineering Technology';
    $studentProgramme = $student->programme ?? '';
    $studentEmail = $student->user->email ?? '';
    $studentPhone = $student->phone ?? '';
    $wblDur = $wblDuration ?? '6 months';
    $dateFormat = $template->settings['date_format'] ?? 'd F Y';
    $currentDate = now()->format($dateFormat);
    $groupName = $student->group->name ?? '';
    $signatoryName = $template->signatory_name ?? 'WBL Coordinator';
    $companyName = $student->company->name ?? '';
    $companyAddress = $student->company->address ?? '';

    // Group dates
    $grpStartDate = isset($groupStartDate) && $groupStartDate
        ? \Carbon\Carbon::parse($groupStartDate)->format($dateFormat)
        : '';
    $grpEndDate = isset($groupEndDate) && $groupEndDate
        ? \Carbon\Carbon::parse($groupEndDate)->format($dateFormat)
        : '';

    // SAL-specific settings (manual input from admin)
    $salReleaseDate = !empty($template->settings['sal_release_date'])
        ? \Carbon\Carbon::parse($template->settings['sal_release_date'])->format($dateFormat)
        : '';
    $salReferenceNumber = $template->settings['sal_reference_number'] ?? '';

    $replacements = [
        // Normal variables
        '{{student_name}}' => $studentName,
        '{{student_matric}}' => $studentMatric,
        '{{student_ic}}' => $studentIc,
        '{{student_faculty}}' => $studentFaculty,
        '{{student_programme}}' => $studentProgramme,
        '{{student_email}}' => $studentEmail,
        '{{student_phone}}' => $studentPhone,
        '{{wbl_duration}}' => $wblDur,
        '{{current_date}}' => $currentDate,
        '{{group_name}}' => $groupName,
        '{{group_start_date}}' => $grpStartDate,
        '{{group_end_date}}' => $grpEndDate,
        '{{signatory_name}}' => $signatoryName,
        '{{company_name}}' => $companyName,
        '{{company_address}}' => $companyAddress,
        '{{sal_release_date}}' => $salReleaseDate,
        '{{sal_reference_number}}' => $salReferenceNumber,
        // Uppercase versions (for :upper suffix)
        '{{student_name:upper}}' => strtoupper($studentName),
        '{{student_matric:upper}}' => strtoupper($studentMatric),
        '{{student_ic:upper}}' => strtoupper($studentIc),
        '{{student_faculty:upper}}' => strtoupper($studentFaculty),
        '{{student_programme:upper}}' => strtoupper($studentProgramme),
        '{{student_email:upper}}' => strtoupper($studentEmail),
        '{{student_phone:upper}}' => strtoupper($studentPhone),
        '{{wbl_duration:upper}}' => strtoupper($wblDur),
        '{{current_date:upper}}' => strtoupper($currentDate),
        '{{group_name:upper}}' => strtoupper($groupName),
        '{{group_start_date:upper}}' => strtoupper($grpStartDate),
        '{{group_end_date:upper}}' => strtoupper($grpEndDate),
        '{{signatory_name:upper}}' => strtoupper($signatoryName),
        '{{company_name:upper}}' => strtoupper($companyName),
        '{{company_address:upper}}' => strtoupper($companyAddress),
        '{{sal_release_date:upper}}' => strtoupper($salReleaseDate),
        '{{sal_reference_number:upper}}' => strtoupper($salReferenceNumber),
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->title ?? 'SAL Document' }}</title>
    <style>
        @if($hasCanvasElements)
        @page {
            margin: 0;
            size: {{ $canvasWidth }}pt {{ $canvasHeight }}pt;
        }
        @else
        @page {
            margin: {{ $marginTop }}mm {{ $marginRight }}mm {{ $marginBottom }}mm {{ $marginLeft }}mm;
        }
        @endif

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: {{ $bgColor }};
        }

        /* Canvas-based layout - use pt for DOMPDF compatibility */
        .canvas-page {
            position: relative;
            width: {{ $canvasWidth }}pt;
            height: {{ $canvasHeight }}pt;
            background: {{ $bgColor }};
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .canvas-element {
            position: absolute;
            overflow: visible;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        .text-element {
            width: 100%;
            height: 100%;
            word-wrap: break-word;
            overflow: visible;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .line-element {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Table element - match designer exactly */
        table {
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            border-spacing: 0;
            page-break-inside: avoid;
        }
        table td {
            box-sizing: border-box;
            word-wrap: break-word;
            word-break: break-word;
            margin: 0;
        }
        table tr {
            margin: 0;
            padding: 0;
            page-break-inside: avoid;
        }
        .table-wrapper {
            page-break-inside: avoid;
        }

        /* Image element */
        .canvas-element img {
            display: block;
            width: 100%;
            height: 100%;
        }

        /* Fallback styles for old template format */
        .header {
            text-align: center;
            margin-bottom: 30px;
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
@if($hasCanvasElements)
    {{-- Render canvas elements from designer --}}
    <div class="canvas-page">
        @foreach($template->canvas_elements as $index => $element)
            @php
                $x = $element['x'] ?? 0;
                $y = $element['y'] ?? 0;
                $width = $element['width'] ?? 100;
                $height = $element['height'] ?? 50;
                $type = $element['type'] ?? '';
                // Z-index based on array position (same as designer)
                $zIndex = $index + 1;
            @endphp

            @if($type === 'text')
                @php
                    $content = $element['content'] ?? '';
                    $isRichText = !empty($element['isRichText']) || preg_match('/<[^>]+>/', $content);

                    // Replace variables in content
                    foreach ($replacements as $key => $value) {
                        $content = str_replace($key, $value, $content);
                    }

                    // Map fonts to DOMPDF compatible fonts
                    $fontFamily = $element['fontFamily'] ?? 'DejaVu Sans';
                    $fontMap = [
                        'Arial' => 'DejaVu Sans',
                        'Helvetica' => 'DejaVu Sans',
                        'Times New Roman' => 'DejaVu Serif',
                        'Times' => 'DejaVu Serif',
                        'Georgia' => 'DejaVu Serif',
                        'Courier New' => 'DejaVu Sans Mono',
                        'Courier' => 'DejaVu Sans Mono',
                    ];
                    $mappedFont = $fontMap[$fontFamily] ?? 'DejaVu Sans';

                    $fontSize = $element['fontSize'] ?? 12;
                    $color = $element['color'] ?? '#000000';
                    $bold = !empty($element['bold']);
                    $italic = !empty($element['italic']);
                    $underline = !empty($element['underline']);
                    $align = $element['align'] ?? 'left';
                    $lineHeight = $element['lineHeight'] ?? 1.5;

                    // For rich text, clean up and render HTML directly
                    // For plain text, escape and add line breaks
                    if ($isRichText) {
                        // Only strip script and dangerous tags
                        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
                        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);
                        $content = preg_replace('/on\w+="[^"]*"/i', '', $content);
                        $content = preg_replace('/on\w+=\'[^\']*\'/i', '', $content);
                    } else {
                        $content = nl2br(e($content));
                    }
                @endphp
                <div class="canvas-element" style="left: {{ $x }}pt; top: {{ $y }}pt; width: {{ $width }}pt; height: {{ $height }}pt; z-index: {{ $zIndex }};">
                    <div class="text-element" style="font-family: '{{ $mappedFont }}', sans-serif; font-size: {{ $fontSize }}pt; color: {{ $color }}; font-weight: {{ $bold ? 'bold' : 'normal' }}; font-style: {{ $italic ? 'italic' : 'normal' }}; text-decoration: {{ $underline ? 'underline' : 'none' }}; text-align: {{ $align }}; line-height: {{ $lineHeight }};">{!! $content !!}</div>
                </div>

            @elseif($type === 'line')
                @php
                    $lineColor = $element['color'] ?? '#000000';
                    $thickness = $element['thickness'] ?? 1;
                    $rotation = $element['rotation'] ?? 0;
                    // For DOMPDF: position line in center of container without transforms
                    $lineThickness = max(1, $thickness);
                    $topOffset = ($height - $lineThickness) / 2;
                @endphp
                <div class="canvas-element" style="left: {{ $x }}pt; top: {{ $y }}pt; width: {{ $width }}pt; height: {{ $height }}pt; z-index: {{ $zIndex }}; @if($rotation != 0) transform: rotate({{ $rotation }}deg); transform-origin: center center; @endif">
                    <div style="position: absolute; top: {{ $topOffset }}pt; left: 0; width: 100%; height: {{ $lineThickness }}pt; background: {{ $lineColor }};"></div>
                </div>

            @elseif($type === 'box')
                @php
                    $fill = $element['fill'] ?? '#ffffff';
                    $borderColor = $element['borderColor'] ?? '#000000';
                    $borderWidth = $element['borderWidth'] ?? 1;
                @endphp
                <div class="canvas-element" style="left: {{ $x }}pt; top: {{ $y }}pt; width: {{ $width }}pt; height: {{ $height }}pt; z-index: {{ $zIndex }}; background: {{ $fill }}; border: {{ $borderWidth }}pt solid {{ $borderColor }};"></div>

            @elseif($type === 'image' && !empty($element['src']))
                <div class="canvas-element" style="left: {{ $x }}pt; top: {{ $y }}pt; width: {{ $width }}pt; height: {{ $height }}pt; z-index: {{ $zIndex }};">
                    <img src="{{ $element['src'] }}" style="width: 100%; height: 100%;">
                </div>

            @elseif($type === 'table')
                @php
                    // Check if using new schema (columns and rows arrays) or legacy format
                    $hasNewSchema = isset($element['columns']) && is_array($element['columns']) && isset($element['rows']) && is_array($element['rows']);

                    // Common styling
                    $tableBorderColor = $element['borderColor'] ?? '#000000';
                    $tableBorderWidth = $element['borderWidth'] ?? 1;
                    $tableCellPadding = $element['cellPadding'] ?? 6;
                    // Ensure minimum font size of 9pt (browsers enforce minimum ~10px, DOMPDF doesn't)
                    $storedFontSize = $element['fontSize'] ?? 10;
                    $tableFontSize = max($storedFontSize, 9);
                    $tableHeaderBg = $element['headerBg'] ?? '#f0f0f0';
                    $hasHeader = $element['hasHeader'] ?? true;
                    $tableFontFamily = $element['fontFamily'] ?? 'Arial';
                    $tableTextAlign = $element['textAlign'] ?? 'left';
                    $tableVerticalAlign = $element['verticalAlign'] ?? 'middle';

                    // Map fonts
                    $fontMap = [
                        'Arial' => 'DejaVu Sans',
                        'Helvetica' => 'DejaVu Sans',
                        'Times New Roman' => 'DejaVu Serif',
                        'Times' => 'DejaVu Serif',
                        'Georgia' => 'DejaVu Serif',
                        'Courier New' => 'DejaVu Sans Mono',
                        'Courier' => 'DejaVu Sans Mono',
                    ];
                    $mappedTableFont = $fontMap[$tableFontFamily] ?? 'DejaVu Sans';

                    // Calculate exact pt dimensions for DOMPDF
                    // Account for borders in total width/height
                    $tableWidth = $width;
                    $tableHeight = $height;

                    if ($hasNewSchema) {
                        $columns = $element['columns'];
                        $tableRows = $element['rows'];
                        $numCols = count($columns);
                        $numRows = count($tableRows);

                        // Calculate column widths in pt (proportional based on percentages)
                        $totalColPercent = array_sum(array_column($columns, 'width'));
                        if ($totalColPercent <= 0) $totalColPercent = 100;

                        $colWidthsPt = [];
                        foreach ($columns as $col) {
                            $pct = ($col['width'] ?? (100 / $numCols));
                            $colWidthsPt[] = ($pct / $totalColPercent) * $tableWidth;
                        }

                        // Use stored row heights directly (they are in px which equals pt at 72 DPI)
                        // The designer syncs element.height with sum of row heights
                        $rowHeightsPt = [];
                        foreach ($tableRows as $row) {
                            $rowHeightsPt[] = $row['height'] ?? 30;
                        }

                        // Use the actual sum of row heights as table height
                        // This matches what the designer displays
                        $actualTableHeight = array_sum($rowHeightsPt);
                    } else {
                        // Legacy format
                        $numRows = is_numeric($element['rows']) ? (int)$element['rows'] : 3;
                        $numCols = $element['cols'] ?? 3;
                        $cellData = $element['cellData'] ?? [];
                        $colWidthsRaw = $element['colWidths'] ?? array_fill(0, $numCols, 100 / $numCols);

                        if (!is_array($colWidthsRaw) || count($colWidthsRaw) !== $numCols) {
                            $colWidthsRaw = array_fill(0, $numCols, 100 / $numCols);
                        }

                        $totalColPercent = array_sum($colWidthsRaw);
                        if ($totalColPercent <= 0) $totalColPercent = 100;

                        $colWidthsPt = [];
                        foreach ($colWidthsRaw as $w) {
                            $colWidthsPt[] = ($w / $totalColPercent) * $tableWidth;
                        }

                        // Equal row heights
                        $rowHeightsPt = array_fill(0, $numRows, $tableHeight / $numRows);
                        $actualTableHeight = $tableHeight;
                    }
                @endphp
                {{-- Table with fixed pt dimensions for DOMPDF --}}
                <div class="table-wrapper" style="position: absolute; left: {{ $x }}pt; top: {{ $y }}pt; width: {{ $tableWidth }}pt; height: {{ $tableHeight }}pt; z-index: {{ $zIndex }};">
                    <table cellspacing="0" cellpadding="0" border="1" style="width: {{ $tableWidth }}pt; height: {{ $tableHeight }}pt; border-collapse: collapse; font-family: '{{ $mappedTableFont }}', sans-serif; border: {{ $tableBorderWidth }}pt solid {{ $tableBorderColor }};">
                        @if($hasNewSchema)
                            @foreach($tableRows as $rowIndex => $row)
                                @php
                                    $rowH = round($rowHeightsPt[$rowIndex]);
                                @endphp
                                <tr height="{{ $rowH }}" style="height: {{ $rowH }}pt;">
                                    @foreach($columns as $colIndex => $col)
                                        @php
                                            $cellContent = $row['cells'][$col['id']] ?? '';
                                            // Replace variables
                                            foreach ($replacements as $key => $value) {
                                                $cellContent = str_replace($key, $value, $cellContent);
                                            }
                                            // Clean up HTML
                                            $hasHtml = preg_match('/<[^>]+>/', $cellContent);
                                            if ($hasHtml) {
                                                // Remove trailing <br> tags
                                                $cellContent = preg_replace('/<br\s*\/?>\s*$/i', '', $cellContent);
                                                // Remove script/style tags
                                                $cellContent = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cellContent);
                                                $cellContent = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $cellContent);
                                                $cellContent = preg_replace('/on\w+="[^"]*"/i', '', $cellContent);
                                                // Remove text-transform style (not supported by DOMPDF)
                                                $cellContent = preg_replace('/style="[^"]*text-transform:\s*uppercase[^"]*"/i', '', $cellContent);
                                                $cellContent = preg_replace('/class="var-uppercase"/i', '', $cellContent);
                                            } else {
                                                $cellContent = nl2br(e($cellContent));
                                            }
                                            $isHeaderRow = $hasHeader && $rowIndex === 0;
                                            $bgStyle = $isHeaderRow ? "background-color: {$tableHeaderBg};" : '';
                                            $fontWeight = $isHeaderRow ? 'font-weight: bold;' : '';
                                            $cellW = round($colWidthsPt[$colIndex]);
                                            $cellH = round($rowHeightsPt[$rowIndex]);
                                        @endphp
                                        <td width="{{ $cellW }}" height="{{ $cellH }}" style="width: {{ $cellW }}pt; height: {{ $cellH }}pt; border: {{ $tableBorderWidth }}pt solid {{ $tableBorderColor }}; padding: {{ $tableCellPadding }}pt; font-size: {{ $tableFontSize }}pt; line-height: 1.3; {{ $bgStyle }} {{ $fontWeight }} vertical-align: middle; text-align: {{ $tableTextAlign }};">{!! $cellContent !!}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                            @for($r = 0; $r < $numRows; $r++)
                                @php
                                    $rowH = round($rowHeightsPt[$r]);
                                @endphp
                                <tr style="height: {{ $rowH }}pt;">
                                    @for($c = 0; $c < $numCols; $c++)
                                        @php
                                            $cellContent = '';
                                            if (isset($cellData[$r]) && is_array($cellData[$r]) && isset($cellData[$r][$c])) {
                                                $cellContent = $cellData[$r][$c];
                                            } elseif (isset($cellData[$r . '_' . $c])) {
                                                $cellContent = $cellData[$r . '_' . $c];
                                            }
                                            // Replace variables
                                            foreach ($replacements as $key => $value) {
                                                $cellContent = str_replace($key, $value, $cellContent);
                                            }
                                            // Clean up HTML
                                            $hasHtml = preg_match('/<[^>]+>/', $cellContent);
                                            if ($hasHtml) {
                                                // Remove trailing <br> tags
                                                $cellContent = preg_replace('/<br\s*\/?>\s*$/i', '', $cellContent);
                                                // Remove script/style tags
                                                $cellContent = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cellContent);
                                                $cellContent = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $cellContent);
                                                $cellContent = preg_replace('/on\w+="[^"]*"/i', '', $cellContent);
                                                // Remove text-transform style (not supported by DOMPDF) - content already uppercased via :upper
                                                $cellContent = preg_replace('/style="[^"]*text-transform:\s*uppercase[^"]*"/i', '', $cellContent);
                                                $cellContent = preg_replace('/class="var-uppercase"/i', '', $cellContent);
                                            } else {
                                                $cellContent = nl2br(e($cellContent));
                                            }
                                            $isHeaderRow = $hasHeader && $r === 0;
                                            $bgStyle = $isHeaderRow ? "background-color: {$tableHeaderBg};" : '';
                                            $fontWeight = $isHeaderRow ? 'font-weight: bold;' : '';
                                            $cellW = round($colWidthsPt[$c]);
                                            $cellH = round($rowHeightsPt[$r]);
                                        @endphp
                                        <td width="{{ $cellW }}" height="{{ $cellH }}" style="width: {{ $cellW }}pt; height: {{ $cellH }}pt; border: {{ $tableBorderWidth }}pt solid {{ $tableBorderColor }}; padding: {{ $tableCellPadding }}pt; font-size: {{ $tableFontSize }}pt; line-height: 1.3; {{ $bgStyle }} {{ $fontWeight }} vertical-align: middle; text-align: {{ $tableTextAlign }};">{!! $cellContent !!}</td>
                                    @endfor
                                </tr>
                            @endfor
                        @endif
                    </table>
                </div>
            @endif
        @endforeach
    </div>
@else
    {{-- Fallback to old template format --}}
    @if($template->settings['show_logo'] ?? true)
    <div class="header">
        @if(file_exists(public_path('images/umpsa-logo.png')))
            <img src="{{ public_path('images/umpsa-logo.png') }}" alt="UMPSA Logo" style="max-height: 80px; margin-bottom: 15px;">
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
            $content = $template->body_content ?? '';
            foreach ($replacements as $key => $value) {
                $content = str_replace($key, $value, $content);
            }
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
@endif
</body>
</html>
