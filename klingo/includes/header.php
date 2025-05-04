<header>
    <nav>
        <div class="logo">
            <a href="/klingo/views/dashboard.php">
                <img src="/klingo/assets/images/logo.png" alt="logo" style="width: 150px; height: 50px;">
            </a>
        </div>
        <div class="hamburger" onclick="toggleMenu()">☰</div>
        <ul id="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="/klingo/views/dashboard.php">Dashboard</a></li>
                <li><a href="/klingo/views/learn.php">Learn</a></li>
                <li><a href="/klingo/views/quiz.php">Quiz</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="/klingo/views/admin/upload.php">Upload Words</a></li>
                <?php endif; ?>
                <?php if (isSuperAdmin()): ?>
                    <li><a href="/klingo/views/superadmin/manage-admins.php">Manage Admins</a></li>
                <?php endif; ?>
                <li><a href="/klingo/api/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="/klingo/views/login.php">Login</a></li>
                <li><a href="/klingo/views/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<style>
    header {
        z-index: 9999;
        background-color: rgba(83, 83, 83, 0.41);
        color: #efff;
        padding: 10px 0;
        border-radius: 10px;
    }

    nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 10px;
        position: relative;
    }

    .logo a {
        text-decoration: none;
    }

    ul {
        list-style: none;
        display: flex;
        gap: 20px;
        transition: all 0.3s ease-in-out;
    }

    li a {
        color: #fff;
        text-decoration: none;
        font-size: 16px;
    }

    .hamburger {
        display: none;
        font-size: 26px;
        color: white;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .hamburger {
            display: block;
        }

        ul {
            z-index: 999;
            flex-direction: column;
            position: absolute;
            top: 60px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.9);
            width: 200px;
            padding: 10px;
            display: none;
            border-radius: 10px;
        }

        ul.active {
            display: flex;
        }

        li {
            margin: 10px 0;
        }
    }
</style>

<script>
    function toggleMenu() {
        const navLinks = document.getElementById('nav-links');
        navLinks.classList.toggle('active');
    }
</script>
