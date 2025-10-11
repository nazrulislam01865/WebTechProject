#  GoBus
GoBus is a complete web-based bus ticket booking system designed to make bus travel easier and more organized for passengers, administrators, and transport companies. Users can search for available buses, purchase tickets online, make payments, and manage their bookings comfortably from home, no more waiting in long lines at the counter

The system supports three types of users **Customer**, **Admin**, and **Bus Company** .Each with their own dashboards and dedicated functionalities. From managing routes, tracking revenue, assigning drivers, and handling feedback to simple ticket booking,This project provides an all-in-one digital solution for bus ticket booking system.

In the future, we plan to enhance GoBus with features such as real-time bus tracking, online seat selection, push notifications, and mobile-friendly design for an even better user experience.


## Technologies Used
- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP  
- **Database:** MySQL  
- **Server:** XAMPP
---

## Main Features
-  **Three user types:**  Customer, Admin, and Bus Company
-  **Authentication:**  Login, logout, user registration
-  **Account Management:**  Change or reset password, manage profile information (view, edit, delete)
-  **Dashboard:**  Access a personalized dashboard after login
-  **Customer Features:**  Buy ticket, cancel ticket, make payment, give feedback about experience
-  **Admin Features:**  Revenue tracking per route, manage discounts and promotions, generate reports, handle feedback and complaints
-  **Bus Company Features:**  Monitor passenger list, view revenue report per bus, assign drivers, cancel trips

---

## Installation
1. **Clone the repository:**  
   Using HTTPS:  
     ```sh
     git clone https://github.com/nazrulislam01865/WebTechProject.git
     ```  
   Using SSH (optional) :  
     ```sh
     git clone git@github.com:nazrulislam01865/WebTechProject.git
     ```
2. **Going to the project folder:** (for Pull)
   ```sh
   cd WebTechProject
   ```
3. **To Start the Local Server:**

   Before running the project, the user must open the ***XAMPP*** Control Panel and start the required modules **Apache** and **MySQL** by clicking the “Start” button under    the Actions column.
   
4. **Set Up the Database:**
   
     Open your browser and go to:
     ```sh
    http://localhost/phpmyadmin
     ```
    Create a new database:
    ```sh
     gobus
      ```
     Click Import, and upload the file:
    ```sh
     gobus.sql
     ```
     Click **Go** to import the tables and data successfully.

6. **Start a local server (e.g., XAMPP ) and access the project via browser:**
   
    (Make Sure the folder location must be in **main drive**>**xampp**>**htdocs**>**WebTechProject** to Start the project)
   ```
   http://localhost/WebTechProject/index.php
   ```
---
##  Admin Panel Credentials

- **Phone:** `12345678901`  
- **Password:** `admin123`  
---

##  Bus Company Panel Credentials

- **Phone:** `01723232323`  
- **Password:** `Xyz@1234`  
---

## Ticket Booking Process
To book a ticket in **GoBus**, the user must follow these steps:
1. **Login to the System:**
   * The user must be logged in to access the ticket booking feature.
   * If the user does not have an account, registration is required.

2. **Access the Dashboard:**
   * After successful login, the user will be redirected to the personal dashboard.
   * From the dashboard, the user can click on the **“Search Bus”** option.

3. **Search for a Bus:**
   * A search form will appear on the screen.
   * The user must enter the following details:

     * **From:** Starting location
     * **To:** Destination
     * **Date:** Date of travel
     * **Trip Type:** One-way or Two-way
   * After submitting the form, the system will display the available buses along with their prices.

4. **Select a Bus:**
   * The user can select a preferred bus for travel from the list of available options.

5. **Choose Seat Position:**
   * A seat layout of the selected bus will appear.
   * The user can view which seats are **booked** and which are **available**, and then choose a preferred seat.

6. **Provide Passenger Details:**
   * The user must enter the following information:

     * **Boarding Point**
     * **Dropping Point (Address)**
     * **Phone Number**
     * **Promo Code** (if applicable)

7. **Proceed to Payment:**
   * The system will then display available payment methods.
   * The user can complete payment using either:

     * **bKash**, or
     * **Bank Payment**

8. **Booking Confirmation:**
   * After successful payment, the booking will be confirmed.
   * The ticket and booking details will be available in the user’s dashboard for future reference.
---

## Authors
- *Nazrul Islam*
https://github.com/nazrulislam01865
- *Samia Sharmin*
https://github.com/SamiaSharmin
- *Merin Taj*
https://github.com/Merin23508651

---
