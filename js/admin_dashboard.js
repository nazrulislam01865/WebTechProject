// document.addEventListener('DOMContentLoaded', () => {
//     const links = document.querySelectorAll('.sidebar-link');
//     const sections = document.querySelectorAll('.section');

//     links.forEach(link => {
//         link.addEventListener('click', (e) => {
//             e.preventDefault();


//             links.forEach(l => l.classList.remove('active'));

//             link.classList.add('active');


//             sections.forEach(section => section.style.display = 'none');

//             const sectionId = link.getAttribute('data-section');
//             document.getElementById(sectionId).style.display = 'block';
//         });
//     });
// });

// function validateForm(event) {
//     event.preventDefault();
                
//     const phone = document.getElementById('phone').value;
//     const password = document.getElementById('password').value;
//     const phoneRegex = /^\d{11}$/;
                
//     if (!phoneRegex.test(phone)) {
//         alert('Please enter a valid 10-digit phone number.');
//         return false;
//     }
                
//     if (password.length < 8) {
//         alert('Password must be at least 8 characters long.');
//         return false;
//     }
                
//     // If validation passes, you can submit the form or handle login
//     alert('Login successful!');
//     return true;
// }


// // document.querySelectorAll('.sidebar-link').forEach(link => {
// //     link.addEventListener('click', function(e) {
// //         e.preventDefault();
// //         document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
// //         this.classList.add('active');
// //         document.querySelectorAll('.section').forEach(section => {
// //             section.style.display = 'none';
// //         });
// //         const sectionId = this.getAttribute('data-section');
// //         document.getElementById(sectionId).style.display = 'block';
// //     });
// // });




document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.sidebar-link');
    const sections = document.querySelectorAll('.section');

    // Get the active section from PHP or URL hash, default to 'revenue'
    const activeSection = window.activeSection || window.location.hash.replace('#', '') || 'revenue';

    // Set initial state
    links.forEach(link => link.classList.remove('active'));
    sections.forEach(section => section.style.display = 'none');

    const activeLink = document.querySelector(`.sidebar-link[data-section="${activeSection}"]`);
    const activeSectionElement = document.getElementById(activeSection);
    if (activeLink && activeSectionElement) {
        activeLink.classList.add('active');
        activeSectionElement.style.display = 'block';
    } else {
        // Fallback to revenue section
        document.querySelector('.sidebar-link[data-section="revenue"]').classList.add('active');
        document.getElementById('revenue').style.display = 'block';
    }

    // Handle sidebar link clicks
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