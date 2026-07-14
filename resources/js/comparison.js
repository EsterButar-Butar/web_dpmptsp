document.addEventListener(
    "DOMContentLoaded",
    () => {



        /*
        ===========================
        FILTER EFFECT
        ===========================
        */


        const filters =
            document.querySelectorAll(
                ".comparison-filter select"
            );



        filters.forEach(
            filter => {


                filter.addEventListener(
                    "change",
                    () => {


                        filter.classList.add(
                            "changed"
                        );



                        setTimeout(
                            ()=>{


                                filter.classList.remove(
                                    "changed"
                                );


                            },
                            300
                        );


                    }
                );


            }
        );







        /*
        ===========================
        SCROLL ANIMATION
        ===========================
        */


        const elements =
            document.querySelectorAll(

                ".summary-card, .chart-card, .table-card"

            );



        const observer =
            new IntersectionObserver(

                entries=>{


                    entries.forEach(
                        entry=>{


                            if(entry.isIntersecting){


                                entry.target.classList.add(
                                    "show"
                                );


                            }


                        }
                    );


                },


                {
                    threshold:.2
                }


            );



        elements.forEach(

            element=>{


                observer.observe(
                    element
                );


            }

        );




    }
);