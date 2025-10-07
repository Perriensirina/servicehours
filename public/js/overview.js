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