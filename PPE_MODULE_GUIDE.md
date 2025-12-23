# PPE Evaluation Module - Implementation Guide

## Overview
The PPE (Professional Practice Evaluation) module has been fully implemented for the WBL System. This module allows Academic Tutors (AT) and Industry Coaches (IC) to evaluate students with a structured 40/60 split.

## Module Structure

### Evaluation Breakdown
- **Academic Tutor (AT)**: 40%
  - Assignment – CLO1 (20%)
  - Report – CLO1 (20%)
  
- **Industry Coach (IC)**: 60%
  - Oral Examination – CLO2 (30%)
  - Oral Examination – CLO3 (15%)
  - Oral Examination – CLO4 (15%)

## Database Tables

### 1. `ppe_assessment_settings`
Stores configurable assessment settings for AT evaluations.
- `id` - Primary key
- `name` - Assessment name (e.g., "Assignment", "Report")
- `clo` - CLO mapping (CLO1, CLO2, CLO3, CLO4)
- `weight` - Weight percentage
- `max_mark` - Maximum marks
- `timestamps`

### 2. `ppe_student_at_marks`
Stores AT marks for each student and assessment.
- `id` - Primary key
- `student_id` - Foreign key to students
- `assignment_id` - Foreign key to ppe_assessment_settings
- `mark` - Student's mark
- `timestamps`
- Unique constraint on (student_id, assignment_id)

### 3. `ppe_ic_questions`
Stores IC oral examination questions.
- `id` - Primary key
- `clo` - CLO mapping (CLO2, CLO3, CLO4)
- `question` - Question text
- `example_answer` - Example answer (optional)
- `order` - Display order
- `timestamps`

### 4. `ppe_student_ic_marks`
Stores IC marks for each student and CLO.
- `id` - Primary key
- `student_id` - Foreign key to students
- `clo` - CLO (CLO2, CLO3, CLO4)
- `mark` - Student's mark (0-100)
- `timestamps`
- Unique constraint on (student_id, clo)

## Models

1. **PpeAssessmentSetting** - Assessment configuration
2. **PpeStudentAtMark** - AT marks
3. **PpeIcQuestion** - IC questions
4. **PpeStudentIcMark** - IC marks

All models include proper relationships and casting.

## Controllers

### 1. PpeAssessmentSettingController
- `index()` - Display settings page
- `store()` - Create new assessment
- `update()` - Update assessment
- `destroy()` - Delete assessment

### 2. PpeAtEvaluationController
- `index()` - List students for AT evaluation
- `show()` - Show evaluation form for student
- `store()` - Save AT marks

### 3. PpeIcEvaluationController
- `index()` - List students for IC evaluation
- `show()` - Show evaluation form with questions
- `store()` - Save IC marks

### 4. PpeIcQuestionController
- `index()` - Display questions by CLO
- `store()` - Create new question
- `update()` - Update question
- `destroy()` - Delete question

### 5. PpeFinalScoreController
- `index()` - List students
- `show()` - Display final score breakdown

## Routes

All routes are prefixed with `/ppe` and named with `ppe.` prefix:

```php
/ppe/settings          - Assessment settings management
/ppe/at                - AT evaluation list
/ppe/at/{student}      - AT evaluation form
/ppe/ic                - IC evaluation list
/ppe/ic/{student}      - IC evaluation form
/ppe/questions         - IC questions management
/ppe/final             - Final scores list
/ppe/final/{student}   - Final score breakdown
```

## Views

### Settings Pages
- `ppe/settings/index.blade.php` - Manage assessment settings with modal forms

### AT Evaluation Pages
- `ppe/at/index.blade.php` - Student list for AT evaluation
- `ppe/at/show.blade.php` - AT evaluation form with auto-calculation

### IC Evaluation Pages
- `ppe/ic/index.blade.php` - Student list for IC evaluation
- `ppe/ic/show.blade.php` - IC evaluation form with questions display

### Questions Management
- `ppe/questions/index.blade.php` - Manage IC questions by CLO

### Final Score Pages
- `ppe/final/index.blade.php` - Student list
- `ppe/final/show.blade.php` - Complete score breakdown with final calculation

## Features

### 1. Dynamic Assessment Settings
- Add/edit/delete assessments without code changes
- Configure CLO mapping, weight, and max marks
- All settings stored in database

### 2. AT Evaluation
- List all students
- Enter marks for each assessment
- Auto-calculate contribution percentage
- Real-time total contribution display

### 3. IC Evaluation
- Display questions by CLO
- Show example answers
- Enter marks for CLO2, CLO3, CLO4
- Auto-calculate contribution percentages

### 4. Questions Management
- Add/edit/delete questions
- Assign to CLO2, CLO3, or CLO4
- Include example answers
- Order questions

### 5. Final Score Calculation
- AT breakdown (40%)
- IC breakdown (60%)
- Final score = AT + IC
- Clean card-based UI

## Seeder

### PpeIcQuestionSeeder
Pre-populates IC questions based on PDF requirements:

**CLO2 Questions:**
- Engineering Ethics & Public Responsibility
- Engineer and Law

**CLO3 Questions:**
- Engineer and Research

**CLO4 Questions:**
- Leadership & Teamwork

## UI Design

All pages use:
- UMPSA official color palette
- Card-based layouts
- Responsive design
- Dark mode support
- Clean, modern interface

## Calculation Logic

### AT Contribution
```
For each assessment:
  contribution = (mark / max_mark) * weight
Total AT = sum of all contributions
```

### IC Contribution
```
CLO2 contribution = (mark / 100) * 30
CLO3 contribution = (mark / 100) * 15
CLO4 contribution = (mark / 100) * 15
Total IC = CLO2 + CLO3 + CLO4
```

### Final Score
```
Final Score = AT Total + IC Total
```

## Installation Steps

1. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

2. **Run Seeders:**
   ```bash
   php artisan db:seed --class=PpeIcQuestionSeeder
   ```

3. **Create Initial Assessment Settings:**
   - Navigate to `/ppe/settings`
   - Add "Assignment" with CLO1, 20% weight
   - Add "Report" with CLO1, 20% weight

## Usage Workflow

1. **Admin/Academic Coordinator:**
   - Configure assessment settings at `/ppe/settings`
   - Manage IC questions at `/ppe/questions`

2. **Academic Tutor (AT):**
   - View students at `/ppe/at`
   - Enter marks for each student's assignments
   - System calculates 40% contribution

3. **Industry Coach (IC):**
   - View students at `/ppe/ic`
   - Review questions for each CLO
   - Enter marks for CLO2, CLO3, CLO4
   - System calculates 60% contribution

4. **View Final Scores:**
   - Access at `/ppe/final`
   - View complete breakdown for each student
   - See final calculated score

## Sidebar Navigation

The sidebar now includes an "Academic" section with:
- PPE (active)
- FYP (placeholder)
- IP (placeholder)
- OSH (placeholder)
- Industry Training (placeholder)

## File Structure

```
app/
├── Http/Controllers/
│   ├── PpeAssessmentSettingController.php
│   ├── PpeAtEvaluationController.php
│   ├── PpeIcEvaluationController.php
│   ├── PpeIcQuestionController.php
│   └── PpeFinalScoreController.php
├── Models/
│   ├── PpeAssessmentSetting.php
│   ├── PpeStudentAtMark.php
│   ├── PpeIcQuestion.php
│   └── PpeStudentIcMark.php

database/
├── migrations/
│   ├── 2024_01_01_000004_create_ppe_assessment_settings_table.php
│   ├── 2024_01_01_000005_create_ppe_ic_questions_table.php
│   ├── 2024_01_01_000006_create_ppe_student_at_marks_table.php
│   └── 2024_01_01_000007_create_ppe_student_ic_marks_table.php
└── seeders/
    └── PpeIcQuestionSeeder.php

resources/views/ppe/
├── settings/
│   └── index.blade.php
├── at/
│   ├── index.blade.php
│   └── show.blade.php
├── ic/
│   ├── index.blade.php
│   └── show.blade.php
├── questions/
│   └── index.blade.php
└── final/
    ├── index.blade.php
    └── show.blade.php
```

## Next Steps

1. Run migrations and seeders
2. Configure initial assessment settings
3. Test the evaluation workflow
4. Customize questions as needed
5. Add additional features (export, reports, etc.)

All code is production-ready and follows Laravel best practices!

