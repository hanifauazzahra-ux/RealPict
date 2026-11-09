<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'OPTIONS') {
    exit(0);
}

$dataFile = __DIR__ . '/../data/laporan.json';
$uploadDir = __DIR__ . '/../uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Function to get all reports
function getReports($dataFile) {
    if (!file_exists($dataFile)) {
        return [];
    }
    $content = file_get_contents($dataFile);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

// GET reports
if ($method === 'GET') {
    $reports = getReports($dataFile);
    echo json_encode([
        'status' => 'success',
        'total' => count($reports),
        'data' => array_reverse($reports)
    ]);
    exit;
}

// POST new report
if ($method === 'POST') {
    $category = isset($_POST['kategori']) ? trim($_POST['kategori']) : 'Hoaks / disinformasi';
    $description = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    
    $savedImage = null;
    $originalName = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $cleanName = 'report_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $target = $uploadDir . $cleanName;
        
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $savedImage = 'uploads/' . $cleanName;
            $originalName = $file['name'];
        }
    }

    $reports = getReports($dataFile);

    $newReport = [
        'id' => 'RPT-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
        'timestamp' => date('Y-m-d H:i:s'),
        'date_formatted' => date('d M Y, H:i'),
        'kategori' => htmlspecialchars($category, ENT_QUOTES, 'UTF-8'),
        'deskripsi' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
        'image' => $savedImage,
        'file_name' => $originalName ? htmlspecialchars($originalName, ENT_QUOTES, 'UTF-8') : 'Tidak ada gambar',
        'status' => 'Sedang Ditinjau',
        'status_code' => 'reviewing'
    ];

    $reports[] = $newReport;
    file_put_contents($dataFile, json_encode($reports, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode([
        'status' => 'success',
        'message' => 'Laporan berhasil dikirim dan masuk antrean tim threat intelligence RealPict.',
        'report' => $newReport
    ]);
    exit;
}
