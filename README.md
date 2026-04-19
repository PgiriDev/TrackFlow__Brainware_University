<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=16a34a&height=150&section=header&text=TrackFlow&fontSize=50&fontAlignY=35&animation=twinkling&fontColor=ffffff" alt="Header Wave" width="100%" />
</p>

<p align="center">
  <img src="public/trackflow-main/logo.png" alt="TrackFlow Logo" width="120" />
</p>

<p align="center">
  <img src="https://readme-typing-svg.herokuapp.com?font=Poppins&weight=700&size=26&duration=3000&pause=1000&color=16A34A&center=true&vCenter=true&width=900&lines=Smart+Financial+Management+Platform;Track+Expenses+and+Plan+Budgets;Built+with+Laravel+12+%2B+Vite+%2B+Tailwind;Take+Control+of+Your+Finances" alt="TrackFlow Animated Headline" />
</p>

<p align="center">
  <a href="#-quick-start"><img src="https://img.shields.io/badge/Quick%20Start-Ready-16a34a?style=for-the-badge&logo=rocket" alt="Quick Start" /></a>
  <img src="https://img.shields.io/badge/PHP-8.2+-1f2937?style=for-the-badge&logo=php" alt="PHP 8.2+" />
  <img src="https://img.shields.io/badge/Laravel-12.x-f43f5e?style=for-the-badge&logo=laravel" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/Vite-7.x-4338ca?style=for-the-badge&logo=vite" alt="Vite 7" />
  <img src="https://img.shields.io/badge/License-MIT-0f766e?style=for-the-badge" alt="License MIT" />
</p>

<div align="center">
  <h3>
    <a href="https://trackflow.mooo.com">🟢 Live Demo</a>
    <span> | </span>
    <a href="#-core-features">✨ Features</a>
    <span> | </span>
    <a href="#-visual-showcase">📸 Screenshots</a>
  </h3>
</div>

<br/>

> **TrackFlow** is a modern finance platform designed to help users manage money with clarity and speed. It combines daily transaction tracking with budgeting, goals, reports, group expense workflows, and account security in one seamless, responsive web experience.

---

## ⚡ Core Features

| Feature | Description |
| :--- | :--- |
| 💸 **Expense Tracking** | Add, edit, categorize, and search transactions quickly. Track inflow/outflow with clean historical records built for fast, everyday usage. |
| 📊 **Budget Planning** | Create monthly/custom budgets, define budget items, and monitor spent vs. remaining. Improve discipline with structured visibility. |
| 🎯 **Financial Goals** | Create savings goals with target amounts and timelines. Track progress automatically and keep long-term plans visible. |
| 📈 **Reports & Analytics**| Analyze performance with interactive charts. Review category and period-wise spending. Export-ready views for sharing and audits. |
| 🤝 **Group Workflows** | Manage shared spending. Track requests, payments, and settlements. Perfect for trips, roommates, teams, and families. |
| 🔐 **Auth & Security** | Google OAuth support, optional 2FA, and a trusted, device-friendly authentication flow. |

---

## 📱 PWA App Experience

TrackFlow works as an installable **Progressive Web App (PWA)**, meaning you can use it like a native app directly from your home screen!

<details>
<summary><b>✨ Click to see what users get & how to install</b></summary>

### What users get
- 📲 **Native Feel:** Standalone app-like experience after installation.
- ⚡ **Speed:** Faster revisit experience with cached static assets.
- 📴 **Offline Support:** Built-in offline fallback page when the network drops.

### How to install
1. Open TrackFlow in Chrome, Edge, or a PWA-supported mobile browser.
2. Click the **Install App** prompt or browser install icon.
3. Confirm installation to add TrackFlow to your home screen.
4. Launch directly as an app without opening a browser tab.

### Technical Implementation
- **Manifest:** `public/manifest.webmanifest`
- **Service Worker:** `public/sw.js`
- **Install Script:** `public/js/pwa-install.js`
- **Offline Page:** `public/offline.html`

</details>

---

## 📸 Visual Showcase

<p align="center">
  <img src="public/img/dashboard.png" alt="Dashboard" width="90%" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />
</p>

| 🧾 Expense Tracking | 💰 Budgets |
| :---: | :---: |
| <img src="public/img/expensetracking.png" alt="Expense Tracking" width="100%" /> | <img src="public/img/budgets.png" alt="Budgets" width="100%" /> |

| 🎯 Goals | 📈 Reports |
| :---: | :---: |
| <img src="public/img/goals.png" alt="Goals" width="100%" /> | <img src="public/img/reports.png" alt="Reports" width="100%" /> |

| 🤝 Group Expense | 🔔 Notifications |
| :---: | :---: |
| <img src="public/img/groupexpense.png" alt="Group Expense" width="100%" /> | <img src="public/img/notifications.png" alt="Notifications" width="100%" /> |

---

## 🛠️ Tech Stack

<p align="center">
  <img src="https://img.shields.io/badge/Laravel_12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP_8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL_8+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
  <img src="https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" />
</p>

- **Auth:** Laravel Session Auth, Google OAuth (Socialite)
- **Background Jobs:** Laravel queue workers
- **Mail:** SMTP-ready configuration

---

## 🚀 Quick Start

### Prerequisites
`PHP 8.2+` | `Composer` | `Node.js 18+` | `MySQL 8+`

### Installation

```bash
# 1. Clone the repository
git clone <your-repo-url>
cd Trackflow

# 2. Install PHP and Node dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Set DB credentials in .env, then migrate:
php artisan migrate

# 5. Build assets and start the server
npm run build
php artisan serve

🎉 Open: http://127.0.0.1:8000

Production Notes
Keep queue worker running for async jobs.

Use HTTPS for OAuth and cookie security.

Rebuild caches on every deploy:

Bash
php artisan optimize
OAuth Setup (Google)
Callback route used by app: /auth/google/callback

Add this callback URL in your Google Cloud Console OAuth credentials.

Project Structure
Plaintext
app/                # Controllers, Models, Services, Jobs
config/             # App and service configuration
database/           # Migrations, seeders, factories
public/             # Public assets and images
resources/views/    # Blade views (auth, dashboard, settings, landing)
routes/             # Web and API routes
📜 License
This project is distributed under the MIT License.