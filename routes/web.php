<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabWorkflowController;
use App\Http\Controllers\NetworkPortalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\QualityInventoryController;
use App\Http\Controllers\SampleController;
use Illuminate\Support\Facades\Route;

// Authentication Routes (Public)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Diagnostic Lab System (Requires Login & Role Access)
Route::middleware(['auth', 'role.access'])->group(function () {
    // 1. Dashboard & Global Search
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'search'])->name('search');

    // 2. Patient Management
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::post('/patients/{patient}/documents', [PatientController::class, 'uploadDocument'])->name('patients.documents');
    Route::post('/patients/{patient}/merge', [PatientController::class, 'merge'])->name('patients.merge');

    // 3. Appointment Management
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

    // 7. Billing & Invoicing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::get('/billing/{invoice}', [BillingController::class, 'show'])->name('billing.show');
    Route::post('/billing/{invoice}/payments', [BillingController::class, 'recordPayment'])->name('billing.payments');

    // 8 & 9. Sample Collection, Accession & Phlebotomy
    Route::get('/samples', [SampleController::class, 'index'])->name('samples.index');
    Route::post('/samples', [SampleController::class, 'store'])->name('samples.store');
    Route::patch('/samples/{sample}/status', [SampleController::class, 'updateStatus'])->name('samples.status');
    Route::get('/phlebotomy', [SampleController::class, 'phlebotomy'])->name('phlebotomy.index');
    Route::post('/phlebotomy', [SampleController::class, 'storeHomeCollection'])->name('phlebotomy.store');
    Route::patch('/phlebotomy/{homeCollection}/status', [SampleController::class, 'updateHomeCollection'])->name('phlebotomy.status');

    // 10, 11, 14, 15, 17. Lab Departments, Worklist, Results, Reports & Critical Alerts
    Route::get('/departments', [LabWorkflowController::class, 'departments'])->name('lab.departments');
    Route::get('/worklist', [LabWorkflowController::class, 'worklist'])->name('lab.worklist');
    Route::patch('/worklist/{labOrder}', [LabWorkflowController::class, 'updateWorklist'])->name('lab.worklist.update');
    Route::get('/results', [LabWorkflowController::class, 'results'])->name('lab.results');
    Route::get('/results/{labOrder}', [LabWorkflowController::class, 'showResultDetail'])->name('lab.results.detail');
    Route::post('/results/{labOrder}', [LabWorkflowController::class, 'saveResults'])->name('lab.results.save');
    Route::get('/reports', [LabWorkflowController::class, 'reports'])->name('lab.reports');
    Route::get('/reports/{labReport}', [LabWorkflowController::class, 'showReport'])->name('lab.reports.show');
    Route::post('/reports/{labReport}/deliver', [LabWorkflowController::class, 'deliverReport'])->name('lab.reports.deliver');
    Route::get('/critical-alerts', [LabWorkflowController::class, 'criticalAlerts'])->name('lab.critical');
    Route::post('/critical-alerts/{criticalAlert}/acknowledge', [LabWorkflowController::class, 'acknowledgeAlert'])->name('lab.critical.ack');

    // 5 & 6. Test Master & Packages
    Route::get('/tests', [CatalogController::class, 'tests'])->name('catalog.tests');
    Route::post('/tests', [CatalogController::class, 'storeTest'])->name('catalog.tests.store');
    Route::get('/packages', [CatalogController::class, 'packages'])->name('catalog.packages');
    Route::post('/packages', [CatalogController::class, 'storePackage'])->name('catalog.packages.store');

    // 12, 13, 16. Equipment, Inventory & Quality Control
    Route::get('/equipment', [QualityInventoryController::class, 'equipment'])->name('ops.equipment');
    Route::post('/equipment', [QualityInventoryController::class, 'storeAnalyzer'])->name('ops.equipment.store');
    Route::post('/equipment/{analyzer}/log', [QualityInventoryController::class, 'logAnalyzerEvent'])->name('ops.equipment.log');
    Route::get('/inventory', [QualityInventoryController::class, 'inventory'])->name('ops.inventory');
    Route::post('/inventory', [QualityInventoryController::class, 'storeReagent'])->name('ops.inventory.store');
    Route::post('/inventory/{reagent}/adjust', [QualityInventoryController::class, 'adjustStock'])->name('ops.inventory.adjust');
    Route::get('/quality-control', [QualityInventoryController::class, 'qualityControl'])->name('ops.qc');
    Route::post('/quality-control', [QualityInventoryController::class, 'storeQc'])->name('ops.qc.store');

    // 4, 18, 19, 20, 21. Doctors, Corporates, Branches & Portals
    Route::get('/doctors', [NetworkPortalController::class, 'doctors'])->name('network.doctors');
    Route::post('/doctors', [NetworkPortalController::class, 'storeDoctor'])->name('network.doctors.store');
    Route::get('/corporates', [NetworkPortalController::class, 'corporates'])->name('network.corporates');
    Route::post('/corporates', [NetworkPortalController::class, 'storeCorporate'])->name('network.corporates.store');
    Route::get('/branches', [NetworkPortalController::class, 'branches'])->name('network.branches');
    Route::post('/branches', [NetworkPortalController::class, 'storeBranch'])->name('network.branches.store');
    Route::get('/portal/doctor', [NetworkPortalController::class, 'doctorPortal'])->name('portal.doctor');
    Route::get('/portal/patient', [NetworkPortalController::class, 'patientPortal'])->name('portal.patient');

    // 22, 23, 24, 25, 26. HR, Finance, CRM, Compliance & Integrations
    Route::get('/hr', [AdminController::class, 'hr'])->name('admin.hr');
    Route::post('/hr', [AdminController::class, 'storeEmployee'])->name('admin.hr.store');
    Route::get('/finance', [AdminController::class, 'finance'])->name('admin.finance');
    Route::post('/finance', [AdminController::class, 'storeTransaction'])->name('admin.finance.store');
    Route::get('/finance/export', [AdminController::class, 'exportFinanceCsv'])->name('admin.finance.export');
    Route::get('/crm', [AdminController::class, 'crm'])->name('admin.crm');
    Route::post('/crm/leads', [AdminController::class, 'storeLead'])->name('admin.crm.leads');
    Route::patch('/crm/leads/{lead}', [AdminController::class, 'updateLeadStatus'])->name('admin.crm.leads.update');
    Route::post('/crm/campaigns', [AdminController::class, 'storeCampaign'])->name('admin.crm.campaigns');
    Route::post('/crm/reminders', [AdminController::class, 'storeReminder'])->name('admin.crm.reminders');
    Route::post('/crm/reminders/{reminder}/send', [AdminController::class, 'sendReminder'])->name('admin.crm.reminders.send');
    Route::post('/crm/tickets', [AdminController::class, 'storeTicket'])->name('admin.crm.tickets');
    Route::patch('/crm/tickets/{ticket}', [AdminController::class, 'updateTicket'])->name('admin.crm.tickets.update');
    Route::get('/compliance', [AdminController::class, 'compliance'])->name('admin.compliance');
    Route::post('/compliance/backup', [AdminController::class, 'triggerBackup'])->name('admin.compliance.backup');
    Route::get('/integrations', [AdminController::class, 'integrations'])->name('admin.integrations');
    Route::post('/integrations/{integrationConfig}/test', [AdminController::class, 'testIntegration'])->name('admin.integrations.test');
});
