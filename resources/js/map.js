document.addEventListener("DOMContentLoaded", () => {

    // ==========================
    // CEK DATA
    // ==========================
    if (typeof lokasi === "undefined") {
        console.error("Data lokasi tidak ditemukan!");
        return;
    }

    // ==========================
    // INISIALISASI MAP
    // ==========================
    const map = L.map("map", {
        zoomControl: true,
        scrollWheelZoom: true,
    });

    map.setView([2.9, 99.2], 8);

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution: "&copy; OpenStreetMap",
            maxZoom: 18,
        }
    ).addTo(map);

    // ==========================
    // ICON HIJAU
    // ==========================
    const greenIcon = L.icon({

        iconUrl:
            "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png",

        shadowUrl:
            "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",

        iconSize: [25, 41],

        iconAnchor: [12, 41],

        popupAnchor: [1, -34],

        shadowSize: [41, 41]

    });

    // ==========================
    // MARKER GROUP
    // ==========================
    const group = L.featureGroup().addTo(map);

    const markers = [];

    // ==========================
    // LOOP DATA
    // ==========================
    lokasi.forEach(item => {

        const marker = L.marker(
            [item.latitude, item.longitude],
            {
                icon: greenIcon
            }
        ).addTo(group);

        marker.bindPopup(`

            <div class="popup-card">

                <div class="popup-title">

                    📍 ${item.nama}

                </div>

                <div class="popup-desc">

                    Klik untuk melihat
                    informasi investasi.

                </div>

            </div>

        `);

       marker.on("click", () => {

    document.getElementById("detailKosong").style.display = "none";

    document.getElementById("detailDaerah").style.display = "block";

    document.getElementById("namaDaerah").innerText = item.nama;

    document.getElementById("statusDaerah").innerText = "Sudah Dianalisis";

    document.getElementById("sektorDaerah").innerText = "Memuat hasil analisis...";

    document.getElementById("koordinatDaerah").innerText =
        item.latitude + ", " + item.longitude;

            // ======================
            // NANTI GANTI INI
            // ======================
            // fetch(`/api/analisis/${item.id}`)
            // .then(res=>res.json())
            // .then(data=>{
            //     document.getElementById("sektorDaerah").innerText =
            //     data.sektor_unggulan;
            // });

        });

        markers.push({

            nama: item.nama.toLowerCase(),

            marker: marker,

            lat: item.latitude,

            lng: item.longitude

        });

    });

    // ==========================
    // FIT BOUNDS
    // ==========================
    if (group.getLayers().length > 0) {

        map.fitBounds(group.getBounds(), {

            padding: [50, 50]

        });

    }

    // ==========================
    // SEARCH
    // ==========================
    const search =
        document.getElementById("searchKabupaten");

    if (search) {

        search.addEventListener("keyup", function () {

            const keyword =
                this.value.toLowerCase();

            markers.forEach(item => {

                if (item.nama.includes(keyword)) {

                    map.flyTo(
                        [item.lat, item.lng],
                        10,
                        {
                            duration: 1.5
                        }
                    );

                    item.marker.openPopup();

                }

            });

        });

    }

    // ==========================
    // CLOSE POPUP
    // ==========================
    map.on("click", () => {

        map.closePopup();

    });

});