# 🖥️ Futurixtech

A modern, admin-focused dashboard for managing servers, users, security groups, and SSH keys. The platform helps you stay on top of your cloud resources with application monitoring, weekly audits, intuitive UI, and automated alerts — all with support for dark/light mode, multi-language support, and secure authentication.

---

## 🚀 Features

- ✅ **Manage Servers**
  - Create, view, edit, and delete server resources
  - Filter and search functionality
  - Assign security groups and SSH keys to servers
  - Assign VPC, OS family, and instance type to servers

- ✅ **Security Groups**
  - Create, view, edit, and delete security group resources
  - Filter and search functionality
  - Identify unused groups with automated auditing

- ✅ **SSH Key Management**
  - Add and remove SSH keys
  - Filter and search functionality
  - Identify unused keys

- ✅ **User Management**
  - Create and manage users
  - Change user status (Active / Inactive)
  - Delete users
  - Scoped access control for each authenticated user

- 📊 **Dashboard & Weekly Audit**
  - Visualize resource usage at a glance
  - Automated email & in-app notifications for unused resources

- 🩺 **Application Monitoring**
  - Real-time metrics and service health overview
  - Detect performance issues or resource anomalies
  - Summarized monitoring stats in the dashboard

- 🌍 **Internationalization (i18n)**
  - Full multilingual support for **English**, **German**, **Spanish**, and **French**
  - Dynamic language switching across the entire platform

- 🔐 **Authentication & Security**
  - Login, Forgot Password, and Reset Password flows
  - Authenticated user scoping for data access
  - Tokenized and secure password reset

- 👤 **User Profile**
  - Edit personal details and profile photo

- 🌙 **Dark / Light Mode**
  - Toggle seamlessly between light and dark themes

- 🔔 **Email & In-App Notifications**
  - Stay informed with system alerts and updates

---

## 🧱 Tech Stack

- **Frontend**: Vue.js, Quasar Framework, TailwindCSS, ApexCharts
- **Backend**: Laravel, Laravel Sanctum (API authentication) / Fortify, MySQL, AWS SDK PHP
- **Notifications**: Laravel Notifications (Mail & Database)
- **Cloud Storage**: AWS S3 for profile image uploads
- **Job Scheduling**: Laravel Scheduler for weekly audits
- **Internationalization**: Vue I18n with 4-language support (EN, DE, ES, FR)
- **Monitoring**: Custom monitoring service integrated into dashboard

---

## 📷 Screenshots
![Preview](https://firebasestorage.googleapis.com/v0/b/karim-portfolio-bc1e8.appspot.com/o/deploy-admin-panel.png?alt=media&token=432957fc-8f68-472f-b6f8-70a000443bb4)

![Preview](https://firebasestorage.googleapis.com/v0/b/karim-portfolio-bc1e8.appspot.com/o/Screenshot%202025-10-15%20at%2018.23.11.png?alt=media&token=78fe8547-81fc-4446-a694-a520c7d3e019)

![Preview](https://firebasestorage.googleapis.com/v0/b/karim-portfolio-bc1e8.appspot.com/o/deploy-admin-panel-database-details-page.png?alt=media&token=b723c2eb-e8f5-46eb-b851-5c188f01b374)

![Preview](https://firebasestorage.googleapis.com/v0/b/karim-portfolio-bc1e8.appspot.com/o/deploy-admin-panel-databases-page.png?alt=media&token=a223df4e-7545-49b9-9482-4341170dda48)

---

## 🎥 Demo

[![Watch the demo](https://firebasestorage.googleapis.com/v0/b/karim-portfolio-bc1e8.appspot.com/o/deploy-admin-panel-login.png?alt=media&token=8c810ac6-0991-4852-a3ca-3e459ef88986)](https://firebasestorage.googleapis.com/v0/b/karim-portfolio-bc1e8.appspot.com/o/deploy-admin-panel-project.mp4?alt=media&token=e42c8217-078c-478f-81ab-e967bf190c7b)

---

## 📩 Weekly Audit Report

Each week, users receive a professional report highlighting:
- Number of unused **Security Groups**
- Number of unused **SSH Keys**
- Direct links to dashboard
- Example subject: `Weekly Unused Resources Report`
- Sample action: _"Visit your dashboard"_

---

## 🛡️ Security & Permissions

- All user data is scoped to the authenticated user
- Console commands and background jobs are authenticated properly
- Password reset flow is tokenized and secure
- User role and status are enforced through middleware

---

## 🔧 Setup Instructions

1. Clone the repo:
   ```bash
   git clone https://github.com/KarimPortfolio/deploy-admin-panel.git
   cd deploy-admin-panel

2. Install backend dependencies:
   composer install
   cp .env.example .env
   php artisan key:generate

3. Configure .env (DB, S3, mail settings)
4. Install frontend dependencies:
   cd frontend
   npm install

5. Run the project:
   php artisan serve
   npm run dev

## 🧪 Testing
This project includes feature tests to ensure critical functionality is working as expected.
To run the test suite:
   ```bash
   php artisan test
   ```


## 🤝 Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you'd like to change or improve.

## ✨ Credits
Made with ❤️ by Mohamed Karim Balla