/**
 * RealPict - Forensik Foto Digital & Deteksi AI
 * Client-side Controller & Interactive Engine
 */

document.addEventListener('DOMContentLoaded', () => {
    // ---------------------------------------------------------
    // 1. Navigation & Tab Routing System
    // ---------------------------------------------------------
    const navLinks = document.querySelectorAll('.nav-link, .footer-link, [data-tab-target]');
    const pageTabs = document.querySelectorAll('.page-tab');

    function switchTab(tabId) {
        // Normalize tab id
        const targetId = tabId.replace('#', '');
        const targetElement = document.getElementById(`tab-${targetId}`);

        if (!targetElement) return;

        // Hide all tabs
        pageTabs.forEach(tab => {
            tab.classList.remove('active-tab');
        });

        // Show target tab
        targetElement.classList.add('active-tab');

        // Update nav pills
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('data-tab') === targetId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // Update URL hash without jumping
        if (history.pushState) {
            history.pushState(null, null, `#${targetId}`);
        } else {
            location.hash = `#${targetId}`;
        }

        // Scroll to top of content smoothly
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Attach click listeners to all tab links
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const tab = link.getAttribute('data-tab') || link.getAttribute('data-tab-target');
            if (tab) {
                switchTab(tab);
            }
        });
    });

    // Check initial hash on load
    const initialHash = window.location.hash.replace('#', '');
    if (initialHash && document.getElementById(`tab-${initialHash}`)) {
        switchTab(initialHash);
    }

    window.addEventListener('popstate', () => {
        const currentHash = window.location.hash.replace('#', '') || 'beranda';
        if (document.getElementById(`tab-${currentHash}`)) {
            switchTab(currentHash);
        }
    });

    // ---------------------------------------------------------
    // 2. SVG Donut Chart Animator Helper
    // ---------------------------------------------------------
    function updateDonutChart(svgArc, textPercent, percent, isAiVerdict = false) {
        const circumference = 2 * Math.PI * 52; // r=52 -> ~326.72
        const offset = circumference - (percent / 100) * circumference;

        svgArc.style.strokeDasharray = `${circumference} ${circumference}`;
        svgArc.style.strokeDashoffset = offset;

        // Color coding
        if (percent > 65) {
            svgArc.style.stroke = '#ef4444'; // Red
        } else if (percent > 30) {
            svgArc.style.stroke = '#f59e0b'; // Amber/Yellow
        } else {
            svgArc.style.stroke = '#86efac'; // Emerald/Green
        }

        // Animate counter
        let start = 0;
        const duration = 600;
        const stepTime = 15;
        const totalSteps = duration / stepTime;
        const increment = percent / totalSteps;

        const counter = setInterval(() => {
            start += increment;
            if (start >= percent) {
                textPercent.textContent = `${percent}%`;
                clearInterval(counter);
            } else {
                textPercent.textContent = `${Math.round(start)}%`;
            }
        }, stepTime);
    }

    // Initialize Hero Donut
    const heroArc = document.getElementById('heroDonutArc');
    const heroPercentText = document.getElementById('heroDonutPercent');
    if (heroArc && heroPercentText) {
        updateDonutChart(heroArc, heroPercentText, 12);
    }

    // ---------------------------------------------------------
    // 3. Cek Foto (Single Check) Logic
    // ---------------------------------------------------------
    let currentSingleData = null;
    let currentModalBatchItem = null;
    const singleDropzone = document.getElementById('singleDropzone');
    const singleFileInput = document.getElementById('singleFileInput');
    const singleScanOverlay = document.getElementById('singleScanOverlay');
    const singleResultCard = document.getElementById('singleResultCard');
    const scanStatusText = document.getElementById('scanStatusText');
    const scanStepText = document.getElementById('scanStepText');

    if (singleDropzone && singleFileInput) {
        singleDropzone.addEventListener('click', () => singleFileInput.click());

        singleDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            singleDropzone.classList.add('dragover');
        });

        singleDropzone.addEventListener('dragleave', () => {
            singleDropzone.classList.remove('dragover');
        });

        singleDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            singleDropzone.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                handleSingleFile(e.dataTransfer.files[0]);
            }
        });

        singleFileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                handleSingleFile(e.target.files[0]);
            }
        });
    }

    const scanSteps = [
        "Membaca struktur biner file...",
        "Menganalisis pola distribusi piksel & histogram...",
        "Memeriksa keaslian metadata EXIF & header kamera...",
        "Memeriksa artefak kompresi & noise optik sensor...",
        "Mengalkulasi skor probabilitas AI vs RealPic..."
    ];

    function handleSingleFile(file) {
        // Validate type
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showToast('Format file tidak didukung. Harap pilih JPG, PNG, atau WEBP.', 'error');
            return;
        }

        // Show scanning state
        singleDropzone.style.display = 'none';
        singleResultCard.style.display = 'none';
        singleScanOverlay.style.display = 'block';

        let stepIndex = 0;
        scanStatusText.textContent = `Memindai "${file.name}"...`;
        scanStepText.textContent = scanSteps[0];

        const stepInterval = setInterval(() => {
            stepIndex++;
            if (stepIndex < scanSteps.length) {
                scanStepText.textContent = scanSteps[stepIndex];
            } else {
                clearInterval(stepInterval);
            }
        }, 320);

        // Upload and analyze via PHP backend
        const formData = new FormData();
        formData.append('image', file);

        // Create local object URL for preview
        const previewUrl = URL.createObjectURL(file);

        fetch('api/analyze.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                setTimeout(() => {
                    clearInterval(stepInterval);
                    singleScanOverlay.style.display = 'none';

                    if (data.status === 'success') {
                        displaySingleResult(data, previewUrl);
                    } else {
                        singleDropzone.style.display = 'flex';
                        showToast(data.message || 'Gagal menganalisis file.', 'error');
                    }
                }, 1200);
            })
            .catch(err => {
                clearInterval(stepInterval);
                singleScanOverlay.style.display = 'none';
                singleDropzone.style.display = 'flex';
                showToast('Terjadi gangguan jaringan saat memproses gambar.', 'error');
            });
    }

    function displaySingleResult(data, previewUrl) {
        currentSingleData = data;
        const quickTrainNotice = document.getElementById('quickTrainNotice');
        if (quickTrainNotice) quickTrainNotice.style.display = 'none';

        singleResultCard.style.display = 'block';
        singleDropzone.style.display = 'none';

        // Set Preview
        const previewImg = document.getElementById('singlePreviewImg');
        if (previewImg) previewImg.src = previewUrl;

        // Set basic file info
        document.getElementById('resultFileName').textContent = data.file_name;
        document.getElementById('resultDimensions').textContent = data.dimensions;
        document.getElementById('resultFileSize').textContent = data.file_size_formatted;

        // Set Verdict Badge
        const verdictBadge = document.getElementById('resultVerdictBadge');
        verdictBadge.textContent = data.status_label;
        verdictBadge.className = 'result-verdict ' + (
            data.status_type === 'real' ? 'verdict-real' :
                data.status_type === 'ai' || data.status_type === 'suspect' ? 'verdict-ai' : 'verdict-edited'
        );

        // Set Donut
        const resultArc = document.getElementById('resultDonutArc');
        const resultPercent = document.getElementById('resultDonutPercent');
        updateDonutChart(resultArc, resultPercent, data.ai_percentage);

        // Set Summary
        document.getElementById('resultSummaryText').textContent = data.summary;

        // Set Progress Bars
        updateBar('barPolaPiksel', 'valPolaPiksel', data.metrics.pola_piksel);
        updateBar('barMetadata', 'valMetadata', data.metrics.metadata);
        updateBar('barNoise', 'valNoise', data.metrics.noise_pattern);
        updateBar('barStruktur', 'valStruktur', data.metrics.struktur_gambar);

        // Set EXIF Table
        document.getElementById('exifKamera').textContent = data.camera;
        document.getElementById('exifSoftware').textContent = data.software;
        document.getElementById('exifTanggal').textContent = data.date_taken;
        document.getElementById('exifResolusi').textContent = data.dimensions;

        // Findings list
        const findingsList = document.getElementById('resultFindingsList');
        if (findingsList) {
            findingsList.innerHTML = '';
            if (data.findings && data.findings.length > 0) {
                data.findings.forEach(f => {
                    const li = document.createElement('li');
                    li.textContent = f;
                    li.style.color = '#cbd5e1';
                    li.style.fontSize = '0.85rem';
                    li.style.marginBottom = '4px';
                    findingsList.appendChild(li);
                });
            }
        }
    }

    function updateBar(barId, valId, percent) {
        const bar = document.getElementById(barId);
        const val = document.getElementById(valId);
        if (bar && val) {
            bar.style.width = `${percent}%`;
            val.textContent = `${percent}%`;

            if (percent > 65) {
                bar.style.backgroundColor = '#ef4444';
            } else if (percent > 30) {
                bar.style.backgroundColor = '#f59e0b';
            } else {
                bar.style.backgroundColor = '#86efac';
            }
        }
    }

    // Reset button for Cek Foto
    const btnResetSingle = document.getElementById('btnResetSingle');
    if (btnResetSingle) {
        btnResetSingle.addEventListener('click', () => {
            singleResultCard.style.display = 'none';
            singleDropzone.style.display = 'flex';
            if (singleFileInput) singleFileInput.value = '';
        });
    }

    // ---------------------------------------------------------
    // 4. Upload Massal (Batch Audit - Output Langsung di Web)
    // ---------------------------------------------------------
    const massalDropzone = document.getElementById('massalDropzone');
    const massalFileInput = document.getElementById('massalFileInput');
    const batchContainer = document.getElementById('batchContainer');
    const batchProgressBar = document.getElementById('batchProgressInner');
    const batchProgressText = document.getElementById('batchProgressText');
    const batchTableBody = document.getElementById('batchTableBody');
    const batchSummarySection = document.getElementById('batchSummarySection');
    const btnResetBatch = document.getElementById('btnResetBatch');

    // Batch Detail Modal Elements
    const batchDetailModal = document.getElementById('batchDetailModal');
    const btnModalClose = document.getElementById('btnModalClose');
    const modalBatchImg = document.getElementById('modalBatchImg');
    const modalBatchFileName = document.getElementById('modalBatchFileName');
    const modalBatchDimensions = document.getElementById('modalBatchDimensions');
    const modalBatchFileSize = document.getElementById('modalBatchFileSize');
    const modalBatchVerdict = document.getElementById('modalBatchVerdict');
    const modalBatchDonutArc = document.getElementById('modalBatchDonutArc');
    const modalBatchDonutPercent = document.getElementById('modalBatchDonutPercent');
    const modalBatchSummary = document.getElementById('modalBatchSummary');
    const modalBatchFindings = document.getElementById('modalBatchFindings');

    let batchItems = [];

    if (massalDropzone && massalFileInput) {
        massalDropzone.addEventListener('click', () => massalFileInput.click());

        massalDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            massalDropzone.classList.add('dragover');
        });

        massalDropzone.addEventListener('dragleave', () => {
            massalDropzone.classList.remove('dragover');
        });

        massalDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            massalDropzone.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                handleBatchFiles(Array.from(e.dataTransfer.files));
            }
        });

        massalFileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                handleBatchFiles(Array.from(e.target.files));
            }
        });
    }

    async function handleBatchFiles(files) {
        const imageFiles = files.filter(f => ['image/jpeg', 'image/png', 'image/webp'].includes(f.type));

        if (imageFiles.length === 0) {
            showToast('Tidak ada file gambar valid (JPG, PNG, WEBP) yang dipilih.', 'error');
            return;
        }

        // Setup UI
        massalDropzone.style.display = 'none';
        batchContainer.style.display = 'block';
        batchTableBody.innerHTML = '';
        if (batchSummarySection) batchSummarySection.style.display = 'none';
        if (btnResetBatch) btnResetBatch.style.display = 'none';
        batchItems = [];

        const total = imageFiles.length;
        let completed = 0;

        batchProgressText.textContent = `Memproses 0 dari ${total} file...`;
        batchProgressBar.style.width = '0%';

        for (let i = 0; i < total; i++) {
            const file = imageFiles[i];
            const previewUrl = URL.createObjectURL(file);

            // Add skeleton row
            const rowId = `batch-row-${i}`;
            const row = document.createElement('tr');
            row.id = rowId;
            row.className = 'batch-row';
            row.innerHTML = `
                <td style="width: 60px;">
                    <img src="${previewUrl}" class="batch-thumb" alt="preview">
                </td>
                <td>
                    <div style="font-weight: 600; color: #fff;">${file.name}</div>
                    <div style="font-size: 0.75rem; color: #64748b;">${(file.size / 1024).toFixed(1)} KB</div>
                </td>
                <td style="width: 220px;">
                    <span style="font-size: 0.8rem; color: #94a3b8;">Sedang menganalisis forensik...</span>
                </td>
                <td style="width: 140px; text-align: center;">
                    <div class="status-dot" style="display: inline-block;"></div>
                </td>
                <td style="width: 130px; text-align: right;">
                    <span style="font-size: 0.75rem; color: #64748b;">Memproses...</span>
                </td>
            `;
            batchTableBody.appendChild(row);

            // Send to analyze.php
            const formData = new FormData();
            formData.append('image', file);

            try {
                const res = await fetch('api/analyze.php', { method: 'POST', body: formData });
                if (!res.ok) {
                    throw new Error(`Server error (${res.status})`);
                }
                const data = await res.json();

                completed++;
                const percentDone = Math.round((completed / total) * 100);
                batchProgressBar.style.width = `${percentDone}%`;
                batchProgressText.textContent = `Selesai ${completed} dari ${total} foto (${percentDone}%)`;

                if (data.status === 'success') {
                    const itemIndex = batchItems.length;
                    batchItems.push({ data: data, previewUrl: previewUrl });
                    updateBatchRow(rowId, data, itemIndex);
                } else {
                    document.getElementById(rowId).innerHTML = `
                        <td><img src="${previewUrl}" class="batch-thumb" alt="preview"></td>
                        <td><div style="color: #fff;">${file.name}</div></td>
                        <td colspan="3" style="color: #ef4444; font-size: 0.8rem;">${data.message || 'Gagal menganalisis file'}</td>
                    `;
                }
            } catch (err) {
                completed++;
                const percentDone = Math.round((completed / total) * 100);
                batchProgressBar.style.width = `${percentDone}%`;
                batchProgressText.textContent = `Selesai ${completed} dari ${total} foto (${percentDone}%)`;
                const rowEl = document.getElementById(rowId);
                if (rowEl) {
                    rowEl.innerHTML = `
                        <td><img src="${previewUrl}" class="batch-thumb" alt="preview"></td>
                        <td><div style="color: #fff;">${file.name}</div></td>
                        <td colspan="3" style="color: #ef4444; font-size: 0.8rem;">Gagal menganalisis file (${err.message || 'Kesalahan jaringan'})</td>
                    `;
                }
            }
        }

        // Summary calculations
        let aiCount = 0;
        let realCount = 0;
        let suspectCount = 0;

        batchItems.forEach(item => {
            if (item.data.status_type === 'real') {
                realCount++;
            } else if (item.data.ai_percentage > 50 || item.data.status_type === 'ai') {
                aiCount++;
            } else {
                suspectCount++;
            }
        });

        if (batchSummarySection) {
            document.getElementById('batchStatTotal').textContent = batchItems.length;
            document.getElementById('batchStatAi').textContent = aiCount;
            document.getElementById('batchStatReal').textContent = realCount;
            document.getElementById('batchStatSuspect').textContent = suspectCount;
            batchSummarySection.style.display = 'grid';
        }

        if (btnResetBatch) {
            btnResetBatch.style.display = 'inline-block';
        }

        showToast(`Audit massal ${completed} foto selesai! Hasil dapat dilihat langsung di bawah.`, 'success');
    }

    function updateBatchRow(rowId, data, index) {
        const row = document.getElementById(rowId);
        if (!row) return;

        const isAi = data.ai_percentage > 50;
        const badgeColor = data.status_type === 'real' ? '#34d399' : (isAi ? '#f87171' : '#fbbf24');
        const badgeBg = data.status_type === 'real' ? 'rgba(16, 185, 129, 0.15)' : (isAi ? 'rgba(239, 68, 68, 0.15)' : 'rgba(245, 158, 11, 0.15)');

        row.innerHTML = `
            <td style="width: 60px;">
                <img src="${row.querySelector('img').src}" class="batch-thumb" alt="preview" style="cursor: pointer;" onclick="window.viewBatchDetail(${index})">
            </td>
            <td>
                <div style="font-weight: 600; color: #fff;">${data.file_name}</div>
                <div style="font-size: 0.75rem; color: #64748b;">${data.dimensions} · ${data.file_size_formatted}</div>
            </td>
            <td style="width: 220px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 4px;">
                    <span style="color: #94a3b8;">Probabilitas AI</span>
                    <span style="color: #fff; font-weight: 600;">${data.ai_percentage}%</span>
                </div>
                <div class="progress-track" style="height: 6px;">
                    <div class="progress-fill" style="width: ${data.ai_percentage}%; background-color: ${badgeColor};"></div>
                </div>
            </td>
            <td style="width: 140px; text-align: center;">
                <span style="background: ${badgeBg}; color: ${badgeColor}; border: 1px solid ${badgeColor}40; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-block;">
                    ${data.status_label.split(' ')[0]}
                </span>
            </td>
            <td style="width: 130px; text-align: right;">
                <button type="button" class="btn btn-secondary" style="padding: 5px 12px; font-size: 0.75rem;" onclick="window.viewBatchDetail(${index})">
                    🔍 Detail
                </button>
            </td>
        `;
    }

    // Modal popup logic for batch detail view
    window.viewBatchDetail = function(index) {
        const item = batchItems[index];
        if (!item || !batchDetailModal) return;
        currentModalBatchItem = item;
        const modalTrainNotice = document.getElementById('modalTrainNotice');
        if (modalTrainNotice) modalTrainNotice.style.display = 'none';

        const data = item.data;

        // Preview Image & Info
        if (modalBatchImg) modalBatchImg.src = item.previewUrl;
        if (modalBatchFileName) modalBatchFileName.textContent = data.file_name;
        if (modalBatchDimensions) modalBatchDimensions.textContent = data.dimensions;
        if (modalBatchFileSize) modalBatchFileSize.textContent = data.file_size_formatted;

        // Verdict Badge
        if (modalBatchVerdict) {
            modalBatchVerdict.textContent = data.status_label;
            modalBatchVerdict.className = 'result-verdict ' + (
                data.status_type === 'real' ? 'verdict-real' :
                    data.status_type === 'ai' || data.status_type === 'suspect' ? 'verdict-ai' : 'verdict-edited'
            );
        }

        // Donut Chart
        if (modalBatchDonutArc && modalBatchDonutPercent) {
            updateDonutChart(modalBatchDonutArc, modalBatchDonutPercent, data.ai_percentage);
        }

        // Summary
        if (modalBatchSummary) modalBatchSummary.textContent = data.summary;

        // 4 Indicators
        updateBar('modalBarPolaPiksel', 'modalValPolaPiksel', data.metrics.pola_piksel);
        updateBar('modalBarMetadata', 'modalValMetadata', data.metrics.metadata);
        updateBar('modalBarNoise', 'modalValNoise', data.metrics.noise_pattern);
        updateBar('modalBarStruktur', 'modalValStruktur', data.metrics.struktur_gambar);

        // Technical EXIF
        const elKamera = document.getElementById('modalExifKamera');
        const elSoftware = document.getElementById('modalExifSoftware');
        const elTanggal = document.getElementById('modalExifTanggal');
        const elDimensi = document.getElementById('modalExifDimensi');

        if (elKamera) elKamera.textContent = data.camera || 'Tidak ada data EXIF';
        if (elSoftware) elSoftware.textContent = data.software || 'Tidak terdeteksi';
        if (elTanggal) elTanggal.textContent = data.date_taken || '-';
        if (elDimensi) elDimensi.textContent = data.dimensions || '-';

        // Findings
        if (modalBatchFindings) {
            modalBatchFindings.innerHTML = '';
            if (data.findings && data.findings.length > 0) {
                data.findings.forEach(f => {
                    const li = document.createElement('li');
                    li.textContent = f;
                    li.style.color = '#cbd5e1';
                    li.style.fontSize = '0.8rem';
                    li.style.marginBottom = '4px';
                    modalBatchFindings.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'Tidak ditemukan anomali signifikan pada struktur file.';
                li.style.color = '#94a3b8';
                li.style.fontSize = '0.8rem';
                modalBatchFindings.appendChild(li);
            }
        }

        // Show modal
        batchDetailModal.classList.add('active');
    };

    function closeBatchModal() {
        if (batchDetailModal) {
            batchDetailModal.classList.remove('active');
        }
    }

    if (btnModalClose) {
        btnModalClose.addEventListener('click', closeBatchModal);
    }

    if (batchDetailModal) {
        batchDetailModal.addEventListener('click', (e) => {
            if (e.target === batchDetailModal) {
                closeBatchModal();
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && batchDetailModal && batchDetailModal.classList.contains('active')) {
            closeBatchModal();
        }
    });

    // Reset Batch Button
    if (btnResetBatch) {
        btnResetBatch.addEventListener('click', () => {
            batchContainer.style.display = 'none';
            massalDropzone.style.display = 'flex';
            if (massalFileInput) massalFileInput.value = '';
            batchItems = [];
            batchTableBody.innerHTML = '';
            if (batchSummarySection) batchSummarySection.style.display = 'none';
            btnResetBatch.style.display = 'none';
        });
    }

    // ---------------------------------------------------------
    // 5. Laporan Form Logic
    // ---------------------------------------------------------
    const reportDropzone = document.getElementById('reportDropzone');
    const reportFileInput = document.getElementById('reportFileInput');
    const reportPreviewContainer = document.getElementById('reportPreviewContainer');
    const reportPreviewImg = document.getElementById('reportPreviewImg');
    const reportForm = document.getElementById('reportForm');
    const btnSubmitReport = document.getElementById('btnSubmitReport');
    const reportsListContainer = document.getElementById('reportsListContainer');

    let currentReportFile = null;

    if (reportDropzone && reportFileInput) {
        reportDropzone.addEventListener('click', () => reportFileInput.click());

        reportDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            reportDropzone.classList.add('dragover');
        });

        reportDropzone.addEventListener('dragleave', () => {
            reportDropzone.classList.remove('dragover');
        });

        reportDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            reportDropzone.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                setReportImage(e.dataTransfer.files[0]);
            }
        });

        reportFileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                setReportImage(e.target.files[0]);
            }
        });
    }

    function setReportImage(file) {
        currentReportFile = file;
        reportPreviewImg.src = URL.createObjectURL(file);
        reportDropzone.style.display = 'none';
        reportPreviewContainer.style.display = 'block';
    }

    window.removeReportImage = function () {
        currentReportFile = null;
        if (reportFileInput) reportFileInput.value = '';
        reportDropzone.style.display = 'flex';
        reportPreviewContainer.style.display = 'none';
    };

    if (reportForm) {
        reportForm.addEventListener('submit', (e) => {
            e.preventDefault();

            btnSubmitReport.disabled = true;
            btnSubmitReport.innerHTML = 'Mengirim Laporan...';

            const formData = new FormData();
            if (currentReportFile) {
                formData.append('image', currentReportFile);
            }
            formData.append('kategori', document.getElementById('reportKategori').value);
            formData.append('deskripsi', document.getElementById('reportDeskripsi').value);

            fetch('api/report.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    btnSubmitReport.disabled = false;
                    btnSubmitReport.innerHTML = 'Kirim Laporan';

                    if (data.status === 'success') {
                        showToast('Laporan Anda berhasil diterima oleh tim forensik RealPict.', 'success');
                        reportForm.reset();
                        removeReportImage();
                        loadRecentReports();
                    } else {
                        showToast('Gagal mengirim laporan. Silakan coba kembali.', 'error');
                    }
                })
                .catch(() => {
                    btnSubmitReport.disabled = false;
                    btnSubmitReport.innerHTML = 'Kirim Laporan';
                    showToast('Terjadi kesalahan koneksi saat mengirim laporan.', 'error');
                });
        });
    }

    function loadRecentReports() {
        if (!reportsListContainer) return;

        fetch('api/report.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.data) {
                    if (data.data.length === 0) {
                        reportsListContainer.innerHTML = '<div style="color: #64748b; font-size: 0.85rem; text-align: center; padding: 20px;">Belum ada laporan foto yang masuk.</div>';
                        return;
                    }

                    reportsListContainer.innerHTML = '';
                    data.data.slice(0, 5).forEach(rep => {
                        const item = document.createElement('div');
                        item.className = 'history-card-item';
                        item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 12px;">
                            ${rep.image ? `<img src="${rep.image}" style="width: 42px; height: 42px; border-radius: 8px; object-fit: cover;" alt="thumb">` : `<div style="width: 42px; height: 42px; background: #1c212a; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">📷</div>`}
                            <div>
                                <div style="font-weight: 600; color: #fff; font-size: 0.9rem;">${rep.kategori}</div>
                                <div style="color: #64748b; font-size: 0.775rem;">${rep.id} · ${rep.date_formatted}</div>
                            </div>
                        </div>
                        <span style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                            ${rep.status}
                        </span>
                    `;
                        reportsListContainer.appendChild(item);
                    });
                }
            })
            .catch(() => { });
    }

    // Load initial reports on startup
    loadRecentReports();

    // ---------------------------------------------------------
    // 6. Quick Training Controller (from Cek Foto & Batch Modal)
    // ---------------------------------------------------------
    function quickTrainSingle(label) {
        if (!currentSingleData) return;
        const noticeEl = document.getElementById('quickTrainNotice');
        if (!noticeEl) return;

        noticeEl.style.display = 'block';
        noticeEl.style.background = 'rgba(59, 130, 246, 0.15)';
        noticeEl.style.color = '#60a5fa';
        noticeEl.style.border = '1px solid rgba(59, 130, 246, 0.3)';
        noticeEl.innerHTML = 'Menyimpan karakteristik foto ke dataset pelatihan model...';

        const formData = new FormData();
        formData.append('label', label);
        formData.append('file_name', currentSingleData.file_name);
        if (currentSingleData.temp_file_path) {
            formData.append('file_path', currentSingleData.temp_file_path);
        }
        formData.append('source_notes', 'Pelatihan Cepat dari Cek Foto (' + (label === 'ai' ? 'Foto AI' : 'Foto Asli') + ')');

        fetch('api/train.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    noticeEl.style.background = label === 'ai' ? 'rgba(239, 68, 68, 0.15)' : 'rgba(16, 185, 129, 0.15)';
                    noticeEl.style.color = label === 'ai' ? '#f87171' : '#34d399';
                    noticeEl.style.border = label === 'ai' ? '1px solid rgba(239, 68, 68, 0.35)' : '1px solid rgba(16, 185, 129, 0.35)';
                    noticeEl.innerHTML = `✓ Berhasil! Model telah dilatih bahwa foto ini adalah <strong>${label === 'ai' ? 'FOTO AI' : 'FOTO ASLI'}</strong>. Karakteristik disimpan ke memori deteksi!`;
                    showToast(`Foto berhasil dilatih sebagai ${label === 'ai' ? 'FOTO AI' : 'FOTO ASLI'}!`, 'success');
                    loadTrainingDataset();
                } else {
                    noticeEl.style.color = '#ef4444';
                    noticeEl.textContent = res.message || 'Gagal menyimpan pelatihan.';
                }
            })
            .catch(() => {
                noticeEl.style.color = '#ef4444';
                noticeEl.textContent = 'Terjadi kesalahan saat menghubungkan ke server pelatihan.';
            });
    }

    const btnQuickTrainAi = document.getElementById('btnQuickTrainAi');
    const btnQuickTrainReal = document.getElementById('btnQuickTrainReal');
    if (btnQuickTrainAi) btnQuickTrainAi.addEventListener('click', () => quickTrainSingle('ai'));
    if (btnQuickTrainReal) btnQuickTrainReal.addEventListener('click', () => quickTrainSingle('real'));

    function quickTrainModal(label) {
        if (!currentModalBatchItem) return;
        const noticeEl = document.getElementById('modalTrainNotice');
        if (!noticeEl) return;

        noticeEl.style.display = 'block';
        noticeEl.style.background = 'rgba(59, 130, 246, 0.15)';
        noticeEl.style.color = '#60a5fa';
        noticeEl.style.border = '1px solid rgba(59, 130, 246, 0.3)';
        noticeEl.textContent = 'Menyimpan karakteristik ke model...';

        const formData = new FormData();
        formData.append('label', label);
        formData.append('file_name', currentModalBatchItem.data.file_name);
        if (currentModalBatchItem.data.temp_file_path) {
            formData.append('file_path', currentModalBatchItem.data.temp_file_path);
        }
        formData.append('source_notes', 'Pelatihan Cepat dari Batch Audit (' + (label === 'ai' ? 'Foto AI' : 'Foto Asli') + ')');

        fetch('api/train.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    noticeEl.style.background = label === 'ai' ? 'rgba(239, 68, 68, 0.15)' : 'rgba(16, 185, 129, 0.15)';
                    noticeEl.style.color = label === 'ai' ? '#f87171' : '#34d399';
                    noticeEl.style.border = label === 'ai' ? '1px solid rgba(239, 68, 68, 0.35)' : '1px solid rgba(16, 185, 129, 0.35)';
                    noticeEl.innerHTML = `✓ Berhasil! Model mengingat foto ini sebagai <strong>${label === 'ai' ? 'FOTO AI' : 'FOTO ASLI'}</strong>.`;
                    showToast(`Foto berhasil dilatih sebagai ${label === 'ai' ? 'FOTO AI' : 'FOTO ASLI'}!`, 'success');
                    loadTrainingDataset();
                } else {
                    noticeEl.style.color = '#ef4444';
                    noticeEl.textContent = res.message || 'Gagal menyimpan pelatihan.';
                }
            })
            .catch(() => {
                noticeEl.style.color = '#ef4444';
                noticeEl.textContent = 'Terjadi kesalahan jaringan saat menyimpan pelatihan.';
            });
    }

    const btnModalTrainAi = document.getElementById('btnModalTrainAi');
    const btnModalTrainReal = document.getElementById('btnModalTrainReal');
    if (btnModalTrainAi) btnModalTrainAi.addEventListener('click', () => quickTrainModal('ai'));
    if (btnModalTrainReal) btnModalTrainReal.addEventListener('click', () => quickTrainModal('real'));

    // ---------------------------------------------------------
    // 7. Training AI Page Controller
    // ---------------------------------------------------------
    const radioLabelAi = document.getElementById('radioLabelAi');
    const radioLabelReal = document.getElementById('radioLabelReal');
    const cardLabelAi = document.getElementById('cardLabelAi');
    const cardLabelReal = document.getElementById('cardLabelReal');
    const trainDropzone = document.getElementById('trainDropzone');
    const trainFileInput = document.getElementById('trainFileInput');
    const trainFilesSelectedList = document.getElementById('trainFilesSelectedList');
    const trainDropzoneTitle = document.getElementById('trainDropzoneTitle');
    const formTraining = document.getElementById('formTraining');
    const btnSubmitTraining = document.getElementById('btnSubmitTraining');
    const btnRefreshDataset = document.getElementById('btnRefreshDataset');
    const datasetTableContainer = document.getElementById('datasetTableContainer');

    // Label toggle card styling
    if (radioLabelAi && radioLabelReal && cardLabelAi && cardLabelReal) {
        radioLabelAi.addEventListener('change', () => {
            if (radioLabelAi.checked) {
                cardLabelAi.style.border = '2px solid #ef4444';
                cardLabelAi.style.background = 'rgba(239, 68, 68, 0.08)';
                cardLabelReal.style.border = '2px solid #334155';
                cardLabelReal.style.background = '#0f1218';
            }
        });

        radioLabelReal.addEventListener('change', () => {
            if (radioLabelReal.checked) {
                cardLabelReal.style.border = '2px solid #10b981';
                cardLabelReal.style.background = 'rgba(16, 185, 129, 0.08)';
                cardLabelAi.style.border = '2px solid #334155';
                cardLabelAi.style.background = '#0f1218';
            }
        });
    }

    // Training file picker and drag-drop
    let trainingSelectedFiles = [];

    if (trainDropzone && trainFileInput) {
        trainDropzone.addEventListener('click', () => trainFileInput.click());

        trainDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            trainDropzone.classList.add('dragover');
        });

        trainDropzone.addEventListener('dragleave', () => {
            trainDropzone.classList.remove('dragover');
        });

        trainDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            trainDropzone.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                handleTrainingFilesSelected(Array.from(e.dataTransfer.files));
            }
        });

        trainFileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                handleTrainingFilesSelected(Array.from(e.target.files));
            }
        });
    }

    function handleTrainingFilesSelected(files) {
        const validFiles = files.filter(f => ['image/jpeg', 'image/png', 'image/webp'].includes(f.type));
        if (validFiles.length === 0) {
            showToast('Pilih file gambar berformat JPG, PNG, atau WEBP.', 'error');
            return;
        }

        trainingSelectedFiles = validFiles;
        renderTrainingFilesList();
    }

    function renderTrainingFilesList() {
        if (!trainFilesSelectedList) return;

        if (trainingSelectedFiles.length === 0) {
            trainFilesSelectedList.style.display = 'none';
            trainFilesSelectedList.innerHTML = '';
            if (trainDropzoneTitle) trainDropzoneTitle.textContent = 'Pilih satu atau beberapa foto untuk dilatih';
            return;
        }

        trainFilesSelectedList.style.display = 'block';
        if (trainDropzoneTitle) trainDropzoneTitle.textContent = `${trainingSelectedFiles.length} foto siap dilatih`;

        let html = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 0.8rem; font-weight: 700; color: #fff;">${trainingSelectedFiles.length} File Dipilih:</span>
                <button type="button" onclick="window.clearTrainingFiles()" style="background: none; border: none; color: #ef4444; font-size: 0.75rem; cursor: pointer; text-decoration: underline;">Kosongkan</button>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap; max-height: 120px; overflow-y: auto;">
        `;

        trainingSelectedFiles.forEach((file, idx) => {
            html += `
                <span style="background: #1c212c; color: #cbd5e1; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
                    📷 ${file.name} (${(file.size / 1024).toFixed(0)} KB)
                </span>
            `;
        });

        html += `</div>`;
        trainFilesSelectedList.innerHTML = html;
    }

    window.clearTrainingFiles = function() {
        trainingSelectedFiles = [];
        if (trainFileInput) trainFileInput.value = '';
        renderTrainingFilesList();
    };

    // Submit Training Form
    if (formTraining) {
        formTraining.addEventListener('submit', (e) => {
            e.preventDefault();

            if (trainingSelectedFiles.length === 0) {
                showToast('Pilih setidaknya satu file gambar untuk dilatih.', 'error');
                return;
            }

            const label = radioLabelReal && radioLabelReal.checked ? 'real' : 'ai';
            const notes = document.getElementById('trainSourceNotes') ? document.getElementById('trainSourceNotes').value.trim() : '';

            const formData = new FormData();
            formData.append('label', label);
            formData.append('source_notes', notes);

            trainingSelectedFiles.forEach(file => {
                formData.append('images[]', file);
            });

            btnSubmitTraining.disabled = true;
            btnSubmitTraining.innerHTML = '🧠 Sedang Mengekstrak Pola & Melatih Model...';

            fetch('api/train.php', {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    btnSubmitTraining.disabled = false;
                    btnSubmitTraining.innerHTML = '🧠 Mulai Training & Simpan ke Model';

                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        window.clearTrainingFiles();
                        if (document.getElementById('trainSourceNotes')) {
                            document.getElementById('trainSourceNotes').value = '';
                        }
                        loadTrainingDataset();
                    } else {
                        showToast(data.message || 'Gagal melatih foto.', 'error');
                    }
                })
                .catch(() => {
                    btnSubmitTraining.disabled = false;
                    btnSubmitTraining.innerHTML = '🧠 Mulai Training & Simpan ke Model';
                    showToast('Terjadi kesalahan jaringan saat mengirim data pelatihan.', 'error');
                });
        });
    }

    // Load dataset and stats
    function loadTrainingDataset() {
        if (!datasetTableContainer) return;

        fetch('api/train.php')
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    // Update stats
                    const statTotal = document.getElementById('trnStatTotal');
                    const statAi = document.getElementById('trnStatAi');
                    const statReal = document.getElementById('trnStatReal');

                    if (statTotal) statTotal.textContent = res.stats.total;
                    if (statAi) statAi.textContent = res.stats.ai_count;
                    if (statReal) statReal.textContent = res.stats.real_count;

                    // Render table
                    if (res.data.length === 0) {
                        datasetTableContainer.innerHTML = `
                            <div style="text-align: center; padding: 32px; color: #64748b; font-size: 0.85rem;">
                                Belum ada sampel foto yang dilatih. Unggah foto di formulir atas untuk memulai melatih model.
                            </div>
                        `;
                        return;
                    }

                    let tableHtml = `
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #1c212c; color: #64748b; font-size: 0.75rem; text-transform: uppercase;">
                                        <th style="padding: 12px 16px;">Foto</th>
                                        <th style="padding: 12px 16px;">Nama & Dimensi</th>
                                        <th style="padding: 12px 16px;">Label Model</th>
                                        <th style="padding: 12px 16px;">Sumber / Keterangan</th>
                                        <th style="padding: 12px 16px;">Waktu Latih</th>
                                        <th style="padding: 12px 16px; text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    res.data.forEach(item => {
                        const isAi = item.label === 'ai';
                        const badgeColor = isAi ? '#f87171' : '#34d399';
                        const badgeBg = isAi ? 'rgba(239, 68, 68, 0.15)' : 'rgba(16, 185, 129, 0.15)';
                        const badgeBorder = isAi ? 'rgba(239, 68, 68, 0.35)' : 'rgba(16, 185, 129, 0.35)';
                        const badgeText = isAi ? '🤖 FOTO AI' : '📷 FOTO ASLI';

                        const thumbSrc = item.image_url || '';
                        const dimText = item.fingerprint ? `${item.fingerprint.width} × ${item.fingerprint.height} px` : '-';
                        const sizeText = item.fingerprint ? `${(item.fingerprint.size / 1024).toFixed(1)} KB` : '';

                        tableHtml += `
                            <tr style="border-bottom: 1px solid #141822;">
                                <td style="padding: 12px 16px; width: 60px;">
                                    ${thumbSrc ? `<img src="${thumbSrc}" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid #1c212c;" alt="thumb">` : `<div style="width: 44px; height: 44px; background: #1c212c; border-radius: 8px; display: flex; align-items: center; justify-content: center;">📷</div>`}
                                </td>
                                <td style="padding: 12px 16px;">
                                    <div style="font-weight: 600; color: #fff;">${item.file_name}</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">${dimText} · ${sizeText}</div>
                                </td>
                                <td style="padding: 12px 16px;">
                                    <span style="background: ${badgeBg}; color: ${badgeColor}; border: 1px solid ${badgeBorder}; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                        ${badgeText}
                                    </span>
                                </td>
                                <td style="padding: 12px 16px; color: #94a3b8; font-size: 0.8rem;">
                                    ${item.source_notes || '-'}
                                </td>
                                <td style="padding: 12px 16px; color: #64748b; font-size: 0.75rem;">
                                    ${item.date_formatted || item.created_at}
                                </td>
                                <td style="padding: 12px 16px; text-align: right;">
                                    <button type="button" onclick="window.deleteTrainingSample('${item.id}')" style="background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; cursor: pointer;">
                                        ✕ Hapus
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    datasetTableContainer.innerHTML = tableHtml;
                }
            })
            .catch(() => {
                datasetTableContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #ef4444; font-size: 0.85rem;">Gagal memuat dataset pelatihan.</div>';
            });
    }

    if (btnRefreshDataset) {
        btnRefreshDataset.addEventListener('click', () => {
            loadTrainingDataset();
            showToast('Dataset pelatihan diperbarui.', 'info');
        });
    }

    window.deleteTrainingSample = function(id) {
        if (!confirm('Yakin ingin menghapus sampel ini dari memori model?')) return;

        fetch(`api/train.php?action=delete&id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('Sampel berhasil dihapus dari model.', 'success');
                    loadTrainingDataset();
                } else {
                    showToast(res.message || 'Gagal menghapus sampel.', 'error');
                }
            })
            .catch(() => {
                showToast('Terjadi kesalahan jaringan saat menghapus.', 'error');
            });
    };

    // Load dataset on initial startup
    loadTrainingDataset();

    // ---------------------------------------------------------
    // 8. Toast Notification Helper
    // ---------------------------------------------------------
    function showToast(message, type = 'info') {
        let toast = document.getElementById('globalToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'globalToast';
            toast.className = 'toast-notice';
            document.body.appendChild(toast);
        }

        const icon = type === 'success' ? '✓' : (type === 'error' ? '✕' : 'ℹ');
        const iconColor = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#f59e0b');

        toast.innerHTML = `
            <span style="color: ${iconColor}; font-weight: 700; font-size: 1.1rem;">${icon}</span>
            <span>${message}</span>
        `;

        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3800);
    }
});
