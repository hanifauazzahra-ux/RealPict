<?php
// RealPict - Deteksi Foto AI & Forensik Digital
// PHP Main Application
$activePage = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealPict – Deteksi Foto AI & Forensik Digital</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23f59e0b'><path d='M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z'/></svg>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="ambient-glow"></div>

    <!-- ==========================================================================
         Header & Navigation (Navbar)
         ========================================================================== -->
    <header class="site-header">
        <div class="container nav-container">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-logo" data-tab-target="beranda">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <span>RealPict</span>
            </a>

            <!-- Navigation Pills -->
            <ul class="nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'beranda' ? 'active' : '' ?>" data-tab="beranda">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'cek_foto' ? 'active' : '' ?>" data-tab="cek-foto">Cek Foto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'upload_massal' ? 'active' : '' ?>" data-tab="massal">Upload Massal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'edukasi' ? 'active' : '' ?>" data-tab="edukasi">Edukasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'laporan' ? 'active' : '' ?>" data-tab="laporan">Laporan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activePage === 'training' ? 'active' : '' ?>" data-tab="training">Latih AI</a>
                </li>
            </ul>

            <!-- Encrypted Session Badge -->
            <div class="nav-status">
                <span class="status-dot"></span>
                <span>Sesi Anonim · Terenkripsi</span>
            </div>
        </div>
    </header>

    <!-- ==========================================================================
         Main Dynamic Content Area
         ========================================================================== -->
    <main>
        <!-- TAB 1: BERANDA -->
        <section id="tab-beranda" class="page-tab <?= $activePage === 'beranda' ? 'active-tab' : '' ?> hero-section">
            <div class="container hero-grid">
                <!-- Left Hero Text & CTA -->
                <div class="hero-left">
                    <div class="badge-tag">
                        <svg viewBox="0 0 24 24">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        <span>FORENSIK FOTO DIGITAL</span>
                    </div>

                    <h1 class="hero-title">
                        Jangan Percaya Foto yang Kamu Lihat <span class="highlight">Begitu Saja</span>
                    </h1>

                    <p class="hero-desc">
                        RealPict menganalisis pola piksel, metadata, noise, dan struktur gambar untuk mendeteksi foto buatan atau suntingan AI. Tanpa daftar, tanpa login. Tinggal unggah dan lihat hasilnya.
                    </p>

                    <div class="hero-actions">
                        <button type="button" class="btn btn-primary" data-tab-target="cek-foto">
                            Cek Sekarang &nbsp;→
                        </button>
                        <button type="button" class="btn btn-secondary" data-tab-target="edukasi">
                            Pelajari Tips
                        </button>
                    </div>

                    <div class="hero-perks">
                        <div class="perk-item">
                            <span class="check">✓</span>
                            <span>Tanpa akun</span>
                        </div>
                        <div class="perk-item">
                            <span class="check">✓</span>
                            <span>Hitungan detik</span>
                        </div>
                        <div class="perk-item">
                            <span class="check">✓</span>
                            <span>JPG · PNG · WEBP</span>
                        </div>
                    </div>
                </div>

                <!-- Right Hero: Live Indicator Card -->
                <div class="hero-right">
                    <div class="live-indicator-card">
                        <div class="card-topbar">
                            <div class="live-badge">
                                <span class="status-dot"></span>
                                <span>LIVE INDICATOR</span>
                            </div>
                            <div class="card-sample-label">contoh analisis</div>
                        </div>

                        <div class="indicator-body">
                            <!-- Circular Donut Chart -->
                            <div class="donut-container">
                                <svg class="donut-svg" viewBox="0 0 120 120">
                                    <circle class="donut-bg" cx="60" cy="60" r="52"></circle>
                                    <circle id="heroDonutArc" class="donut-arc" cx="60" cy="60" r="52" style="stroke-dasharray: 326.7; stroke-dashoffset: 287.5; stroke: #86efac;"></circle>
                                </svg>
                                <div class="donut-center-text">
                                    <div id="heroDonutPercent" class="donut-percentage">12%</div>
                                    <div class="donut-label">KEMUNGKINAN AI</div>
                                </div>
                            </div>

                            <!-- Metrics List -->
                            <div class="indicator-metrics">
                                <div style="height: 1px; background: rgba(245, 158, 11, 0.4); margin-bottom: 2px;"></div>
                                
                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Pola Piksel</span>
                                        <span class="metric-value">10%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: 10%;"></div>
                                    </div>
                                </div>

                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Metadata</span>
                                        <span class="metric-value">18%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: 18%;"></div>
                                    </div>
                                </div>

                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Noise Pattern</span>
                                        <span class="metric-value">9%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: 9%;"></div>
                                    </div>
                                </div>

                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Struktur Gambar</span>
                                        <span class="metric-value">14%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: 14%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 2: CEK FOTO (Single Check) -->
        <section id="tab-cek-foto" class="page-tab <?= $activePage === 'cek_foto' ? 'active-tab' : '' ?>" style="padding: 48px 0 64px;">
            <div class="container">
                <div class="page-header">
                    <h2 class="page-title">Cek Foto</h2>
                    <p class="page-subtitle">Unggah satu atau beberapa foto. File diproses di perangkatmu dan tidak disimpan.</p>
                </div>

                <!-- Dropzone Box -->
                <div id="singleDropzone" class="dropzone-box">
                    <div class="dropzone-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="19" x2="12" y2="5"></line>
                            <polyline points="5 12 12 5 19 12"></polyline>
                        </svg>
                    </div>
                    <div class="dropzone-title">Seret & lepas foto di sini</div>
                    <div class="dropzone-sub">
                        atau <span class="dropzone-link">pilih dari perangkat</span> · JPG, PNG, WEBP
                    </div>
                    <input type="file" id="singleFileInput" class="hidden-input" accept="image/jpeg,image/png,image/webp">
                </div>

                <!-- Scanning Animation Feedback -->
                <div id="singleScanOverlay" class="scan-overlay">
                    <div class="scan-spinner"></div>
                    <div id="scanStatusText" class="scan-status-text">Memproses gambar...</div>
                    <div id="scanStepText" class="scan-step-text">Membaca struktur data...</div>
                </div>

                <!-- Full Forensic Result Card -->
                <div id="singleResultCard" class="result-card">
                    <div class="result-header">
                        <div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px;">HASIL ANALISIS FORENSIK DIGITAL</div>
                            <div id="resultVerdictBadge" class="result-verdict verdict-real">REALPIC (Foto Asli Kamera)</div>
                        </div>
                        <button type="button" id="btnResetSingle" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.85rem;">
                            ↺ Periksa Foto Lain
                        </button>
                    </div>

                    <div class="result-grid">
                        <!-- Image Preview Column -->
                        <div class="result-preview-box">
                            <img id="singlePreviewImg" src="" class="result-preview-img" alt="Foto Preview">
                            <div class="result-file-info">
                                <div id="resultFileName" style="font-weight: 600; color: #fff; margin-bottom: 2px;">foto.jpg</div>
                                <div style="display: flex; justify-content: space-between; color: #64748b;">
                                    <span id="resultDimensions">3024 × 4032 px</span>
                                    <span id="resultFileSize">1.4 MB</span>
                                </div>
                            </div>
                        </div>

                        <!-- Metrics & Exif Column -->
                        <div class="result-metrics-details">
                            <div style="display: flex; gap: 24px; align-items: center; background: #0f1218; padding: 20px; border-radius: 14px; border: 1px solid #1c212c;">
                                <div class="donut-container" style="width: 110px; height: 110px;">
                                    <svg class="donut-svg" viewBox="0 0 120 120">
                                        <circle class="donut-bg" cx="60" cy="60" r="52"></circle>
                                        <circle id="resultDonutArc" class="donut-arc" cx="60" cy="60" r="52"></circle>
                                    </svg>
                                    <div class="donut-center-text">
                                        <div id="resultDonutPercent" class="donut-percentage" style="font-size: 1.5rem;">0%</div>
                                        <div class="donut-label" style="font-size: 0.5rem;">KEMUNGKINAN AI</div>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; color: #fff; margin-bottom: 6px;">Ringkasan Kesimpulan</div>
                                    <p id="resultSummaryText" style="font-size: 0.85rem; color: #94a3b8; line-height: 1.5;"></p>
                                </div>
                            </div>

                            <!-- 4 Forensic Metrics -->
                            <div class="indicator-metrics">
                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Pola Piksel</span>
                                        <span id="valPolaPiksel" class="metric-value">0%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div id="barPolaPiksel" class="progress-fill" style="width: 0%;"></div>
                                    </div>
                                </div>
                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Metadata</span>
                                        <span id="valMetadata" class="metric-value">0%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div id="barMetadata" class="progress-fill" style="width: 0%;"></div>
                                    </div>
                                </div>
                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Noise Pattern</span>
                                        <span id="valNoise" class="metric-value">0%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div id="barNoise" class="progress-fill" style="width: 0%;"></div>
                                    </div>
                                </div>
                                <div class="metric-row">
                                    <div class="metric-info">
                                        <span class="metric-title">Struktur Gambar</span>
                                        <span id="valStruktur" class="metric-value">0%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div id="barStruktur" class="progress-fill" style="width: 0%;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Technical EXIF & Metadata Table -->
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #fff; margin-top: 8px;">Informasi Teknis & EXIF:</div>
                                <table class="exif-table">
                                    <tr>
                                        <td class="label">Perangkat / Kamera</td>
                                        <td id="exifKamera" class="val">-</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Software / Pipeline</td>
                                        <td id="exifSoftware" class="val">-</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Waktu Pemotretan</td>
                                        <td id="exifTanggal" class="val">-</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Dimensi Asli</td>
                                        <td id="exifResolusi" class="val">-</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Forensic Findings Bullet Points -->
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #fff; margin-bottom: 6px;">Catatan Forensik:</div>
                                <ul id="resultFindingsList" style="padding-left: 18px; margin: 0;"></ul>
                            </div>

                            <!-- Quick Training & Feedback Bar -->
                            <div class="quick-train-box" style="margin-top: 20px; padding: 16px; background: rgba(15, 18, 24, 0.95); border: 1px solid #1e293b; border-radius: 12px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                                    <div>
                                        <div style="font-size: 0.85rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 6px;">
                                            <span>🧠</span> Bantu Latih & Tingkatkan Akurasi Model
                                        </div>
                                        <div style="font-size: 0.775rem; color: #94a3b8; margin-top: 2px;">
                                            Hasil kurang pas? Latih sistem secara langsung agar lebih akurat mengenali foto sejenis:
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" id="btnQuickTrainAi" class="btn" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.35); padding: 7px 14px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border-radius: 8px;">
                                            🤖 Latih: Ini Foto AI
                                        </button>
                                        <button type="button" id="btnQuickTrainReal" class="btn" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); padding: 7px 14px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border-radius: 8px;">
                                            📷 Latih: Ini Foto Asli
                                        </button>
                                    </div>
                                </div>
                                <div id="quickTrainNotice" style="display: none; margin-top: 10px; font-size: 0.8rem; padding: 8px 12px; border-radius: 8px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 3: UPLOAD MASSAL (Batch Check) -->
        <section id="tab-massal" class="page-tab <?= $activePage === 'upload_massal' ? 'active-tab' : '' ?>" style="padding: 48px 0 64px;">
            <div class="container">
                <div class="page-header">
                    <h2 class="page-title">Upload Massal</h2>
                    <p class="page-subtitle">Audit banyak foto sekaligus dan lihat persentase deteksi AI per file.</p>
                </div>

                <!-- Dropzone Box for Batch -->
                <div id="massalDropzone" class="dropzone-box">
                    <div class="dropzone-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="19" x2="12" y2="5"></line>
                            <polyline points="5 12 12 5 19 12"></polyline>
                        </svg>
                    </div>
                    <div class="dropzone-title">Pilih banyak foto sekaligus</div>
                    <div class="dropzone-sub">
                        atau <span class="dropzone-link">pilih dari perangkat</span> · JPG, PNG, WEBP
                    </div>
                    <input type="file" id="massalFileInput" class="hidden-input" multiple accept="image/jpeg,image/png,image/webp">
                </div>

                <!-- Batch Progress and Table -->
                <div id="batchContainer" class="batch-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; flex-wrap: wrap; gap: 12px;">
                        <span id="batchProgressText" style="font-size: 0.95rem; font-weight: 600; color: #fff;">Memproses foto...</span>
                        <button type="button" id="btnResetBatch" class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.8rem; display: none;">
                            ↺ Unggah Foto Lain
                        </button>
                    </div>

                    <div class="batch-progress-bar">
                        <div id="batchProgressInner" class="batch-progress-inner"></div>
                    </div>

                    <!-- Summary Stats Displayed Directly on Web -->
                    <div id="batchSummarySection" class="batch-summary-cards" style="display: none;">
                        <div class="batch-stat-card">
                            <span class="batch-stat-label">Total Foto</span>
                            <span id="batchStatTotal" class="batch-stat-num">0</span>
                        </div>
                        <div class="batch-stat-card" style="border-left: 3px solid #ef4444;">
                            <span class="batch-stat-label" style="color: #f87171;">Terindikasi AI</span>
                            <span id="batchStatAi" class="batch-stat-num" style="color: #f87171;">0</span>
                        </div>
                        <div class="batch-stat-card" style="border-left: 3px solid #10b981;">
                            <span class="batch-stat-label" style="color: #34d399;">Foto Asli Kamera</span>
                            <span id="batchStatReal" class="batch-stat-num" style="color: #34d399;">0</span>
                        </div>
                        <div class="batch-stat-card" style="border-left: 3px solid #f59e0b;">
                            <span class="batch-stat-label" style="color: #fbbf24;">Mencurigakan</span>
                            <span id="batchStatSuspect" class="batch-stat-num" style="color: #fbbf24;">0</span>
                        </div>
                    </div>

                    <div class="batch-table-wrapper" style="overflow-x: auto;">
                        <table class="batch-table">
                            <thead>
                                <tr style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <th style="padding: 8px 16px; text-align: left; width: 60px;">Foto</th>
                                    <th style="padding: 8px 16px; text-align: left;">Nama & Dimensi</th>
                                    <th style="padding: 8px 16px; text-align: left; width: 220px;">Probabilitas AI</th>
                                    <th style="padding: 8px 16px; text-align: center; width: 140px;">Hasil Analisis</th>
                                    <th style="padding: 8px 16px; text-align: right; width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="batchTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 4: EDUKASI -->
        <section id="tab-edukasi" class="page-tab <?= $activePage === 'edukasi' ? 'active-tab' : '' ?> edu-section">
            <div class="container">
                <div class="page-header" style="margin-bottom: 32px;">
                    <p class="page-subtitle" style="font-size: 1rem; color: #cbd5e1;">Kenali ciri foto buatan AI sebelum membagikannya.</p>
                </div>

                <!-- 6 Tip Cards Grid matching Screenshot 4 -->
                <div class="tips-grid">
                    <!-- Tip 01 -->
                    <div class="tip-card">
                        <div class="tip-top">
                            <div class="tip-icon-box">🔍</div>
                            <span class="tip-badge">TIP 01</span>
                        </div>
                        <h3 class="tip-title">Cek detail tidak natural</h3>
                        <p class="tip-text">Perhatikan jari, gigi, telinga, dan teks pada latar. AI sering membuat huruf melengkung atau jumlah jari yang janggal.</p>
                    </div>

                    <!-- Tip 02 -->
                    <div class="tip-card">
                        <div class="tip-top">
                            <div class="tip-icon-box">📁</div>
                            <span class="tip-badge">TIP 02</span>
                        </div>
                        <h3 class="tip-title">Periksa metadata (EXIF)</h3>
                        <p class="tip-text">Foto kamera biasanya memuat model kamera, lensa, dan waktu. Metadata kosong atau aneh patut dicurigai.</p>
                    </div>

                    <!-- Tip 03 -->
                    <div class="tip-card">
                        <div class="tip-top">
                            <div class="tip-icon-box">💡</div>
                            <span class="tip-badge">TIP 03</span>
                        </div>
                        <h3 class="tip-title">Amati bayangan & cahaya</h3>
                        <p class="tip-text">Arah cahaya yang tidak konsisten atau bayangan yang hilang adalah tanda umum rekayasa.</p>
                    </div>

                    <!-- Tip 04 -->
                    <div class="tip-card">
                        <div class="tip-top">
                            <div class="tip-icon-box">🧍</div>
                            <span class="tip-badge">TIP 04</span>
                        </div>
                        <h3 class="tip-title">Anatomi & proporsi</h3>
                        <p class="tip-text">Lengan terlalu panjang, mata tidak sejajar, atau kulit terlalu mulus bisa menandakan gambar sintetis.</p>
                    </div>

                    <!-- Tip 05 -->
                    <div class="tip-card">
                        <div class="tip-top">
                            <div class="tip-icon-box">🔄</div>
                            <span class="tip-badge">TIP 05</span>
                        </div>
                        <h3 class="tip-title">Pencarian gambar terbalik</h3>
                        <p class="tip-text">Cari foto di mesin pencari gambar untuk menemukan sumber asli dan konteks pertama kali foto muncul.</p>
                    </div>

                    <!-- Tip 06 -->
                    <div class="tip-card">
                        <div class="tip-top">
                            <div class="tip-icon-box">📰</div>
                            <span class="tip-badge">TIP 06</span>
                        </div>
                        <h3 class="tip-title">Verifikasi sumber</h3>
                        <p class="tip-text">Cocokkan dengan media kredibel dan akun resmi. Tahan diri membagikan sebelum ada konfirmasi.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 5: LAPORAN -->
        <section id="tab-laporan" class="page-tab <?= $activePage === 'laporan' ? 'active-tab' : '' ?> report-section">
            <div class="container">
                <div class="page-header" style="text-align: center; max-width: 680px; margin: 0 auto 32px;">
                    <h2 class="page-title">Laporan Foto Mencurigakan</h2>
                    <p class="page-subtitle">Kirim foto yang kamu curigai untuk ditinjau tim threat intelligence RealPict.</p>
                </div>

                <div class="report-card">
                    <form id="reportForm">
                        <!-- Dropzone inside Form -->
                        <div id="reportDropzone" class="dropzone-box report-dropzone">
                            <div class="dropzone-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                            </div>
                            <div class="dropzone-title">Unggah foto mencurigakan</div>
                            <div class="dropzone-sub">
                                atau <span class="dropzone-link">pilih dari perangkat</span> · JPG, PNG, WEBP
                            </div>
                        </div>

                        <!-- Hidden File Input for Report -->
                        <input type="file" id="reportFileInput" class="hidden-input" accept="image/jpeg,image/png,image/webp">

                        <!-- Preview Container when file chosen -->
                        <div id="reportPreviewContainer" style="display: none; margin-bottom: 20px; text-align: center;">
                            <div style="position: relative; display: inline-block;">
                                <img id="reportPreviewImg" src="" style="max-height: 180px; border-radius: 12px; border: 1px solid var(--border-color);" alt="preview">
                                <button type="button" onclick="removeReportImage()" style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: #fff; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-weight: bold;">✕</button>
                            </div>
                        </div>

                        <!-- Field: Kategori -->
                        <div class="form-group">
                            <label class="form-label" for="reportKategori">Kategori</label>
                            <select id="reportKategori" class="form-select">
                                <option value="Hoaks / disinformasi" selected>Hoaks / disinformasi</option>
                                <option value="Penipuan Toko Online / E-commerce">Penipuan Toko Online / E-commerce</option>
                                <option value="Deepfake / Rekayasa Identitas">Deepfake / Rekayasa Identitas</option>
                                <option value="Konten Sensitif / Ilegal">Konten Sensitif / Ilegal</option>
                                <option value="Pencurian Foto & Hak Cipta">Pencurian Foto & Hak Cipta</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Field: Deskripsi -->
                        <div class="form-group">
                            <label class="form-label" for="reportDeskripsi">Deskripsi (opsional)</label>
                            <textarea id="reportDeskripsi" class="form-textarea" placeholder="Di mana foto ini ditemukan dan mengapa mencurigakan?"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btnSubmitReport" class="btn btn-primary btn-full">
                            Kirim Laporan
                        </button>
                    </form>
                </div>

                <!-- Recent Submitted Reports -->
                <div class="reports-history">
                    <div class="reports-history-title">
                        <span>Laporan Terkirim Baru-baru Ini</span>
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Antrean Tinjauan</span>
                    </div>
                    <div id="reportsListContainer">
                        <!-- Loaded dynamically via JavaScript -->
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 6: LATIH AI (Training Model) -->
        <section id="tab-training" class="page-tab <?= $activePage === 'training' ? 'active-tab' : '' ?>" style="padding: 48px 0 64px;">
            <div class="container">
                <div class="page-header">
                    <div class="badge-tag" style="margin-bottom: 12px;">
                        <span>MACHINE LEARNING · DATASET PELATIHAN</span>
                    </div>
                    <h2 class="page-title">Latih Model Deteksi AI</h2>
                    <p class="page-subtitle">Unggah sampel foto asli atau foto buatan AI untuk melatih sistem RealPict. Semakin banyak contoh yang dilatih, semakin akurat pendeteksi mengenali foto buatan AI maupun foto kamera asli.</p>
                </div>

                <!-- 4 Statistics Counter Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px;">
                    <div style="background: #0d0f14; border: 1px solid #1c212c; border-radius: 12px; padding: 20px;">
                        <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 6px;">TOTAL SAMPEL DILATIH</div>
                        <div id="trnStatTotal" style="font-size: 1.8rem; font-weight: 700; color: #fff;">0</div>
                    </div>
                    <div style="background: #0d0f14; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 20px;">
                        <div style="font-size: 0.8rem; color: #f87171; margin-bottom: 6px;">SAMPEL FOTO AI</div>
                        <div id="trnStatAi" style="font-size: 1.8rem; font-weight: 700; color: #f87171;">0</div>
                    </div>
                    <div style="background: #0d0f14; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 20px;">
                        <div style="font-size: 0.8rem; color: #34d399; margin-bottom: 6px;">SAMPEL FOTO ASLI</div>
                        <div id="trnStatReal" style="font-size: 1.8rem; font-weight: 700; color: #34d399;">0</div>
                    </div>
                    <div style="background: #0d0f14; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 20px;">
                        <div style="font-size: 0.8rem; color: #fbbf24; margin-bottom: 6px;">STATUS MODEL</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fbbf24; margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                            <span class="status-dot"></span> Adaptif & Aktif
                        </div>
                    </div>
                </div>

                <!-- Training Form Card -->
                <div style="background: #0d0f14; border: 1px solid #1c212c; border-radius: 16px; padding: 32px; margin-bottom: 36px;">
                    <h3 style="color: #fff; font-size: 1.2rem; margin-bottom: 8px;">Unggah Sampel Pelatihan Baru</h3>
                    <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 24px;">Pilih label untuk foto yang akan diunggah, lalu sistem akan mengekstrak sidik jari biner dan karakteristik uniknya ke dalam memori model.</p>

                    <form id="formTraining" enctype="multipart/form-data">
                        <!-- Label Selector (Radio Buttons as Cards) -->
                        <div style="margin-bottom: 24px;">
                            <label class="form-label" style="margin-bottom: 10px; display: block;">Tentukan Label Sampel Foto:</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <label style="display: block; cursor: pointer;">
                                    <input type="radio" name="trainLabel" value="ai" checked style="display: none;" id="radioLabelAi">
                                    <div id="cardLabelAi" style="padding: 18px; border-radius: 12px; border: 2px solid #ef4444; background: rgba(239, 68, 68, 0.08); text-align: center; transition: all 0.2s;">
                                        <div style="font-size: 1.8rem; margin-bottom: 6px;">🤖</div>
                                        <div style="font-weight: 700; color: #f87171; font-size: 0.95rem;">FOTO AI (Buatan Mesin)</div>
                                        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">Midjourney, Stable Diffusion, DALL-E, Bing, FaceApp, dll.</div>
                                    </div>
                                </label>
                                <label style="display: block; cursor: pointer;">
                                    <input type="radio" name="trainLabel" value="real" style="display: none;" id="radioLabelReal">
                                    <div id="cardLabelReal" style="padding: 18px; border-radius: 12px; border: 2px solid #334155; background: #0f1218; text-align: center; transition: all 0.2s;">
                                        <div style="font-size: 1.8rem; margin-bottom: 6px;">📷</div>
                                        <div style="font-weight: 700; color: #cbd5e1; font-size: 0.95rem;">FOTO ASLI (Kamera Nyata)</div>
                                        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">Foto asli kamera HP, DSLR, candid, landscape nyata.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Source notes input -->
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label" for="trainSourceNotes">Keterangan Sumber / Nama Model (Opsional)</label>
                            <input type="text" id="trainSourceNotes" class="form-input" placeholder="Misal: DALL-E 3, Midjourney v6, Kamera iPhone 14, Foto WhatsApp">
                        </div>

                        <!-- Dropzone for Training Files -->
                        <div id="trainDropzone" class="dropzone-box" style="margin-bottom: 24px; padding: 36px 20px;">
                            <div class="dropzone-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <div class="dropzone-title" id="trainDropzoneTitle">Pilih satu atau beberapa foto untuk dilatih</div>
                            <div class="dropzone-sub">
                                atau <span class="dropzone-link">jelajahi file</span> · JPG, PNG, WEBP (bisa pilih banyak sekaligus)
                            </div>
                            <input type="file" id="trainFileInput" class="hidden-input" multiple accept="image/jpeg,image/png,image/webp">
                        </div>

                        <!-- Selected files preview list -->
                        <div id="trainFilesSelectedList" style="display: none; margin-bottom: 24px; padding: 12px; background: #0f1218; border-radius: 10px; border: 1px solid #1c212c;"></div>

                        <!-- Submit Button -->
                        <button type="submit" id="btnSubmitTraining" class="btn btn-primary btn-full" style="padding: 14px; font-weight: 700;">
                            🧠 Mulai Training & Simpan ke Model
                        </button>
                    </form>
                </div>

                <!-- Trained Dataset History Table -->
                <div class="reports-history">
                    <div class="reports-history-title">
                        <span>Database Sampel yang Telah Dilatih</span>
                        <button type="button" id="btnRefreshDataset" class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.75rem;">
                            ↺ Segarkan Data
                        </button>
                    </div>
                    <div id="datasetTableContainer">
                        <div style="text-align: center; padding: 24px; color: #64748b; font-size: 0.85rem;">Memuat dataset pelatihan...</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ==========================================================================
         Global Footer matching Screenshot 4
         ========================================================================== -->
    <footer class="site-footer">
        <div class="container footer-container">
            <!-- Left: Badge & Slogan -->
            <div class="footer-left">
                <div class="footer-badge">
                    <span>◆</span>
                    <span>REALPICT</span>
                </div>
                <div class="footer-slogan">Foto Asli, Dunia Lebih Aman</div>
            </div>

            <!-- Middle: Navigation Links -->
            <ul class="footer-links">
                <li><a class="footer-link" data-tab-target="beranda">Beranda</a></li>
                <li><a class="footer-link" data-tab-target="cek-foto">Cek Foto</a></li>
                <li><a class="footer-link" data-tab-target="massal">Upload Massal</a></li>
                <li><a class="footer-link" data-tab-target="edukasi">Edukasi</a></li>
                <li><a class="footer-link" data-tab-target="laporan">Laporan</a></li>
                <li><a class="footer-link" data-tab-target="training">Latih AI</a></li>
            </ul>

            <!-- Right: Subtitle / Event -->
            <div class="footer-right">
                Hackathon Kartika 2026 · Prototipe
            </div>
        </div>
    </footer>

    <!-- Modal Detail Forensik Batch (Output langsung di Web tanpa unduh) -->
    <div id="batchDetailModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 4px; font-weight: 600;">DETAIL FORENSIK DIGITAL</div>
                    <div id="modalBatchVerdict" class="result-verdict verdict-real" style="padding: 4px 12px; font-size: 0.8rem; display: inline-block;">-</div>
                </div>
                <button type="button" id="btnModalClose" class="modal-close-btn" title="Tutup">✕</button>
            </div>
            <div class="modal-body">
                <div class="result-grid" style="margin-top: 0;">
                    <!-- Image Preview -->
                    <div class="result-preview-box">
                        <img id="modalBatchImg" src="" class="result-preview-img" alt="Preview Foto">
                        <div class="result-file-info">
                            <div id="modalBatchFileName" style="font-weight: 600; color: #fff; margin-bottom: 2px; word-break: break-all;">-</div>
                            <div style="display: flex; justify-content: space-between; color: #64748b;">
                                <span id="modalBatchDimensions">-</span>
                                <span id="modalBatchFileSize">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Metrics & EXIF -->
                    <div class="result-metrics-details">
                        <div style="display: flex; gap: 20px; align-items: center; background: #0f1218; padding: 16px; border-radius: 12px; border: 1px solid #1c212c;">
                            <div class="donut-container" style="width: 90px; height: 90px; flex-shrink: 0;">
                                <svg class="donut-svg" viewBox="0 0 120 120">
                                    <circle class="donut-bg" cx="60" cy="60" r="52"></circle>
                                    <circle id="modalBatchDonutArc" class="donut-arc" cx="60" cy="60" r="52"></circle>
                                </svg>
                                <div class="donut-center-text">
                                    <div id="modalBatchDonutPercent" class="donut-percentage" style="font-size: 1.3rem;">0%</div>
                                    <div class="donut-label" style="font-size: 0.45rem;">KEMUNGKINAN AI</div>
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: #fff; font-size: 0.9rem; margin-bottom: 4px;">Kesimpulan</div>
                                <p id="modalBatchSummary" style="font-size: 0.8rem; color: #94a3b8; line-height: 1.4; margin: 0;"></p>
                            </div>
                        </div>

                        <!-- 4 Forensic Metrics -->
                        <div class="indicator-metrics" style="margin-top: 14px;">
                            <div class="metric-row">
                                <div class="metric-info">
                                    <span class="metric-title">Pola Piksel</span>
                                    <span id="modalValPolaPiksel" class="metric-value">0%</span>
                                </div>
                                <div class="progress-track">
                                    <div id="modalBarPolaPiksel" class="progress-fill" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div class="metric-row">
                                <div class="metric-info">
                                    <span class="metric-title">Metadata</span>
                                    <span id="modalValMetadata" class="metric-value">0%</span>
                                </div>
                                <div class="progress-track">
                                    <div id="modalBarMetadata" class="progress-fill" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div class="metric-row">
                                <div class="metric-info">
                                    <span class="metric-title">Noise Pattern</span>
                                    <span id="modalValNoise" class="metric-value">0%</span>
                                </div>
                                <div class="progress-track">
                                    <div id="modalBarNoise" class="progress-fill" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div class="metric-row">
                                <div class="metric-info">
                                    <span class="metric-title">Struktur Gambar</span>
                                    <span id="modalValStruktur" class="metric-value">0%</span>
                                </div>
                                <div class="progress-track">
                                    <div id="modalBarStruktur" class="progress-fill" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- EXIF info -->
                        <div style="margin-top: 14px;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #fff; margin-bottom: 6px;">Informasi Teknis & EXIF:</div>
                            <table class="exif-table">
                                <tr>
                                    <td class="label">Kamera</td>
                                    <td id="modalExifKamera" class="val">-</td>
                                </tr>
                                <tr>
                                    <td class="label">Software</td>
                                    <td id="modalExifSoftware" class="val">-</td>
                                </tr>
                                <tr>
                                    <td class="label">Waktu Pemotretan</td>
                                    <td id="modalExifTanggal" class="val">-</td>
                                </tr>
                                <tr>
                                    <td class="label">Dimensi Asli</td>
                                    <td id="modalExifDimensi" class="val">-</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Findings -->
                        <div style="margin-top: 14px;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #fff; margin-bottom: 4px;">Catatan Forensik:</div>
                            <ul id="modalBatchFindings" style="padding-left: 18px; margin: 0;"></ul>
                        </div>

                        <!-- Quick Training inside Batch Modal -->
                        <div style="margin-top: 18px; padding: 14px; background: #0b0d12; border: 1px solid #1e293b; border-radius: 10px;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #fff; margin-bottom: 4px;">🧠 Latih Model dengan Foto Ini:</div>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 10px;">Bantu tingkatkan akurasi pendeteksi dengan menandai status foto ini:</div>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" id="btnModalTrainAi" class="btn" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.35); padding: 6px 12px; font-size: 0.75rem; font-weight: 600; cursor: pointer; border-radius: 6px;">
                                    🤖 Latih: Ini Foto AI
                                </button>
                                <button type="button" id="btnModalTrainReal" class="btn" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); padding: 6px 12px; font-size: 0.75rem; font-weight: 600; cursor: pointer; border-radius: 6px;">
                                    📷 Latih: Ini Foto Asli
                                </button>
                            </div>
                            <div id="modalTrainNotice" style="display: none; margin-top: 8px; font-size: 0.75rem; padding: 6px 10px; border-radius: 6px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- App JavaScript -->
    <script src="assets/js/app.js"></script>
</body>
</html>

