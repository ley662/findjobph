# FindJobPh 🇵🇭
### Philippine Job Posting & Application Platform with Requirement-Gated Applications

[![Live Demo](https://img.shields.io/badge/Live_Demo-findjobph.vercel.app-blue?style=for-the-badge&logo=vercel)](https://findjobph.vercel.app)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-indigo?style=for-the-badge&logo=php)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38bdf8?style=for-the-badge&logo=tailwindcss)](https://tailwindcss.com)

🌐 **Live Demo Website**: [https://findjobph.vercel.app](https://findjobph.vercel.app)

**FindJobPh** is a modern full-stack web application designed for the Philippine job market. It solves credential mismatches and back-and-forth hiring friction by introducing a **Requirement-Gated Application Engine** — ensuring applicants have all mandatory Philippine workplace credentials (e.g., Valid ID, NBI Clearance, Driver's License, Barangay Clearance, Resume, Portfolio) uploaded and verified before submitting an application.

---

## ✨ Key Features

### 1. Requirement-Gated Application Flow
- **Prerequisite Enforcement**: Employers define mandatory vs. optional document types for each opening.
- **Readiness Meter**: Real-time checklist on feed cards and job detail pages calculating applicant readiness percentage.
- **Direct Inline Upload**: Missing requirements can be uploaded and attached directly from the job post.
- **Personal Document Vault**: Pre-store verified Philippine credentials once at `/vault` to apply to jobs instantly.

### 2. Role-Based Dashboards & Portals
- **Applicant Workspace**:
  - Facebook-style job feed with instant search and category filtering.
  - Personal **Requirements Vault** (`/vault`) with preview, replace, and deletion controls.
  - **My Applications** tracker (`/applications`) with color-coded status badges (`Pending`, `Under Review`, `Hired`, `Rejected`).
- **Employer Workspace**:
  - KPI Dashboard (`/employer/dashboard`) tracking active posts, candidate pipeline, and hired count.
  - Multi-section Job Creator (`/employer/jobs/create`) with toggleable requirement checklists.
  - Applicant Credential Inspector (`/employer/applicants`) with one-click status transitions (Under Review, Hire, Reject).
- **Admin Governance**:
  - System analytics overview (`/admin/dashboard`).
  - User moderation with account activation/suspension toggling (`/admin/users`).
  - Job moderation and system-wide application logs (`/admin/jobs`, `/admin/applications`).

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.3)
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Vite
- **Database**: MySQL
- **Testing**: PHPUnit (20 feature tests covering authentication, gating rules, employer authorization, and admin governance)
- **Code Standards**: Laravel Pint

---

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Server

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ley662/findjobph.git
   cd findjobph
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up the database:**
   Ensure MySQL is running, then configure `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

5. **Compile frontend assets:**
   ```bash
   npm run build
   ```

6. **Serve the application:**
   ```bash
   php artisan serve
   ```
   Visit `http://127.0.0.1:8000` in your browser.

---

## 👥 Demo Accounts

All demo accounts use password: `password`.

| Role | Email | Scenario |
|---|---|---|
| **Platform Admin** | `admin@findjobph.com` | Full platform administration |
| **Employer** | `express@angkaslike.ph` | Express Logistics PH (delivery listings with applicants) |
| **Employer** | `studio@manilamedia.ph` | Manila Media Co. (creative videographer listing) |
| **Applicant (Gated)** | `juan.rider@example.com` | Missing NBI Clearance (gated from Rider job) |
| **Applicant (Ready)** | `maria.creative@example.com` | 100% complete vault, application under review |
| **Applicant (New)** | `pedro.jobseeker@example.com` | Empty vault (fresh user experience) |

---

## 🧪 Automated Tests

Run the test suite:
```bash
php artisan test --compact
```
All 20 feature tests cover:
- Authentication, role redirects, and account suspension enforcement
- Prerequisite requirement gating & inline document satisfaction
- Employer job CRUD and applicant credential review
- Admin permissions and self-deactivation safeguards

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
