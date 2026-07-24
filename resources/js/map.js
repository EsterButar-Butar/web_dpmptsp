document.addEventListener("DOMContentLoaded", () => {

    if (typeof lokasi === "undefined") {
        console.error("Data lokasi tidak ditemukan!");
        return;
    }

    if (typeof analysis === "undefined") {
        console.error("Data analysis tidak ditemukan!");
    // MAP
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
    // ICON
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

    const group = L.featureGroup().addTo(map);

    const markers = [];

    // ==========================
    // MARKER
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
                    Klik untuk melihat informasi investasi.
                </div>
            </div>
        `);

        marker.on("click", () => {
            console.log("Nama marker:", item.nama);
            console.log("Jumlah analysis:", window.analysis.length);
            console.log("Contoh data pertama:", window.analysis[0]);

            document.getElementById("detailKosong").style.display = "none";
            document.getElementById("detailDaerah").style.display = "block";

            document.getElementById("namaDaerah").innerText = item.nama;
            document.getElementById("statusDaerah").innerText = "Sudah Dianalisis";
            document.getElementById("koordinatDaerah").innerText =
                item.latitude + ", " + item.longitude;

            // Cari semua data kabupaten
            const dataKabupaten = window.analysis.filter(row =>
            row.kabupaten_kota === item.nama
                );

console.log("Hasil filter:", dataKabupaten);
            if (dataKabupaten.length === 0) {

                document.getElementById("sektorDaerah").innerHTML =
                    "<strong>Belum ada data</strong>";

                return;
            }

            // Cari sektor unggulan
            const unggulan = dataKabupaten
                .filter(row =>
                    Number(row.lq) > 1 &&
                    row.tipologi === "Unggulan" &&
                    row.klassen === "I"
                )
                .sort((a, b) => Number(b.ssa) - Number(a.ssa))[0];

            if (!unggulan) {

                document.getElementById("sektorDaerah").innerHTML =
                    "<strong>Belum ada sektor unggulan</strong>";

                return;
            }

            document.getElementById("sektorDaerah").innerHTML = `
                <strong>${unggulan.sektor}</strong>

                <br><br>

                <small>
                    <b>LQ</b> : ${unggulan.lq}<br>
                    <b>SSA</b> : ${unggulan.ssa}<br>
                    <b>Klassen</b> : ${unggulan.klassen}<br>
                    <b>Tipologi</b> : ${unggulan.tipologi}
                </small>
            `;

        });

        markers.push({
            nama: item.nama.toLowerCase(),
            marker: marker,
            lat: item.latitude,
            lng: item.longitude
        });

    });

    // ==========================
    // FIT MAP
    // ==========================
    if (group.getLayers().length > 0) {
        map.fitBounds(group.getBounds(), {
            padding: [50, 50]
        });
    }

    // ==========================
    // SEARCH
    // ==========================
    const search = document.getElementById("searchKabupaten");

    if (search) {

        search.addEventListener("keyup", function () {

            const keyword = this.value.toLowerCase();

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

    map.on("click", () => {
        map.closePopup();
    });

});