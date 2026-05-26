<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>myROBOT-V80</title>

<link rel="icon" href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING%20APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', sans-serif;
    }

    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #0f172a, #1e3a8a, #0ea5e9);
    }

    .container {
        width: 100%;
        max-width: 420px;
        padding: 30px;
    }

    .card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        color: white;
    }

    .logo {
        text-align: center;
        margin-bottom: 20px;
    }

    .logo h2 {
        font-size: 22px;
        letter-spacing: 1px;
    }

    .alert {
        background: rgba(255, 0, 0, 0.2);
        border: 1px solid rgba(255, 0, 0, 0.4);
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    label {
        font-size: 13px;
        margin-top: 10px;
        display: block;
        opacity: 0.9;
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin-top: 5px;
        border-radius: 12px;
        border: none;
        outline: none;
        background: rgba(255,255,255,0.15);
        color: white;
    }

    input::placeholder {
        color: #ddd;
    }

    select option {
        color: black;
    }

    .btn {
        width: 100%;
        padding: 12px;
        margin-top: 20px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(90deg, #22c55e, #0ea5e9);
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .footer {
        text-align: center;
        margin-top: 15px;
        font-size: 12px;
        opacity: 0.7;
    }
</style>
</head>

<body>

<div class="container">
    <div class="card">

        <div class="logo">
            <h2>BANK-myROBOT-V80</h2>
            <p style="font-size:12px; opacity:0.7;">Secure Login System</p>
        </div>

        <?php
        session_start();
        if(isset($_SESSION['login_error'])){
            echo '<div class="alert">'.$_SESSION['login_error'].'</div>';
            unset($_SESSION['login_error']);
        }
        ?>

        <form action="proses_login.php" method="post">

            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>

            <label>Password</label>
            <input type="text" name="password" placeholder="Masukkan password" required>

            <label>Level</label>
            <select name="level" required>
                <option value="">-- Pilih Level --</option>
                <option value="admin">Admin</option>
                <option value="karyawan">Karyawan</option>
            </select>

            <button class="btn" type="submit">Login</button>
        </form>

        <div class="footer">
           myROBOT-V80 Secure Banking System
        </div>

    </div>
</div>

</body>
</html>
