🎓 Lab Rescheduling Management System

A web-based project built using PHP, MySQL, HTML, and CSS to manage laboratory rescheduling requests between students, coordinators, and instructors. This system simplifies reschedule requests, approvals, notifications, and attendance updates.

🚀 Project Overview

This system includes separate login portals for Students, Coordinators, and Instructors.
Each role has specific responsibilities:

Students submit reschedule requests.

Coordinators approve or reject requests.

Instructors notify students with updated lab schedules and mark attendance.

The entire workflow is managed through clean, simple, and user-friendly web pages.

✨ Features
👩‍🎓 Student Features

Login with email & password

Submit lab reschedule request

View lab schedule

👩‍🏫 Coordinator Features

Login to coordinator panel

View and approve student requests

Forward approved requests to respective instructors

Manage lab schedule (create/view/edit/delete)

👨‍🏫 Instructor Features

Login to instructor dashboard

Notify students with updated lab details

Mark attendance after rescheduled lab session

🖥 System Features

Clean and consistent dark-theme UI

Validation on forms

Organized pages with responsive design

Email notification to students & instructors (if server configured)

🛠 Technologies Used

PHP – Backend logic

MySQL – Database

HTML & CSS – Frontend

XAMPP – Local development

phpMyAdmin – Database management

📁 Project Structure (based on this repository)
Lab-Reschedule-Management/
│
├── css/
│   └── style.css
│
├── index.php
├── login.php
├── student_login.php
├── instructor_login.php
│
├── reshedule_request.php
├── lab_schedule_create.php
├── view_lab_schedule.php
├── edit_lab_schedule.php
├── delete_lab_schedule.php
│
├── coordinator_approval.php
├── notify_student.php
│
└── README.md



⚙️ How to Run This Project (XAMPP)

Install XAMPP.

Start Apache and MySQL from the XAMPP Control Panel.

Move your project folder to:

C:\xampp\htdocs\


Open phpMyAdmin:
http://localhost/phpmyadmin

Create a new database:

lab_reschedule_db


Import your .sql file (if available).

Run the project in your browser:

http://localhost/Lab-Reschedule-Management/


📌 Future Enhancements

Add secure password hashing

Improve responsive design

Merge all logins into one unified login page

Add notification history

Add attendance reports and charts

🙋‍♀️ Author

Rathini Rajendran
Lab Rescheduling Management System – University Project
