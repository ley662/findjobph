<?php

namespace Database\Seeders;

use App\Models\ApplicantDocument;
use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\RequirementType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Categories
        $categoriesData = [
            [
                'name' => 'Delivery & Logistics',
                'slug' => 'delivery-logistics',
                'icon' => 'truck',
                'description' => 'Motorcycle delivery riders, couriers, van drivers, and warehouse logistics associates.',
            ],
            [
                'name' => 'Media, Creative & Design',
                'slug' => 'media-creative-design',
                'icon' => 'camera',
                'description' => 'Videographers, video editors, graphic designers, content creators, and photographers.',
            ],
            [
                'name' => 'IT & Software Development',
                'slug' => 'it-software-development',
                'icon' => 'code',
                'description' => 'Web developers, backend engineers, mobile app developers, UI/UX designers, and QA testers.',
            ],
            [
                'name' => 'Food, Dining & Hospitality',
                'slug' => 'food-dining-hospitality',
                'icon' => 'utensils',
                'description' => 'Service crew, baristas, cashiers, kitchen staff, cooks, and restaurant managers.',
            ],
            [
                'name' => 'Customer Support & BPO',
                'slug' => 'customer-support-bpo',
                'icon' => 'headset',
                'description' => 'Call center representatives, chat support, virtual assistants, and client success specialists.',
            ],
            [
                'name' => 'Retail, Sales & Marketing',
                'slug' => 'retail-sales-marketing',
                'icon' => 'shopping-bag',
                'description' => 'Store associates, retail cashiers, sales promodisers, and digital marketing assistants.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[$data['slug']] = Category::create($data);
        }

        // 2. Requirement Types
        $reqTypesData = [
            [
                'name' => 'Valid Government ID',
                'slug' => 'valid-id',
                'description' => 'National ID (PhilSys), Passport, UMID, Postal ID, Driver License, SSS, or PRC ID.',
                'icon' => 'id-card',
                'is_default' => true,
            ],
            [
                'name' => 'NBI Clearance',
                'slug' => 'nbi-clearance',
                'description' => 'Unexpired National Bureau of Investigation Clearance.',
                'icon' => 'shield-check',
                'is_default' => true,
            ],
            [
                'name' => "Driver's License (Pro / Non-Pro)",
                'slug' => 'drivers-license',
                'description' => 'Valid LTO Driver License with motorcycle (Code A/A1) or 4-wheel (Code B/B1) restriction.',
                'icon' => 'car',
                'is_default' => false,
            ],
            [
                'name' => 'Resume / Curriculum Vitae',
                'slug' => 'resume-cv',
                'description' => 'Updated resume or biodata including work history and contact references.',
                'icon' => 'document-text',
                'is_default' => true,
            ],
            [
                'name' => 'Portfolio / Work Samples',
                'slug' => 'portfolio-samples',
                'description' => 'PDF portfolio, showreel link, Behance/Google Drive portfolio link or work showcase.',
                'icon' => 'collection',
                'is_default' => false,
            ],
            [
                'name' => 'Barangay Clearance',
                'slug' => 'barangay-clearance',
                'description' => 'Current year Barangay Clearance from residence.',
                'icon' => 'badge-check',
                'is_default' => false,
            ],
            [
                'name' => 'Medical / Health Certificate',
                'slug' => 'medical-certificate',
                'description' => 'Fit to work certificate or City Health Office Food Handler permit.',
                'icon' => 'heart',
                'is_default' => false,
            ],
        ];

        $reqTypes = [];
        foreach ($reqTypesData as $data) {
            $reqTypes[$data['slug']] = RequirementType::create($data);
        }

        // 3. Seed Users
        // Admin
        $admin = User::create([
            'name' => 'Platform Administrator',
            'email' => 'admin@findjobph.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+63 917 800 0001',
            'headline' => 'FindJobPh Head of Operations',
            'is_active' => true,
        ]);

        // Employers
        $employer1 = User::create([
            'name' => 'Express Logistics PH',
            'email' => 'express@angkaslike.ph',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'company_name' => 'Express Logistics PH Inc.',
            'phone' => '+63 917 555 1234',
            'headline' => 'Fastest on-demand courier and freight logistics in Metro Manila',
            'is_active' => true,
        ]);

        $employer2 = User::create([
            'name' => 'Lights & Motion Studios',
            'email' => 'media@lightscamera.ph',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'company_name' => 'Lights & Motion Digital Studios',
            'phone' => '+63 918 333 4567',
            'headline' => 'Premier commercial video production & viral social media content agency',
            'is_active' => true,
        ]);

        $employer3 = User::create([
            'name' => 'PinoyCloud Solutions',
            'email' => 'tech@pinoytech.ph',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'company_name' => 'PinoyCloud Solutions Corp.',
            'phone' => '+63 920 444 8899',
            'headline' => 'Building enterprise web apps and cloud digital transformations',
            'is_active' => true,
        ]);

        $employer4 = User::create([
            'name' => 'Manila Bites Food Group',
            'email' => 'resto@jollibites.ph',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'company_name' => 'Manila Bites Fast Casual Group',
            'phone' => '+63 922 777 9900',
            'headline' => 'Rapidly expanding comfort food chain across Luzon',
            'is_active' => true,
        ]);

        // Suspended Employer (for Admin oversight demo)
        $suspendedUser = User::create([
            'name' => 'Scammy Job Offers (Deactivated)',
            'email' => 'suspended@fakeagency.ph',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'company_name' => 'Unverified Agency',
            'phone' => '+63 999 000 0000',
            'is_active' => false,
        ]);

        // Applicants
        // Applicant 1: Juan (Delivery Rider) -> Has Valid ID & Driver's License, MISSING NBI Clearance!
        $juan = User::create([
            'name' => 'Juan dela Cruz (Delivery Rider)',
            'email' => 'juan.rider@example.com',
            'password' => Hash::make('password'),
            'role' => 'applicant',
            'phone' => '+63 915 111 2233',
            'headline' => 'Professional Motorcycle Delivery Rider with 4 years courier experience',
            'is_active' => true,
        ]);

        // Applicant 2: Maria (Creative & Media) -> All requirements complete!
        $maria = User::create([
            'name' => 'Maria Santos (Videographer)',
            'email' => 'maria.creative@example.com',
            'password' => Hash::make('password'),
            'role' => 'applicant',
            'phone' => '+63 916 444 5566',
            'headline' => 'Content Creator, Videographer & Colorist | Sony Alpha shooter',
            'is_active' => true,
        ]);

        // Applicant 3: Pedro (Fresh Jobseeker) -> 0 documents uploaded yet
        $pedro = User::create([
            'name' => 'Pedro Penduko (Applicant)',
            'email' => 'pedro.jobseeker@example.com',
            'password' => Hash::make('password'),
            'role' => 'applicant',
            'phone' => '+63 917 888 9900',
            'headline' => 'Enthusiastic entry-level job seeker ready to work immediately',
            'is_active' => true,
        ]);

        // 4. Create sample document files in public storage
        Storage::disk('public')->makeDirectory("documents/{$juan->id}");
        Storage::disk('public')->makeDirectory("documents/{$maria->id}");

        $samplePdfContent = '%PDF-1.4 sample verification document for FindJobPh demonstration.';

        // Juan's Documents:
        $juanIdPath = "documents/{$juan->id}/juan_valid_id.pdf";
        Storage::disk('public')->put($juanIdPath, $samplePdfContent);
        ApplicantDocument::create([
            'user_id' => $juan->id,
            'requirement_type_id' => $reqTypes['valid-id']->id,
            'file_path' => $juanIdPath,
            'file_name' => 'Juan_National_ID.pdf',
            'file_size' => 1024 * 420,
            'mime_type' => 'application/pdf',
            'notes' => 'Philippine National ID card copy',
        ]);

        $juanLicensePath = "documents/{$juan->id}/juan_drivers_license.pdf";
        Storage::disk('public')->put($juanLicensePath, $samplePdfContent);
        ApplicantDocument::create([
            'user_id' => $juan->id,
            'requirement_type_id' => $reqTypes['drivers-license']->id,
            'file_path' => $juanLicensePath,
            'file_name' => 'Juan_LTO_License_CodeA.pdf',
            'file_size' => 1024 * 512,
            'mime_type' => 'application/pdf',
            'notes' => 'Non-Pro Drivers License (Code A/A1 motorcycle)',
        ]);
        // Notice: Juan does NOT have NBI Clearance seeded!

        // Maria's Documents: ALL complete
        $mariaIdPath = "documents/{$maria->id}/maria_valid_id.pdf";
        Storage::disk('public')->put($mariaIdPath, $samplePdfContent);
        ApplicantDocument::create([
            'user_id' => $maria->id,
            'requirement_type_id' => $reqTypes['valid-id']->id,
            'file_path' => $mariaIdPath,
            'file_name' => 'Maria_Passport_Scan.pdf',
            'file_size' => 1024 * 380,
            'mime_type' => 'application/pdf',
        ]);

        $mariaNbiPath = "documents/{$maria->id}/maria_nbi.pdf";
        Storage::disk('public')->put($mariaNbiPath, $samplePdfContent);
        ApplicantDocument::create([
            'user_id' => $maria->id,
            'requirement_type_id' => $reqTypes['nbi-clearance']->id,
            'file_path' => $mariaNbiPath,
            'file_name' => 'Maria_NBI_Clearance_2026.pdf',
            'file_size' => 1024 * 610,
            'mime_type' => 'application/pdf',
        ]);

        $mariaResumePath = "documents/{$maria->id}/maria_resume.pdf";
        Storage::disk('public')->put($mariaResumePath, $samplePdfContent);
        ApplicantDocument::create([
            'user_id' => $maria->id,
            'requirement_type_id' => $reqTypes['resume-cv']->id,
            'file_path' => $mariaResumePath,
            'file_name' => 'Maria_Santos_Videographer_Resume.pdf',
            'file_size' => 1024 * 250,
            'mime_type' => 'application/pdf',
        ]);

        $mariaPortfolioPath = "documents/{$maria->id}/maria_portfolio.pdf";
        Storage::disk('public')->put($mariaPortfolioPath, $samplePdfContent);
        ApplicantDocument::create([
            'user_id' => $maria->id,
            'requirement_type_id' => $reqTypes['portfolio-samples']->id,
            'file_path' => $mariaPortfolioPath,
            'file_name' => 'Maria_Video_Portfolio_Reel.pdf',
            'file_size' => 1024 * 890,
            'mime_type' => 'application/pdf',
        ]);

        // 5. Job Posts with Requirements
        // Job 1: Delivery Rider (Requires: Valid ID, Driver's License, NBI Clearance)
        $jobRider = JobPost::create([
            'user_id' => $employer1->id,
            'category_id' => $categories['delivery-logistics']->id,
            'title' => 'Express Motorcycle Delivery Rider (Urgent)',
            'company_name' => 'Express Logistics PH Inc.',
            'location' => 'Quezon City & CAMANAVA Hubs',
            'job_type' => 'Full-time',
            'workplace_type' => 'On-site',
            'salary_min' => 22000,
            'salary_max' => 35000,
            'salary_period' => 'month',
            'description' => 'We are urgently hiring 20 Motorcycle Delivery Riders for parcel and food deliveries across Northern Metro Manila. Guaranteed daily booking incentives, accident insurance coverage, and flexible schedule options.',
            'responsibilities' => "• Pick up customer parcels from dispatch hubs and merchant stores\n• Inspect package condition before departure\n• Safely navigate assigned delivery routes using our mobile driver app\n• Collect cash-on-delivery (COD) payments accurately and remit promptly",
            'qualifications' => "• Must own a registered motorcycle (at least 100cc to 160cc) with complete OR/CR\n• Valid Driver's License with Code A/A1 (Motorcycle)\n• Clean NBI record and valid government identification\n• Android smartphone with active mobile data",
            'benefits' => "• Daily trip incentives + 100% rider tips\n• Comprehensive rider accident insurance\n• Fuel subsidies and tire maintenance vouchers\n• Free branded safety helmet, vest, and thermal bag",
            'tags' => ['Delivery', 'Rider', 'Motorcycle', 'UrgentHiring', 'COD'],
            'status' => 'active',
            'views_count' => 142,
        ]);
        $jobRider->requirements()->sync([
            $reqTypes['valid-id']->id => ['is_mandatory' => true],
            $reqTypes['drivers-license']->id => ['is_mandatory' => true],
            $reqTypes['nbi-clearance']->id => ['is_mandatory' => true],
        ]);

        // Job 2: Senior Videographer (Requires: Portfolio, Resume, Valid ID)
        $jobVideo = JobPost::create([
            'user_id' => $employer2->id,
            'category_id' => $categories['media-creative-design']->id,
            'title' => 'Senior Videographer & TikTok / Reels Content Creator',
            'company_name' => 'Lights & Motion Digital Studios',
            'location' => 'Makati City, Metro Manila',
            'job_type' => 'Full-time',
            'workplace_type' => 'Hybrid',
            'salary_min' => 35000,
            'salary_max' => 55000,
            'salary_period' => 'month',
            'description' => 'Lights & Motion is looking for a creative, forward-thinking Videographer and Video Editor to shoot high-impact commercial campaigns, short-form reels for client brands, and on-location podcast interviews.',
            'responsibilities' => "• Plan shoot shotlists, lighting setups, and multi-camera audio capture\n• Film on-location and in-studio productions using Sony FX3 / A7SIII cameras\n• Edit high-retention short-form videos (Reels, TikTok, YouTube Shorts) in Premiere Pro / DaVinci Resolve\n• Collaborate closely with social media directors and client brand managers",
            'qualifications' => "• At least 2+ years of professional video production experience\n• Strong portfolio or showreel showcasing cinematography and modern pacing\n• Proficiency with Premiere Pro, DaVinci Resolve, or Final Cut Pro\n• Knowledge of 3-point lighting setups and wireless lavalier microphones",
            'benefits' => "• Studio equipment access (Cinema cameras, G Master lenses, lighting grid)\n• Hybrid setup (2 days shoot / 3 days remote editing)\n• HMO with dental from Day 1\n• Generous annual learning budget for film gear and masterclasses",
            'tags' => ['Videography', 'VideoEditor', 'PremierePro', 'Reels', 'Hybrid'],
            'status' => 'active',
            'views_count' => 98,
        ]);
        $jobVideo->requirements()->sync([
            $reqTypes['portfolio-samples']->id => ['is_mandatory' => true],
            $reqTypes['resume-cv']->id => ['is_mandatory' => true],
            $reqTypes['valid-id']->id => ['is_mandatory' => true],
        ]);

        // Job 3: Full-Stack Laravel Developer (Requires: Resume, Portfolio, Valid ID)
        $jobDev = JobPost::create([
            'user_id' => $employer3->id,
            'category_id' => $categories['it-software-development']->id,
            'title' => 'Full-Stack Laravel / PHP & Tailwind Developer',
            'company_name' => 'PinoyCloud Solutions Corp.',
            'location' => 'BGC Taguig (Remote Option)',
            'job_type' => 'Full-time',
            'workplace_type' => 'Remote',
            'salary_min' => 60000,
            'salary_max' => 95000,
            'salary_period' => 'month',
            'description' => 'PinoyCloud Solutions is seeking a mid-to-senior Full Stack Developer skilled in modern PHP, Laravel 11/12, Tailwind CSS, MySQL, and modern frontend tools. You will build and scale internal enterprise SaaS systems for our global clients.',
            'responsibilities' => "• Architect, develop, and maintain high-traffic web applications using Laravel\n• Design clean relational database schemas and optimize MySQL queries\n• Build modern, responsive user interfaces with Blade and Tailwind CSS\n• Write comprehensive PHPUnit automated feature tests and maintain CI pipelines",
            'qualifications' => "• 3+ years demonstrable experience with Laravel framework\n• Solid understanding of Eloquent, queue workers, and RESTful API standards\n• Strong command of Tailwind CSS, responsive layouts, and Git workflow\n• Experience with Docker and cloud deployments is a plus",
            'benefits' => "• 100% Work from Home / Remote setup with high-speed internet allowance\n• Competitive salary with 14th month pay and performance bonuses\n• Top-tier Maxicare HMO including 2 dependents\n• Company-provided MacBook Pro",
            'tags' => ['Laravel', 'PHP', 'TailwindCSS', 'MySQL', 'FullStack', 'Remote'],
            'status' => 'active',
            'views_count' => 210,
        ]);
        $jobDev->requirements()->sync([
            $reqTypes['resume-cv']->id => ['is_mandatory' => true],
            $reqTypes['portfolio-samples']->id => ['is_mandatory' => true],
            $reqTypes['valid-id']->id => ['is_mandatory' => true],
        ]);

        // Job 4: Restaurant Service Crew & Cashier (Requires: Valid ID, Medical Cert, Barangay Clearance)
        $jobCrew = JobPost::create([
            'user_id' => $employer4->id,
            'category_id' => $categories['food-dining-hospitality']->id,
            'title' => 'Restaurant Service Crew & Store Cashier',
            'company_name' => 'Manila Bites Fast Casual Group',
            'location' => 'Mandaluyong & Pasig City Branches',
            'job_type' => 'Full-time',
            'workplace_type' => 'On-site',
            'salary_min' => 16500,
            'salary_max' => 21000,
            'salary_period' => 'month',
            'description' => 'Manila Bites is hiring energetic and friendly dining service crew members and cashiers for our newly opened branches. High school graduates and college students with pleasing personality are encouraged to apply!',
            'responsibilities' => "• Greet customers with warmth and take dining orders accurately at the counter\n• Operate POS terminal and process cash/credit/e-wallet payments (GCash, Maya)\n• Maintain cleanliness of the dining area, service counters, and food prep station\n• Serve prepared food orders promptly while ensuring top food sanitation",
            'qualifications' => "• At least High School Graduate or Senior High School Graduate\n• Energetic, friendly demeanor with passion for customer service\n• Willing to work on shifting schedules (including weekends and holidays)\n• Food Handler Health Certificate and Barangay Clearance required",
            'benefits' => "• Free duty meals and beverage allowance per shift\n• SSS, PhilHealth, and Pag-IBIG mandatory statutory contributions\n• Night differential and overtime pay in accordance with DOLE standards\n• Clear career progression to Shift Supervisor and Branch Manager",
            'tags' => ['ServiceCrew', 'Cashier', 'Restaurant', 'CustomerService', 'EntryLevel'],
            'status' => 'active',
            'views_count' => 175,
        ]);
        $jobCrew->requirements()->sync([
            $reqTypes['valid-id']->id => ['is_mandatory' => true],
            $reqTypes['medical-certificate']->id => ['is_mandatory' => true],
            $reqTypes['barangay-clearance']->id => ['is_mandatory' => true],
        ]);

        // Job 5: Warehouse Logistics Associate (Requires: Valid ID, NBI Clearance)
        $jobWarehouse = JobPost::create([
            'user_id' => $employer1->id,
            'category_id' => $categories['delivery-logistics']->id,
            'title' => 'Warehouse Sorting & Inventory Associate',
            'company_name' => 'Express Logistics PH Inc.',
            'location' => 'Caloocan Sorting Hub, Metro Manila',
            'job_type' => 'Full-time',
            'workplace_type' => 'On-site',
            'salary_min' => 18000,
            'salary_max' => 24000,
            'salary_period' => 'month',
            'description' => 'Join our central warehouse team handling regional logistics sorting, barcode scanning, and parcel dispatching. Steady full-time employment with complete government benefits.',
            'responsibilities' => "• Receive incoming regional courier trucks and unload shipment pallets\n• Scan barcodes and sort parcels according to delivery zones and rider routes\n• Conduct periodic inventory counts and report damaged goods\n• Follow workplace safety protocols and forklift lane regulations",
            'qualifications' => "• Physically fit to lift packages up to 20kg\n• Attention to detail for barcode matching and sorting\n• Valid Government ID and clean NBI Clearance",
            'benefits' => "• Complete DOLE mandated statutory benefits\n• Shuttle service from major transport terminals\n• Overtime pay with hot meal allowance for extended shifts",
            'tags' => ['Warehouse', 'Logistics', 'Inventory', 'Sorting'],
            'status' => 'active',
            'views_count' => 84,
        ]);
        $jobWarehouse->requirements()->sync([
            $reqTypes['valid-id']->id => ['is_mandatory' => true],
            $reqTypes['nbi-clearance']->id => ['is_mandatory' => true],
        ]);

        // Job 6: Customer Support Specialist (Requires: Resume, Valid ID)
        $jobBpo = JobPost::create([
            'user_id' => $employer3->id,
            'category_id' => $categories['customer-support-bpo']->id,
            'title' => 'Customer Care & Live Chat Representative',
            'company_name' => 'PinoyCloud Solutions Corp.',
            'location' => 'Cebu City / Work from Home',
            'job_type' => 'Full-time',
            'workplace_type' => 'Remote',
            'salary_min' => 28000,
            'salary_max' => 38000,
            'salary_period' => 'month',
            'description' => 'Provide world-class chat and email support for international eCommerce buyers. Fast-paced environment with supportive team leads and excellent training.',
            'responsibilities' => "• Answer customer queries regarding product features, shipping, and refunds\n• Maintain high CSAT ratings and fast response times\n• Log tickets in Zendesk and escalate technical defects to engineering",
            'qualifications' => "• Excellent written English communication skills\n• Typing speed of at least 45 WPM\n• Quiet workspace and stable fiber internet connection",
            'benefits' => "• Night differential pay (if assigned to US shifts)\n• HMO with dependent subsidy\n• Performance-based monthly incentives",
            'tags' => ['BPO', 'ChatSupport', 'Remote', 'CustomerCare'],
            'status' => 'active',
            'views_count' => 129,
        ]);
        $jobBpo->requirements()->sync([
            $reqTypes['resume-cv']->id => ['is_mandatory' => true],
            $reqTypes['valid-id']->id => ['is_mandatory' => true],
        ]);

        // 6. Sample Application (Maria applied to Senior Videographer)
        JobApplication::create([
            'job_post_id' => $jobVideo->id,
            'applicant_id' => $maria->id,
            'status' => 'under_review',
            'cover_letter' => "Hello Lights & Motion team! I have watched your recent commercial work and I would love to bring my cinema camera setup and fast-paced editing style to your video campaigns. Please review my portfolio and let's create amazing reels together!",
            'employer_notes' => 'Impressive showreel! Shortlisted for first-round interview.',
            'reviewed_at' => now(),
        ]);
    }
}
