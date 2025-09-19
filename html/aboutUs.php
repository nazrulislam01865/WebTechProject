<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - GoBus</title>
    <link rel="stylesheet" href="../css/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>

        <div class="header-right">
            <a href="tel:xxxxxxxxxxxxx" class="call-btn">Call +880</a>
            <a href="html/login.html" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
        </div>
    </header>
    <div class="container">
        <section class="about-content">
            <div class="about-text">
                <h2>About Us</h2>
                <p>GoBus was founded with a simple mission: to make bus travel more comfortable, reliable, and
                    accessible for everyone. Since our establishment, we've been connecting cities and communities
                    across the country with our modern fleet of buses and professional service.</p>

                <p>What started as a small local service has grown into one of the region's most trusted bus
                    transportation providers. We take pride in our commitment to safety, punctuality, and customer
                    satisfaction.</p>
            </div>

            <div class="about-image">
                <img src="../picture/bus.gif" alt="GoBus modern fleet">
            </div>
        </section>

        <section class="features">
            <h3>Why Choose GoBus?</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Safety First</h4>
                    <p>Our buses are regularly maintained and our drivers are highly trained professionals with
                        excellent safety records.</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <h4>Punctuality</h4>
                    <p>We understand the value of your time. Our buses depart and arrive on schedule, every time.
                    </p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-tags"></i>
                    <h4>Affordable Prices</h4>
                    <p>Enjoy comfortable travel at prices that won't break your budget. We offer competitive rates
                        for all routes.</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-concierge-bell"></i>
                    <h4>Quality Service</h4>
                    <p>From online booking to onboard experience, we strive to provide exceptional service at every
                        touchpoint.</p>
                </div>
            </div>
        </section>

        <section class="stats">
            <h3>GoBus By The Numbers</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">10+</span>
                    <span class="stat-label">Destinations</span>
                </div>

                <div class="stat-item">
                    <span class="stat-number">20+</span>
                    <span class="stat-label">Buses</span>
                </div>

                <div class="stat-item">
                    <span class="stat-number">2000+</span>
                    <span class="stat-label">Happy Passengers</span>
                </div>

                <div class="stat-item">
                    <span class="stat-number">1 year+</span>
                    <span class="stat-label">Years of Service</span>
                </div>
            </div>
        </section>

        <section class="mission">
            <div class="mission-content">
                <h3>Our Mission</h3>
                <p>At GoBus, our mission is to provide safe, reliable, and affordable transportation services that
                    connect people and communities. We strive to make bus travel a pleasant experience through
                    continuous improvement of our services and commitment to customer satisfaction.</p>

                <h3>Our Vision</h3>
                <p>To become the leading bus transportation service in the region, recognized for excellence in
                    safety, customer service, and innovation in the travel industry.</p>
            </div>
        </section>
    </div>

    <footer>
        <div class="footerContainer">
            <div class="footerSection">
                <h2>GO BUS</h2>
                <p>
                    gobus.com is a premium online booking portal which allows you to purchase ticket
                    for various bus booking services locally across the country.
                </p>
            </div>

            <div class="footerSection">
                <h3>About GoBUS</h3>
                <a href="../html/about us.html">About Us</a>
                <a href="../html/contact.html">Contact Us</a>
                <a href="../html/cancelTicket.html">Cancel Ticket</a>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="../html/terms.html">Terms and Condition</a>
                <a href="../html/privacy.html">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span></p>
        </div>
    </footer>
</body>

</html> -->




<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

try {

    $conn = new mysqli($servername, $username, $password);


    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }


    if (!$conn->query("CREATE DATABASE IF NOT EXISTS gobus")) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    $conn->select_db($dbname);
} catch (Exception $e) {
    error_log($e->getMessage());
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - GoBus</title>
    <link rel="stylesheet" href="../css/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8809611123456" class="call-btn">Call +880</a>
            <a href="login.php" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
        </div>
    </header>
    
    <div class="container">
        <section class="about-content">
            <div class="about-text">
                <h2>About Us</h2>
                <p>GoBus was founded with a simple mission: to make bus travel more comfortable, reliable, and
                    accessible for everyone. Since our establishment, we've been connecting cities and communities
                    across the country with our modern fleet of buses and professional service.</p>
                <p>What started as a small local service has grown into one of the region's most trusted bus
                    transportation providers. We take pride in our commitment to safety, punctuality, and customer
                    satisfaction.</p>
            </div>
            <div class="about-image">
                <img src="../picture/bus.gif" alt="GoBus modern fleet">
            </div>
        </section>

        <section class="features">
            <h3>Why Choose GoBus?</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Safety First</h4>
                    <p>Our buses are regularly maintained and our drivers are highly trained professionals with
                        excellent safety records.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <h4>Punctuality</h4>
                    <p>We understand the value of your time. Our buses depart and arrive on schedule, every time.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-tags"></i>
                    <h4>Affordable Prices</h4>
                    <p>Enjoy comfortable travel at prices that won't break your budget. We offer competitive rates
                        for all routes.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-concierge-bell"></i>
                    <h4>Quality Service</h4>
                    <p>From online booking to onboard experience, we strive to provide exceptional service at every
                        touchpoint.</p>
                </div>
            </div>
        </section>

        <section class="stats">
            <h3>GoBus By The Numbers</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">10+</span>
                    <span class="stat-label">Destinations</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">20+</span>
                    <span class="stat-label">Buses</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">2000+</span>
                    <span class="stat-label">Happy Passengers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">1 year+</span>
                    <span class="stat-label">Years of Service</span>
                </div>
            </div>
        </section>

        <section class="mission">
            <div class="mission-content">
                <h3>Our Mission</h3>
                <p>At GoBus, our mission is to provide safe, reliable, and affordable transportation services that
                    connect people and communities. We strive to make bus travel a pleasant experience through
                    continuous improvement of our services and commitment to customer satisfaction.</p>
                <h3>Our Vision</h3>
                <p>To become the leading bus transportation service in the region, recognized for excellence in
                    safety, customer service, and innovation in the travel industry.</p>
            </div>
        </section>
    </div>

    <footer>
        <div class="footerContainer">
            <div class="footerSection">
                <h2>GO BUS</h2>
                <p>
                    gobus.com is a premium online booking portal which allows you to purchase ticket
                    for various bus booking services locally across the country.
                </p>
            </div>
            <div class="footerSection">
                <h3>About GoBUS</h3>
                <a href="aboutus.php">About Us</a>
                <a href="contact.php">Contact Us</a>
                <a href="cancelTicket.php">Cancel Ticket</a>
            </div>
            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="terms.php">Terms and Condition</a>
                <a href="privacy.php">Privacy Policy</a>
            </div>
        </div>
        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>
</body>
</html>