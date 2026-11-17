<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN DASHBOARD</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div id="loginOverlay" class="container" style="max-width: 450px; margin-top: 60px;">
        <div class="comic-card" style="text-align: center;">
            <h1 class="comic-title" style="margin-bottom: 10px;">ADMIN LOGIN</h1>
            <p style="font-weight: 700; margin-bottom: 20px;">Masukkan akses rahasia pengelola</p>
            <form id="loginForm">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="loginUser" required placeholder="admin">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="loginPass" required placeholder="••••••••">
                </div>
                <button type="submit" class="comic-btn" style="width: 100%; margin-top: 10px;">LOGIN SEKARANG</button>
                <div id="loginError" class="comic-alert alert-danger hidden" style="margin-top: 15px;"></div>
            </form>
            <div style="margin-top: 20px;">
                <a href="https://canvas.my.id" class="comic-btn blue" style="font-size: 1rem; padding: 8px 14px; width: 100%;">⬅ Kembali ke Halaman Scan</a>
            </div>
        </div>
    </div>

    <div id="appContainer" class="container hidden">
        <div class="comic-header">
            <h1 class="comic-title">💥 DASHBOARD ADMIN 💥</h1>
            <div class="header-actions">
                <a href="https://canvas.my.id" class="comic-btn blue" target="_blank">📷 Buka Scanner</a>
                <button id="btnLogout" class="comic-btn" style="background: #ef4444; color: white;">🚪 Logout</button>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="comic-card">
                <h2 style="font-family: 'Bangers', cursive; font-size: 1.6rem; margin-bottom: 15px; letter-spacing: 1px;">🪪 BUAT & PILIH LOGO QR</h2>
                <form id="qrForm">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="inputNama" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="form-group">
                        <label>Kelas / Divisi</label>
                        <input type="text" id="inputKelas" required placeholder="Contoh: XII IPA 1">
                    </div>
                    <div class="form-group">
                        <label>Pilih Logo dari Folder 'image/'</label>
                        <select id="selectLogo" required>
                            <option value="">-- Pilih File Logo --</option>
                        </select>
                    </div>
                    <button type="submit" class="comic-btn green" style="width: 100%;">GENERATE KARTU</button>
                </form>

                <div style="text-align: center; margin-top: 20px;">
                    <!-- Ditambahkan class="hidden" agar canvas tidak muncul kosong sebelum di-generate -->
                    <canvas id="qrCardCanvas" class="hidden" width="400" height="520" style="max-width: 100%; height: auto; border: var(--comic-border); border-radius: 8px; box-shadow: 3px 3px 0px #111;"></canvas>
                    <br>
                    <button id="btnDownloadCard" class="comic-btn hidden" style="margin-top: 15px; background: #8b5cf6; color: white; width: 100%;">⬇️ DOWNLOAD KARTU</button>
                </div>
            </div>

            <div class="comic-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                    <h2 style="font-family: 'Bangers', cursive; font-size: 1.6rem; letter-spacing: 1px;">📊 REKAP & MANAJEMEN</h2>
                    <div class="filter-bar">
                        <input type="date" id="filterTanggal" style="padding: 8px; border: var(--comic-border); border-radius: 6px; font-weight: 700;">
                        <button id="btnRefreshData" class="comic-btn blue" style="padding: 8px 14px; font-size: 1rem;">Cari</button>
                    </div>
                </div>

                <div class="manual-box">
                    <h3 style="font-size: 0.95rem; margin-bottom: 10px; font-weight: 800;">➕ Input Absen Manual</h3>
                    <form id="manualForm" class="manual-form">
                        <input type="text" id="manualNama" placeholder="Nama" required style="flex: 2;">
                        <input type="text" id="manualKelas" placeholder="Kelas" required style="flex: 1;">
                        <select id="manualStatus">
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                        <button type="submit" class="comic-btn green" style="padding: 8px 14px; font-size: 0.95rem;">Tambah</button>
                    </form>
                </div>

                <p id="rekapInfo" style="font-weight: 800; font-size: 0.9rem; margin-bottom: 10px; color: #333;"></p>
                
                <div class="table-wrapper">
                    <table class="table-comic">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="rekapTableBody">
                            <tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const SECRET_KEY = 'KODEPANJANG';

        async function checkSession() {
            try {
                const res = await fetch('../api/api.php?action=check_session');
                const data = await res.json();
                if (data.status === 'logged_in') {
                    document.getElementById('loginOverlay').classList.add('hidden');
                    document.getElementById('appContainer').classList.remove('hidden');
                    document.getElementById('filterTanggal').value = new Date().toISOString().split('T')[0];
                    loadImages();
                    loadAttendanceData();
                } else {
                    document.getElementById('loginOverlay').classList.remove('hidden');
                    document.getElementById('appContainer').classList.add('hidden');
                }
            } catch (e) { console.error(e); }
        }
        checkSession();

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('loginUser').value;
            const password = document.getElementById('loginPass').value;
            const errBox = document.getElementById('loginError');

            const res = await fetch('../api/api.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            const result = await res.json();

            if (result.status === 'success') {
                location.reload();
            } else {
                errBox.textContent = result.message;
                errBox.classList.remove('hidden');
            }
        });

        document.getElementById('btnLogout').addEventListener('click', async () => {
            await fetch('../api/api.php?action=logout');
            location.reload();
        });

        async function loadImages() {
            try {
                const res = await fetch('../api/api.php?action=list_images');
                const result = await res.json();
                const select = document.getElementById('selectLogo');
                select.innerHTML = '<option value="">-- Pilih File Logo dari Folder Image --</option>';
                if (result.status === 'success' && result.images.length > 0) {
                    result.images.forEach(img => {
                        const opt = document.createElement('option');
                        opt.value = '../image/' + img;
                        opt.textContent = img;
                        select.appendChild(opt);
                    });
                } else {
                    const opt = document.createElement('option');
                    opt.value = "";
                    opt.textContent = "Tidak ada gambar di folder image/";
                    select.appendChild(opt);
                }
            } catch (err) {
                console.error(err);
            }
        }

        const qrForm = document.getElementById('qrForm');
        const canvas = document.getElementById('qrCardCanvas');
        const ctx = canvas.getContext('2d');
        const btnDownload = document.getElementById('btnDownloadCard');

        qrForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nama = document.getElementById('inputNama').value.trim();
            const kelas = document.getElementById('inputKelas').value.trim();
            const logoSrc = document.getElementById('selectLogo').value;

            if (!logoSrc) {
                alert('Pilih logo terlebih dahulu!');
                return;
            }

            const payload = JSON.stringify({ key: SECRET_KEY, nama, kelas });
            const logoImg = new Image();
            // Atribut crossOrigin dihapus untuk menghindari error CORS pada path lokal server
            logoImg.onload = () => {
                renderComicCard(nama, kelas, payload, logoImg);
            };
            logoImg.onerror = () => {
                alert('Gagal memuat gambar logo dari path tersebut.');
            };
            logoImg.src = logoSrc;
        });

        function renderComicCard(nama, kelas, payload, logoImg) {
            const W = canvas.width;
            const H = canvas.height;

            ctx.clearRect(0, 0, W, H);

            ctx.fillStyle = '#ffcc00';
            ctx.fillRect(0, 0, W, H);

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(20, 20, W - 40, H - 40);
            ctx.strokeStyle = '#111';
            ctx.lineWidth = 4;
            ctx.strokeRect(20, 20, W - 40, H - 40);

            ctx.fillStyle = '#111';
            ctx.font = '800 16px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('KARTU ABSENSI RESMI', W / 2, 55);

            ctx.font = '800 24px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(nama.toUpperCase(), W / 2, 90);

            ctx.font = '700 14px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = '#ff3366';
            ctx.fillText('KELAS: ' + kelas.toUpperCase(), W / 2, 115);

            const qrSize = 220;
            const qrX = (W - qrSize) / 2;
            const qrY = 140;

            const tempCanvas = document.createElement('canvas');
            QRCode.toCanvas(tempCanvas, payload, { width: qrSize, margin: 1, errorCorrectionLevel: 'H' }, (error) => {
                if (error) {
                    console.error(error);
                    alert('Gagal membuat QR Code.');
                    return;
                }

                ctx.drawImage(tempCanvas, qrX, qrY);

                const logoSize = 50;
                const logoX = (W - logoSize) / 2;
                const logoY = qrY + (qrSize - logoSize) / 2;

                ctx.fillStyle = '#fff';
                ctx.fillRect(logoX - 4, logoY - 4, logoSize + 8, logoSize + 8);
                ctx.drawImage(logoImg, logoX, logoY, logoSize, logoSize);

                ctx.fillStyle = '#111';
                ctx.font = '700 12px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('SCAN UNTUK ABSEN KEHADIRAN', W / 2, 490);

                // Munculkan canvas dan tombol download setelah proses render selesai
                canvas.classList.remove('hidden');
                btnDownload.classList.remove('hidden');
            });
        }

        btnDownload.addEventListener('click', () => {
            const link = document.createElement('a');
            link.download = 'Kartu_Absensi.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });

        async function loadAttendanceData() {
            const tbody = document.getElementById('rekapTableBody');
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>';
            const tanggal = document.getElementById('filterTanggal').value;

            try {
                const res = await fetch(`../api/api.php?action=list&tanggal=${tanggal}`);
                const result = await res.json();

                if (result.status === 'success') {
                    document.getElementById('rekapInfo').textContent = `Tanggal: ${result.tanggal} | Total: ${result.data.length} Orang`;
                    if (result.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Belum ada data hadir pada tanggal ini.</td></tr>';
                        return;
                    }

                    tbody.innerHTML = '';
                    result.data.forEach((row, index) => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><b>${row.nama}</b></td>
                                <td>${row.kelas}</td>
                                <td>${row.jam_masuk}</td>
                                <td><b>${row.status}</b></td>
                                <td><button onclick="deleteRecord(${row.id})" class="comic-btn" style="background:#ef4444; color:#fff; padding:4px 8px; font-size:0.8rem;">Hapus</button></td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Gagal memuat data dari server.</td></tr>';
                }
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Terjadi kesalahan koneksi.</td></tr>';
            }
        }

        document.getElementById('btnRefreshData').addEventListener('click', loadAttendanceData);

        document.getElementById('manualForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const nama = document.getElementById('manualNama').value.trim();
            const kelas = document.getElementById('manualKelas').value.trim();
            const tanggal = document.getElementById('filterTanggal').value;
            const status = document.getElementById('manualStatus').value;

            const res = await fetch('../api/api.php?action=add_manual', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nama, kelas, tanggal, status })
            });
            const result = await res.json();
            if (result.status === 'success') {
                document.getElementById('manualNama').value = '';
                document.getElementById('manualKelas').value = '';
                loadAttendanceData();
            } else {
                alert(result.message);
            }
        });

        async function deleteRecord(id) {
            if (!confirm('Yakin ingin menghapus data absensi ini?')) return;
            const res = await fetch('../api/api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const result = await res.json();
            if (result.status === 'success') {
                loadAttendanceData();
            } else {
                alert(result.message);
            }
        }
    </script>
</body>
</html>