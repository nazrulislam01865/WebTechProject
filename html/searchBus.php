<?php
session_start();
?>

<!-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS|Search Bus</title>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel = "stylesheet" type="text/css" href="../css/searchBus.css">
    </head>
    <body>
        <header>
            <div class="logo">Go<span id = "logo">Bus</span></div>
        
            <div class="header-right">
            <a href="tel:xxxxxxxxxxxxx" class="call-btn">Call xxxx</a>
            <a href="html/login.html" class="login-btn"><i class = "fa-solid fa-user-circle"></i> Login</a>
            </div>
        </header>

        <div class="search-info">
            <div class="search-details">
                <span>ONWARD</span>
                <span>Dhaka To Barisal On 29 Aug 2025</span>
            </div>
            <div class="modify-search">
                <button>MODIFY SEARCH</button>
            </div>
            <div class="stats">
                <span>Total Buses Found:</span>
                <span>Total Operators Found:</span>
                <span>Total Seats Available:</span>
            </div>
        </div>

        <div class = "container">
            <div class = "bus-details">
                <li>
                    <div class = "bus-details-new-left">
                        <h3>Sakura paribahan</h3>
                        <h6>102. SBD-BSL (PADMA)*</h6>
                        <div class = "non-ac-bus_couch-type">
                            <i class="fa-solid fa-snowflake"></i><span>Non AC</span>
                        </div>
                        <a href="#">Cancellation policy</a>
                    </div>
                </li>
                <li>
                    <div class = "bus-details-new-middle">
                        <div class = "middle-left">
                            <h6>Starting</h6>
                            <h5>5:00 AM</h5>
                            <h6>Saydabad Terminal -1</h6>
                        </div>

                        <div class = "middle-middle">
                            <div class = "bus-image">
                                <img src="../picture/bus.png">
                            </div>
                            <h6 class ="seat-left">Seat left:</h6>
                        </div>

                        <div class = "middle-right">
                            <h6>Arrival</h6>
                            <h5>7:30 AM</h5>
                            <h6>Barisal Terminal</h6>
                        </div>
                    </div>
                </li>
                <li>
                    <div class = "bus-details-new-right">
                        <div class = "no-extra-charge">No Extra Charge</div>
                        <div class = "price">
                            <h3>550.00 tk</h3>
                        </div>
                        <div class = "view-seat">
                            <button class="view-seat-btn">View Seat</button>
                        </div>
                    </div>
                </li>
            </div>

            <div id="seatSelectionContainer" class="seat-selection-container">
            <div class="seat-legend">
                <span class="legend-item booked-m"><i class = "fa-solid fa-sofa"></i>BOOKED</span>
                <span class="legend-item blocked">BLOCKED</span>
                <span class="legend-item available">AVAILABLE</span>
                <span class="legend-item selected">SELECTED</span>
                <span class="legend-item sold-m">SOLD</span>
            </div>
            <div class="seat-selection-content">
                <div class="seat-layout">
                    # Seat layout will be dynamically generated here
                </div>
                <div class="selection-details">
                    <div class="boarding-dropping">
                        <div>
                            <label>BOARDING POINT*</label>
                            <select>
                                <option value="05:00 AM">05:00 AM - Saydabad(D)</option>
                            </select>
                        </div>
                        <div>
                            <label>DROPPING POINT*</label>
                            <select>
                                <option value="select">Select dropping point</option>
                            </select>
                        </div>
                    </div>
                    <div class="mobile-number">
                        <label>PHONE NUMBER*</label>
                        <input type="text" placeholder="Enter phone number">
                    </div>
                    <button class="submit-btn">SUBMIT</button>
                    <p>I have already have a account. <a href="login.html">Login with password</a>.</p>
                    <p>By logging in you are agreeing to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Notice of GoBUS</a></p>
                    <div class="seat-info">
                        <p>SEAT INFORMATION:</p>
                        <p>Seat Fare: 0 Tk</p>
                        <p>Service Charge: 0 Tk</p>
                        <p>PGW Charge: 0 Tk</p>
                    </div>
                </div>
            </div>
        </div>

            <div class = "bus-details">
                <li>
                    <div class = "bus-details-new-left">
                        <h3>Shyamoli NR Travels</h3>
                        <h6>6301-Barisal</h6>
                        <div class = "non-ac-bus_couch-type">
                            <i class="fa-solid fa-snowflake"></i><span>Non AC</span>
                        </div>
                        <a href="#">Cancellation policy</a>
                    </div>
                </li>
                <li>
                    <div class = "bus-details-new-middle">
                        <div class = "middle-left">
                            <h6>Starting</h6>
                            <h5>6:00 AM</h5>
                            <h6>Rainkhola Counter</h6>
                        </div>

                        <div class = "middle-middle">
                            <div class = "bus-image">
                                <img src="../picture/bus.png">
                            </div>
                            <h6 class ="seat-left">Seat left:</h6>
                        </div>

                        <div class = "middle-right">
                            <h6>Arrival</h6>
                            <h5>11:00 AM</h5>
                            <h6>Barisal Terminal</h6>
                        </div>
                    </div>
                </li>
                <li>
                    <div class = "bus-details-new-right">
                        <div class = "no-extra-charge">No Extra Charge</div>
                        <div class = "price">
                            <h3>450.00 tk</h3>
                        </div>
                        <div class = "view-seat">
                            <button  class="view-seat-btn">View Seat</button>
                        </div>
                    </div>
                </li>
            </div>
        </div>

        <footer>
            <div class = "footerContainer">
                <div class = "footerSection">
                    <h2>GO BUS</h2>
                    <p>
                        gobus.com is a premium online booking portal which allows you to purchase ticket 
                        for various bus booking services locally across the country.
                    </p>
                </div>

                <div class = "footerSection">
                    <h3>About GoBUS</h3>
                    <a href="#">About Us</a>
                    <a href="#">Contact Us</a>
                    <a href="cancelTicket.html">Cancel Ticket</a>
                </div>

                <div class = "footerSection">
                    <h3>Company Info</h3>
                    <a href="">Terms and Condition</a>
                    <a href="">Privacy Policy</a>
                </div>
            </div>

            <div class="footerBottom">
                Copyright &copy;2025 | All Rights Reserved Designed by <span class = "designer">Group 1</span></p>
            </div>
        </footer>

        <script>
            document.querySelector('.view-seat-btn').addEventListener('click', function() {
                const seatContainer = document.getElementById('seatSelectionContainer');
                seatContainer.style.display = seatContainer.style.display === 'block' ? 'none' : 'block';

                // Dynamically generate seat layout (example 5x4 grid)
                // const seatLayout = document.querySelector('.seat-layout');
                // seatLayout.innerHTML = '';
                // for (let i = 0; i < 20; i++) {
                //     const seat = document.createElement('div');
                //     seat.classList.add('seat');
                //     if (i === 5) seat.classList.add('selected');
                //     else if ([0, 1, 2, 3].includes(i)) seat.classList.add('booked-m');
                //     else if ([4].includes(i)) seat.classList.add('blocked');
                //     else if ([6, 7].includes(i)) seat.classList.add('sold-m');
                //     else seat.classList.add('available');
                //     seatLayout.appendChild(seat);
                // }
            });
        </script>
    </body>
</html> 

 -->



<!-- 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS|Search Bus</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="../css/searchBus.css">
    </head>
    <body>
        <header>
            <div class="logo">Go<span id="logo">Bus</span></div>
            <div class="header-right">
                <a href="tel:xxxxxxxxxxxxx" class="call-btn">Call xxxx</a>
                <a href="html/login.html" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
            </div>
        </header>

        <div class="search-info">
            <div class="search-details">
                <span>ONWARD</span>
                <span>Dhaka To Barisal On 29 Aug 2025</span>
            </div>
            <div class="modify-search">
                <button>MODIFY SEARCH</button>
            </div>
            <div class="stats">
                <span>Total Buses Found:</span>
                <span>Total Operators Found:</span>
                <span>Total Seats Available:</span>
            </div>
        </div>

        <div class="container">
            <div class="bus-details">
                <li>
                    <div class="bus-details-new-left">
                        <h3>Sakura paribahan</h3>
                        <h6>102. SBD-BSL (PADMA)*</h6>
                        <div class="non-ac-bus_couch-type">
                            <i class="fa-solid fa-snowflake"></i><span>Non AC</span>
                        </div>
                        <a href="#">Cancellation policy</a>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-middle">
                        <div class="middle-left">
                            <h6>Starting</h6>
                            <h5>5:00 AM</h5>
                            <h6>Saydabad Terminal -1</h6>
                        </div>
                        <div class="middle-middle">
                            <div class="bus-image">
                                <img src="../picture/bus.png">
                            </div>
                            <h6 class="seat-left">Seat left:</h6>
                        </div>
                        <div class="middle-right">
                            <h6>Arrival</h6>
                            <h5>7:30 AM</h5>
                            <h6>Barisal Terminal</h6>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-right">
                        <div class="no-extra-charge">No Extra Charge</div>
                        <div class="price">
                            <h3>550.00 tk</h3>
                        </div>
                        <div class="view-seat">
                            <button class="view-seat-btn">View Seat</button>
                        </div>
                    </div>
                </li>
            </div>

        
            <div id="seatSelectionContainer" class="seat-selection-container">
                <div class="seat-legend">
                    <span class="legend-item booked-m"><i class="fa-solid fa-sofa"></i>BOOKED</span>
                    <span class="legend-item blocked">BLOCKED</span>
                    <span class="legend-item available">AVAILABLE</span>
                    <span class="legend-item selected">SELECTED</span>
                    <span class="legend-item sold-m">SOLD</span>
                </div>
                <div class="seat-selection-content">
                    <ul class="seat-layout">

                    </ul>
                    <div class="selection-details">
                        <div class="boarding-dropping">
                            <div>
                                <label>BOARDING POINT*</label>
                                <select>
                                    <option value="05:00 AM">05:00 AM - Saydabad(D)</option>
                                </select>
                            </div>
                            <div>
                                <label>DROPPING POINT*</label>
                                <select>
                                    <option value="select">Select dropping point</option>
                                </select>
                            </div>
                        </div>
                        <div class="mobile-number">
                            <label>PHONE NUMBER*</label>
                            <input type="text" placeholder="Enter phone number">
                        </div>
                        <button class="submit-btn">SUBMIT</button>
                        <p>I have already have a account. <a href="login.html">Login with password</a>.</p>
                        <p>By logging in you are agreeing to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Notice of GoBUS</a></p>
                        <div class="seat-info">
                            <p>SEAT INFORMATION:</p>
                            <p>Seat Fare: 0 Tk</p>
                            <p>Service Charge: 0 Tk</p>
                            <p>PGW Charge: 0 Tk</p>
                        </div>
                    </div>
                </div>
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
                    <a href="#">About Us</a>
                    <a href="#">Contact Us</a>
                    <a href="cancelTicket.html">Cancel Ticket</a>
                </div>

                <div class="footerSection">
                    <h3>Company Info</h3>
                    <a href="">Terms and Condition</a>
                    <a href="">Privacy Policy</a>
                </div>
            </div>

            <div class="footerBottom">
                Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
            </div>
        </footer>

        <script>
            document.querySelectorAll('.view-seat-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const seatContainer = document.getElementById('seatSelectionContainer');
                    const isVisible = seatContainer.style.display === 'block';
                    seatContainer.style.display = isVisible ? 'none' : 'block';

                    if (!isVisible) {
                        // Get the price for this bus
                        const selectedPrice = this.closest('.bus-details').querySelector('.price h3').textContent;
                        const seatFareP = document.querySelector('.seat-info p:nth-of-type(2)');

                        // Dynamically generate seat layout (10 rows x 4 columns with gap between 2nd and 3rd columns)
                        const seatLayout = document.querySelector('.seat-layout');
                        seatLayout.innerHTML = '';
                        for (let i = 0; i < 40; i++) {
                            const row = Math.floor(i / 4);
                            const col = i % 4;
                            const seat = document.createElement('li');
                            seat.classList.add('seat');

                            // Adjust column placement to create a gap between 2nd and 3rd columns
                            let gridCol = col;
                            if (col >= 2) {
                                gridCol += 1; // Shift 3rd and 4th columns to account for the gap
                            }
                            seat.style.gridRow = row + 1;
                            seat.style.gridColumn = gridCol + 1;

                            // Assign seat status classes
                            if (i === 5) {
                                seat.classList.add('selected');
                            } else if ([0, 1, 2, 3].includes(i)) {
                                seat.classList.add('booked-m');
                            } else if ([4].includes(i)) {
                                seat.classList.add('blocked');
                            } else if ([6, 7].includes(i)) {
                                seat.classList.add('sold-m');
                            } else if ([8].includes(i)) {
                                seat.classList.add('sold-f');
                            } else {
                                seat.classList.add('available');
                            }

                            seatLayout.appendChild(seat);
                        }


                        const allSeats = seatLayout.querySelectorAll('.seat');
                        allSeats.forEach(seat => {
                            seat.addEventListener('click', function() {
                                if (this.classList.contains('available') || this.classList.contains('selected')) {
                                    if (this.classList.contains('selected')) {
                                        // Deselect
                                        this.classList.remove('selected');
                                        this.classList.add('available');
                                        seatFareP.textContent = 'Seat Fare: 0 Tk';
                                    } else {
                                        // Select this seat, deselect others
                                        document.querySelectorAll('.seat.selected').forEach(s => {
                                            s.classList.remove('selected');
                                            s.classList.add('available');
                                        });
                                        this.classList.remove('available');
                                        this.classList.add('selected');
                                        seatFareP.textContent = `Seat Fare: ${selectedPrice}`;
                                    }
                                }
                            });
                        });
                    }
                });
            });
        </script>
    </body>
</html> -->

<!-- 

<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "gobus";          // Database name from SQL dump

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch buses from database for Dhaka to Barisal on 2025-08-29
$sql = "SELECT * FROM buses WHERE starting_point = 'Rainkhola Counter' AND destination = 'Barisal Terminal' AND journey_date = '2025-08-29'";
$result = $conn->query($sql);

// Check if query executed successfully
if (!$result) {
    die("Query failed: " . $conn->error);
}

$total_buses = $result->num_rows;
$total_operators = $conn->query("SELECT COUNT(DISTINCT operator_name) as count FROM buses WHERE starting_point = 'Dhaka' AND destination = 'Barisal' AND journey_date = '2025-08-29'")->fetch_assoc()['count'];
$total_seats = 0;
if ($total_buses > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_seats += (int)$row['seats_available'];
    }
    $result->data_seek(0); // Reset result pointer
} else {
    echo "<p>Debug: No buses found for Dhaka to Barisal on 2025-08-29. Check table data.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS|Search Bus</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="../css/searchBus.css">
    </head>
    <body>
        <header>
            <div class="logo">Go<span id="logo">Bus</span></div>
            <div class="header-right">
                <a href="tel:xxxxxxxxxxxxx" class="call-btn">Call xxxx</a>
                <a href="login.php" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
            </div>
        </header>

        <div class="search-info">
            <div class="search-details">
                <span>ONWARD</span>
                <span>Dhaka To Barisal On 29 Aug 2025</span>
            </div>
            <div class="modify-search">
                <button>MODIFY SEARCH</button>
            </div>
            <div class="stats">
                <span>Total Buses Found: <?php echo $total_buses; ?></span>
                <span>Total Operators Found: <?php echo $total_operators; ?></span>
                <span>Total Seats Available: <?php echo $total_seats; ?></span>
            </div>
        </div>

        <div class="container">
            <?php
            if ($total_buses > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
            <div class="bus-details">
                <li>
                    <div class="bus-details-new-left">
                        <h3><?php echo htmlspecialchars($row['operator_name']); ?></h3>
                        <h6><?php echo htmlspecialchars($row['bus_number']); ?></h6>
                        <div class="non-ac-bus_couch-type">
                            <i class="fa-solid fa-snowflake"></i><span><?php echo htmlspecialchars($row['bus_type']); ?></span>
                        </div>
                        <a href="#">Cancellation policy</a>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-middle">
                        <div class="middle-left">
                            <h6>Starting</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['starting_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['starting_point']); ?></h6>
                        </div>
                        <div class="middle-middle">
                            <div class="bus-image">
                                <img src="../picture/bus.png" alt="Bus Image">
                            </div>
                            <h6 class="seat-left">Seat left: <?php echo htmlspecialchars($row['seats_available']); ?></h6>
                        </div>
                        <div class="middle-right">
                            <h6>Arrival</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['arrival_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['destination']); ?></h6>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-right">
                        <div class="no-extra-charge">No Extra Charge</div>
                        <div class="price">
                            <h3><?php echo number_format($row['fare'], 2); ?> tk</h3>
                        </div>
                        <div class="view-seat">
                            <button class="view-seat-btn">View Seat</button>
                        </div>
                    </div>
                </li>
            </div>
            <?php
                }
            } else {
                echo '<p>No buses found for the selected route.</p>';
            }
            $conn->close();
            ?>

            <div id="seatSelectionContainer" class="seat-selection-container">
                <div class="seat-legend">
                    <span class="legend-item booked-m"><i class="fa-solid fa-sofa"></i>BOOKED</span>
                    <span class="legend-item blocked">BLOCKED</span>
                    <span class="legend-item available">AVAILABLE</span>
                    <span class="legend-item selected">SELECTED</span>
                    <span class="legend-item sold-m">SOLD</span>
                </div>
                <div class="seat-selection-content">
                    <ul class="seat-layout">

                    </ul>
                    <div class="selection-details">
                        <div class="boarding-dropping">
                            <div>
                                <label>BOARDING POINT*</label>
                                <select>
                                    <?php
                                    // Reopen connection to fetch boarding points
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $sql_boarding = "SELECT DISTINCT starting_point, starting_time FROM buses WHERE starting_point = 'Dhaka' AND destination = 'Barisal' AND journey_date = '2025-08-29'";
                                    $result_boarding = $conn->query($sql_boarding);
                                    if ($result_boarding->num_rows > 0) {
                                        while ($row_boarding = $result_boarding->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars(date('h:i A', strtotime($row_boarding['starting_time']))) . '">' . htmlspecialchars(date('h:i A', strtotime($row_boarding['starting_time']))) . ' - ' . htmlspecialchars($row_boarding['starting_point']) . '</option>';
                                        }
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label>DROPPING POINT*</label>
                                <select>
                                    <option value="select">Select dropping point</option>
                                    <option value="Barisal Terminal">Barisal Terminal</option>
                                </select>
                            </div>
                        </div>
                        <div class="mobile-number">
                            <label>PHONE NUMBER*</label>
                            <input type="text" placeholder="Enter phone number">
                        </div>
                        <button class="submit-btn">SUBMIT</button>
                        <p>I have already have an account. <a href="login.php">Login with password</a>.</p>
                        <p>By logging in you are agreeing to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Notice of GoBUS</a></p>
                        <div class="seat-info">
                            <p>SEAT INFORMATION:</p>
                            <p>Seat Fare: 0 Tk</p>
                            <p>Service Charge: 0 Tk</p>
                            <p>PGW Charge: 0 Tk</p>
                        </div>
                    </div>
                </div>
            </div>
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
                    <a href="#">About Us</a>
                    <a href="#">Contact Us</a>
                    <a href="cancelTicket.php">Cancel Ticket</a>
                </div>

                <div class="footerSection">
                    <h3>Company Info</h3>
                    <a href="#">Terms and Condition</a>
                    <a href="#">Privacy Policy</a>
                </div>
            </div>

            <div class="footerBottom">
                Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
            </div>
        </footer>

        <script>
            document.querySelectorAll('.view-seat-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const seatContainer = document.getElementById('seatSelectionContainer');
                    const isVisible = seatContainer.style.display === 'block';
                    seatContainer.style.display = isVisible ? 'none' : 'block';

                    if (!isVisible) {
                        // Get the price for this bus
                        const selectedPrice = this.closest('.bus-details').querySelector('.price h3').textContent;
                        const seatFareP = document.querySelector('.seat-info p:nth-of-type(2)');

                        // Dynamically generate seat layout (10 rows x 4 columns with gap between 2nd and 3rd columns)
                        const seatLayout = document.querySelector('.seat-layout');
                        seatLayout.innerHTML = '';
                        for (let i = 0; i < 40; i++) {
                            const row = Math.floor(i / 4);
                            const col = i % 4;
                            const seat = document.createElement('li');
                            seat.classList.add('seat');

                            // Adjust column placement to create a gap between 2nd and 3rd columns
                            let gridCol = col;
                            if (col >= 2) {
                                gridCol += 1; // Shift 3rd and 4th columns to account for the gap
                            }
                            seat.style.gridRow = row + 1;
                            seat.style.gridColumn = gridCol + 1;

                            // Assign seat status classes
                            if (i === 5) {
                                seat.classList.add('selected');
                            } else if ([0, 1, 2, 3].includes(i)) {
                                seat.classList.add('booked-m');
                            } else if ([4].includes(i)) {
                                seat.classList.add('blocked');
                            } else if ([6, 7].includes(i)) {
                                seat.classList.add('sold-m');
                            } else if ([8].includes(i)) {
                                seat.classList.add('sold-f');
                            } else {
                                seat.classList.add('available');
                            }

                            seatLayout.appendChild(seat);
                        }

                        const allSeats = seatLayout.querySelectorAll('.seat');
                        allSeats.forEach(seat => {
                            seat.addEventListener('click', function() {
                                if (this.classList.contains('available') || this.classList.contains('selected')) {
                                    if (this.classList.contains('selected')) {
                                        // Deselect
                                        this.classList.remove('selected');
                                        this.classList.add('available');
                                        seatFareP.textContent = 'Seat Fare: 0 Tk';
                                    } else {
                                        // Select this seat, deselect others
                                        document.querySelectorAll('.seat.selected').forEach(s => {
                                            s.classList.remove('selected');
                                            s.classList.add('available');
                                        });
                                        this.classList.remove('available');
                                        this.classList.add('selected');
                                        seatFareP.textContent = `Seat Fare: ${selectedPrice}`;
                                    }
                                }
                            });
                        });
                    }
                });
            });
        </script>
    </body>
</html> -->

<!-- 
<?php
// Start session

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize query parameters
$from = isset($_GET['from']) ? $conn->real_escape_string($_GET['from']) : 'Dhaka';
$to = isset($_GET['to']) ? $conn->real_escape_string($_GET['to']) : 'Barisal';
$journey_date = isset($_GET['journey_date']) ? $conn->real_escape_string($_GET['journey_date']) : date('Y-m-d');
$travel_type = isset($_GET['travel_type']) ? $conn->real_escape_string($_GET['travel_type']) : 'One Way';
$return_date = isset($_GET['return_date']) ? $conn->real_escape_string($_GET['return_date']) : '';

// Store search data in session for potential use
$_SESSION['search_data'] = [
    'from' => $from,
    'to' => $to,
    'journey_date' => $journey_date,
    'travel_type' => $travel_type,
    'return_date' => $return_date
];

// Format journey date for display
$formatted_journey_date = date('d M Y', strtotime($journey_date));

// Fetch buses from database
$sql = "SELECT * FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'";
$result = $conn->query($sql);

// Check if query executed successfully
if (!$result) {
    die("Query failed: " . $conn->error);
}

$total_buses = $result->num_rows;
$total_operators = $conn->query("SELECT COUNT(DISTINCT operator_name) as count FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'")->fetch_assoc()['count'];
$total_seats = 0;
if ($total_buses > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_seats += (int)$row['seats_available'];
    }
    $result->data_seek(0); // Reset result pointer
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS|Search Bus</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="../css/searchBus.css">
    </head>
    <body>
        <header>
            <div class="logo">Go<span id="logo">Bus</span></div>
            <div class="header-right">
                <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
                    <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                        <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                <?php else: ?>
                    <a href="./login.php" class="login-btn"><img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="search-info">
            <div class="search-details">
                <span>ONWARD</span>
                <span><?php echo htmlspecialchars("$from To $to On $formatted_journey_date"); ?></span>
                <?php if ($travel_type === 'Round Way' && $return_date): ?>
                    <span>RETURN</span>
                    <span><?php echo htmlspecialchars("$to To $from On " . date('d M Y', strtotime($return_date))); ?></span>
                <?php endif; ?>
            </div>
            <div class="modify-search">
                <button onclick="window.location.href='../index.php'">MODIFY SEARCH</button>
            </div>
            <div class="stats">
                <span>Total Buses Found: <?php echo $total_buses; ?></span>
                <span>Total Operators Found: <?php echo $total_operators; ?></span>
                <span>Total Seats Available: <?php echo $total_seats; ?></span>
            </div>
        </div>

        <div class="container">
            <?php
            if ($total_buses > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
            <div class="bus-details">
                <li>
                    <div class="bus-details-new-left">
                        <h3><?php echo htmlspecialchars($row['operator_name']); ?></h3>
                        <h6><?php echo htmlspecialchars($row['bus_number']); ?></h6>
                        <div class="non-ac-bus_couch-type">
                            <i class="fa-solid fa-snowflake"></i><span><?php echo htmlspecialchars($row['bus_type']); ?></span>
                        </div>
                        <a href="#">Cancellation policy</a>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-middle">
                        <div class="middle-left">
                            <h6>Starting</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['starting_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['starting_point']); ?></h6>
                        </div>
                        <div class="middle-middle">
                            <div class="bus-image">
                                <img src="../picture/bus.png" alt="Bus Image">
                            </div>
                            <h6 class="seat-left">Seat left: <?php echo htmlspecialchars($row['seats_available']); ?></h6>
                        </div>
                        <div class="middle-right">
                            <h6>Arrival</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['arrival_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['destination']); ?></h6>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-right">
                        <div class="no-extra-charge">No Extra Charge</div>
                        <div class="price">
                            <h3><?php echo number_format($row['fare'], 2); ?> tk</h3>
                        </div>
                        <div class="view-seat">
                            <button class="view-seat-btn">View Seat</button>
                        </div>
                    </div>
                </li>
            </div>
            <?php
                }
            } else {
                echo '<p>No buses found for the selected route.</p>';
            }
            $conn->close();
            ?>

            <div id="seatSelectionContainer" class="seat-selection-container">
                <div class="seat-legend">
                    <span class="legend-item booked-m"><i class="fa-solid fa-sofa"></i>BOOKED</span>
                    <span class="legend-item blocked">BLOCKED</span>
                    <span class="legend-item available">AVAILABLE</span>
                    <span class="legend-item selected">SELECTED</span>
                    <span class="legend-item sold-m">SOLD</span>
                </div>
                <div class="seat-selection-content">
                    <ul class="seat-layout">
                    </ul>
                    <div class="selection-details">
                        <div class="boarding-dropping">
                            <div>
                                <label>BOARDING POINT*</label>
                                <select>
                                    <?php
                                    // Reopen connection to fetch boarding points
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $sql_boarding = "SELECT DISTINCT starting_point, starting_time FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'";
                                    $result_boarding = $conn->query($sql_boarding);
                                    if ($result_boarding->num_rows > 0) {
                                        while ($row_boarding = $result_boarding->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars(date('h:i A', strtotime($row_boarding['starting_time']))) . '">' . htmlspecialchars(date('h:i A', strtotime($row_boarding['starting_time']))) . ' - ' . htmlspecialchars($row_boarding['starting_point']) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No boarding points available</option>';
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label>DROPPING POINT*</label>
                                <select>
                                    <option value="select">Select dropping point</option>
                                    <option value="<?php echo htmlspecialchars($to); ?>"><?php echo htmlspecialchars($to); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="mobile-number">
                            <label>PHONE NUMBER*</label>
                            <input type="text" placeholder="Enter phone number">
                        </div>
                        <button class="submit-btn">SUBMIT</button>
                        <p>I have already have an account. <a href="login.php">Login with password</a>.</p>
                        <p>By logging in you are agreeing to the <a href="terms.php">Terms & Conditions</a> and <a href="../privacy.php">Privacy Notice of GoBUS</a></p>
                        <div class="seat-info">
                            <p>SEAT INFORMATION:</p>
                            <p>Seat Fare: 0 Tk</p>
                            <p>Service Charge: 0 Tk</p>
                            <p>PGW Charge: 0 Tk</p>
                        </div>
                    </div>
                </div>
            </div>
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
                    <a href="../aboutUs.php">About Us</a>
                    <a href="../contact.php">Contact Us</a>
                    <a href="../cancelTicket.php">Cancel Ticket</a>
                </div>

                <div class="footerSection">
                    <h3>Company Info</h3>
                    <a href="terms.php">Terms and Condition</a>
                    <a href="../privacy.php">Privacy Policy</a>
                </div>
            </div>

            <div class="footerBottom">
                Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
            </div>
        </footer>

        <script>
            document.querySelectorAll('.view-seat-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const seatContainer = document.getElementById('seatSelectionContainer');
                    const isVisible = seatContainer.style.display === 'block';
                    seatContainer.style.display = isVisible ? 'none' : 'block';

                    if (!isVisible) {
                        // Get the price for this bus
                        const selectedPrice = this.closest('.bus-details').querySelector('.price h3').textContent;
                        const seatFareP = document.querySelector('.seat-info p:nth-of-type(2)');

                        // Dynamically generate seat layout (10 rows x 4 columns with gap between 2nd and 3rd columns)
                        const seatLayout = document.querySelector('.seat-layout');
                        seatLayout.innerHTML = '';
                        for (let i = 0; i < 40; i++) {
                            const row = Math.floor(i / 4);
                            const col = i % 4;
                            const seat = document.createElement('li');
                            seat.classList.add('seat');

                            // Adjust column placement to create a gap between 2nd and 3rd columns
                            let gridCol = col;
                            if (col >= 2) {
                                gridCol += 1; // Shift 3rd and 4th columns to account for the gap
                            }
                            seat.style.gridRow = row + 1;
                            seat.style.gridColumn = gridCol + 1;

                            // Assign seat status classes
                            if (i === 5) {
                                seat.classList.add('selected');
                            } else if ([0, 1, 2, 3].includes(i)) {
                                seat.classList.add('booked-m');
                            } else if ([4].includes(i)) {
                                seat.classList.add('blocked');
                            } else if ([6, 7].includes(i)) {
                                seat.classList.add('sold-m');
                            } else if ([8].includes(i)) {
                                seat.classList.add('sold-f');
                            } else {
                                seat.classList.add('available');
                            }

                            seatLayout.appendChild(seat);
                        }

                        const allSeats = seatLayout.querySelectorAll('.seat');
                        allSeats.forEach(seat => {
                            seat.addEventListener('click', function() {
                                if (this.classList.contains('available') || this.classList.contains('selected')) {
                                    if (this.classList.contains('selected')) {
                                        // Deselect
                                        this.classList.remove('selected');
                                        this.classList.add('available');
                                        seatFareP.textContent = 'Seat Fare: 0 Tk';
                                    } else {
                                        // Select this seat, deselect others
                                        document.querySelectorAll('.seat.selected').forEach(s => {
                                            s.classList.remove('selected');
                                            s.classList.add('available');
                                        });
                                        this.classList.remove('available');
                                        this.classList.add('selected');
                                        seatFareP.textContent = `Seat Fare: ${selectedPrice}`;
                                    }
                                }
                            });
                        });
                    }
                });
            });
        </script>
    </body>
</html> -->


<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize query parameters
$from = isset($_GET['from']) ? $conn->real_escape_string($_GET['from']) : 'Dhaka';
$to = isset($_GET['to']) ? $conn->real_escape_string($_GET['to']) : 'Barisal';
$journey_date = isset($_GET['journey_date']) ? $conn->real_escape_string($_GET['journey_date']) : date('Y-m-d');
$travel_type = isset($_GET['travel_type']) ? $conn->real_escape_string($_GET['travel_type']) : 'One Way';
$return_date = isset($_GET['return_date']) ? $conn->real_escape_string($_GET['return_date']) : '';

// Store search data in session
$_SESSION['search_data'] = [
    'from' => $from,
    'to' => $to,
    'journey_date' => $journey_date,
    'travel_type' => $travel_type,
    'return_date' => $return_date
];

// Format journey date for display
$formatted_journey_date = date('d M Y', strtotime($journey_date));

// Fetch buses from database
$sql = "SELECT * FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'";
$result = $conn->query($sql);

// Check if query executed successfully
if (!$result) {
    die("Query failed: " . $conn->error);
}

$total_buses = $result->num_rows;
$total_operators = $conn->query("SELECT COUNT(DISTINCT operator_name) as count FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'")->fetch_assoc()['count'];
$total_seats = 0;
if ($total_buses > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_seats += (int)$row['seats_available'];
    }
    $result->data_seek(0); // Reset result pointer
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to book a seat.'); window.location.href='login.php';</script>";
        exit;
    }

    $bus_id = $conn->real_escape_string($_POST['bus_id']);
    $seat_number = $conn->real_escape_string($_POST['seat_number']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $boarding_point = $conn->real_escape_string($_POST['boarding_point']);
    $dropping_point = $conn->real_escape_string($_POST['dropping_point']);
    $route = "$from To $to";

    // Validate phone number format
    if (!preg_match('/^\+?[0-9]{10,14}$/', $phone_number)) {
        echo "<script>alert('Invalid phone number format.');</script>";
        exit;
    }

    // Check if seat is already booked
    $check_seat = "SELECT * FROM bookings WHERE bus_id = '$bus_id' AND date = '$journey_date' AND seat_number = '$seat_number' AND status = 'Upcoming'";
    $seat_result = $conn->query($check_seat);
    if (!$seat_result) {
        echo "<script>alert('Error checking seat availability: " . $conn->error . "');</script>";
        exit;
    }
    if ($seat_result->num_rows > 0) {
        echo "<script>alert('This seat is already booked.');</script>";
    } else {
        // Fetch bus details including operator_name
        $check_seats = "SELECT seats_available, fare, operator_name FROM buses WHERE id = '$bus_id' AND journey_date = '$journey_date'";
        $seats_result = $conn->query($check_seats);
        if ($seats_result && $seats_result->num_rows > 0) {
            $bus_data = $seats_result->fetch_assoc();
            if ($bus_data['seats_available'] > 0) {
                // Debug: Log operator_name to ensure it's retrieved
                error_log("Retrieved operator_name: " . ($bus_data['operator_name'] ?? 'Not found'));

                // Store booking details in session
                $_SESSION['booking_data'] = [
                    'user_id' => $_SESSION['user_id'],
                    'bus_id' => $bus_id,
                    'seat_number' => $seat_number,
                    'phone_number' => $phone_number,
                    'boarding_point' => $boarding_point,
                    'dropping_point' => $dropping_point,
                    'route' => $route,
                    'journey_date' => $journey_date,
                    'fare' => $bus_data['fare'],
                    'operator_name' => $bus_data['operator_name'] ?? 'Unknown Operator' // Fallback if operator_name is null
                ];

                // Debug: Log session data
                error_log("Booking data stored in session: " . print_r($_SESSION['booking_data'], true));

                // Redirect to payment page
                echo "<script>window.location.href='payment.php';</script>";
                exit;
            } else {
                echo "<script>alert('No seats available for this bus.');</script>";
            }
        } else {
            error_log("Error fetching bus details: " . $conn->error);
            echo "<script>alert('Error fetching bus details: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS | Search Bus</title>
        <link rel="stylesheet" type="text/css" href="../css/searchBus.css">
    </head>
    <body>
        <header>
            <div class="logo">Go<span id="logo">Bus</span></div>
            <div class="header-right">
                <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
                    <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                        <img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                <?php else: ?>
                    <a href="./login.php" class="login-btn"><img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="search-info">
            <div class="search-details">
                <span>ONWARD</span>
                <span><?php echo htmlspecialchars("$from To $to On $formatted_journey_date"); ?></span>
                <?php if ($travel_type === 'Round Way' && $return_date): ?>
                    <span>RETURN</span>
                    <span><?php echo htmlspecialchars("$to To $from On " . date('d M Y', strtotime($return_date))); ?></span>
                <?php endif; ?>
            </div>
            <div class="modify-search">
                <button onclick="window.location.href='../index.php'">MODIFY SEARCH</button>
            </div>
            <div class="stats">
                <span>Total Buses Found: <?php echo $total_buses; ?></span>
                <span>Total Operators Found: <?php echo $total_operators; ?></span>
                <span>Total Seats Available: <?php echo $total_seats; ?></span>
            </div>
        </div>

        <div class="container">
            <?php
            if ($total_buses > 0) {
                while ($row = $result->fetch_assoc()) {
                    $bus_id = $row['id'];
            ?>
            <div class="bus-details" data-bus-id="<?php echo $bus_id; ?>">
                <li>
                    <div class="bus-details-new-left">
                        <h3><?php echo htmlspecialchars($row['operator_name']); ?></h3>
                        <h6><?php echo htmlspecialchars($row['bus_number']); ?></h6>
                        <div class="non-ac-bus_couch-type">
                            <img src="../picture/snowflake.png" alt="AC Icon" style="width: 14px; height: 14px; vertical-align: middle;"><span> <?php echo htmlspecialchars($row['bus_type']); ?></span>
                        </div>
                        <a href="#">Cancellation policy</a>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-middle">
                        <div class="middle-left">
                            <h6>Starting</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['starting_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['starting_point']); ?></h6>
                        </div>
                        <div class="middle-middle">
                            <div class="bus-image">
                                <img src="../picture/bus.png" alt="Bus Image">
                            </div>
                            <h6 class="seat-left">Seat left: <?php echo htmlspecialchars($row['seats_available']); ?></h6>
                        </div>
                        <div class="middle-right">
                            <h6>Arrival</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['arrival_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['destination']); ?></h6>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-right">
                        <div class="no-extra-charge">No Extra Charge</div>
                        <div class="price">
                            <h3><?php echo number_format($row['fare'], 2); ?> tk</h3>
                        </div>
                        <div class="view-seat">
                            <button class="view-seat-btn" data-bus-id="<?php echo $bus_id; ?>" <?php echo $row['seats_available'] == 0 ? 'disabled' : ''; ?>>View Seat</button>
                        </div>
                    </div>
                </li>
            </div>
            <?php
                }
            } else {
                echo '<p>No buses found for the selected route.</p>';
            }
            $conn->close();
            ?>

            <div id="seatSelectionContainer" class="seat-selection-container" style="display: none;">
                <div class="seat-legend">
                    <span class="legend-item booked-m"><i class="fa-solid fa-sofa"></i>BOOKED</span>
                    <span class="legend-item blocked">BLOCKED</span>
                    <span class="legend-item available">AVAILABLE</span>
                    <span class="legend-item selected">SELECTED</span>
                    <span class="legend-item sold-m">SOLD</span>
                </div>
                <div class="seat-selection-content">
                    <ul class="seat-layout">
                        <!-- Seat layout will be dynamically generated here -->
                    </ul>
                    <div class="selection-details">
                        <form id="bookingForm" method="POST" action="">
                            <input type="hidden" name="bus_id" id="bus_id">
                            <input type="hidden" name="seat_number" id="seat_number">
                            <div class="boarding-dropping">
                                <div>
                                    <label>BOARDING POINT*</label>
                                    <select name="boarding_point" required>
                                        <?php
                                        // Reopen connection to fetch boarding points
                                        $conn = new mysqli($servername, $username, $password, $dbname);
                                        if ($conn->connect_error) {
                                            die("Connection failed: " . $conn->connect_error);
                                        }
                                        $sql_boarding = "SELECT DISTINCT starting_point, starting_time FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'";
                                        $result_boarding = $conn->query($sql_boarding);
                                        if ($result_boarding && $result_boarding->num_rows > 0) {
                                            while ($row_boarding = $result_boarding->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_boarding['starting_point']) . '">' . htmlspecialchars(date('h:i A', strtotime($row_boarding['starting_time']))) . ' - ' . htmlspecialchars($row_boarding['starting_point']) . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">No boarding points available</option>';
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label>DROPPING POINT*</label>
                                    <select name="dropping_point" required>
                                        <option value="select">Select dropping point</option>
                                        <option value="<?php echo htmlspecialchars($to); ?>"><?php echo htmlspecialchars($to); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mobile-number">
                                <label>PHONE NUMBER*</label>
                                <input type="text" name="phone_number" placeholder="Enter phone number" pattern="\+?[0-9]{10,14}" required>
                            </div>
                            <button type="submit" name="submit_booking" class="submit-btn">Proceed to Payment</button>
                        </form>
                        <p>I have already have an account. <a href="login.php">Login with password</a>.</p>
                        <p>By logging in you are agreeing to the <a href="terms.php">Terms & Conditions</a> and <a href="../privacy.php">Privacy Notice of GoBUS</a></p>
                        <div class="seat-info">
                            <p>SEAT INFORMATION:</p>
                            <p>Seat Fare: <span id="seatFare">0 Tk</span></p>
                            <p>Service Charge: 0 Tk</p>
                            <p>PGW Charge: 0 Tk</p>
                        </div>
                    </div>
                </div>
            </div>
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
                    <a href="../aboutUs.php">About Us</a>
                    <a href="../contact.php">Contact Us</a>
                    <a href="../cancelTicket.php">Cancel Ticket</a>
                </div>

                <div class="footerSection">
                    <h3>Company Info</h3>
                    <a href="terms.php">Terms and Condition</a>
                    <a href="../privacy.php">Privacy Policy</a>
                </div>
            </div>

            <div class="footerBottom">
                Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
            </div>
        </footer>

        <script>
            document.querySelectorAll('.view-seat-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('View Seat button clicked, bus_id:', this.getAttribute('data-bus-id'));
                    const seatContainer = document.getElementById('seatSelectionContainer');
                    const isVisible = seatContainer.style.display === 'block';
                    seatContainer.style.display = isVisible ? 'none' : 'block';

                    if (!isVisible) {
                        const busId = this.getAttribute('data-bus-id');
                        const selectedPrice = this.closest('.bus-details').querySelector('.price h3').textContent;
                        const seatFareP = document.getElementById('seatFare');
                        const busIdInput = document.getElementById('bus_id');
                        busIdInput.value = busId;

                        console.log('Fetching reserved seats for bus_id:', busId, 'journey_date:', '<?php echo $journey_date; ?>');
                        fetch(`get_reserved_seats.php?bus_id=${busId}&journey_date=<?php echo $journey_date; ?>`)
                            .then(response => {
                                console.log('Fetch response status:', response.status);
                                if (!response.ok) {
                                    throw new Error('Network response was not ok: ' + response.statusText);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Received data:', data);
                                if (data.error) {
                                    console.error('Server error:', data.error);
                                    alert('Error fetching reserved seats: ' + data.error);
                                    return;
                                }
                                const reservedSeats = data.reserved_seats || [];

                                // Clear existing seat layout
                                const seatLayout = document.querySelector('.seat-layout');
                                seatLayout.innerHTML = '';

                                // Generate seat layout (10 rows x 4 columns with gap)
                                for (let i = 0; i < 40; i++) {
                                    const row = Math.floor(i / 4);
                                    const col = i % 4;
                                    const seat = document.createElement('li');
                                    seat.classList.add('seat');
                                    const seatNumber = String.fromCharCode(65 + row) + (col + 1);

                                    let gridCol = col + 1;
                                    if (col >= 2) {
                                        gridCol += 1;
                                    }
                                    seat.style.gridRow = row + 1;
                                    seat.style.gridColumn = gridCol;

                                    seat.dataset.seatNumber = seatNumber;
                                    seat.textContent = seatNumber;
                                    if (reservedSeats.includes(seatNumber)) {
                                        seat.classList.add('booked-m');
                                    } else {
                                        seat.classList.add('available');
                                    }

                                    seatLayout.appendChild(seat);
                                }

                                // Add click event listeners to seats
                                const allSeats = seatLayout.querySelectorAll('.seat');
                                allSeats.forEach(seat => {
                                    seat.addEventListener('click', function() {
                                        console.log('Seat clicked:', this.dataset.seatNumber);
                                        if (this.classList.contains('available') || this.classList.contains('selected')) {
                                            if (this.classList.contains('selected')) {
                                                this.classList.remove('selected');
                                                this.classList.add('available');
                                                seatFareP.textContent = '0 Tk';
                                                document.getElementById('seat_number').value = '';
                                            } else {
                                                document.querySelectorAll('.seat.selected').forEach(s => {
                                                    s.classList.remove('selected');
                                                    s.classList.add('available');
                                                });
                                                this.classList.remove('available');
                                                this.classList.add('selected');
                                                seatFareP.textContent = selectedPrice;
                                                document.getElementById('seat_number').value = this.dataset.seatNumber;
                                            }
                                        }
                                    });
                                });
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                alert('Failed to load seat data: ' + error.message);
                            });
                    }
                });
            });
        </script>
    </body>
</html>