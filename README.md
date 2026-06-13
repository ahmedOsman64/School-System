# Modern School Management System

A comprehensive, professional, and easy-to-use School Management System built with PHP, MySQL, and AdminLTE. This system is designed to streamline school operations, including student management, attendance tracking, finance, and examination results.

## 🚀 Key Features

### 📊 Professional Dashboard
* **Dynamic Statistics**: Real-time counts of Students, Teachers, Classes, and Total Revenue.
* **Modern UI**: Clean and responsive design using AdminLTE 3.

### 📅 Advanced Attendance System
* **Premium Dashboard**: Visual summary of attendance with circular progress bars (Present, Absent, Late).
* **Weekly Attendance Grid**: Detailed tracking of student attendance across the week.
* **Printable Reports**: One-click professional weekly attendance reports.

### 🔐 Secure Authentication
* **Session-based Login**: Secure login system with role-based access.
* **User Management**: Create and manage staff accounts with different roles (Super Admin, Admin, Staff, Finance).

### 💰 Finance & Fee Management
* **Fee Types**: Create and manage different types of fees (Monthly, Admission, etc.).
* **Class-Specific Fees**: Associate fees with specific classes or keep them universal.
* **Payment Tracking**: Record and monitor student fee payments.

### 🧑‍🎓 Student & Teacher Management
* **Student Profiles**: Comprehensive student records including admission details and documents.
* **Teacher Management**: Manage teacher profiles and assigned subjects.
* **Promotions**: Seamlessly promote students from one class to another.

### 📝 Examination & Results
* **Marks Entry**: Easy entry for exam results by subject and class.
* **Bulk Import**: Support for importing marks and student data via CSV/Excel.
* **Result View**: View academic performance summaries.

## 🛠️ Technology Stack
* **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 4, AdminLTE 3.
* **Backend**: PHP 7.4+ (Procedural / Prepared Statements).
* **Database**: MySQL.
* **Icons**: FontAwesome 5, Ionicons.

## 📦 Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/ahmedOsman64/School-System.git
   ```
2. **Setup Database**:
   - Create a database named `school_systemdb` in your MySQL server.
   - Import the `schema.sql` and `finance_exam_tables.sql` files to initialize the tables.
3. **Configure Database Connection**:
   - Open `lib/db.php` and update the database credentials (host, user, password, port).
4. **Deploy**:
   - Move the project folder to your web server (e.g., XAMPP `htdocs`).
   - Access the system via `http://localhost/school-system/login.php`.

## 🤝 Contributing
Contributions are welcome! If you have any suggestions or bug reports, please open an issue or submit a pull request.

## 📄 License
This project is licensed under the MIT License.
