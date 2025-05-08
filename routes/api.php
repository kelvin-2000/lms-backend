<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseVideoController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\DiscussionReplyController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\JobOpportunityController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\MentorshipProgramController;
use App\Http\Controllers\MentorshipApplicationController;
use App\Http\Controllers\MentorshipSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Routes
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/featured', [CourseController::class, 'featured']);
Route::get('/courses/popular', [CourseController::class, 'popular']);
Route::get('/courses/latest', [CourseController::class, 'latest']);
Route::get('/courses/{course}', [CourseController::class, 'show']);
Route::get('/instructors/{instructorId}/related-courses', [CourseController::class, 'instructorRelatedCourses']);
Route::get('/events/upcoming', [EventController::class, 'upcoming']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/job-opportunities/open', [JobOpportunityController::class, 'open']);
Route::get('/job-opportunities/{jobOpportunity}', [JobOpportunityController::class, 'show']);
Route::get('/mentorship-programs/open', [MentorshipProgramController::class, 'open']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/mentorship-programs/debug/{id}', function($id) {
    $program = \App\Models\MentorshipProgram::find($id);
    if (!$program) {
        return response()->json(['error' => "Program ID $id not found"], 404);
    }
    return response()->json([
        'id' => $program->id,
        'title' => $program->title,
        'status' => $program->status,
        'raw_record' => \DB::table('mentorship_programs')->where('id', $id)->first()
    ]);
});
Route::get('/mentorship-programs/{mentorshipProgram}', [MentorshipProgramController::class, 'show']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::apiResource('users', UserController::class);
    
    // Course Routes - Instructor and Admin only
    Route::middleware('role:instructor')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{course}', [CourseController::class, 'update']);
        Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
    });
    Route::get('/courses/{course}/videos', [CourseController::class, 'videos']);
    Route::get('/courses/{course}/discussions', [CourseController::class, 'discussions']);
    Route::get('/courses/{course}/enrollments', [CourseController::class, 'enrollments']);
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll']);
    
    // Course Video Routes - Instructor and Admin only
    Route::middleware('role:instructor')->group(function () {
        Route::apiResource('course-videos', CourseVideoController::class);
        Route::post('/course-videos/reorder', [CourseVideoController::class, 'reorder']);
    });
    
    // Discussion Routes
    Route::apiResource('discussions', DiscussionController::class);
    Route::get('/discussions/{discussion}/replies', [DiscussionController::class, 'replies']);
    
    // Discussion Reply Routes
    Route::apiResource('discussion-replies', DiscussionReplyController::class);
    
    // Enrollment Routes
    Route::apiResource('enrollments', EnrollmentController::class);
    Route::put('/enrollments/{enrollment}/progress', [EnrollmentController::class, 'updateProgress']);
    Route::put('/enrollments/{enrollment}/complete', [EnrollmentController::class, 'complete']);
    Route::put('/enrollments/{enrollment}/cancel', [EnrollmentController::class, 'cancel']);
    Route::post('/student/enroll', [EnrollmentController::class, 'studentEnroll']);
    Route::post('/enrollment/check-status', [EnrollmentController::class, 'checkStatus']);
    
    // Event Routes - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
    });
    Route::get('/events/{event}/registrations', [EventController::class, 'registrations']);
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'register']);
    
    // Event Registration Routes
    Route::apiResource('event-registrations', EventRegistrationController::class);
    Route::put('/event-registrations/{eventRegistration}/attend', [EventRegistrationController::class, 'markAttended']);
    Route::put('/event-registrations/{eventRegistration}/cancel', [EventRegistrationController::class, 'cancel']);
    Route::post('/event-registration/check-status', [EventRegistrationController::class, 'checkStatus']);
    
    // Job Opportunity Routes - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/job-opportunities', [JobOpportunityController::class, 'store']);
        Route::put('/job-opportunities/{jobOpportunity}', [JobOpportunityController::class, 'update']);
        Route::delete('/job-opportunities/{jobOpportunity}', [JobOpportunityController::class, 'destroy']);
    });
    Route::get('/job-opportunities/{jobOpportunity}/applications', [JobOpportunityController::class, 'applications']);
    
    // Job Application Routes
    Route::apiResource('job-applications', JobApplicationController::class);
    Route::put('/job-applications/{jobApplication}/status', [JobApplicationController::class, 'updateStatus']);
    
    // Mentorship Program Routes - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/mentorship-programs', [MentorshipProgramController::class, 'store']);
        Route::put('/mentorship-programs/{mentorshipProgram}', [MentorshipProgramController::class, 'update']);
        Route::delete('/mentorship-programs/{mentorshipProgram}', [MentorshipProgramController::class, 'destroy']);
    });
    Route::get('/mentorship-programs/{mentorshipProgram}/applications', [MentorshipProgramController::class, 'applications']);
    Route::get('/mentorship-programs/{mentorshipProgram}/sessions', [MentorshipProgramController::class, 'sessions']);
    Route::post('/mentorship-programs/{mentorshipProgram}/apply', [MentorshipApplicationController::class, 'apply']);
    
    // Mentorship Application Routes
    Route::apiResource('mentorship-applications', MentorshipApplicationController::class);
    Route::put('/mentorship-applications/{mentorshipApplication}/accept', [MentorshipApplicationController::class, 'accept']);
    Route::put('/mentorship-applications/{mentorshipApplication}/reject', [MentorshipApplicationController::class, 'reject']);
    Route::post('/mentorship-application/check-status', [MentorshipApplicationController::class, 'checkStatus']);
    
    // Mentorship Session Routes
    Route::apiResource('mentorship-sessions', MentorshipSessionController::class);
    Route::put('/mentorship-sessions/{mentorshipSession}/complete', [MentorshipSessionController::class, 'complete']);
    Route::put('/mentorship-sessions/{mentorshipSession}/cancel', [MentorshipSessionController::class, 'cancel']);
});
