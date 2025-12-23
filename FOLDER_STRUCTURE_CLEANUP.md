# Folder Structure Cleanup Summary

This document summarizes the cleanup performed on the WBL Management System folder structure.

## Cleanup Date
December 14, 2025

## Removed Items

### 1. Deprecated Controllers (Old Structure)
- **Deleted:** `app/Http/Controllers/Courses/` (entire folder)
  - This contained old PPE controllers that were replaced by `Academic/PPE/` structure
  - Files removed:
    - `Courses/PPE/PpeAtEvaluationController.php`
    - `Courses/PPE/PpeIcEvaluationController.php`
    - `Courses/PPE/PpeAssessmentSettingController.php`
    - `Courses/PPE/PpeFinalScoreController.php`
    - `Courses/PPE/PpeGroupSelectionController.php`

### 2. Deprecated Models (Old Structure)
- **Deleted:** `app/Models/Courses/` (entire folder)
  - Old PPE models replaced by `Models/PPE/` structure
  - Files removed:
    - `Courses/PPE/PpeAssessmentSetting.php`
    - `Courses/PPE/PpeStudentAtMark.php`
    - `Courses/PPE/PpeStudentIcMark.php`

### 3. Deprecated Views (Old Structure)
- **Deleted:** `resources/views/courses/` (entire folder)
  - Old PPE views replaced by `academic/ppe/` structure

### 4. Empty Controller Folders
- **Deleted:**
  - `app/Http/Controllers/FYP/`
  - `app/Http/Controllers/LI/`
  - `app/Http/Controllers/Common/`
  - `app/Http/Controllers/Academic/IP/`
  - `app/Http/Controllers/Academic/OSH/`

### 5. Empty Model Folders
- **Deleted:**
  - `app/Models/FYP/`
  - `app/Models/IP/`
  - `app/Models/OSH/`
  - `app/Models/LI/`
  - `app/Models/Industry/`

### 6. Empty View Folders
- **Deleted:**
  - `resources/views/common/`
  - `resources/views/fyp/`
  - `resources/views/li/`
  - `resources/views/academic/ppe/questions/`
  - `resources/views/academic/fyp/ic/`
  - `resources/views/academic/ip/ic/`
  - `resources/views/academic/osh/ic/`

### 7. Empty Policy Folders
- **Deleted:**
  - `app/Policies/Common/`
  - `app/Policies/Courses/` (entire folder)
  - `app/Policies/FYP/`
  - `app/Policies/IC/`
  - `app/Policies/IP/`
  - `app/Policies/LI/`
  - `app/Policies/OSH/`

## Current Clean Structure

### Controllers
```
app/Http/Controllers/
├── Academic/
│   ├── AssessmentController.php
│   └── PPE/
│       ├── PpeAssessmentSettingController.php
│       ├── PpeAtEvaluationController.php
│       ├── PpeAuditController.php
│       ├── PpeFinalisationController.php
│       ├── PpeFinalScoreController.php
│       ├── PpeGroupSelectionController.php
│       ├── PpeIcEvaluationController.php
│       ├── PpeModerationController.php
│       ├── PpeProgressController.php
│       ├── PpeReportsController.php
│       ├── PpeScheduleController.php
│       └── PpeStudentPerformanceController.php
├── Admin/
│   ├── AssessmentController.php
│   └── StudentAssignmentController.php
├── Auth/
│   ├── AuthenticatedSessionController.php
│   └── RegisteredUserController.php
├── Industry/
│   └── MyStudentsController.php
├── Lecturer/
│   └── MyStudentsController.php
├── Wbl/
│   └── WblAssignmentController.php
├── CompanyController.php
├── Controller.php
├── DashboardController.php
├── GroupController.php (kept for potential future use)
├── SearchController.php
├── StudentController.php
└── StudentProfileController.php
```

### Models
```
app/Models/
├── PPE/
│   ├── PpeActivityLog.php
│   ├── PpeAssessmentSetting.php
│   ├── PpeAssessmentWindow.php
│   ├── PpeAuditLog.php
│   ├── PpeModerationRecord.php
│   ├── PpeResultFinalisation.php
│   ├── PpeStudentAtMark.php
│   └── PpeStudentIcMark.php
├── Assessment.php
├── AssessmentRubric.php
├── Company.php
├── CompanyContact.php
├── CompanyDocument.php
├── CompanyNote.php
├── CourseSetting.php
├── LecturerCourseAssignment.php
├── Moa.php
├── Mou.php
├── Student.php
├── StudentAssessmentMark.php
├── StudentAssessmentRubricMark.php
├── StudentCourseAssignment.php
├── User.php
└── WblGroup.php
```

### Views
```
resources/views/
├── academic/
│   ├── assessments/
│   ├── fyp/
│   ├── ip/
│   ├── li/
│   ├── osh/
│   └── ppe/
├── admin/
│   ├── assessments/
│   └── students/
├── auth/
├── companies/
│   └── tabs/
├── components/
├── groups/ (kept for potential future use)
├── industry/
├── layouts/
├── lecturer/
├── student/
├── students/
│   └── profile/
└── wbl/
    └── assign/
```

### Policies
```
app/Policies/
├── PPE/
│   ├── PpeAssessmentSettingPolicy.php
│   ├── PpeStudentAtMarkPolicy.php
│   └── PpeStudentIcMarkPolicy.php
└── StudentPolicy.php
```

## Notes

1. **GroupController** and `resources/views/groups/` are kept as they may be used in the future, even though they're not currently in active routes.

2. **Admin Controllers** (`Admin/AssessmentController.php` and `Admin/StudentAssignmentController.php`) are kept as they are still referenced in `routes/admin.php`.

3. All empty folders have been removed to keep the structure clean and maintainable.

4. The structure now follows a clear pattern:
   - **Academic modules** use `Academic/{Module}/` structure
   - **PPE-specific models** are in `Models/PPE/`
   - **Views** follow the same structure as controllers
   - **Policies** are organized by module

## Benefits

- **Cleaner structure:** No empty or deprecated folders
- **Easier navigation:** Clear separation of concerns
- **Better maintainability:** Related files are grouped logically
- **Reduced confusion:** No duplicate or conflicting files
- **Scalable:** Easy to add new modules following the same pattern

