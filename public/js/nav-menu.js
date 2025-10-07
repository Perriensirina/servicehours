document.addEventListener("DOMContentLoaded", () => {
  const navLinks = document.querySelectorAll(".nav-link-btn");
  const sections = document.querySelectorAll(".content-section");

  navLinks.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();

      // 1. Remove 'active' from all nav links
      navLinks.forEach(l => l.classList.remove("active"));

      // 2. Add 'active' to the clicked link
      link.classList.add("active");

      // 3. Hide all sections
      sections.forEach(section => section.classList.add("d-none"));

      // 4. Show the one linked by data-target
      const targetId = link.getAttribute("data-target");
      const targetSection = document.getElementById(targetId);
      if (targetSection) {
        targetSection.classList.remove("d-none");
      }
    });
  });
});
