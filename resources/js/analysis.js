document.addEventListener("DOMContentLoaded", () => {


    /*
    ==============================
    FILTER ANIMATION
    ==============================
    */

    const filters = document.querySelectorAll(
        ".filter-box select"
    );


    filters.forEach((item)=>{


        item.addEventListener(
            "change",
            ()=>{

                item.classList.add(
                    "active-filter"
                );


                setTimeout(()=>{

                    item.classList.remove(
                        "active-filter"
                    );

                },300);


            }
        );


    });





    /*
    ==============================
    CARD ANIMATION
    ==============================
    */


    const cards =
    document.querySelectorAll(
        ".summary-card"
    );



    const observer =
    new IntersectionObserver(

        entries=>{


            entries.forEach(entry=>{


                if(entry.isIntersecting){


                    entry.target.classList.add(
                        "show"
                    );


                }


            });


        },


        {
            threshold:0.2
        }


    );



    cards.forEach(card=>{


        observer.observe(card);


    });



});