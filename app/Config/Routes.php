<?php

declare(strict_types=1);

/**
 * CodeIgniter 4 Routes Configuration
 *
 * This file handles the mapping of URLs to specific Controller methods.
 * It uses Route Grouping for security filtering, namespace organization,
 * and performance optimization.
 *
 * @package    App
 * @author     Your Name / Team
 * @var RouteCollection $routes
 */

use CodeIgniter\Router\RouteCollection;

// --- Public Controllers ---
use App\Controllers\Public\{
    AccountVerificationController,
    ForgotPasswordController,
    HomeController,
    LoginController,
    LogoutController,
    RegisterController,
    RegistrationTermsController,
    ResetPasswordController,
    UnauthorizedController
};


// --- Root Controllers ---
use App\Controllers\ProfileController;

// --- Administrator Controllers ---
use App\Controllers\Administrator\{
    AcademicYearController,
    AdmissionOfferingController,
    AdmissionPeriodController,
    AdmissionPathwayController,
    ApplicantTypeController,
    ApplicationSettingController,
    BranchController,
    DashboardController,
    DocumentController,
    EducationProgramController,
    EducationTrackController,
    EntryGradeController,
    GeneralDocumentRequirementController,
    GeneralEntranceExamScoreController,
    GeneralRequirementController,
    GeneralSubjectGradeRequirementController,
    OperatorController,
    RegistrantController,

    SchoolController,
    SpecificDocumentRequirementController,
    SpecificEntranceExamScoreController,
    StudentController,
    SubjectController,
    SpecificSubjectGradeRequirementController,
    AdmissionStatisticReportController
};

// --- Student Controllers ---
use App\Controllers\Student\{
    AddressController,
    AdmissionController,
    ConfirmationController,
    ContactController,
    DashboardController as StudentDashboard,
    DocumentRequirementController,
    GuardianController,
    ParentController,
    ProfileController as StudentProfileController,
    SchoolController as StudentSchoolController,
    SubjectGradeRequirementController
};

/**
 * --------------------------------------------------------------------
 * Public Routes
 * --------------------------------------------------------------------
 * Routes accessible by guests/unauthenticated users.
 */
$routes->get('/', [HomeController::class, 'index']);
$routes->get('upload/(:any)', [HomeController::class, 'download']);
$routes->get('unauthorized', [UnauthorizedController::class, 'index']);

$routes->group('', static function (RouteCollection $routes) {
    /** @route GET|POST /register */
    $routes->get('register', [RegisterController::class, 'index']);
    $routes->post('register', [RegisterController::class, 'index']);

    /** @route GET|POST /login */
    $routes->post('login', [LoginController::class, 'index']);

    /** @route GET|POST /forgot-password */
    $routes->get('forgot-password', [ForgotPasswordController::class, 'sendPasswordResetLink']);
    $routes->post('forgot-password', [ForgotPasswordController::class, 'sendPasswordResetLink']);
    $routes->post('send-password-reset-link', [ForgotPasswordController::class, 'sendPasswordResetLink']);

    /** @route GET /account-verification/(:any) */
    $routes->get('account-verification/(:any)', [AccountVerificationController::class, 'index']);

    /** @route GET /reset-password/(:any) */
    $routes->get('reset-password/(:any)', [ResetPasswordController::class, 'index']);
    $routes->post('reset-password/verify/(:any)', [ResetPasswordController::class, 'verify']);

    $routes->get('registration-terms/(:segment)', [RegistrationTermsController::class, 'file']);
});

/**
 * --------------------------------------------------------------------
 * Authenticated Routes
 * --------------------------------------------------------------------
 * Requires user to be logged in. Roles are handled via 'auth' filter.
 */
$routes->group('', ['filter' => 'auth:admin,operator,student'], static function (RouteCollection $routes) {

    /** @route GET /dashboard */
    $routes->get('dashboard', [HomeController::class, 'dashboard']);

    /** @route POST /logout (logging out changes state, so it needs the CSRF token; GET only redirects) */
    $routes->post('logout', [LogoutController::class, 'index']);
    $routes->get('logout', [LogoutController::class, 'redirectToDashboard']);

    /**
     * --------------------------------------------------------------------
     * Administrator & Operator Domain
     * --------------------------------------------------------------------
     */
    $routes->group('administrator', ['filter' => 'auth:admin,operator'], static function (RouteCollection $routes) {

        /** @route GET /administrator/dashboard */
        $routes->get('dashboard', [DashboardController::class, 'index']);

        /** @route GET|POST /administrator/profile */
        $routes->get('profile', [ProfileController::class, 'index']);
        $routes->post('profile/save', [ProfileController::class, 'save']);

        /**
         * Helper to register standard CRUD routes dynamically.
         * Minimizes code duplication for master data modules.
         * * @param RouteCollection $routes
         * @param string $path Target URL path
         * @param string $controller Fully qualified controller class name
         */
        $registerStandardCrud = function (RouteCollection $routes, string $path, string $controller) {
            $routes->group($path, static function (RouteCollection $routes) use ($controller) {
                $routes->get('/', [$controller, 'index']);
                foreach (['lists', 'form', 'save', 'delete', 'restore'] as $action) {
                    $routes->post($action, [$controller, $action]);
                }
            });
        };

        /** @var array $crudControllers List of controllers following the standard CRUD pattern */
        $crudControllers = [
            'admission-pathways' => AdmissionPathwayController::class,
            'applicant-types' => ApplicantTypeController::class,
            'branches' => BranchController::class,
            'subjects' => SubjectController::class,
            'documents' => DocumentController::class,
            'schools' => SchoolController::class,

            'academic-years' => AcademicYearController::class,
            'entry-grades' => EntryGradeController::class,
            'education-tracks' => EducationTrackController::class,
            'education-programs' => EducationProgramController::class,

            'admission-periods' => AdmissionPeriodController::class,
            'admission-offerings' => AdmissionOfferingController::class,
        ];

        foreach ($crudControllers as $path => $ctrl) {
            $registerStandardCrud($routes, $path, $ctrl);
        }

        /**
         * Administrator-Only Routes
         * (the sidebar shows these menus to administrators only)
         */
        $routes->group('', ['filter' => 'auth:admin'], static function (RouteCollection $routes) use ($registerStandardCrud) {
            $registerStandardCrud($routes, 'operators', OperatorController::class);

            /** @section Application Configuration */
            $routes->get('application-settings', [ApplicationSettingController::class, 'index']);
            $routes->post('application-settings/save', [ApplicationSettingController::class, 'save']);
            $routes->post('application-settings/upload', [ApplicationSettingController::class, 'upload']);
            $routes->get('application-settings/download/(:any)', [ApplicationSettingController::class, 'download']);

            /** @section Registrant Records */
            $routes->group('registrants', static function (RouteCollection $routes) {
                $routes->get('/', [RegistrantController::class, 'index']);
                $routes->post('lists', [RegistrantController::class, 'lists']);
                $routes->post('form', [RegistrantController::class, 'form']);
                $routes->post('save', [RegistrantController::class, 'save']);
                $routes->post('update-subject-score', [RegistrantController::class, 'updateSubjectScore']);
                $routes->post('entrance-exam-subjects', [RegistrantController::class, 'listEntranceExamSubjects']);
                $routes->post('update-entrance-exam-score', [RegistrantController::class, 'updateEntranceExamScore']);
                $routes->get('download/(:any)', [RegistrantController::class, 'download']);
                $routes->get('print/(:num)', [RegistrantController::class, 'print']);
                $routes->post('form-student', [RegistrantController::class, 'formStudent']);
                $routes->post('save-student', [RegistrantController::class, 'saveStudent']);
                $routes->post('provinces', [RegistrantController::class, 'provinces']);
                $routes->post('districts', [RegistrantController::class, 'districts']);
                $routes->post('subdistricts', [RegistrantController::class, 'subdistricts']);
            });

            /** @section Admission Statistics Report */
            $routes->get('admission-statistic-report', [AdmissionStatisticReportController::class, 'index']);
            $routes->post('admission-statistic-report/data', [AdmissionStatisticReportController::class, 'data']);
        });

        /**
         * Geographical Data (Provinces, Districts, Subdistricts)
         * Pattern: AJAX-based lists and general index.
         */
        foreach (['provinces', 'districts', 'subdistricts'] as $geo) {
            $baseName = ucfirst(substr($geo, 0, -1));
            $controller = "App\Controllers\Administrator\\" . $baseName . 'Controller';

            $routes->group($geo, static function (RouteCollection $routes) use ($controller) {
                $routes->get('/', [$controller, 'index']);
                $routes->post('lists', [$controller, 'lists']);
                $routes->get('(:segment)', [$controller, 'index']);
            });
        }

        $routes->get('general-requirements', [GeneralRequirementController::class, 'index']);
        $routes->post('general-requirements/lists', [GeneralRequirementController::class, 'lists']);

        $routes->get('general-subject-grade-requirements/(:num)', [GeneralSubjectGradeRequirementController::class, 'index']);
        $routes->post('general-subject-grade-requirements/lists', [GeneralSubjectGradeRequirementController::class, 'lists']);
        $routes->post('general-subject-grade-requirements/form', [GeneralSubjectGradeRequirementController::class, 'form']);
        $routes->post('general-subject-grade-requirements/save', [GeneralSubjectGradeRequirementController::class, 'save']);

        $routes->get('general-entrance-exam-scores/(:num)', [GeneralEntranceExamScoreController::class, 'index']);
        $routes->post('general-entrance-exam-scores/lists', [GeneralEntranceExamScoreController::class, 'lists']);
        $routes->post('general-entrance-exam-scores/form', [GeneralEntranceExamScoreController::class, 'form']);
        $routes->post('general-entrance-exam-scores/save', [GeneralEntranceExamScoreController::class, 'save']);

        $routes->get('general-document-requirements/(:num)', [GeneralDocumentRequirementController::class, 'index']);
        $routes->post('general-document-requirements/lists', [GeneralDocumentRequirementController::class, 'lists']);
        $routes->post('general-document-requirements/form', [GeneralDocumentRequirementController::class, 'form']);
        $routes->post('general-document-requirements/save', [GeneralDocumentRequirementController::class, 'save']);


        $routes->get('specific-subject-grade-requirements/(:num)', [SpecificSubjectGradeRequirementController::class, 'index']);
        $routes->post('specific-subject-grade-requirements/lists', [SpecificSubjectGradeRequirementController::class, 'lists']);
        $routes->post('specific-subject-grade-requirements/form', [SpecificSubjectGradeRequirementController::class, 'form']);
        $routes->post('specific-subject-grade-requirements/save', [SpecificSubjectGradeRequirementController::class, 'save']);

        $routes->get('specific-entrance-exam-scores/(:num)', [SpecificEntranceExamScoreController::class, 'index']);
        $routes->post('specific-entrance-exam-scores/lists', [SpecificEntranceExamScoreController::class, 'lists']);
        $routes->post('specific-entrance-exam-scores/form', [SpecificEntranceExamScoreController::class, 'form']);
        $routes->post('specific-entrance-exam-scores/save', [SpecificEntranceExamScoreController::class, 'save']);

        $routes->get('specific-document-requirements/(:num)', [SpecificDocumentRequirementController::class, 'index']);
        $routes->post('specific-document-requirements/lists', [SpecificDocumentRequirementController::class, 'lists']);
        $routes->post('specific-document-requirements/form', [SpecificDocumentRequirementController::class, 'form']);
        $routes->post('specific-document-requirements/save', [SpecificDocumentRequirementController::class, 'save']);

        /** @section Student Management Modules */
        $routes->group('students', static function (RouteCollection $routes) {
            $routes->post('lists', [StudentController::class, 'lists']);
            $routes->post('form', [StudentController::class, 'form']);
            $routes->post('save', [StudentController::class, 'save']);
            $routes->post('update-subject-score', [StudentController::class, 'updateSubjectScore']);
            $routes->get('download/(:any)', [StudentController::class, 'download']);
            $routes->get('print/(:num)', [StudentController::class, 'print']);
            $routes->get('(:num)', [StudentController::class, 'index']);
        });
    });

    /**
     * --------------------------------------------------------------------
     * Student Domain
     * --------------------------------------------------------------------
     */
    $routes->group('students', ['filter' => 'auth:student'], static function (RouteCollection $routes) {
        /** @route GET /students/dashboard */
        $routes->get('dashboard', [StudentDashboard::class, 'index']);
        $routes->get('payment-method', [StudentDashboard::class, 'paymentMethod']);
        $routes->get('registration-terms', [StudentDashboard::class, 'registrationTerms']);

        /** @var array $studentGroups Grouped configuration for student profile modules */
        $studentGroups = [
            'profile' => [StudentProfileController::class, ['save', 'provinces', 'districts']],
            'address' => [AddressController::class, ['save', 'provinces', 'districts', 'subdistricts']],
            'contact' => [ContactController::class, ['save']],
            'parents' => [ParentController::class, ['save']],
            'guardian' => [GuardianController::class, ['save']],
            'school' => [StudentSchoolController::class, ['save']],
            'admissions' => [AdmissionController::class, ['save', 'lists', 'remove']],

            'account' => [ProfileController::class, ['save']],
        ];

        foreach ($studentGroups as $path => $config) {
            [$controller, $actions] = $config;
            $routes->group($path, static function (RouteCollection $routes) use ($controller, $actions) {
                $routes->get('/', [$controller, 'index']);
                foreach ($actions as $action) {
                    $routes->post($action, [$controller, $action]);
                }
            });
        }

        /** @section Academic Requirements */
        $routes->group('subject-grade-requirements', static function (RouteCollection $routes) {
            $routes->get('/', [SubjectGradeRequirementController::class, 'index']);
            $routes->post('lists', [SubjectGradeRequirementController::class, 'lists']);
            $routes->post('save', [SubjectGradeRequirementController::class, 'save']);
        });

        /** @section File & Document Management */
        $routes->group('document-requirements', static function (RouteCollection $routes) {
            $routes->get('/', [DocumentRequirementController::class, 'index']);
            $routes->get('download/(:any)', [DocumentRequirementController::class, 'download']);
            $routes->post('lists', [DocumentRequirementController::class, 'lists']);
            $routes->post('upload', [DocumentRequirementController::class, 'upload']);
        });

        /** @section Final Confirmation & Exports */
        $routes->group('confirmation', static function (RouteCollection $routes) {
            $routes->get('/', [ConfirmationController::class, 'index']);
            foreach (['delete', 'submit', 'submitAll', 'upload', 'lists', 'details', 'print'] as $postAction) {
                $routes->post($postAction, [ConfirmationController::class, $postAction]);
            }
            $routes->get('print', [ConfirmationController::class, 'print']);
            $routes->get('download/(:any)', [ConfirmationController::class, 'download']);
        });
    });
});

/**
 * --------------------------------------------------------------------
 * --------------------------------------------------------------------
 */
