<?php
require 'includes/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($email) || empty($password)) {
      $message = "Email and password are required!";
  } else {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password'])) {
          // Successful login
          session_start();
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          header("Location: about");
          exit;
      } else {
          $message = "Invalid email or password!";
      }
  }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login - BrunoChic</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <?php include_once "includes/links.php" ?>

<style>

body {
    background: #f8f8f8;
}
.main {
  background: #f8f8f8;
  /* padding: 150px 0; */
}

.sign-container {
  width: 900px;
  background: #fff;
  margin: 0 auto;
  box-shadow: 0px 15px 16.83px 0.17px rgba(0, 0, 0, 0.05);
  border-radius: 20px;
}

.signup-content {
    display: flex;
    padding: 75px 0;
}

.signup {
    margin-bottom: 150px;
}

.signup-form, .signup-image {
    width: 50%;
}

.form-title {
    margin-bottom: 30px;
}

.signup-image {
    margin-top: 45px;
    display: flex;
    flex-direction: column; 
    justify-content: space-between;
}

.signup-form {
  margin-left: 75px;
  margin-right: 75px;
  padding-left: 34px; 
}

.form-submit {
  display: inline-block;
  background: #6dabe4;
  color: #fff;
  border-bottom: none;
  width: auto;
  padding: 15px 39px;
  border-radius: 5px;
  margin-top: 25px;
  cursor: pointer; 
}

.form-submit:hover {
    background: #4292dc; 
}
.form-group {
    position: relative;
    margin-bottom: 25px;
    overflow: hidden;
}
.form-group:last-child {
    margin-bottom: 0px; 
}

input {
    width: 100%;
    display: block;
    border: none;
    border-bottom: 1px solid #999;
    padding: 6px 30px;
    box-sizing: border-box;
}
input::placeholder {
  color: #999;
  font-size: 13px;
}
input:focus {
    outline: none;
    border-bottom: 1px solid #222;
}

input:focus::placeholder {
  color: #222;
}

.agree-term {
    display: inline-block;
    width: auto;
    margin-right: 15px;
    margin-bottom: 3px;
    background: white;
}

label {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    color: #222; 
}
.label-agree-term {
    position: relative;
    top: 0%;
    transform: translateY(0);
    font-size: 14px;
}

.signup-image-link {
  font-size: 14px;
  color: #222;
  display: block;
  text-align: center;
  margin-bottom: 10px;
  text-decoration: underline;
 
}


figure {
  margin-bottom: 50px;
  margin-top: 110px;
  text-align: center; 
}
.term-service {
    text-decoration: underline;
}

</style>

</head>

<body class="contact-page">
<?php include_once "includes/navbar.php" ?>

<main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.webp);">
      <div class="container position-relative">
        <h1>Sign In</h1>
        
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index">Home</a></li>
            <li class="current">Sign In</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Contact Section -->
<section id="signup" class="signup section" style="background: #f8f8f8;">
    <div class="sign-container">
        <div class="signup-content">
            <div class="signup-form">
                <h2 class="form-title">Sign In</h2>

                <?php if (!empty($message)): ?>
                    <p style="color: <?php echo strpos($message, 'Invalid') !== false ? 'red' : 'green'; ?>;">
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                <?php endif; ?>


                <form class="" action="" method="POST">
                    <div class="form-group">
                        <label for="username"><i class="bi bi-person-fill"></i></label>
                        <input type="text" name="username" id="username" placeholder="Your Username">
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="bi bi-envelope-arrow-down"></i></label>
                        <input type="email" name="email" id="email" placeholder="Your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="bi bi-lock-fill"></i></label>
                        <input type="password" name="password" id="password" placeholder="Password">
                    </div>
                  
                    <div class="form-group form-button">
                        <input type="submit" name="login" id="login" class="form-submit" value="Login">
                    </div>
                </form>
            </div>
            <div class="signup-image">
                <figure><img src="assets/img/logo.png" alt="signup"></figure>
                <a href="signup" class="signup-image-link">Create an Account</a>
            </div>
        </div>
    </div>

    </section><!-- /Login Section -->


  </main>

  <?php include_once "includes/footer.php" ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

<?php include_once "includes/scripts.php" ?>

</body>

</html>