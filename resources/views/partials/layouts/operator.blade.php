{{-- Halaman Layout Utama untuk Operator --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Operator</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/data-wilayah.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>




    @stack('styles')
</head>
<body>
    <button type="button" id="mobileSidebarButton" class="mobile-sidebar-button" aria-label="Buka menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

    <div class="admin-shell">
        <aside id="adminSidebar" class="admin-sidebar">
            <div class="sidebar-logo">
                <img src="{{ asset('images/logo-dpmptsp.png') }}" alt="Logo DPMPTSP Sumatera Utara">
            </div>

            <div id="adminProfile" class="sidebar-profile">
                <button type="button" id="adminProfileToggle" class="profile-toggle">
                    <span class="profile-avatar" style="overflow: hidden; padding: 0; display: block;">
                        <img src="{{ Auth::user()?->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()?->name ?? 'Siti') . '&background=FFD54F&color=145239' }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    </span>

                    <span class="profile-copy">
                        <span class="profile-name">
                            {{ Auth::user()?->name ?? 'Siti' }}
                        </span>
                        <span class="profile-role">
                            {{ auth()->user()->role ?? 'operator' }}
                        </span>
                    </span>

                    <i class="fa-solid fa-chevron-down profile-chevron"></i>
                </button>

                <div class="profile-dropdown">
                    <div class="profile-dropdown-inner">
                        <a href="{{ route('operator.profile') }}" class="profile-dropdown-link {{ request()->routeIs('operator.profile') ? 'active' : '' }}">
                            <i class="fa-regular fa-user"></i>
                            <span>Profile</span>
                        </a>

                        <a href="{{ route('operator.settings') }}" class="profile-dropdown-link {{ request()->routeIs('operator.settings') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear"></i>
                            <span>Settings</span>
                        </a>

                        <div class="profile-dropdown-divider"></div>

                        <button type="button" id="openLogoutModal" class="profile-dropdown-link logout">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>

            <nav class="sidebar-content">
                <div class="sidebar-section-title">
                    Menu Operator
                </div>

                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('operator.dashboard') }}" class="sidebar-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-table-cells-large"></i>
                            <span>Dashboard Operator</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('operator.lq.index') }}" class="sidebar-link {{ request()->routeIs('operator.lq.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Analisis LQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('operator.ss.index') }}" class="sidebar-link {{ request()->routeIs('operator.ss.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Analisis SS</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('operator.tipologi.index') }}" class="sidebar-link {{ request()->routeIs('operator.tipologi.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-layer-group"></i>
                            <span>Analisis Tipologi Sektor</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('operator.klassen.index') }}" class="sidebar-link {{ request()->routeIs('operator.klassen.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-bar"></i>
                            <span>Analisis Klassen</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section-title">
                    Menu Utama
                </div>

                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ url('/') }}" class="sidebar-link">
                            <i class="fa-solid fa-house"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            {{-- Add pt-20 on mobile to prevent hamburger menu from overlapping the content --}}
            <div class="p-4 pt-20 md:p-6 md:pt-6 lg:p-8 w-full space-y-6 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    <form id="logoutForm" action="{{ route('logout') }}" method="POST" hidden>
        @csrf
    </form>

    <div id="logoutModal" class="logout-modal" hidden>
        <div class="logout-modal-backdrop" data-close-logout></div>
        <div class="logout-modal-card">
            <div class="logout-modal-icon">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h3>Keluar dari akun?</h3>
            <p>Anda akan keluar dari halaman operator dan perlu login kembali untuk mengakses dashboard.</p>
            <div class="logout-modal-actions">
                <button type="button" class="logout-cancel" data-close-logout>Batal</button>
                <button type="button" id="confirmLogout" class="logout-confirm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profile = document.getElementById('adminProfile');
            const profileToggle = document.getElementById('adminProfileToggle');

            const sidebar = document.getElementById('adminSidebar');
            const sidebarButton = document.getElementById('mobileSidebarButton');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            const logoutButton = document.getElementById('openLogoutModal');
            const logoutModal = document.getElementById('logoutModal');
            const confirmLogout = document.getElementById('confirmLogout');
            const logoutForm = document.getElementById('logoutForm');
            const closeLogoutButtons = document.querySelectorAll(
                '[data-close-logout]'
            );

            if (profile && profileToggle) {
                profileToggle.addEventListener('click', function () {
                    profile.classList.toggle('open');
                });
            }

            function closeSidebar() {
                sidebar?.classList.remove('open');
                sidebarBackdrop?.classList.remove('show');
            }

            sidebarButton?.addEventListener('click', function () {
                sidebar?.classList.toggle('open');
                sidebarBackdrop?.classList.toggle('show');
            });

            sidebarBackdrop?.addEventListener('click', closeSidebar);

            function openLogoutModal() {
                if (! logoutModal) {
                    return;
                }

                logoutModal.hidden = false;
                document.body.classList.add('modal-open');
            }

            function closeLogoutModal() {
                if (! logoutModal) {
                    return;
                }

                logoutModal.hidden = true;
                document.body.classList.remove('modal-open');
            }

            logoutButton?.addEventListener('click', openLogoutModal);

            closeLogoutButtons.forEach(function (button) {
                button.addEventListener('click', closeLogoutModal);
            });

            confirmLogout?.addEventListener('click', function () {
                logoutForm?.submit();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeLogoutModal();
                    closeSidebar();
                }
            });
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(event, form) {
            event.preventDefault();
            Swal.fire({
                title: '<span class="text-lg">Hapus Data?</span>',
                html: '<span class="text-sm">Data yang dihapus tidak dapat dikembalikan!</span>',
                icon: 'warning',
                width: '24em',
                padding: '1.5em',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'rounded-lg text-sm px-4 py-2',
                    cancelButton: 'rounded-lg text-sm px-4 py-2',
                    popup: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        function confirmDeleteAll(event, form) {
            event.preventDefault();
            Swal.fire({
                title: '<span class="text-lg">Hapus Semua Data?</span>',
                html: '<span class="text-sm">Apakah Anda yakin ingin menghapus semua data? Aksi ini tidak dapat dibatalkan!</span>',
                icon: 'warning',
                width: '24em',
                padding: '1.5em',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'rounded-lg text-sm px-4 py-2',
                    cancelButton: 'rounded-lg text-sm px-4 py-2',
                    popup: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        const pageKey = 'selected_rows_' + window.location.pathname;

        function getSelectedIds() {
            const ids = sessionStorage.getItem(pageKey);
            return ids ? JSON.parse(ids) : [];
        }

        function saveSelectedIds(ids) {
            sessionStorage.setItem(pageKey, JSON.stringify(ids));
        }

        function toggleSelectAll(source) {
            let selectedIds = getSelectedIds();
            const checkboxes = document.querySelectorAll('.row-checkbox');
            
            checkboxes.forEach(cb => {
                cb.checked = source.checked;
                if (source.checked) {
                    if (!selectedIds.includes(cb.value)) selectedIds.push(cb.value);
                } else {
                    selectedIds = selectedIds.filter(id => id !== cb.value);
                }
            });
            
            saveSelectedIds(selectedIds);
            updateBulkDeleteState();
        }

        function updateBulkDeleteState() {
            const selectedIds = getSelectedIds();
            const bulkBtn = document.getElementById('bulkDeleteBtn');
            const form = document.getElementById('bulkDeleteForm');
            
            if (!bulkBtn) return;
            
            if (selectedIds.length > 0) {
                bulkBtn.classList.remove('hidden');
                
                // Update button text with count while preserving SVG
                bulkBtn.innerHTML = `
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Terpilih (${selectedIds.length})
                `;
                
                if(form) {
                    // Update hidden inputs for submission
                    document.querySelectorAll('.bulk-id-input').forEach(el => el.remove());
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        input.className = 'bulk-id-input';
                        form.appendChild(input);
                    });
                }
            } else {
                bulkBtn.classList.add('hidden');
            }

            // Update master checkbox state for current page
            const allCheckboxes = document.querySelectorAll('.row-checkbox');
            const selectAll = document.getElementById('selectAll');
            
            if (selectAll && allCheckboxes.length > 0) {
                const checkedCount = Array.from(allCheckboxes).filter(cb => cb.checked).length;
                selectAll.checked = checkedCount === allCheckboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < allCheckboxes.length;
            }
        }

        // Initialize checkboxes on page load based on sessionStorage
        document.addEventListener('DOMContentLoaded', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length > 0) {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(cb => {
                    if (selectedIds.includes(cb.value)) {
                        cb.checked = true;
                    }
                });
            }
            updateBulkDeleteState();
        });

        // Event listener for individual checkboxes
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('row-checkbox')) {
                let selectedIds = getSelectedIds();
                const id = e.target.value;
                
                if (e.target.checked) {
                    if (!selectedIds.includes(id)) selectedIds.push(id);
                } else {
                    selectedIds = selectedIds.filter(i => i !== id);
                }
                
                saveSelectedIds(selectedIds);
                updateBulkDeleteState();
            }
        });

        function confirmBulkDelete(event, form) {
            event.preventDefault();
            const selectedIds = getSelectedIds();
            const count = selectedIds.length;
            
            if(count === 0) return false;
            
            Swal.fire({
                title: '<span class="text-lg">Hapus Terpilih?</span>',
                html: `<span class="text-sm">Apakah Anda yakin ingin menghapus <b>${count}</b> data terpilih?<br>Aksi ini tidak dapat dibatalkan!</span>`,
                icon: 'warning',
                width: '24em',
                padding: '1.5em',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'rounded-lg text-sm px-4 py-2',
                    cancelButton: 'rounded-lg text-sm px-4 py-2',
                    popup: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    sessionStorage.removeItem(pageKey); // Clear memory after deletion
                    form.submit();
                }
            });
            return false;
        }

        window.exportToPDF = function (tableId, title, filename) {
            const table = document.getElementById(tableId);
            if (!table) return;

            // Clone the table
            const clone = table.cloneNode(true);

            // Remove Checkbox (first col) and Aksi (last col) columns from clone
            const rows = clone.rows;
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].cells.length > 0) {
                    rows[i].deleteCell(-1); // Delete Aksi column
                    rows[i].deleteCell(0);  // Delete Checkbox column
                }
            }

            // Set styling for cloned table to look beautiful in PDF
            clone.style.width = '100%';
            clone.style.borderCollapse = 'collapse';
            clone.style.fontSize = '9px';
            clone.style.fontFamily = "'Poppins', sans-serif";
            clone.style.color = '#334155';
            clone.style.marginTop = '10px';

            // Style headers
            const ths = clone.querySelectorAll('th');
            ths.forEach(th => {
                th.style.backgroundColor = '#075936';
                th.style.color = '#ffffff';
                th.style.padding = '8px 6px';
                th.style.border = '1px solid #cbd5e1';
                th.style.fontWeight = '600';
                th.style.fontSize = '9px';
                th.style.textAlign = 'center';
                // Hide selectAll checkbox element inside th if present
                const cb = th.querySelector('input[type="checkbox"]');
                if (cb) cb.remove();
            });

            // Style data cells
            const tds = clone.querySelectorAll('td');
            tds.forEach(td => {
                td.style.padding = '6px';
                td.style.border = '1px solid #cbd5e1';
                td.style.lineHeight = '1.3';
                // Hide checkboxes inside td if present
                const cb = td.querySelector('input[type="checkbox"]');
                if (cb) cb.remove();
                
                // Align center classes
                if (td.classList.contains('text-center')) {
                    td.style.textAlign = 'center';
                }
            });

            // Create a container to export
            const element = document.createElement('div');
            element.style.padding = '15px';
            element.style.backgroundColor = '#ffffff';

            // Create Cop/Header
            const header = document.createElement('div');
            header.style.display = 'flex';
            header.style.alignItems = 'center';
            header.style.justifyContent = 'center';
            header.style.borderBottom = '3px double #075936';
            header.style.paddingBottom = '12px';
            header.style.marginBottom = '15px';

            const logo = document.createElement('img');
            logo.src = '/images/logo-dpmptsp.png';
            logo.style.height = '50px';
            logo.style.marginRight = '15px';

            const headerText = document.createElement('div');
            headerText.style.textAlign = 'center';
            
            const title1 = document.createElement('h2');
            title1.textContent = 'PEMERINTAH PROVINSI SUMATERA UTARA';
            title1.style.margin = '0';
            title1.style.fontSize = '14px';
            title1.style.fontWeight = 'bold';
            title1.style.color = '#075936';

            const title2 = document.createElement('h3');
            title2.textContent = 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU';
            title2.style.margin = '3px 0 0 0';
            title2.style.fontSize = '11px';
            title2.style.fontWeight = 'bold';
            title2.style.color = '#075936';

            headerText.appendChild(title1);
            headerText.appendChild(title2);
            
            header.appendChild(logo);
            header.appendChild(headerText);
            element.appendChild(header);

            // Document Title & Metadata
            const docTitle = document.createElement('h4');
            docTitle.textContent = title;
            docTitle.style.textAlign = 'center';
            docTitle.style.fontSize = '12px';
            docTitle.style.fontWeight = 'bold';
            docTitle.style.margin = '0 0 10px 0';
            docTitle.style.textTransform = 'uppercase';
            element.appendChild(docTitle);

            const meta = document.createElement('div');
            meta.style.display = 'flex';
            meta.style.justifyContent = 'space-between';
            meta.style.fontSize = '9px';
            meta.style.color = '#64748b';
            meta.style.marginBottom = '10px';
            meta.style.borderBottom = '1px solid #e2e8f0';
            meta.style.paddingBottom = '4px';

            const dateInfo = document.createElement('span');
            dateInfo.textContent = 'Tanggal Unduh: ' + new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
            
            const operatorInfo = document.createElement('span');
            const pName = document.querySelector('.profile-name');
            operatorInfo.textContent = 'Operator: ' + (pName ? pName.textContent.trim() : 'Operator');

            meta.appendChild(dateInfo);
            meta.appendChild(operatorInfo);
            element.appendChild(meta);

            // Append Cloned Table
            element.appendChild(clone);

            // Append Signature/Tanda Tangan at bottom
            const signature = document.createElement('div');
            signature.style.marginTop = '30px';
            signature.style.display = 'flex';
            signature.style.justifyContent = 'flex-end';
            signature.style.fontSize = '10px';
            signature.style.color = '#334155';

            const sigBox = document.createElement('div');
            sigBox.style.width = '180px';
            sigBox.style.textAlign = 'center';

            const sigTitle = document.createElement('p');
            sigTitle.textContent = 'Petugas Operator,';
            sigTitle.style.margin = '0 0 50px 0';

            const sigName = document.createElement('p');
            sigName.textContent = (pName ? pName.textContent.trim() : 'Operator');
            sigName.style.fontWeight = 'bold';
            sigName.style.margin = '0';
            sigName.style.textDecoration = 'underline';

            const sigRole = document.createElement('p');
            sigRole.textContent = 'DPMPTSP Sumatera Utara';
            sigRole.style.margin = '1px 0 0 0';

            sigBox.appendChild(sigTitle);
            sigBox.appendChild(sigName);
            sigBox.appendChild(sigRole);
            signature.appendChild(sigBox);
            element.appendChild(signature);

            // Options for html2pdf
            const opt = {
                margin:       10,
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            // Run html2pdf
            html2pdf().set(opt).from(element).save();
        };
    </script>

</body>
</html>
