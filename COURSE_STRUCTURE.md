# Course-Based Folder Structure

This project has been reorganized to support multiple courses with a clear, scalable structure.

## Folder Structure

### Controllers
```
app/Http/Controllers/
├── Courses/
│   ├── PPE/              # Professional Practice & Ethics
│   │   ├── PpeAtEvaluationController.php
│   │   ├── PpeIcEvaluationController.php
│   │   ├── PpeAssessmentSettingController.php
│   │   ├── PpeIcQuestionController.php
│   │   ├── PpeFinalScoreController.php
│   │   └── PpeGroupSelectionController.php
│   ├── OSH/              # Occupational Safety & Health (Future)
│   ├── IP/               # Industrial Project (Future)
│   └── IndustrialTraining/  # Industrial Training (Future)
```

### Models
```
app/Models/
├── Courses/
│   ├── PPE/
│   │   ├── PpeAssessmentSetting.php
│   │   ├── PpeIcQuestion.php
│   │   ├── PpeStudentAtMark.php
│   │   └── PpeStudentIcMark.php
│   ├── OSH/              # (Future)
│   ├── IP/               # (Future)
│   └── IndustrialTraining/  # (Future)
```

### Policies
```
app/Policies/
├── Courses/
│   ├── PPE/
│   │   ├── PpeAssessmentSettingPolicy.php
│   │   ├── PpeIcQuestionPolicy.php
│   │   ├── PpeStudentAtMarkPolicy.php
│   │   └── PpeStudentIcMarkPolicy.php
│   ├── OSH/              # (Future)
│   ├── IP/               # (Future)
│   └── IndustrialTraining/  # (Future)
```

### Views
```
resources/views/
├── courses/
│   ├── ppe/
│   │   ├── at/
│   │   ├── ic/
│   │   ├── final/
│   │   ├── groups/
│   │   ├── questions/
│   │   └── settings/
│   ├── osh/              # (Future)
│   ├── ip/               # (Future)
│   └── industrial-training/  # (Future)
```

## Namespace Conventions

### Controllers
- `App\Http\Controllers\Courses\PPE\*`
- `App\Http\Controllers\Courses\OSH\*`
- `App\Http\Controllers\Courses\IP\*`
- `App\Http\Controllers\Courses\IndustrialTraining\*`

### Models
- `App\Models\Courses\PPE\*`
- `App\Models\Courses\OSH\*`
- `App\Models\Courses\IP\*`
- `App\Models\Courses\IndustrialTraining\*`

### Policies
- `App\Policies\Courses\PPE\*`
- `App\Policies\Courses\OSH\*`
- `App\Policies\Courses\IP\*`
- `App\Policies\Courses\IndustrialTraining\*`

## Route Structure

Routes are organized by course prefix:
- `/ppe/*` - Professional Practice & Ethics
- `/osh/*` - Occupational Safety & Health (Future)
- `/ip/*` - Industrial Project (Future)
- `/industrial-training/*` - Industrial Training (Future)

## Adding New Courses

To add a new course (e.g., OSH):

1. **Create Controller Structure:**
   ```bash
   mkdir -p app/Http/Controllers/Courses/OSH
   ```

2. **Create Model Structure:**
   ```bash
   mkdir -p app/Models/Courses/OSH
   ```

3. **Create Policy Structure:**
   ```bash
   mkdir -p app/Policies/Courses/OSH
   ```

4. **Create View Structure:**
   ```bash
   mkdir -p resources/views/courses/osh
   ```

5. **Update Routes:**
   Add routes in `routes/web.php` with the course prefix:
   ```php
   Route::prefix('osh')->name('osh.')->group(function () {
       // OSH routes
   });
   ```

6. **Update AuthServiceProvider:**
   Register policies in `app/Providers/AuthServiceProvider.php`

## Benefits

- **Scalability:** Easy to add new courses without cluttering the main structure
- **Organization:** Clear separation of concerns by course
- **Maintainability:** Related files are grouped together
- **Consistency:** Uniform structure across all courses

