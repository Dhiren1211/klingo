<header>
    <nav>
        <div class="logo">
            <a href="/klingo/views/dashboard.php"><img src="/klingo/assets/images/logo.png" alt="logo" srcset="" style="width: 150px; height: 50px;"></a>
        </div>
        <ul>
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
<Style>
    header {
        background-color:rgba(83, 83, 83, 0.41);
        color: #efff;
        padding: 10px 0;
        border-radius:10px;
    }

    nav {
        display: flex;
        justify-content: space-between;
        align-items: left;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 10px;
    }

    .logo {
        font-size: 24px;
        font-weight: bold;
    }

    .logo a {
        color: #fff;
        text-decoration: none;
    }

    ul {
        list-style: none;
        display: flex;
        gap: 20px;
    }

    li {
        font-size: 16px;
    }

    li a {
        color: #fff;
        text-decoration: none;  
    }
</Style>