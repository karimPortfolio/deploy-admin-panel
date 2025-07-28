# 🖥️ Cloud Resource Manager

A modern, admin-focused dashboard for managing servers, security groups, and SSH keys. The platform helps you stay on top of your cloud resources with a weekly audit, intuitive UI, and automated alerts — all with support for dark/light mode and secure authentication.

## 🚀 Features

- ✅ **Manage Servers**
  - Create, view, edit, and delete server resources
  - Filter and search functionality
  - Assign security groups and ssh keys to servers
  - Assign VPC, OS family and instance type to servers.
- ✅ **Security Groups**
  - Create, view, edit, and delete security group resources
  - Filter and search functionality
  - Identify unused groups with automated auditing
- ✅ **SSH Key Management**
  - Add and remove SSH keys
  - Filter and search functionality
  - Identify unused keys
- 📊 **Dashboard & Weekly Audit**
  - Visualize resource usage at a glance
  - Automated email & in-app notifications for unused resources
- 🔐 **Authentication & Security**
  - Login, Forget Password, and Reset Password
  - Authenticated user scoping for data access
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

---

## 📷 Screenshots



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

---

## 🔧 Setup Instructions

1. Clone the repo:
   ```bash
   git clone https://github.com/KarimPortfolio/cloud-resource-manager.git
   cd cloud-resource-manager

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