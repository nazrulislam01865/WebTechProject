//Nav Item Tabs
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function () {
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(i => {
            i.classList.remove('active');
        });

        // Add active class to clicked item
        this.classList.add('active');

        // Hide all content tabs
        document.querySelectorAll('.content-tabs').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show the selected tab
        const tabId = this.getAttribute('data-tab');
        if (tabId) {
            document.getElementById(tabId).classList.add('active');
        }
    });
});

//Passenger Search Toggle 
const searchOptionBtns = document.querySelectorAll('.search-option-btn');
const basicForm = document.getElementById('basic-search-form');
const dateForm = document.getElementById('date-search-form');

searchOptionBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        searchOptionBtns.forEach(b => b.classList.remove('active'));
        // Add active to clicked button
        btn.classList.add('active');

        // Show/hide corresponding form
        if (btn.dataset.searchType === 'basic') {
            basicForm.classList.add('active');
            dateForm.classList.remove('active');
        } else if (btn.dataset.searchType === 'date') {
            dateForm.classList.add('active');
            basicForm.classList.remove('active');
        }
    });
});

//Handle Cancel Trip Modal
const cancelModal = document.getElementById('cancelModal');
const closeModalBtn = document.querySelector('.close');
const dangerButtons = document.querySelectorAll('.btn-danger');

dangerButtons.forEach(btn => {
    btn.addEventListener('click', function () {
        cancelModal.style.display = 'flex';
    });
});

closeModalBtn.addEventListener('click', function () {
    cancelModal.style.display = 'none';
});

window.addEventListener('click', function (event) {
    if (event.target === cancelModal) {
        cancelModal.style.display = 'none';
    }
});

// Show "Other reason" textarea
document.getElementById('cancelReason').addEventListener('change', function () {
    const otherDiv = document.getElementById('otherReasonDiv');
    otherDiv.style.display = this.value === 'Other' ? 'block' : 'none';
});

// Confirm cancellation
document.getElementById('cancelTripForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const reason = document.getElementById('cancelReason').value;
    const otherReason = document.getElementById('otherReason').value;
    const notifyEmail = document.getElementById('emailNotif').checked;
    const notifySms = document.getElementById('smsNotif').checked;

    let finalReason = reason === 'Other' ? otherReason : reason;

    alert(`Trip cancelled!\nReason: ${finalReason}\nNotify via: ${notifyEmail ? 'Email ' : ''}${notifySms ? 'SMS' : ''}`);

    document.getElementById('cancelTripForm').reset();
    document.getElementById('otherReasonDiv').style.display = 'none';
    document.getElementById('cancelModal').style.display = 'none';
});

//Add Trip
const tripForm = document.getElementById('tripForm');
const upcomingTripsDiv = document.getElementById('upcoming-trips');

tripForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const from = document.getElementById('trip-from').value;
    const to = document.getElementById('trip-to').value;
    const date = document.getElementById('trip-date').value;
    const time = document.getElementById('trip-time').value;

    const tripItem = document.createElement('div');
    tripItem.classList.add('trip-item');

    tripItem.innerHTML = `
        <div class="trip-info">
            <div class="trip-route">${from} → ${to}</div>
            <div class="trip-time">${date} ${time}</div>
        </div>
        <div class="passenger-count">0 passengers</div>
    `;

    upcomingTripsDiv.appendChild(tripItem);
    tripForm.reset();
});

//Search Trip
document.getElementById('searchTripForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const from = document.getElementById('search-from').value.toLowerCase();
    const to = document.getElementById('search-to').value.toLowerCase();
    const date = document.getElementById('search-date').value;
    const time = document.getElementById('search-time').value;

    const trips = document.querySelectorAll('.trip-item');
    let found = false;

    trips.forEach(trip => {
        const tripFrom = trip.querySelector('.trip-route').innerText.split('→')[0].trim().toLowerCase();
        const tripTo = trip.querySelector('.trip-route').innerText.split('→')[1].trim().toLowerCase();
        const tripDateTime = trip.querySelector('.trip-time').innerText.split(' ');
        const tripDate = tripDateTime[0];
        const tripTime = tripDateTime[1];

        if (tripFrom === from && tripTo === to && tripDate === date && tripTime === time) {
            trip.scrollIntoView({ behavior: "smooth" });
            trip.style.background = '#ffe0e0';
            found = true;
        } else {
            trip.style.background = '';
        }
    });

    if (!found) {
        alert('No trip found at this time!');
    }
});

//Profile Dropdown & Password Update 
const userProfile = document.getElementById('userProfile');
const profileDropdown = document.getElementById('profileDropdown');

userProfile.addEventListener('click', (e) => {
    e.stopPropagation();
    profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
});

window.addEventListener('click', () => {
    profileDropdown.style.display = 'none';
});

const changePasswordForm = document.getElementById('changePasswordForm');
changePasswordForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const current = document.getElementById('currentPassword').value;
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;

    if (newPass !== confirmPass) {
        alert("New password and confirm password do not match!");
        return;
    }

    alert("Password updated successfully!");
    changePasswordForm.reset();
    profileDropdown.style.display = 'none';
});

// Logout
const logoutBtn = document.getElementById('logoutBtn');
logoutBtn.addEventListener('click', () => {
    alert("Logged out successfully!");
    // window.location.href = "login.html";
});
