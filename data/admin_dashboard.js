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