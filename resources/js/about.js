/* ==========================================
        NAVBAR SCROLL
========================================== */

const navbar = document.getElementById("navbar");

window.addEventListener("scroll", () => {

    if (window.scrollY > 80) {

        navbar.classList.add("scrolled");

    } else {

        navbar.classList.remove("scrolled");

    }

});


/* ==========================================
        COUNTER
========================================== */

const counters = document.querySelectorAll(".counter");

const speed = 80;

const startCounter = () => {

    counters.forEach(counter => {

        const update = () => {

            const target = +counter.getAttribute("data-target");

            const count = +counter.innerText;

            const increment = Math.ceil(target / speed);

            if (count < target) {

                counter.innerText = count + increment;

                setTimeout(update, 25);

            } else {

                counter.innerText = target;

            }

        };

        update();

    });

};

const statSection = document.querySelector(".stats");

let counterStarted = false;

window.addEventListener("scroll", () => {

    const top = statSection.getBoundingClientRect().top;

    if (top < window.innerHeight - 120 && !counterStarted) {

        counterStarted = true;

        startCounter();

    }

});


/* ==========================================
        FAQ
========================================== */

const faqs = document.querySelectorAll(".faq-item");

faqs.forEach(item => {

    const btn = item.querySelector(".faq-question");

    btn.addEventListener("click", () => {

        faqs.forEach(f => {

            if (f !== item) {

                f.classList.remove("active");

            }

        });

        item.classList.toggle("active");

    });

});


/* ==========================================
        BACK TO TOP
========================================== */

const topBtn = document.getElementById("topBtn");

window.addEventListener("scroll", () => {

    if (window.scrollY > 500) {

        topBtn.style.display = "block";

    } else {

        topBtn.style.display = "none";

    }

});

topBtn.onclick = () => {

    window.scrollTo({

        top:0,

        behavior:"smooth"

    });

};


/* ==========================================
        SMOOTH LINK
========================================== */

document.querySelectorAll('a[href^="#"]').forEach(anchor=>{

    anchor.addEventListener("click",function(e){

        e.preventDefault();

        const target=document.querySelector(this.getAttribute("href"));

        if(target){

            target.scrollIntoView({

                behavior:"smooth"

            });

        }

    });

});


/* ==========================================
        SCROLL ANIMATION
========================================== */

const observer = new IntersectionObserver((entries)=>{

    entries.forEach(entry=>{

        if(entry.isIntersecting){

            entry.target.classList.add("show");

        }

    });

},{
    threshold:.2
});

document.querySelectorAll(

".about,.stats,.visi,.layanan,.flow,.unggulan,.faq,.contact,.maps,.cta"

).forEach(el=>{

    el.classList.add("hidden");

    observer.observe(el);

});


/* ==========================================
        BUTTON RIPPLE
========================================== */

const buttons = document.querySelectorAll(".btn1,.btn2,.cta a");

buttons.forEach(button=>{

    button.addEventListener("mouseenter",()=>{

        button.style.transform="translateY(-5px) scale(1.03)";

    });

    button.addEventListener("mouseleave",()=>{

        button.style.transform="translateY(0) scale(1)";

    });

});


/* ==========================================
        CARD HOVER
========================================== */

const cards = document.querySelectorAll(

".layanan-card,.stats .card,.unggulan-item,.visi-card,.contact-card"

);

cards.forEach(card=>{

    card.addEventListener("mousemove",(e)=>{

        const x=e.offsetX/card.offsetWidth-.5;

        const y=e.offsetY/card.offsetHeight-.5;

        card.style.transform=`
        rotateY(${x*8}deg)
        rotateX(${y*-8}deg)
        translateY(-8px)
        `;

    });

    card.addEventListener("mouseleave",()=>{

        card.style.transform="rotateY(0deg) rotateX(0deg) translateY(0px)";

    });

});


/* ==========================================
        HERO PARALLAX
========================================== */

window.addEventListener("scroll",()=>{

    const hero=document.querySelector(".hero");

    hero.style.backgroundPositionY=window.scrollY*0.4+"px";

});


/* ==========================================
        LOADING EFFECT
========================================== */

window.onload=()=>{

    document.body.style.opacity="0";

    setTimeout(()=>{

        document.body.style.transition="1s";

        document.body.style.opacity="1";

    },100);

};