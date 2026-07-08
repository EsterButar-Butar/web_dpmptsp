window.addEventListener("scroll",()=>{

    const navbar=document.querySelector(".main-header");

    navbar.classList.toggle("scrolled",window.scrollY>50);

});