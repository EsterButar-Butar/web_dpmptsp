window.addEventListener("scroll",()=>{

    const navbar=document.querySelector(".main-header");

    navbar.classList.toggle("scrolled",window.scrollY>50);

});

document.addEventListener("DOMContentLoaded", () => {

    // Kalau bukan halaman home, biarkan Laravel yang mengatur active
    if (window.location.pathname !== "/") return;

    const hero = document.getElementById("hero");
    const tentang = document.getElementById("tentang");

    const navHome = document.getElementById("nav-home");
    const navAbout = document.getElementById("nav-about");

    function setActive() {

        const posisi = window.scrollY;

        if (posisi < tentang.offsetTop - 150) {

            navHome.classList.add("active");
            navAbout.classList.remove("active");

        } else {

            navHome.classList.remove("active");
            navAbout.classList.add("active");

        }
    }

    setActive();

    window.addEventListener("scroll", setActive);

});