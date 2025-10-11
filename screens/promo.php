<?php
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flash Sale - GoBus</title>
    <link rel="stylesheet" href="../css/flashsale.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8809611123456" class="call-btn">Call +8801234567890</a>
            <a href="./html/login.php" class="login-btn"><img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
           
    </header>
    
    <div class="container">
        <main>
            <section class="flashsale-header">
                <h2>Travel Season Flash Sale</h2>
                <p>Limited time offers! Book now and save big on your next journey</p>
            </section>
            
            <!-- Banner Section -->
            <section class="banner-section">
                <div class="banner-card">
                    <div class="banner-content">
                        <div class="banner-text">
                            <span class="validity">Validity: Till 31 JANUARY 2026</span>
                            <h1>Save Upto Tk 4000 With Bkash</h1>
                            <h2 class="bigger-text">BIGGER EVERYTHING</h2>
                            <p>Get up to BDT 4,000 Cashback on bookings us with bKash Payment</p>
                        </div>
                        <div class="banner-image">
                            <img src="../picture/travBk.png" alt="bkash Icon" style="width: 570px; height: 300px; vertical-align: middle;">
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Discount Offers Section -->
            <section class="discount-offers">
                <h2>Special Discount Offers</h2>
                <div class="offers-grid">
                    <!-- First User Discount -->
                    <div class="offer-card">
                        <div class="offer-icon">
                            <img src="../picture/human.png" alt="User Icon" style="width: 40px; height: 45px; vertical-align: middle;">
                        </div>
                        <h3>First User Discount</h3>
                        <div class="discount-amount">15% OFF</div>
                        <p>Special discount for first-time users of GoBus. Create an account and get 15% off on your first booking.</p>
                        <div class="offer-code">
                            <span>Use Code: </span>
                            <strong>GOBUS15</strong>
                        </div>
                    </div>
                    
                    <!-- bKash Payment Discount -->
                    <div class="offer-card">
                        <div class="offer-icon">
                            <div class="icon"><img src="../picture/bkash.png" alt="call Icon" style="width: 45px; height: 45px; vertical-align: middle;"></div>
                   
                        </div>
                        <h3>bKash Payment</h3>
                        <div class="discount-amount">10% OFF</div>
                        <p>Pay with bKash and get 10% instant cashback on your ticket booking. Maximum cashback Tk 4000.</p>
                        <div class="offer-code">
                            <span>Use Code: </span>
                            <strong>BKASH10</strong>
                        </div>
    
                    </div>
                    
                    <!-- Card Payment Discount -->
                    <div class="offer-card">
                        <div class="offer-icon">
                            <img src="../picture/card.png" alt="User Icon" style="width: 40px; height: 45px; vertical-align: middle;">
                        </div>
                        <h3>Card Payment</h3>
                        <div class="discount-amount">8% OFF</div>
                        <p>Use your credit or debit card to pay and get 8% discount on your booking. Secure and easy payment.</p>
                        <div class="offer-code">
                            <span>Use Code: </span>
                            <strong>CARD8</strong>
                        </div>
                        
                    </div>
                </div>
            </section>
            
            <!-- Terms and Conditions -->
            <section class="terms-section">
                <h3>Terms & Conditions</h3>
                <ul>
                    <li>Offers are valid till 31st January 2026</li>
                    <li>Discounts cannot be combined with other promotions</li>
                    <li>Cashback will be credited within 24 hours of booking</li>
                    <li>Maximum cashback for bKash payment is Tk 4000</li>
                    <li>Offers are applicable on selected routes only</li>
                    <li>GoBus reserves the right to modify or cancel offers at any time</li>
                </ul>
            </section>
        </main>
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
                <a href="../html/aboutus.php">About Us</a>
                <a href="contact.php">Contact Us</a>
                <a href="../html/cancelTicket.php">Cancel Ticket</a>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="../html/terms.php">Terms and Condition</a>
                <a href="../html/privacy.php">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>
</body>
</html>