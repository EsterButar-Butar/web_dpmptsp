import Chart from "chart.js/auto";

document.addEventListener("DOMContentLoaded", () => {

    const charts = window.comparisonCharts ?? {};

    renderChart("growthChart", charts.growth);
    renderChart("contributionChart", charts.contribution);
    renderChart("lqChart", charts.lq);
    renderChart("ssaChart", charts.ssa);
    /*
    =====================================
    FILTER EFFECT
    =====================================
    */

    document
        .querySelectorAll(".comparison-filter select")
        .forEach(select => {

            select.addEventListener("change", () => {

                select.classList.add("changed");

                setTimeout(() => {
                    select.classList.remove("changed");
                }, 300);

            });

        });

    /*
    =====================================
    SCROLL ANIMATION
    =====================================
    */

    const observer = new IntersectionObserver(entries => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }

        });

    }, {
        threshold: 0.2
    });

    document
        .querySelectorAll(".summary-card, .chart-card, .table-card")
        .forEach(el => observer.observe(el));

});


/*
=====================================
CHART RENDERER
=====================================
*/

function renderChart(canvasId, chart) {

    if (!chart) {
        return;
    }

    const canvas = document.getElementById(canvasId);

    if (!canvas) {
        return;
    }

    if (canvas.chartInstance) {
        canvas.chartInstance.destroy();
    }

    canvas.chartInstance = new Chart(canvas, {

        type: chart.type ?? "line",

        data: {

            labels: chart.labels ?? [],

            datasets: chart.datasets ?? []

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                mode: "index",
                intersect: false,
            },

            plugins: {

                legend: {
                    display: false
                },

                title: {
                    display: false
                }

            },


            scales:

                chart.type === "pie"

                    ? {}

                    : {

                        x: {
                            grid: {
                                display: false
                            }
                        },

                        y: {

                            beginAtZero: true,

                            ticks: {
                                precision: 2
                            }

                        }

                    }

        }

    });

}