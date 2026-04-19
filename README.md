# TrackFlow

<p align="center">
  <img src="public/trackflow-main/logo.png" alt="TrackFlow Logo" width="110" />
</p>

<p align="center">
  <img src="https://readme-typing-svg.herokuapp.com?font=Poppins&weight=700&size=24&duration=2200&pause=800&center=true&vCenter=true&width=900&lines=Smart+Financial+Management+Platform;Track+Expenses+and+Plan+Budgets;Built+with+Laravel+12+%2B+Vite+%2B+Tailwind" alt="TrackFlow Animated Headline" />
</p>

<p align="center">
  <a href="#quick-start"><img src="https://img.shields.io/badge/Quick%20Start-Ready-16a34a?style=for-the-badge" alt="Quick Start" /></a>
  <img src="https://img.shields.io/badge/PHP-8.2+-1f2937?style=for-the-badge&logo=php" alt="PHP 8.2+" />
  <img src="https://img.shields.io/badge/Laravel-12.x-f43f5e?style=for-the-badge&logo=laravel" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/Vite-7.x-4338ca?style=for-the-badge&logo=vite" alt="Vite 7" />
  <img src="https://img.shields.io/badge/License-MIT-0f766e?style=for-the-badge" alt="License MIT" />
</p>

---

## Why TrackFlow

TrackFlow is a modern finance platform that helps users manage money with clarity and speed.

It combines daily transaction tracking with budgeting, goals, reports, group expense workflows, and account security in one responsive web experience.

## Live Demo

- Production URL: https://trackflow.mooo.com

---

## Core Features

### 1. Expense and Transaction Management
- Add, edit, categorize, and search transactions quickly.
- Track inflow and outflow with clean historical records.
- Built for fast, everyday usage.

### 2. Budget Planning
- Create monthly or custom budgets.
- Define budget items and monitor spent vs remaining.
- Improve discipline with structured budget visibility.

### 3. Financial Goals
- Create savings goals with target amount and timeline.
- Track progress automatically.
- Keep long-term plans visible and measurable.

### 4. Reports and Analytics
- Analyze financial performance with charts.
- Review category-wise and period-wise spending behavior.
- Use export-ready report views for sharing and audit.

### 5. Group Expense Workflow
- Manage shared spending in groups.
- Track requests, payments, and settlement status.
- Great for trips, roommates, teams, and family finance.

### 6. Auth and Security
- Google OAuth login support.
- Optional 2FA support.
- Trusted session and device-friendly auth flow.

### 7. PWA Experience
- Installable web app support.
- Home-screen launch on supported devices.
- Offline fallback page included.

---

## Product Advantages

- All-in-one finance workflow in one app.
- Clean modern UI and mobile-first responsiveness.
- Fast stack with Laravel 12 and Vite.
- Social login onboarding for better conversion.
- Production-ready architecture with migrations, queue jobs, and SMTP support.

---

## PWA App Experience

TrackFlow works as an installable Progressive Web App (PWA), so users can use it like a native app from home screen.

### What users get
- Install prompt on supported browsers.
- Standalone app-like experience after installation.
- Faster revisit experience with cached static assets.
- Offline fallback support when the network is unavailable.

### How to install
1. Open TrackFlow in Chrome, Edge, or a PWA-supported mobile browser.
2. Click the Install App prompt or browser install icon.
3. Confirm installation to add TrackFlow to your device home screen.
4. Launch directly as an app without opening a browser tab.

### Technical implementation
- Web App Manifest: `public/manifest.webmanifest`
- Service Worker: `public/sw.js`
- Install Script: `public/js/pwa-install.js`
- Offline Fallback Page: `public/offline.html`

---

## Visual Showcase

The visuals below are reused from the landing page modules.

<p align="center">
  <img src="public/img/dashboard.png" alt="Dashboard" width="88%" />
</p>

<table>
  <tr>
    <td align="center"><img src="public/img/expensetracking.png" alt="Expense Tracking" width="100%" /></td>
    <td align="center"><img src="public/img/budgets.png" alt="Budgets" width="100%" /></td>
  </tr>
  <tr>
    <td align="center"><img src="public/img/goals.png" alt="Goals" width="100%" /></td>
    <td align="center"><img src="public/img/reports.png" alt="Reports" width="100%" /></td>
  </tr>
  <tr>
    <td align="center"><img src="public/img/groupexpense.png" alt="Group Expense" width="100%" /></td>
    <td align="center"><img src="public/img/notifications.png" alt="Notifications" width="100%" /></td>
  </tr>
</table>

---

## Tech Stack

- Backend: Laravel 12, PHP 8.2+
- Frontend: Blade, Tailwind CSS, Alpine.js, Vite
- Database: MySQL
- Auth: Laravel Session Auth, Google OAuth (Socialite)
- Jobs and Queues: Laravel queue workers
- Mail: SMTP-ready configuration

---

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+

### Installation

```bash
git clone <your-repo-url>
cd Trackflow

composer install
npm install

cp .env.example .env
php artisan key:generate

# Set DB credentials in .env, then:
php artisan migrate

npm run build
php artisan serve
```

Open: http://127.0.0.1:8000

---

## Production Notes

- Keep queue worker running for async jobs.
- Use HTTPS for OAuth and cookie security.
- Rebuild caches on every deploy:

```bash
php artisan optimize
```

---

## OAuth Setup (Google)

- Callback route used by app: /auth/google/callback
- Add this callback URL in Google Cloud Console OAuth credentials.

---

## Project Structure (High-Level)

```text
app/                # Controllers, Models, Services, Jobs
config/             # App and service configuration
database/           # Migrations, seeders, factories
public/             # Public assets and images
resources/views/    # Blade views (auth, dashboard, settings, landing)
routes/             # Web and API routes
```

---

## License

This project is distributed under the MIT License.

---

<p align="center"><strong>TrackFlow</strong> - Finance clarity for everyday users and teams.</p>
