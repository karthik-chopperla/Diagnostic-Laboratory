<?php

namespace Database\Seeders;

use App\Models\Analyzer;
use App\Models\AnalyzerLog;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\CapaIncident;
use App\Models\ComplianceDoc;
use App\Models\Corporate;
use App\Models\Coupon;
use App\Models\CriticalAlert;
use App\Models\CrmLead;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\FinanceTransaction;
use App\Models\HomeCollection;
use App\Models\IntegrationConfig;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabOrder;
use App\Models\LabReport;
use App\Models\Patient;
use App\Models\PatientDocument;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\QcRecord;
use App\Models\Reagent;
use App\Models\Sample;
use App\Models\SupportTicket;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestPackage;
use App\Models\TestParameter;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoPasswords = config('app.demo_seed_passwords');
        if (
            count(array_filter(
                $demoPasswords,
                fn ($password) => is_string($password) && strlen($password) >= 16,
            )) !== 3
            || count(array_unique($demoPasswords)) !== 3
        ) {
            throw new \RuntimeException('Set three unique demo passwords of at least 16 characters before seeding.');
        }

        User::updateOrCreate(
            ['email' => 'admin@patholab.local'],
            ['name' => 'Dr. Ananya Sharma', 'role' => 'Chief Pathologist', 'is_active' => true, 'password' => Hash::make($demoPasswords['admin'])]
        );
        User::updateOrCreate(
            ['email' => 'tech@patholab.local'],
            ['name' => 'Rohan Kulkarni', 'role' => 'Lab Technician', 'is_active' => true, 'password' => Hash::make($demoPasswords['technician'])]
        );
        User::updateOrCreate(
            ['email' => 'desk@patholab.local'],
            ['name' => 'Priya Nair', 'role' => 'Front Desk & Billing', 'is_active' => true, 'password' => Hash::make($demoPasswords['front_desk'])]
        );

        // 1. Branches
        $mainBranch = Branch::create([
            'code' => 'BR-001',
            'name' => 'PathoLab Central Reference Lab',
            'address' => '14 Medical Square, MG Road',
            'city' => 'Bengaluru',
            'phone' => '+91 80 4123 9000',
            'email' => 'central@patholab.local',
            'is_central' => true,
            'license_number' => 'KA-LAB-2026-00142',
            'accreditation_status' => 'NABL ISO 15189:2022',
        ]);

        $cityBranch = Branch::create([
            'code' => 'BR-002',
            'name' => 'Indiranagar Collection & Stat Hub',
            'address' => '88 100 Feet Road, Indiranagar',
            'city' => 'Bengaluru',
            'phone' => '+91 80 4123 9055',
            'email' => 'indiranagar@patholab.local',
            'is_central' => false,
            'license_number' => 'KA-LAB-2026-00198',
            'accreditation_status' => 'NABL Accredited',
        ]);

        $northBranch = Branch::create([
            'code' => 'BR-003',
            'name' => 'Hebbal Franchise Diagnostic Center',
            'address' => '22 Bellary Road, Hebbal',
            'city' => 'Bengaluru',
            'phone' => '+91 80 4123 9088',
            'email' => 'hebbal@patholab.local',
            'is_central' => false,
            'license_number' => 'KA-LAB-2026-00215',
            'accreditation_status' => 'State Licenced',
        ]);

        // 2. 11 Laboratory Departments
        $deptData = [
            ['code' => 'HAEM', 'name' => 'Haematology', 'head' => 'Dr. Ananya Sharma', 'tat' => 3, 'desc' => 'CBC, ESR, coagulation, blood cell analysis'],
            ['code' => 'BIOC', 'name' => 'Biochemistry', 'head' => 'Dr. Vikram Mehta', 'tat' => 4, 'desc' => 'LFT, KFT, glucose, lipid profile, enzymes'],
            ['code' => 'MICR', 'name' => 'Microbiology', 'head' => 'Dr. Priya Nair', 'tat' => 48, 'desc' => 'Culture, sensitivity, organism identification'],
            ['code' => 'SERO', 'name' => 'Serology', 'head' => 'Dr. Priya Nair', 'tat' => 4, 'desc' => 'Antigen and antibody testing'],
            ['code' => 'IMMU', 'name' => 'Immunology', 'head' => 'Dr. Vikram Mehta', 'tat' => 6, 'desc' => 'Hormone, tumor markers and immunological tests'],
            ['code' => 'CLIN', 'name' => 'Clinical Pathology', 'head' => 'Dr. Ananya Sharma', 'tat' => 3, 'desc' => 'Urine, stool and body fluid analysis'],
            ['code' => 'HIST', 'name' => 'Histopathology', 'head' => 'Dr. R. Subramanian', 'tat' => 72, 'desc' => 'Tissue processing and biopsy examination'],
            ['code' => 'CYTO', 'name' => 'Cytology', 'head' => 'Dr. R. Subramanian', 'tat' => 24, 'desc' => 'FNAC, Pap smear and cellular examination'],
            ['code' => 'MOLE', 'name' => 'Molecular Diagnostics', 'head' => 'Dr. Kavita Rao', 'tat' => 12, 'desc' => 'RT-PCR, viral load and molecular tests'],
            ['code' => 'RADI', 'name' => 'Radiology', 'head' => 'Dr. Arjun Verma', 'tat' => 6, 'desc' => 'X-ray, CT, MRI, ultrasound imaging'],
            ['code' => 'CARD', 'name' => 'Cardiology', 'head' => 'Dr. Suresh Iyer', 'tat' => 2, 'desc' => 'ECG, Echo and cardiac stress investigations'],
        ];

        $departments = [];
        foreach ($deptData as $d) {
            $departments[$d['code']] = Department::create([
                'code' => $d['code'],
                'name' => $d['name'],
                'slug' => Str::slug($d['name']),
                'description' => $d['desc'],
                'head_name' => $d['head'],
                'turn_around_hours' => $d['tat'],
            ]);
        }

        // 3. Employees
        $pathologist = Employee::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['HAEM']->id,
            'employee_code' => 'EMP-001', 'name' => 'Dr. Ananya Sharma', 'role' => 'Pathologist',
            'qualification' => 'MD (Pathology)', 'license_no' => 'KMC-45821',
            'phone' => '9845011101', 'email' => 'ananya@patholab.local', 'shift' => 'Morning (08:00 - 16:00)', 'joined_at' => '2023-03-15',
        ]);
        $tech1 = Employee::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['HAEM']->id,
            'employee_code' => 'EMP-002', 'name' => 'Rohan Kulkarni', 'role' => 'Technician',
            'qualification' => 'B.Sc MLT', 'license_no' => 'MLT-8812',
            'phone' => '9845011102', 'email' => 'rohan@patholab.local', 'shift' => 'Morning (08:00 - 16:00)', 'joined_at' => '2024-01-10',
        ]);
        $tech2 = Employee::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['BIOC']->id,
            'employee_code' => 'EMP-003', 'name' => 'Sneha Deshmukh', 'role' => 'Technician',
            'qualification' => 'M.Sc Biochemistry', 'license_no' => 'MLT-9041',
            'phone' => '9845011103', 'email' => 'sneha@patholab.local', 'shift' => 'Morning (08:00 - 16:00)', 'joined_at' => '2024-05-20',
        ]);
        $phleb1 = Employee::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['CLIN']->id,
            'employee_code' => 'EMP-004', 'name' => 'Manoj Kumar', 'role' => 'Phlebotomist',
            'qualification' => 'DMLT', 'license_no' => 'PHL-3321',
            'phone' => '9845011104', 'email' => 'manoj@patholab.local', 'shift' => 'Early (06:30 - 14:30)', 'joined_at' => '2024-07-01',
        ]);
        $phleb2 = Employee::create([
            'branch_id' => $cityBranch->id, 'department_id' => $departments['CLIN']->id,
            'employee_code' => 'EMP-005', 'name' => 'Deepak Gowda', 'role' => 'Phlebotomist',
            'qualification' => 'DMLT', 'license_no' => 'PHL-3390',
            'phone' => '9845011105', 'email' => 'deepak@patholab.local', 'shift' => 'Early (06:30 - 14:30)', 'joined_at' => '2025-02-12',
        ]);
        $radiologist = Employee::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['RADI']->id,
            'employee_code' => 'EMP-006', 'name' => 'Dr. Arjun Verma', 'role' => 'Radiologist',
            'qualification' => 'MD (Radiodiagnosis)', 'license_no' => 'KMC-51904',
            'phone' => '9845011106', 'email' => 'arjun@patholab.local', 'shift' => 'Day (09:00 - 17:00)', 'joined_at' => '2023-11-01',
        ]);

        foreach ([$pathologist, $tech1, $tech2, $phleb1, $phleb2, $radiologist] as $emp) {
            EmployeeAttendance::create([
                'employee_id' => $emp->id,
                'date' => now()->toDateString(),
                'shift' => $emp->shift,
                'status' => 'Present',
                'check_in' => '08:05 AM',
                'check_out' => null,
            ]);
        }

        // 4. Corporates & Doctors
        $corp1 = Corporate::create([
            'code' => 'CORP-001', 'name' => 'Infosys Wellness Partner', 'type' => 'Corporate',
            'contact_person' => 'Meera Krishnan', 'phone' => '9900112233', 'email' => 'wellness@infy-partner.local',
            'address' => 'Electronics City Phase 1', 'credit_limit' => 500000, 'current_balance' => 42500,
            'discount_percent' => 15, 'contract_start' => '2026-01-01', 'contract_end' => '2026-12-31',
        ]);
        $corp2 = Corporate::create([
            'code' => 'HOSP-002', 'name' => 'St. Martha Memorial Hospital', 'type' => 'Hospital',
            'contact_person' => 'Dr. Joseph Thomas', 'phone' => '9900445566', 'email' => 'labdesk@stmartha.local',
            'address' => 'Nrupathunga Road', 'credit_limit' => 300000, 'current_balance' => 28400,
            'discount_percent' => 20, 'contract_start' => '2026-01-01', 'contract_end' => '2027-03-31',
        ]);

        $doc1 = Doctor::create([
            'code' => 'DOC-101', 'name' => 'Dr. Rajeshwar Rao', 'specialization' => 'Internal Medicine',
            'qualification' => 'MD, FACP', 'hospital_clinic' => 'Manipal Clinic Indiranagar',
            'phone' => '9880012345', 'email' => 'rajeshwar@clinic.local', 'referral_source' => 'Clinic Tie-up', 'commission_percent' => 0,
        ]);
        $doc2 = Doctor::create([
            'code' => 'DOC-102', 'name' => 'Dr. Nandini Hegde', 'specialization' => 'Endocrinology',
            'qualification' => 'DM (Endocrinology)', 'hospital_clinic' => 'EndoCare Center',
            'phone' => '9880067890', 'email' => 'nandini@endocare.local', 'referral_source' => 'Direct', 'commission_percent' => 0,
        ]);
        $doc3 = Doctor::create([
            'code' => 'DOC-103', 'name' => 'Dr. Karthikeya Nair', 'specialization' => 'Cardiology',
            'qualification' => 'DM (Cardiology)', 'hospital_clinic' => 'Heart & Rhythm Institute',
            'phone' => '9880099112', 'email' => 'karthik@heartcare.local', 'referral_source' => 'Hospital Network', 'commission_percent' => 0,
        ]);

        // 5. All 16 Test Categories
        $catMap = [
            ['Haematology', 'HAEM'], ['Biochemistry', 'BIOC'], ['Clinical Pathology', 'CLIN'],
            ['Microbiology', 'MICR'], ['Serology', 'SERO'], ['Immunology', 'IMMU'],
            ['Histopathology', 'HIST'], ['Cytology', 'CYTO'], ['Molecular Diagnostics', 'MOLE'],
            ['Radiology', 'RADI'], ['Ultrasound', 'RADI'], ['X-ray', 'RADI'],
            ['CT Scan', 'RADI'], ['MRI', 'RADI'], ['ECG', 'CARD'], ['Echo', 'CARD'],
        ];
        $categories = [];
        foreach ($catMap as [$catName, $deptCode]) {
            $categories[$catName] = TestCategory::create([
                'department_id' => $departments[$deptCode]->id,
                'name' => $catName,
                'slug' => Str::slug($catName),
                'description' => $catName.' diagnostic investigations',
            ]);
        }

        // 6. Tests & Parameters
        $cbc = Test::create([
            'category_id' => $categories['Haematology']->id, 'department_id' => $departments['HAEM']->id,
            'code' => 'HAEM-001', 'name' => 'Complete Blood Count (CBC)',
            'description' => 'Comprehensive 5-part differential blood cell analysis',
            'sample_type' => 'Whole Blood', 'container_type' => 'EDTA (Lavender)', 'sample_quantity' => '3 mL',
            'tat_hours' => 3, 'methodology' => 'Flow Cytometry & Impedance', 'unit' => 'Panel', 'price' => 450,
            'preparation_instructions' => 'No special preparation required.', 'fasting_required' => false,
            'branch_availability' => [$mainBranch->id, $cityBranch->id, $northBranch->id],
        ]);
        $cbcParams = [
            ['Hemoglobin (Hb)', 'HGB', 'g/dL', 'numeric', '13.0', '17.0', '12.0', '15.5', '11.5', '14.5', '7.0', '20.0', null, 1],
            ['Total WBC Count', 'WBC', 'x10^3/uL', 'numeric', '4.0', '11.0', '4.0', '11.0', '5.0', '14.5', '2.0', '30.0', null, 2],
            ['RBC Count', 'RBC', 'x10^6/uL', 'numeric', '4.5', '5.9', '3.8', '5.2', '4.0', '5.2', null, null, null, 3],
            ['Platelet Count', 'PLT', 'x10^3/uL', 'numeric', '150', '450', '150', '450', '150', '450', '50', '1000', null, 4],
            ['MCV', 'MCV', 'fL', 'numeric', '80', '100', '80', '100', '75', '95', null, null, null, 5],
        ];
        foreach ($cbcParams as $p) {
            TestParameter::create([
                'test_id' => $cbc->id, 'name' => $p[0], 'code' => $p[1], 'unit' => $p[2], 'data_type' => $p[3],
                'ref_min_male' => $p[4], 'ref_max_male' => $p[5], 'ref_min_female' => $p[6], 'ref_max_female' => $p[7],
                'ref_min_child' => $p[8], 'ref_max_child' => $p[9], 'critical_low' => $p[10], 'critical_high' => $p[11],
                'formula' => $p[12], 'sort_order' => $p[13],
            ]);
        }

        $lft = Test::create([
            'category_id' => $categories['Biochemistry']->id, 'department_id' => $departments['BIOC']->id,
            'code' => 'BIOC-001', 'name' => 'Liver Function Test (LFT)',
            'description' => 'Hepatic panel including Bilirubin, Transaminases, ALP & Proteins',
            'sample_type' => 'Serum', 'container_type' => 'SST (Yellow)', 'sample_quantity' => '4 mL',
            'tat_hours' => 4, 'methodology' => 'Photometry / IFCC', 'unit' => 'Panel', 'price' => 750,
            'preparation_instructions' => 'Overnight fasting preferred (8-10 hours).', 'fasting_required' => true,
            'branch_availability' => [$mainBranch->id, $cityBranch->id, $northBranch->id],
        ]);
        foreach ([
            ['Total Bilirubin', 'TBIL', 'mg/dL', '0.2', '1.2', '0.2', '1.2', '15.0', 1],
            ['SGOT (AST)', 'AST', 'U/L', '10', '40', '10', '35', '500', 2],
            ['SGPT (ALT)', 'ALT', 'U/L', '10', '45', '10', '35', '500', 3],
            ['Alkaline Phosphatase', 'ALP', 'U/L', '40', '129', '35', '104', null, 4],
            ['Serum Albumin', 'ALB', 'g/dL', '3.5', '5.2', '3.5', '5.2', null, 5],
        ] as $p) {
            TestParameter::create([
                'test_id' => $lft->id, 'name' => $p[0], 'code' => $p[1], 'unit' => $p[2], 'data_type' => 'numeric',
                'ref_min_male' => $p[3], 'ref_max_male' => $p[4], 'ref_min_female' => $p[5], 'ref_max_female' => $p[6],
                'critical_high' => $p[7], 'sort_order' => $p[8],
            ]);
        }

        $kft = Test::create([
            'category_id' => $categories['Biochemistry']->id, 'department_id' => $departments['BIOC']->id,
            'code' => 'BIOC-002', 'name' => 'Kidney Function Test (KFT)',
            'description' => 'Renal panel with Urea, Creatinine, Uric Acid & Electrolytes',
            'sample_type' => 'Serum', 'container_type' => 'SST (Yellow)', 'sample_quantity' => '4 mL',
            'tat_hours' => 4, 'methodology' => 'ISE / Enzymatic', 'unit' => 'Panel', 'price' => 800,
            'branch_availability' => [$mainBranch->id, $cityBranch->id, $northBranch->id],
        ]);
        foreach ([
            ['Serum Creatinine', 'CREA', 'mg/dL', '0.7', '1.3', '0.6', '1.1', null, '5.0', 1],
            ['Blood Urea', 'UREA', 'mg/dL', '15', '40', '15', '40', null, '120', 2],
            ['Serum Potassium (K+)', 'K', 'mmol/L', '3.5', '5.1', '3.5', '5.1', '2.5', '6.2', 3],
            ['Serum Sodium (Na+)', 'NA', 'mmol/L', '136', '145', '136', '145', '120', '160', 4],
        ] as $p) {
            TestParameter::create([
                'test_id' => $kft->id, 'name' => $p[0], 'code' => $p[1], 'unit' => $p[2], 'data_type' => 'numeric',
                'ref_min_male' => $p[3], 'ref_max_male' => $p[4], 'ref_min_female' => $p[5], 'ref_max_female' => $p[6],
                'critical_low' => $p[7], 'critical_high' => $p[8], 'sort_order' => $p[9],
            ]);
        }

        $hba1c = Test::create([
            'category_id' => $categories['Biochemistry']->id, 'department_id' => $departments['BIOC']->id,
            'code' => 'BIOC-003', 'name' => 'HbA1c (Glycated Hemoglobin)',
            'sample_type' => 'Whole Blood', 'container_type' => 'EDTA (Lavender)', 'sample_quantity' => '2 mL',
            'tat_hours' => 4, 'methodology' => 'HPLC', 'unit' => '%', 'price' => 550,
            'ref_range_male' => '4.0 - 5.6 %', 'ref_range_female' => '4.0 - 5.6 %', 'critical_high' => '12.0',
            'branch_availability' => [$mainBranch->id, $cityBranch->id, $northBranch->id],
        ]);
        TestParameter::create([
            'test_id' => $hba1c->id, 'name' => 'HbA1c', 'code' => 'HBA1C', 'unit' => '%',
            'ref_min_male' => '4.0', 'ref_max_male' => '5.6', 'ref_min_female' => '4.0', 'ref_max_female' => '5.6',
            'critical_high' => '12.0', 'sort_order' => 1,
        ]);

        $lipid = Test::create([
            'category_id' => $categories['Biochemistry']->id, 'department_id' => $departments['BIOC']->id,
            'code' => 'BIOC-004', 'name' => 'Lipid Profile',
            'sample_type' => 'Serum', 'container_type' => 'SST (Yellow)', 'sample_quantity' => '3 mL',
            'tat_hours' => 4, 'methodology' => 'Enzymatic Colorimetric', 'unit' => 'mg/dL', 'price' => 650,
            'fasting_required' => true, 'preparation_instructions' => '12 hours fasting required.',
            'branch_availability' => [$mainBranch->id, $cityBranch->id],
        ]);
        foreach ([
            ['Total Cholesterol', 'CHOL', 'mg/dL', '0', '200', 1],
            ['Triglycerides', 'TG', 'mg/dL', '0', '150', 2],
            ['HDL Cholesterol', 'HDL', 'mg/dL', '40', '60', 3],
            ['LDL Cholesterol', 'LDL', 'mg/dL', '0', '100', 4],
        ] as $p) {
            TestParameter::create([
                'test_id' => $lipid->id, 'name' => $p[0], 'code' => $p[1], 'unit' => $p[2],
                'ref_min_male' => $p[3], 'ref_max_male' => $p[4], 'ref_min_female' => $p[3], 'ref_max_female' => $p[4], 'sort_order' => $p[5],
            ]);
        }

        $tsh = Test::create([
            'category_id' => $categories['Immunology']->id, 'department_id' => $departments['IMMU']->id,
            'code' => 'IMMU-001', 'name' => 'Thyroid Profile (T3, T4, TSH)',
            'sample_type' => 'Serum', 'container_type' => 'SST (Yellow)', 'sample_quantity' => '3 mL',
            'tat_hours' => 6, 'methodology' => 'CMIA / ECLIA', 'unit' => 'Panel', 'price' => 700,
            'branch_availability' => [$mainBranch->id, $cityBranch->id, $northBranch->id],
        ]);
        foreach ([
            ['TSH (3rd Gen)', 'TSH', 'uIU/mL', '0.35', '4.94', 1],
            ['Free T4', 'FT4', 'ng/dL', '0.70', '1.48', 2],
            ['Total T3', 'T3', 'ng/mL', '0.58', '1.59', 3],
        ] as $p) {
            TestParameter::create([
                'test_id' => $tsh->id, 'name' => $p[0], 'code' => $p[1], 'unit' => $p[2],
                'ref_min_male' => $p[3], 'ref_max_male' => $p[4], 'ref_min_female' => $p[3], 'ref_max_female' => $p[4], 'sort_order' => $p[5],
            ]);
        }

        $urine = Test::create([
            'category_id' => $categories['Clinical Pathology']->id, 'department_id' => $departments['CLIN']->id,
            'code' => 'CLIN-001', 'name' => 'Urine Routine & Microscopy',
            'sample_type' => 'Urine', 'container_type' => 'Sterile Container', 'sample_quantity' => '20 mL',
            'tat_hours' => 2, 'methodology' => 'Dipstick & Microscopy', 'unit' => 'Panel', 'price' => 220,
            'branch_availability' => [$mainBranch->id, $cityBranch->id, $northBranch->id],
        ]);
        $culture = Test::create([
            'category_id' => $categories['Microbiology']->id, 'department_id' => $departments['MICR']->id,
            'code' => 'MICR-001', 'name' => 'Blood Culture & Sensitivity (Automated)',
            'sample_type' => 'Whole Blood', 'container_type' => 'BacT/ALERT Bottle', 'sample_quantity' => '10 mL',
            'tat_hours' => 48, 'methodology' => 'Automated BacT/ALERT + Vitek 2', 'unit' => 'Report', 'price' => 1200,
            'branch_availability' => [$mainBranch->id],
        ]);
        $dengue = Test::create([
            'category_id' => $categories['Serology']->id, 'department_id' => $departments['SERO']->id,
            'code' => 'SERO-001', 'name' => 'Dengue NS1 Antigen & IgM/IgG',
            'sample_type' => 'Serum', 'container_type' => 'SST (Yellow)', 'sample_quantity' => '3 mL',
            'tat_hours' => 3, 'methodology' => 'ELISA / Rapid ICT', 'unit' => 'Qualitative', 'price' => 950,
            'branch_availability' => [$mainBranch->id, $cityBranch->id],
        ]);
        $pcr = Test::create([
            'category_id' => $categories['Molecular Diagnostics']->id, 'department_id' => $departments['MOLE']->id,
            'code' => 'MOLE-001', 'name' => 'Respiratory Viral Panel (RT-PCR)',
            'sample_type' => 'Nasopharyngeal Swab', 'container_type' => 'VTM Tube', 'sample_quantity' => '3 mL',
            'tat_hours' => 12, 'methodology' => 'Real-Time RT-PCR', 'unit' => 'Ct Value', 'price' => 1800,
            'branch_availability' => [$mainBranch->id],
        ]);
        $xray = Test::create([
            'category_id' => $categories['X-ray']->id, 'department_id' => $departments['RADI']->id,
            'code' => 'RAD-X01', 'name' => 'Chest X-Ray PA View (Digital)',
            'sample_type' => 'Imaging', 'container_type' => 'DICOM', 'sample_quantity' => '1 Film',
            'tat_hours' => 2, 'methodology' => 'Digital Radiography', 'unit' => 'Report', 'price' => 450,
            'branch_availability' => [$mainBranch->id, $northBranch->id],
        ]);
        $usg = Test::create([
            'category_id' => $categories['Ultrasound']->id, 'department_id' => $departments['RADI']->id,
            'code' => 'RAD-U01', 'name' => 'Ultrasound Whole Abdomen',
            'sample_type' => 'Imaging', 'container_type' => 'DICOM', 'sample_quantity' => 'Scan',
            'tat_hours' => 3, 'methodology' => 'High-Res Ultrasound', 'unit' => 'Report', 'price' => 1400,
            'fasting_required' => true, 'preparation_instructions' => 'Full bladder & 6 hours fasting.',
            'branch_availability' => [$mainBranch->id],
        ]);
        $ecg = Test::create([
            'category_id' => $categories['ECG']->id, 'department_id' => $departments['CARD']->id,
            'code' => 'CARD-E01', 'name' => '12-Lead Digital ECG',
            'sample_type' => 'Physiology', 'container_type' => 'Waveform', 'sample_quantity' => '1 Trace',
            'tat_hours' => 1, 'methodology' => '12-Lead Electrocardiography', 'unit' => 'Report', 'price' => 350,
            'branch_availability' => [$mainBranch->id, $cityBranch->id, $northBranch->id],
        ]);
        $echo = Test::create([
            'category_id' => $categories['Echo']->id, 'department_id' => $departments['CARD']->id,
            'code' => 'CARD-EC2', 'name' => '2D Echocardiography with Color Doppler',
            'sample_type' => 'Imaging', 'container_type' => 'DICOM', 'sample_quantity' => 'Scan',
            'tat_hours' => 2, 'methodology' => 'Transthoracic Echo', 'unit' => 'Report', 'price' => 2200,
            'branch_availability' => [$mainBranch->id],
        ]);

        // 7. Health Checkup Packages
        $packagesList = [
            ['PKG-001', 'Full Body Checkup', 'Preventive', 4200, 25, 3150, false, [$cbc->id, $lft->id, $kft->id, $lipid->id, $hba1c->id, $tsh->id, $urine->id]],
            ['PKG-002', 'Basic Health Checkup', 'Preventive', 1650, 15, 1400, false, [$cbc->id, $hba1c->id, $urine->id, $ecg->id]],
            ['PKG-003', 'Diabetes Profile', 'Disease Profile', 1800, 15, 1530, false, [$hba1c->id, $kft->id, $urine->id]],
            ['PKG-004', 'Thyroid Profile', 'Disease Profile', 1150, 15, 975, false, [$tsh->id, $cbc->id]],
            ['PKG-005', 'Cardiac Profile', 'Disease Profile', 3650, 20, 2920, false, [$lipid->id, $ecg->id, $echo->id, $cbc->id]],
            ['PKG-006', "Women's Health Package", 'Wellness', 3200, 20, 2560, false, [$cbc->id, $tsh->id, $lft->id, $urine->id]],
            ['PKG-007', "Men's Health Package", 'Wellness', 3400, 20, 2720, false, [$cbc->id, $lipid->id, $kft->id, $ecg->id]],
            ['PKG-008', 'Corporate Executive Checkup', 'Corporate', 4800, 25, 3600, true, [$cbc->id, $lft->id, $kft->id, $lipid->id, $tsh->id, $xray->id, $ecg->id]],
        ];
        foreach ($packagesList as $pk) {
            $pkg = TestPackage::create([
                'code' => $pk[0], 'name' => $pk[1], 'category' => $pk[2],
                'description' => $pk[1].' comprehensive diagnostic bundle.',
                'price' => $pk[3], 'discount_percent' => $pk[4], 'final_price' => $pk[5], 'is_corporate' => $pk[6],
            ]);
            $pkg->tests()->sync($pk[7]);
        }

        // 8. Patients
        $p1 = Patient::create([
            'branch_id' => $mainBranch->id, 'uhid' => 'UHID-2026-0001', 'name' => 'Aarav Menon',
            'gender' => 'Male', 'dob' => '1988-05-14', 'age' => 38, 'phone' => '9845100001',
            'email' => 'aarav.menon@example.com', 'address' => '42 Residency Road', 'city' => 'Bengaluru',
            'emergency_contact_name' => 'Priya Menon', 'emergency_contact_phone' => '9845100099',
            'blood_group' => 'O+', 'allergies' => 'Penicillin', 'medical_history' => 'Mild Hypertension',
            'clinical_history' => 'Routine annual health evaluation', 'patient_type' => 'Registered',
        ]);
        $p2 = Patient::create([
            'branch_id' => $mainBranch->id, 'uhid' => 'UHID-2026-0002', 'name' => 'Priya Menon',
            'gender' => 'Female', 'dob' => '1991-09-22', 'age' => 35, 'phone' => '9845100099',
            'email' => 'priya.menon@example.com', 'address' => '42 Residency Road', 'city' => 'Bengaluru',
            'blood_group' => 'A+', 'allergies' => 'None', 'patient_type' => 'Registered',
            'family_head_id' => $p1->id, 'family_relation' => 'Spouse',
        ]);
        $p3 = Patient::create([
            'branch_id' => $mainBranch->id, 'corporate_id' => $corp1->id, 'uhid' => 'UHID-2026-0003', 'name' => 'Siddharth Joshi',
            'gender' => 'Male', 'dob' => '1979-02-11', 'age' => 47, 'phone' => '9845100003',
            'email' => 'siddharth.j@example.com', 'address' => '19 Koramangala 4th Block', 'city' => 'Bengaluru',
            'blood_group' => 'B+', 'allergies' => 'Sulfa drugs', 'medical_history' => 'Type 2 Diabetes',
            'clinical_history' => 'Fever & fatigue for 3 days, rule out infection', 'patient_type' => 'Corporate',
        ]);
        $p4 = Patient::create([
            'branch_id' => $cityBranch->id, 'uhid' => 'UHID-2026-0004', 'name' => 'Lakshmi Narayanan',
            'gender' => 'Female', 'dob' => '1962-11-30', 'age' => 64, 'phone' => '9845100004',
            'address' => '108 Indiranagar Stage 2', 'city' => 'Bengaluru',
            'blood_group' => 'AB+', 'medical_history' => 'CKD Stage 2', 'clinical_history' => 'Electrolyte & renal follow-up',
            'patient_type' => 'Walk-in',
        ]);
        // Duplicate patient pair for duplicate detection demo
        $p5 = Patient::create([
            'branch_id' => $mainBranch->id, 'uhid' => 'UHID-2026-0005', 'name' => 'Siddharth Joshi',
            'gender' => 'Male', 'dob' => '1979-02-11', 'age' => 47, 'phone' => '9845100003',
            'address' => 'Koramangala', 'city' => 'Bengaluru', 'patient_type' => 'Walk-in',
        ]);

        PatientDocument::create([
            'patient_id' => $p1->id, 'title' => 'Physician Referral Prescription',
            'document_type' => 'Prescription', 'file_path' => 'docs/rx-aarav-001.pdf', 'uploaded_by' => 'Front Desk',
        ]);

        // 9. Appointments
        Appointment::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p1->id, 'doctor_id' => $doc1->id,
            'department_id' => $departments['HAEM']->id, 'test_id' => $cbc->id,
            'appointment_no' => 'APT-2026-101', 'appointment_type' => 'Online',
            'scheduled_at' => now()->setTime(9, 30), 'time_slot' => '09:30 AM - 10:00 AM',
            'status' => 'Completed', 'sms_sent' => true, 'whatsapp_sent' => true,
        ]);
        Appointment::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p3->id, 'doctor_id' => $doc2->id,
            'department_id' => $departments['BIOC']->id, 'test_id' => $hba1c->id,
            'appointment_no' => 'APT-2026-102', 'appointment_type' => 'Walk-in',
            'scheduled_at' => now()->setTime(11, 0), 'time_slot' => '11:00 AM - 11:30 AM',
            'status' => 'Checked-in', 'sms_sent' => true, 'whatsapp_sent' => true,
        ]);
        $aptHome = Appointment::create([
            'branch_id' => $cityBranch->id, 'patient_id' => $p4->id, 'doctor_id' => $doc3->id,
            'department_id' => $departments['BIOC']->id, 'test_id' => $kft->id,
            'appointment_no' => 'APT-2026-103', 'appointment_type' => 'Home Collection',
            'scheduled_at' => now()->setTime(7, 30), 'time_slot' => '07:30 AM - 08:30 AM',
            'status' => 'Confirmed', 'sms_sent' => true, 'whatsapp_sent' => true,
        ]);

        // 10. Coupons, Invoices & Payments
        Coupon::create(['code' => 'HEALTH15', 'discount_type' => 'percent', 'discount_value' => 15, 'min_amount' => 1000, 'valid_until' => '2026-12-31']);
        Coupon::create(['code' => 'WELCOME100', 'discount_type' => 'fixed', 'discount_value' => 100, 'min_amount' => 500, 'valid_until' => '2026-12-31']);

        $inv1 = Invoice::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p1->id, 'doctor_id' => $doc1->id,
            'invoice_no' => 'INV-2026-0001', 'counter_name' => 'Counter 1',
            'subtotal' => 1200, 'discount_amount' => 100, 'coupon_code' => 'WELCOME100',
            'gst_percent' => 0, 'gst_amount' => 0, 'total_amount' => 1100,
            'paid_amount' => 1100, 'balance_amount' => 0, 'payment_status' => 'Paid', 'invoice_type' => 'Retail',
        ]);
        InvoiceItem::create(['invoice_id' => $inv1->id, 'test_id' => $cbc->id, 'item_name' => $cbc->name, 'item_type' => 'Test', 'quantity' => 1, 'unit_price' => 450, 'total_price' => 450]);
        InvoiceItem::create(['invoice_id' => $inv1->id, 'test_id' => $lft->id, 'item_name' => $lft->name, 'item_type' => 'Test', 'quantity' => 1, 'unit_price' => 750, 'total_price' => 750]);
        Payment::create([
            'invoice_id' => $inv1->id, 'branch_id' => $mainBranch->id, 'receipt_no' => 'RCP-2026-0001',
            'amount' => 1100, 'payment_method' => 'UPI', 'payment_type' => 'Payment',
            'transaction_ref' => 'UPI-9948210394', 'paid_at' => now()->subHours(3),
        ]);

        $inv2 = Invoice::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p3->id, 'doctor_id' => $doc2->id, 'corporate_id' => $corp1->id,
            'invoice_no' => 'INV-2026-0002', 'counter_name' => 'Corporate Desk',
            'subtotal' => 1500, 'discount_amount' => 225, 'gst_percent' => 0, 'gst_amount' => 0, 'total_amount' => 1275,
            'paid_amount' => 500, 'balance_amount' => 775, 'payment_status' => 'Partial', 'invoice_type' => 'Corporate',
        ]);
        InvoiceItem::create(['invoice_id' => $inv2->id, 'test_id' => $hba1c->id, 'item_name' => $hba1c->name, 'item_type' => 'Test', 'quantity' => 1, 'unit_price' => 550, 'total_price' => 550]);
        InvoiceItem::create(['invoice_id' => $inv2->id, 'test_id' => $dengue->id, 'item_name' => $dengue->name, 'item_type' => 'Test', 'quantity' => 1, 'unit_price' => 950, 'total_price' => 950]);
        Payment::create([
            'invoice_id' => $inv2->id, 'branch_id' => $mainBranch->id, 'receipt_no' => 'RCP-2026-0002',
            'amount' => 500, 'payment_method' => 'Card', 'payment_type' => 'Payment',
            'transaction_ref' => 'CARD-4410', 'paid_at' => now()->subHours(2),
        ]);

        $inv3 = Invoice::create([
            'branch_id' => $cityBranch->id, 'patient_id' => $p4->id, 'doctor_id' => $doc3->id,
            'invoice_no' => 'INV-2026-0003', 'counter_name' => 'Home Collection',
            'subtotal' => 950, 'discount_amount' => 0, 'total_amount' => 950,
            'paid_amount' => 0, 'balance_amount' => 950, 'payment_status' => 'Unpaid', 'invoice_type' => 'Retail',
        ]);
        InvoiceItem::create(['invoice_id' => $inv3->id, 'test_id' => $kft->id, 'item_name' => $kft->name, 'item_type' => 'Test', 'quantity' => 1, 'unit_price' => 800, 'total_price' => 800]);
        InvoiceItem::create(['invoice_id' => $inv3->id, 'item_name' => 'Home Collection Charge', 'item_type' => 'Home Collection', 'quantity' => 1, 'unit_price' => 150, 'total_price' => 150]);

        // 11. Samples, Home Collections, Lab Orders, Results, Critical Alerts, Reports
        $s1 = Sample::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p1->id, 'invoice_id' => $inv1->id, 'doctor_id' => $doc1->id,
            'sample_id_code' => 'SMP-261112-001', 'accession_no' => 'ACC-2026-8801', 'barcode_value' => 'PL261112001',
            'sample_type' => 'Whole Blood', 'container_type' => 'EDTA (Lavender)', 'quantity_ml' => 3.0,
            'collected_at' => now()->subHours(3), 'collector_id' => $phleb1->id, 'received_at' => now()->subHours(2)->subMinutes(40),
            'storage_location' => 'Rack A-01', 'status' => 'Completed',
        ]);
        $s2 = Sample::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p3->id, 'invoice_id' => $inv2->id, 'doctor_id' => $doc2->id,
            'sample_id_code' => 'SMP-261112-002', 'accession_no' => 'ACC-2026-8802', 'barcode_value' => 'PL261112002',
            'sample_type' => 'Whole Blood', 'container_type' => 'EDTA (Lavender)', 'quantity_ml' => 2.5,
            'collected_at' => now()->subHours(1), 'collector_id' => $phleb1->id, 'received_at' => now()->subMinutes(45),
            'storage_location' => 'Rack B-04', 'status' => 'Processing', 'is_stat' => true,
        ]);
        $s3 = Sample::create([
            'branch_id' => $cityBranch->id, 'patient_id' => $p4->id, 'invoice_id' => $inv3->id, 'doctor_id' => $doc3->id,
            'sample_id_code' => 'SMP-261112-003', 'accession_no' => 'ACC-2026-8803', 'barcode_value' => 'PL261112003',
            'sample_type' => 'Serum', 'container_type' => 'SST (Yellow)', 'quantity_ml' => 4.0,
            'collected_at' => now()->subMinutes(50), 'collection_location' => 'Home Collection - Indiranagar',
            'collector_id' => $phleb2->id, 'received_at' => now()->subMinutes(20),
            'storage_location' => 'Rack C-02', 'status' => 'Processing', 'is_stat' => true,
        ]);
        $s4 = Sample::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p2->id, 'doctor_id' => $doc1->id,
            'sample_id_code' => 'SMP-261112-004', 'accession_no' => 'ACC-2026-8804', 'barcode_value' => 'PL261112004',
            'sample_type' => 'Serum', 'container_type' => 'SST (Yellow)', 'quantity_ml' => 1.0,
            'collected_at' => now()->subHours(4), 'collector_id' => $phleb1->id,
            'storage_location' => 'Reject Bin', 'status' => 'Rejected', 'rejection_reason' => 'Hemolyzed / Insufficient quantity',
        ]);

        HomeCollection::create([
            'branch_id' => $cityBranch->id, 'patient_id' => $p4->id, 'appointment_id' => $aptHome->id,
            'phlebotomist_id' => $phleb2->id, 'booking_code' => 'HC-2026-501',
            'address' => '108 Indiranagar Stage 2, Bengaluru', 'route_zone' => 'Zone B - Indiranagar',
            'scheduled_date' => now()->toDateString(), 'time_slot' => '07:30 AM - 08:30 AM',
            'collection_charge' => 150, 'cash_to_collect' => 950, 'cash_collected' => 950,
            'checklist' => ['identity_verified' => true, 'fasting_confirmed' => true, 'barcode_labeled' => true, 'ice_pack_used' => true],
            'status' => 'Delivered to Lab', 'picked_up_at' => now()->subMinutes(50),
        ]);
        HomeCollection::create([
            'branch_id' => $mainBranch->id, 'patient_id' => $p2->id,
            'phlebotomist_id' => $phleb1->id, 'booking_code' => 'HC-2026-502',
            'address' => '42 Residency Road, Bengaluru', 'route_zone' => 'Zone A - Central',
            'scheduled_date' => now()->toDateString(), 'time_slot' => '04:30 PM - 05:30 PM',
            'collection_charge' => 150, 'cash_to_collect' => 700, 'cash_collected' => 0,
            'checklist' => ['identity_verified' => false, 'fasting_confirmed' => false, 'barcode_labeled' => false, 'ice_pack_used' => false],
            'status' => 'Assigned',
        ]);

        // Lab Order 1 (Approved & Delivered)
        $lo1 = LabOrder::create([
            'branch_id' => $mainBranch->id, 'sample_id' => $s1->id, 'patient_id' => $p1->id,
            'test_id' => $cbc->id, 'department_id' => $departments['HAEM']->id,
            'assigned_technician_id' => $tech1->id, 'priority' => 'Routine', 'status' => 'Delivered',
            'tat_due_at' => now()->addHours(1), 'completed_at' => now()->subHours(1),
            'verified_by' => 'Rohan Kulkarni', 'verified_at' => now()->subMinutes(50),
            'approved_by' => 'Dr. Ananya Sharma', 'approved_at' => now()->subMinutes(40),
            'pathologist_signature' => 'Dr. Ananya Sharma, MD (Pathology) | KMC-45821',
        ]);
        foreach ([
            ['Hemoglobin (Hb)', '14.2', 'g/dL', '13.0 - 17.0', 'Normal', '13.9'],
            ['Total WBC Count', '7.8', 'x10^3/uL', '4.0 - 11.0', 'Normal', '7.1'],
            ['RBC Count', '4.9', 'x10^6/uL', '4.5 - 5.9', 'Normal', '4.8'],
            ['Platelet Count', '245', 'x10^3/uL', '150 - 450', 'Normal', '230'],
            ['MCV', '88.4', 'fL', '80 - 100', 'Normal', '87.0'],
        ] as $r) {
            TestResult::create([
                'lab_order_id' => $lo1->id, 'parameter_name' => $r[0], 'result_value' => $r[1],
                'unit' => $r[2], 'reference_range' => $r[3], 'flag' => $r[4], 'previous_value' => $r[5],
                'entered_by' => 'Rohan Kulkarni',
            ]);
        }

        LabReport::create([
            'lab_order_id' => $lo1->id, 'patient_id' => $p1->id, 'invoice_id' => $inv1->id,
            'report_no' => 'RPT-2026-0001', 'verification_code' => 'QR-PL-994810',
            'language' => 'en', 'status' => 'Delivered', 'revision_no' => 1,
            'approved_by' => 'Dr. Ananya Sharma', 'approved_at' => now()->subMinutes(40),
            'delivered_via' => ['WhatsApp', 'Email', 'Patient Portal'], 'delivered_at' => now()->subMinutes(35),
        ]);

        // Lab Order 2 (Result Entered -> Awaiting Pathologist Approval, with Critical Potassium!)
        $lo2 = LabOrder::create([
            'branch_id' => $cityBranch->id, 'sample_id' => $s3->id, 'patient_id' => $p4->id,
            'test_id' => $kft->id, 'department_id' => $departments['BIOC']->id,
            'assigned_technician_id' => $tech2->id, 'priority' => 'STAT', 'status' => 'Result Entered',
            'tat_due_at' => now()->addHours(2), 'verified_by' => 'Sneha Deshmukh', 'verified_at' => now()->subMinutes(10),
        ]);
        foreach ([
            ['Serum Creatinine', '2.4', 'mg/dL', '0.6 - 1.1', 'High', '1.8'],
            ['Blood Urea', '68', 'mg/dL', '15 - 40', 'High', '52'],
            ['Serum Potassium (K+)', '6.5', 'mmol/L', '3.5 - 5.1', 'Critical High', '5.0'],
            ['Serum Sodium (Na+)', '138', 'mmol/L', '136 - 145', 'Normal', '139'],
        ] as $r) {
            TestResult::create([
                'lab_order_id' => $lo2->id, 'parameter_name' => $r[0], 'result_value' => $r[1],
                'unit' => $r[2], 'reference_range' => $r[3], 'flag' => $r[4], 'previous_value' => $r[5],
                'entered_by' => 'Sneha Deshmukh',
            ]);
        }

        CriticalAlert::create([
            'lab_order_id' => $lo2->id, 'patient_id' => $p4->id, 'doctor_id' => $doc3->id,
            'parameter_name' => 'Serum Potassium (K+)', 'result_value' => '6.5 mmol/L',
            'threshold_info' => 'Critical High (> 6.2 mmol/L)', 'alert_level' => 'Emergency',
            'notified_pathologist' => true, 'notified_technician' => true, 'notified_doctor' => true,
            'status' => 'Open', 'communication_notes' => 'Auto-flagged by Cobas c311 analyzer; awaiting doctor acknowledgement.',
        ]);

        // Lab Order 3 (In-Progress)
        LabOrder::create([
            'branch_id' => $mainBranch->id, 'sample_id' => $s2->id, 'patient_id' => $p3->id,
            'test_id' => $hba1c->id, 'department_id' => $departments['BIOC']->id,
            'assigned_technician_id' => $tech2->id, 'priority' => 'Urgent', 'status' => 'In-Progress',
            'tat_due_at' => now()->addHours(3),
        ]);

        // 12. Analyzers & Logs
        $an1 = Analyzer::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['HAEM']->id,
            'equipment_code' => 'EQ-HAEM-01', 'name' => 'Sysmex XN-1000', 'manufacturer' => 'Sysmex',
            'model' => 'XN-1000 Hematology Analyzer', 'location' => 'Main Lab Bay 1',
            'interface_type' => 'HL7 / Bidirectional', 'status' => 'Online',
            'last_calibrated_at' => now()->subDays(5), 'next_calibration_at' => now()->addDays(25),
            'last_maintenance_at' => now()->subDays(12), 'next_maintenance_at' => now()->addDays(18),
            'total_tests_run' => 1420, 'downtime_hours' => 0.5,
        ]);
        $an2 = Analyzer::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['BIOC']->id,
            'equipment_code' => 'EQ-BIOC-01', 'name' => 'Roche Cobas c311', 'manufacturer' => 'Roche Diagnostics',
            'model' => 'Cobas c311', 'location' => 'Main Lab Bay 2',
            'interface_type' => 'ASTM E1394', 'status' => 'Online',
            'last_calibrated_at' => now()->subDays(2), 'next_calibration_at' => now()->addDays(28),
            'last_maintenance_at' => now()->subDays(10), 'next_maintenance_at' => now()->addDays(20),
            'total_tests_run' => 2180, 'downtime_hours' => 1.2,
        ]);
        Analyzer::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['IMMU']->id,
            'equipment_code' => 'EQ-IMMU-01', 'name' => 'Abbott Architect i1000SR', 'manufacturer' => 'Abbott',
            'model' => 'Architect i1000SR', 'location' => 'Immunology Suite',
            'interface_type' => 'HL7 / Bidirectional', 'status' => 'Maintenance',
            'last_calibrated_at' => now()->subDays(15), 'next_calibration_at' => now()->addDays(15),
            'last_maintenance_at' => now(), 'next_maintenance_at' => now()->addDays(30),
            'total_tests_run' => 890, 'downtime_hours' => 4.0,
        ]);
        AnalyzerLog::create([
            'analyzer_id' => $an1->id, 'log_type' => 'Calibration',
            'description' => 'Monthly 5-part differential calibration passed within 1SD.',
            'performed_by' => 'Rohan Kulkarni', 'logged_at' => now()->subDays(5), 'downtime_minutes' => 20, 'cost' => 0,
        ]);

        // 13. Reagents & Purchase Orders
        Reagent::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['HAEM']->id,
            'code' => 'REG-101', 'name' => 'Cellpack DCL Diluent (20L)', 'category' => 'Reagent',
            'supplier_name' => 'Sysmex India Pvt Ltd', 'batch_no' => 'SYS-88412',
            'expiry_date' => now()->addMonths(8), 'stock_qty' => 18, 'min_stock_qty' => 5, 'unit' => 'Packs', 'unit_cost' => 4200, 'status' => 'In Stock',
        ]);
        Reagent::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['BIOC']->id,
            'code' => 'REG-102', 'name' => 'Creatinine Jaffé Reagent Kit', 'category' => 'Reagent',
            'supplier_name' => 'Roche Diagnostics', 'batch_no' => 'RCH-44901',
            'expiry_date' => now()->addDays(18), 'stock_qty' => 4, 'min_stock_qty' => 6, 'unit' => 'Kits', 'unit_cost' => 6800, 'status' => 'Low Stock',
        ]);
        Reagent::create([
            'branch_id' => $mainBranch->id, 'department_id' => $departments['CLIN']->id,
            'code' => 'CON-201', 'name' => 'EDTA Vacutainer Tubes (Box of 100)', 'category' => 'Consumable',
            'supplier_name' => 'BD Medical', 'batch_no' => 'BD-9921',
            'expiry_date' => now()->addMonths(14), 'stock_qty' => 45, 'min_stock_qty' => 15, 'unit' => 'Boxes', 'unit_cost' => 650, 'status' => 'In Stock',
        ]);

        PurchaseOrder::create([
            'branch_id' => $mainBranch->id, 'po_number' => 'PO-2026-041',
            'supplier_name' => 'Roche Diagnostics', 'order_date' => now()->subDays(2),
            'expected_date' => now()->addDays(2), 'total_amount' => 40800, 'status' => 'Ordered',
            'items_summary' => '6x Creatinine Jaffé Reagent Kits',
        ]);

        // 14. QC Records (Levey-Jennings Series) & CAPA
        $qcPoints = [
            [-0.4, 'OK', 'Accepted'], [0.6, 'OK', 'Accepted'], [-1.1, 'OK', 'Accepted'],
            [0.9, 'OK', 'Accepted'], [1.4, 'OK', 'Accepted'], [-0.2, 'OK', 'Accepted'],
            [2.3, '1-2s', 'Warning'], [0.3, 'OK', 'Accepted'],
        ];
        foreach ($qcPoints as $idx => $pt) {
            $z = $pt[0];
            $mean = 14.0;
            $sd = 0.3;
            QcRecord::create([
                'branch_id' => $mainBranch->id, 'analyzer_id' => $an1->id, 'department_id' => $departments['HAEM']->id,
                'material_name' => 'XN-CHECK Level 2', 'lot_number' => 'QC-L2-2026A',
                'expiry_date' => now()->addMonths(4), 'control_level' => 'Level 2',
                'parameter_name' => 'Hemoglobin (Hb)', 'target_mean' => $mean, 'sd_value' => $sd,
                'measured_value' => round($mean + ($z * $sd), 3), 'z_score' => $z,
                'westgard_rule' => $pt[1], 'status' => $pt[2], 'recorded_by' => 'Rohan Kulkarni',
                'approved_by' => 'Dr. Ananya Sharma', 'recorded_at' => now()->subDays(7 - $idx),
            ]);
        }

        CapaIncident::create([
            'branch_id' => $mainBranch->id, 'incident_code' => 'CAPA-2026-009', 'type' => 'CAPA',
            'title' => 'Hemolyzed sample received from Indiranagar Hub',
            'description' => 'Sample SMP-261112-004 arrived hemolyzed due to vigorous shaking.',
            'root_cause' => 'Improper tube inversion technique during transit.',
            'corrective_action' => 'Recollection scheduled; phlebotomist re-trained on gentle 8x inversion.',
            'preventive_action' => 'Added foam rack inserts in transport boxes.',
            'status' => 'In Progress', 'reported_by' => 'Dr. Ananya Sharma', 'due_date' => now()->addDays(5),
        ]);

        // 15. Finance, CRM, Compliance, Audit, Integrations
        FinanceTransaction::create([
            'branch_id' => $mainBranch->id, 'type' => 'Income', 'category' => 'Patient Billing',
            'description' => 'Daily Retail & Corporate Collection', 'amount' => 48500,
            'payment_method' => 'UPI / Card', 'reference_no' => 'DAY-CLOSE-1112', 'transaction_date' => now()->toDateString(),
        ]);
        FinanceTransaction::create([
            'branch_id' => $mainBranch->id, 'type' => 'Expense', 'category' => 'Reagent Purchase',
            'description' => 'Roche Diagnostics Reagent Replenishment', 'amount' => 18400,
            'payment_method' => 'Bank Transfer', 'reference_no' => 'NEFT-88210', 'transaction_date' => now()->toDateString(),
        ]);

        CrmLead::create([
            'branch_id' => $mainBranch->id, 'name' => 'Ramesh Babu', 'phone' => '9845998877',
            'email' => 'ramesh.b@example.com', 'source' => 'WhatsApp', 'campaign_name' => 'Full Body Winter Checkup',
            'interest' => 'Full Body Checkup', 'status' => 'Contacted', 'follow_up_date' => now()->addDay(),
        ]);

        SupportTicket::create([
            'patient_id' => $p1->id, 'ticket_no' => 'TKT-2026-012', 'subject' => 'Request bilingual Hindi/English PDF copy',
            'category' => 'Report', 'priority' => 'Low', 'status' => 'Resolved', 'rating' => 5,
            'description' => 'Patient requested bilingual report copy on WhatsApp.',
        ]);

        AuditLog::create([
            'user_name' => 'Dr. Ananya Sharma', 'action' => 'Approved Lab Report',
            'module' => 'Result Verification', 'record_id' => 'RPT-2026-0001',
            'details' => 'Electronically signed and released CBC report for UHID-2026-0001.', 'ip_address' => '127.0.0.1',
        ]);

        ComplianceDoc::create([
            'branch_id' => $mainBranch->id, 'title' => 'NABL ISO 15189:2022 Accreditation Certificate',
            'doc_type' => 'NABL', 'reference_no' => 'MC-4521', 'valid_until' => '2028-06-30', 'status' => 'Active',
        ]);

        $integrations = [
            ['Sysmex XN-1000 Interface', 'Analyzer', 'HL7 v2.5 (TCP/IP)', '192.168.10.21:5000', 'Connected', 'Bidirectional CBC & differential auto-import'],
            ['Roche Cobas c311 Interface', 'Analyzer', 'ASTM E1394', 'COM3 / 192.168.10.22:5001', 'Connected', 'Biochemistry auto-posting with barcode match'],
            ['Orthanc PACS / DICOM Server', 'Imaging', 'DICOM / WADO-RS', 'pacs.patholab.local:8042', 'Connected', 'X-Ray, CT, MRI & Echo study linking'],
            ['WhatsApp Business API', 'Messaging', 'REST Webhook', 'graph.facebook.com/v20.0', 'Connected', 'Automated PDF reports & appointment alerts'],
            ['Razorpay Payment Gateway', 'Payment', 'REST API', 'api.razorpay.com/v1', 'Sandbox', 'Online UPI/Card portal payments'],
            ['Tally Prime Accounting Sync', 'Accounting', 'XML / CSV Export', 'localhost:9000', 'Connected', 'Daily voucher & GST ledger export'],
        ];
        foreach ($integrations as $int) {
            IntegrationConfig::create([
                'name' => $int[0], 'category' => $int[1], 'protocol' => $int[2],
                'endpoint_or_port' => $int[3], 'status' => $int[4],
                'last_sync_at' => now()->subMinutes(rand(2, 45)), 'config_summary' => $int[5],
            ]);
        }
    }
}
