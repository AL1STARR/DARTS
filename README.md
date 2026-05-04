# DARTS
## Document Archiving and Release Tracking System

<p align="center">
    <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql" alt="MySQL">
    <img src="https://img.shields.io/badge/Version-Beta-blue" alt="Status">
</p>

## Overview

*DARTS (Document Archiving and Release Tracking System)* is a web-based document management solution designed to digitalize and streamline the entire document request lifecycle—from submission and routing to processing, tracking, and archiving. It eliminates manual inefficiencies such as paper forms, misplaced files, and lack of real-time visibility.

The system serves multiple user roles:
- *Requesters* (Employees or Clients)
- *Routing Officers*
- *Assigned Personnel*
- *System Administrators & Archive Officers*

🚀 Built with **Laravel MVC**, **MySQL**, and **Google Authentication API**.


---

## Features

| Feature | Description |
|---------|-------------|
| 🔐 *User Management* | Email/password + Google SSO login, role-based permissions |
| 📝 *Document Request* | Submit requests with attachments, auto-generated Tracking ID |
| 🔄 *Multi-stage Routing* | Configurable approval workflows with escalation |
| 👥 *Assignment Module* | Assign requests to specific personnel |
| 📢 *Notifications* | Automated email/in-app alerts on status changes |
| 📦 *Archiving* | Completed requests stored with full history (no duplication) |
| 📊 *Dashboards & Reports* | Real-time request summaries, charts, exportable reports |

---

## Screenshots

### Login Page
![Login Interface](./screenshots/login.png)

### Dashboard
![Dashboard](./screenshots/dashboard.png)

### Create Document Request
![Create Request](./screenshots/create-request.png)

### Tracking Page
![Tracking](./screenshots/tracking.png)

### Admin Panel
![Admin Panel](./screenshots/admin-panel.png)

### Reports & Analytics
![Reports](./screenshots/reports.png)

---

## System Architecture

- *Type* : Web Application
- *Architecture* : MVC (Model-View-Controller), Client-Server
- *Backend* : Laravel PHP Framework
- *Frontend* : Blade templates, HTML5, CSS3
- *Database* : MySQL (via Eloquent ORM)
- *Authentication* : Laravel Breeze/Jetstream + Google OAuth 2.0

### Core Modules
- User Management
- Document Request Module
- Routing Module
- Assignment Module
- Notification Module
- Archive Module
- Dashboard / Reporting

---

## Technologies & Tools

| Category          | Tools |
|-------------------|-------|
| Language          | PHP 8.1+ |
| Framework         | Laravel 10.x |
| Database          | MySQL 8.0 / SQLite (dev) |
| Frontend          | Blade, HTML, CSS, Bootstrap/Tailwind |
| Authentication    | Google OAuth 2.0 API |
| Dev Environment   | XAMPP, VS Code, Git, Postman |


---

## Dependencies

### PHP Dependencies (Composer)
```json
{
    "php": "^8.1",
    "laravel/framework": "^10.0",
    "laravel/sanctum": "^3.2",
    "laravel/jetstream": "^2.0",
    "laravel/fortify": "^1.15",
    "socialiteproviders/google": "^4.0",
    "spatie/laravel-permission": "^5.5"
}