// ===============================
// Navbar Scroll Effect
// ===============================
window.addEventListener("scroll", () => {
    const navbar = document.querySelector(".main-header");

    if (navbar) {
        navbar.classList.toggle("scrolled", window.scrollY > 50);
    }
});


// ===============================
// DOM Ready
// ===============================
document.addEventListener("DOMContentLoaded", () => {

    // ===============================
    // Active menu saat scroll (Home)
    // ===============================
    if (window.location.pathname === "/") {

        const tentang = document.getElementById("tentang");

        const navHome = document.getElementById("nav-home");
        const navAbout = document.getElementById("nav-about");

        function setActive() {

            if (!tentang) return;

            if (window.scrollY < tentang.offsetTop - 150) {

                navHome?.classList.add("active");
                navAbout?.classList.remove("active");

            } else {

                navHome?.classList.remove("active");
                navAbout?.classList.add("active");

            }
        }

        setActive();

        window.addEventListener("scroll", setActive);
    }


    // ===============================
    // Mobile Hamburger Menu
    // ===============================
    const menuBtn = document.querySelector(".mobile-menu-button");
    const navMenu = document.querySelector(".main-navigation");
    const navAction = document.querySelector(".navbar-action");

    const menuBtn = document.getElementById("mobileMenuButton");
const navMenu = document.getElementById("mainNavigation");

if (menuBtn && navMenu) {
    menuBtn.addEventListener("click", () => {
        navMenu.classList.toggle("show");
    });
}
});