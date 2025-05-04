# 🌟 K-Lingo: Korean Language Learning Quiz Platform 🇰🇷

> Master Korean vocabulary through interactive quizzes, bilingual support (English/Nepali), and structured lesson plans.
## Dashboard:
![K-Lingo Demo 1](https://drive.google.com/uc?id=1ALZfiBsB76zRO6niLq9GmvhOX7WNwaYd)  
## Learning:
![K-Lingo Demo 2](https://drive.google.com/uc?id=1FXeObAoWj5_yO7hftAhQcPN8WKH2aCJX)
## Quiz Interface:
![K-Lingo Demo 3](https://drive.google.com/uc?id=1s0s8NQ78guSbTFWk48_wZzygvWMmUlGc)

---

## ✨ Key Features

### 🎛️ Role-Based Access
- **Admin Panel**  
  - Manage lessons and vocabulary  
  - Upload words with KR/EN/NE translations  
  - Create and manage other admins  
  - Monitor user progress and performance  
- **User Dashboard**  
  - Track lesson completion (% based)  
  - View quiz performance stats  
  - Start lessons and attempt quizzes  

### 📚 Learning System
- Structured, progressive lessons:
  - Lesson 1: EPS lesson 1  
  - Lesson 2:  EPS lesson 2   
  - Lesson 3:  EPS lesson 3  
- Bilingual support: English & Nepali  
- Progress tracking system  

### 🎯 Quiz Mechanics
- Interactive multiple-choice questions  
- Immediate feedback after each answer  
- Score tracking per lesson  
- Adaptive difficulty  

---

## 💻 Tech Stack
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **Backend**: PHP 8.0
- **Database**: MySQL
- **Authentication**: Session-based
- **Hosting Compatibility**: XAMPP, InfinityFree, or similar

---

## 🛠️ Installation Guide

### Requirements
- PHP 8.0 or higher
- MySQL Server
- Modern web browser (Chrome, Firefox, Edge)

### Setup Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Dhiren1211/klingo.git
   ```

2. **Database Setup**
   - Create a new MySQL database (e.g., `klingo_db`).
   - Import the SQL schema:
     - Locate `/database/klingo_db.sql`
     - Import it into your created database.

3. **Configure Database Connection**
   - Open `/includes/db.php` and update with your database credentials:
   ```php
   $servername = "localhost";
   $username = "your_db_username";
   $password = "your_db_password";
   $database = "klingo_db";
   ```

4. **Admin Account login **
   - from login page 
   - superadmin:- superadmin@gmail.com.
   - password:- 12345.
   - then you can create your own admins. 

5. **Run the Application**
   - If using **XAMPP**, place the project inside the `htdocs/` folder.
   - Then visit:
   ```
   http://localhost/klingo
   ```

---

## 📖 Usage Guide

### For Admins 👩‍💻

- **Upload Vocabulary**
  - Go to "Upload Words"
  - Format CSV like:
    ```csv
    lesson_id,korean,english,nepali
    2,가수,Singer,गायक
    ```

- **Manage Lessons**
  - Add,  🔜 edit, or delete lessons.
  - 🔜 Update existing vocabulary.

- **Monitor Users**
  - View detailed user progress.
  - Analyze quiz performances.

---

### For Learners 🧑‍🎓

- **Start Learning**
  - Select a lesson from the dashboard.
  - Learn vocabulary with translations.

- **Take Quizzes**
  - Choose a difficulty level.
  - Attempt quizzes and receive instant feedback.
  - Complete lessons and level up!

- **Track Progress**
  - Monitor your completed lessons.
  - Analyze correct vs attempted answers.

---

## 🗺️ Roadmap

- ✅ Web Version Completed
- 🔜 Mobile App (Flutter)
- 🔜 Voice Pronunciation Support
- 🔜 Achievement Badge System
- 🔜 Multiplayer Quiz Mode
- 🔜 Social Sharing Features

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

1. **Fork the repository**
2. **Create a new feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Commit your changes**
   ```bash
   git commit -m "Add: your feature description"
   ```
4. **Push to your branch**
   ```bash
   git push origin feature/your-feature-name
   ```
5. **Open a Pull Request**

---

## 📄 License

© 2025 KLingo. All rights reserved.  
For licensing or collaboration inquiries, please contact the project maintainers.

---
