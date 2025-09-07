document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.sidebar-link');
    const sections = document.querySelectorAll('.section');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();


            links.forEach(l => l.classList.remove('active'));

            link.classList.add('active');


            sections.forEach(section => section.style.display = 'none');

            const sectionId = link.getAttribute('data-section');
            document.getElementById(sectionId).style.display = 'block';
        });
    });
});

function validateForm(event) {
    event.preventDefault();
                
    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;
    const phoneRegex = /^\d{11}$/;
                
    if (!phoneRegex.test(phone)) {
        alert('Please enter a valid 10-digit phone number.');
        return false;
    }
                
    if (password.length < 8) {
        alert('Password must be at least 8 characters long.');
        return false;
    }
                
    // If validation passes, you can submit the form or handle login
    alert('Login successful!');
    return true;
}
