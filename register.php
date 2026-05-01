<?php
include 'config.php';

// Ensure users table exists before inserting
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $stmt->close();
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->bind_param('sss', $name, $email, $password_hash);
            if ($stmt->execute()) {
                header('Location: index.html');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again later.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Register - Shoes Online Store</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="Islem Mabrouk">
  <meta name="keywords" content="Online Store">
  <meta name="description" content="Stylish - Shoes Online Store">

  <link rel="stylesheet" href="css/vendor.css">
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,900;1,900&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>

<body>
  <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol xmlns="http://www.w3.org/2000/svg" id="navbar-icon" viewBox="0 0 16 16">
      <path d="M14 10.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 .5-.5zm0-3a.5.5 0 0 0-.5-.5h-7a.5.5 0 0 0 0 1h7a.5.5 0 0 0 .5-.5zm0-3a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0 0 1h11a.5.5 0 0 0 .5-.5z" />
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" id="user" viewBox="0 0 24 24">
      <path fill="currentColor" d="M12 2a5 5 0 1 0 5 5a5 5 0 0 0-5-5zm0 8a3 3 0 1 1 3-3a3 3 0 0 1-3 3zm9 11v-1a7 7 0 0 0-7-7h-4a7 7 0 0 0-7 7v1h2v-1a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v1z" />
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" id="shopping-cart" viewBox="0 0 24 24" fill="none">
      <path d="M21 4H2V6H4.3L7.582 15.025C7.79362 15.6029 8.1773 16.1021 8.68134 16.4552C9.18539 16.8083 9.78556 16.9985 10.401 17H19V15H10.401C9.982 15 9.604 14.735 9.461 14.342L8.973 13H18.246C19.136 13 19.926 12.402 20.169 11.549L21.962 5.275C22.0039 5.12615 22.0109 4.96962 21.9823 4.81763C21.9537 4.66565 21.8904 4.52234 21.7973 4.39889C21.7041 4.27544 21.5837 4.1752 21.4454 4.106C21.3071 4.0368 21.1546 4.00053 21 4ZM18.246 11H8.246L6.428 6H19.675L18.246 11Z" fill="black" />
      <path d="M10.5 21C11.3284 21 12 20.3284 12 19.5C12 18.6716 11.3284 18 10.5 18C9.67157 18 9 18.6716 9 19.5C9 20.3284 9.67157 21 10.5 21Z" fill="black" />
      <path d="M16.5 21C17.3284 21 18 20.3284 18 19.5C18 18.6716 17.3284 18 16.5 18C15.6716 18 15 18.6716 15 19.5C15 20.3284 15.6716 21 16.5 21Z" fill="black" />
    </symbol>
  </svg>
  <header id="header" class="site-header text-black">
    <nav id="header-nav" class="navbar navbar-expand-lg">
      <div class="container-lg">
        <a class="navbar-brand" href="index.html">
          <img src="images/main-logo.png" class="logo" alt="logo">
        </a>
        <button class="navbar-toggler d-flex d-lg-none order-3 border-0 p-1 ms-2" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#bdNavbar" aria-controls="bdNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <svg class="navbar-icon">
            <use xlink:href="#navbar-icon"></use>
          </svg>
        </button>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="bdNavbar">
          <div class="offcanvas-header px-4 pb-0">
            <a class="navbar-brand ps-3" href="index.html">
              <img src="images/main-logo.png" class="logo" alt="logo">
            </a>
            <button type="button" class="btn-close btn-close-black p-5" data-bs-dismiss="offcanvas" aria-label="Close"
              data-bs-target="#bdNavbar"></button>
          </div>
          <div class="offcanvas-body">
            <ul id="navbar" class="navbar-nav fw-bold justify-content-end align-items-center flex-grow-1">
              <li class="nav-item">
                <a class="nav-link me-5" href="index.html">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link me-5" href="men.html">Men</a>
              </li>
              <li class="nav-item">
                <a class="nav-link me-5" href="women.html">Women</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="user-items ps-0 ps-md-5">
          <ul class="d-flex justify-content-end list-unstyled align-item-center m-0">
            <li class="pe-3 login-link">
              <a href="login.html" class="border-0">
                <svg class="user" width="24" height="24">
                  <use xlink:href="#user"></use>
                </svg>
              </a>
            </li>
            <li class="pe-3">
              <a href="cart.html" class="border-0">
                <svg class="shopping-cart" width="24" height="24">
                  <use xlink:href="#shopping-cart"></use>
                </svg>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <section id="register" class="py-5">
    <div class="container-lg">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="login-detail bg-light p-5 rounded">
            <div class="text-center mb-4">
              <h2 class="display-6 fw-normal">Create a New Account</h2>
              <p class="text-muted">Fill in your details to register.</p>
            </div>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul class="mb-0">
                  <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
              <div class="alert alert-success">
                <?php echo $success; ?>
              </div>
            <?php endif; ?>

            <div class="login-form">
              <form method="post" action="register.php">
                <div class="mb-4">
                  <label for="name" class="form-label fw-bold">Name *</label>
                  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" placeholder="Enter your name" class="form-control ps-3 text-input" required>
                </div>
                <div class="mb-4">
                  <label for="email" class="form-label fw-bold">Email Address *</label>
                  <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="Enter your email" class="form-control ps-3 text-input" required>
                </div>
                <div class="mb-4">
                  <label for="password" class="form-label fw-bold">Password *</label>
                  <input type="password" name="password" id="password" placeholder="Enter your password" class="form-control ps-3 text-input" required>
                </div>
                <div class="mb-4">
                  <label for="confirm_password" class="form-label fw-bold">Confirm Password *</label>
                  <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" class="form-control ps-3 text-input" required>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-red btn-lg hvr-sweep-to-right">Register</button>
                </div>
              </form>
            </div>
            <div class="text-center mt-4">
              <p class="mb-0">Already have an account? <a href="login.html" class="text-decoration-none fw-bold">Login here</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="py-5 border-top">
    <div class="container-lg">
      <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
          <div class="footer-logo mb-3"><img src="images/main-logo.png" alt="logo" class="img-fluid"></div>
          <p class="mb-3">Buy good shoes and a good mattress, because when you're not in one you're in the other.</p>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6">
          <h5 class="mb-3">Quick Links</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="index.html" class="text-decoration-none">Home</a></li>
            <li class="mb-2"><a href="men.html" class="text-decoration-none">Men</a></li>
            <li class="mb-2"><a href="women.html" class="text-decoration-none">Women</a></li>
            <li class="mb-2"><a href="contact.html" class="text-decoration-none">Contact</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6">
          <h5 class="mb-3">Customer Service</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none">Size Guide</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Privacy Policy</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Terms of Service</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
          <h5 class="mb-3">Newsletter</h5>
          <p class="mb-3">Subscribe to our newsletter for the latest updates and offers.</p>
          <form>
            <div class="input-group mb-3">
              <input type="email" class="form-control" placeholder="Enter your email" aria-label="Email">
              <button class="btn btn-outline-dark" type="submit">Subscribe</button>
            </div>
          </form>
        </div>
      </div>
      <hr class="my-4">
      <div class="row">
        <div class="col-12 text-center">
          <p class="mb-0">&copy; 2026 Stylish Shoes Online Store. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="js/jquery-1.11.0.min (1).js"></script>
  <script src="js/plugins.js"></script>
  <script src="js/script.js"></script>
</body>

</html>
