document.addEventListener("DOMContentLoaded", () => {
    const filterButton = document.querySelector('.filter');
    const filterContainer = document.getElementById('filterContainer');

    if (!filterButton || !filterContainer) return;

    filterButton.addEventListener("click", () => {
        filterContainer.classList.toggle("open");
    });
});

// var hamburger = document.querySelector('.hamburger-menu');
// var navLinks = document.querySelector('.nav-links')

// hamburger.addEventListener('click', openCloseMenu);

// function openCloseMenu(){
//     hamburger.classList.toggle("change");
//     navLinks.classList.toggle("invisible")
// }


document.querySelectorAll('.mobile-card .card-header').forEach(header => {
    header.addEventListener('click', function() {
        const card = this.parentElement;
        const content = card.querySelector('.card-content');
        const icon = this.querySelector('i');

        card.classList.toggle('active');

        if(card.classList.contains('active')){
            content.style.maxHeight = content.scrollHeight + "px";
            icon.style.transform = "rotate(180deg)";
        } else {
            content.style.maxHeight = "0";
            icon.style.transform = "rotate(0deg)";
        }
    });
});
