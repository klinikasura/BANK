<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<link rel="stylesheet" href="style-login.css">
<meta http-equiv="refresh" content="0;url=http://10.10.20.250/dashboard/ROBOT-BANK/login.php"/
</head>
<body>
<div class="container">
    <h2>LOGIN BANK ROBOT</h2>

    <?php
    session_start();
    if(isset($_SESSION['login_error'])){
        echo '<div class="alert">'.$_SESSION['login_error'].'</div>';
        unset($_SESSION['login_error']);
    }
    ?>

    <form action="proses_login.php" method="post">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required><br><br>

      <label for="password">Password:</label>
      <input type="text" id="password" name="password" required><br><br>

      <label for="level">Level:</label>
      <select id="level" name="level" required>
        <option value="">-- Pilih Level --</option>
        <option value="admin">Admin</option>
        <option value="karyawan">Karyawan</option>
      </select><br><br>

      <input type="submit" value="Login">
    </form>
</div>
</body>
</html>

