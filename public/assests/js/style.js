/* =========================================================
   MITHILANCHAL FARMS
   ABOUT PAGE JAVASCRIPT
========================================================= */


/* =========================
   MOBILE NAVIGATION
========================= */

const menuToggle = document.getElementById("menuToggle");

const nav = document.getElementById("nav");


if (menuToggle && nav) {

    menuToggle.addEventListener("click", () => {

        nav.classList.toggle("open");

        const isOpen = nav.classList.contains("open");

        menuToggle.setAttribute(
            "aria-label",
            isOpen ?
            "Close navigation" :
            "Open navigation"
        );

        menuToggle.textContent =
            isOpen ? "✕" : "☰";

    });


    /* Close menu after clicking a link */

    document
        .querySelectorAll(".nav a")
        .forEach(link => {

            link.addEventListener("click", () => {

                nav.classList.remove("open");

                menuToggle.textContent = "☰";

                menuToggle.setAttribute(
                    "aria-label",
                    "Open navigation"
                );

            });

        });

}



/* =========================
   HEADER SHADOW
========================= */

const header =
    document.querySelector(".header");


function updateHeader() {

    if (!header) return;

    if (window.scrollY > 20) {

        header.style.boxShadow =
            "0 8px 30px rgba(0,0,0,.08)";

    } else {

        header.style.boxShadow = "none";

    }

}


window.addEventListener(
    "scroll",
    updateHeader, { passive: true }
);

updateHeader();



/* =========================
   REVEAL ANIMATION
========================= */

const revealElements =
    document.querySelectorAll(
        ".value-card, .process-card, .mission-card"
    );


const revealObserver =
    new IntersectionObserver(

        entries => {

            entries.forEach(entry => {

                if (entry.isIntersecting) {

                    entry.target.classList.add(
                        "revealed"
                    );

                    revealObserver.unobserve(
                        entry.target
                    );

                }

            });

        },

        {
            threshold: 0.12
        }

    );


revealElements.forEach(element => {

    element.style.opacity = "0";

    element.style.transform =
        "translateY(25px)";

    element.style.transition =
        "opacity .7s ease, transform .7s ease";

    revealObserver.observe(element);

});



/* =========================
   REVEAL CLASS
========================= */

const style =
    document.createElement("style");


style.textContent = `

    .revealed {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

`;


document.head.appendChild(style);



/* =========================
   SMOOTH ANCHOR SCROLL
========================= */

document
    .querySelectorAll('a[href^="#"]')
    .forEach(anchor => {

        anchor.addEventListener(
            "click",
            function(event) {

                const targetId =
                    this.getAttribute("href");

                const target =
                    document.querySelector(targetId);

                if (!target) return;

                event.preventDefault();

                const headerHeight =
                    document.querySelector(
                        ".header"
                    ) ? offsetHeight || 0 : 0;

                const targetPosition =
                    target.getBoundingClientRect().top +
                    window.scrollY -
                    headerHeight -
                    15;

                window.scrollTo({

                    top: targetPosition,

                    behavior: "smooth"

                });

            }
        );

    });



/* =========================
   IMAGE ERROR HANDLING
========================= */

document
    .querySelectorAll("img")
    .forEach(image => {

        image.addEventListener(
            "error",
            () => {

                image.style.background =
                    "#eaf4e7";

                image.style.objectFit =
                    "contain";

                console.warn(
                    "Image could not be loaded:",
                    image.src
                );

            }
        );

    });



/* =========================
   CURRENT YEAR
========================= */

const yearElement =
    document.querySelector(
        ".footer-bottom span"
    );


if (yearElement) {

    const currentYear =
        new Date().getFullYear();

    yearElement.innerHTML =
        yearElement.innerHTML.replace(
            "2026",
            currentYear
        );

}