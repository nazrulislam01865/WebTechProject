// function validateForm(event) {
//                 const phone = document.getElementById('phone').value;
//                 const password = document.getElementById('password').value;
//                 const phoneRegex = /^\d{10}$/;
                
//                 if (!phoneRegex.test(phone)) {
//                     alert('Please enter a valid 10-digit phone number.');
//                     return false;
//                 }
                
//                 if (password.length < 8) {
//                     alert('Password must be at least 8 characters long.');
//                     return false;
//                 }
                
//                 return true;
//             }

function validateForm(event) {
    event.preventDefault(); // Prevent form submission for validation

    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;
    const phoneError = document.getElementById('phoneError');
    const passwordError = document.getElementById('passwordError');

    // Clear previous error messages
    phoneError.textContent = '';
    passwordError.textContent = '';

    let isValid = true;

    // Validate phone number (e.g., 10 digits)
    if (!/^\d{10}$/.test(phone)) {
        phoneError.textContent = 'Please enter a valid 10-digit phone number.';
        isValid = false;
    }

    // Validate password (e.g., minimum 6 characters)
    if (password.length < 6) {
        passwordError.textContent = 'Password must be at least 6 characters.';
        isValid = false;
    }

    // If valid, allow form submission
    if (isValid) {
        document.getElementById('loginForm').submit();
    }

    return isValid;
}