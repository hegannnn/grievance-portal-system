# Online Grievance Redressal System

A simple PHP + MySQL portal where students raise hostel, academic and facility complaints, track their status, and staff resolve them from a prioritized queue.

## Setup
1. Install XAMPP and start **Apache** and **MySQL**.
2. Copy this folder into `C:\xampp\htdocs\` (folder name must be `grievance-portal`).
3. Open `http://localhost/phpmyadmin`, go to **Import**, and import `database/schema.sql`.
4. Open `http://localhost/grievance-portal/`.
5. To make a staff account, register with the staff code set in `config/db.php`.