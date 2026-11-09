<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'OPTIONS') {
    exit(0);
}

$dataFile = __DIR__ . '/../data/training_dataset.json';
$trainingUploadDir = __DIR__ . '/../uploads/training/';

if (!is_dir($trainingUploadDir)) {
    mkdir($trainingUploadDir, 0777, true);
}

// Function to get all training samples
function getTrainingData($dataFile) {
    if (!file_exists($dataFile)) {
        return [];
    }
    $content = file_get_contents($dataFile);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

// Function to save training data
function saveTrainingData($dataFile, $data) {
    return file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Function to compute robust forensic fingerprint
function computeImageFingerprint($filePath) {
    if (!file_exists($filePath)) return null;
    $content = @file_get_contents($filePath);
    if ($content === false) return null;

    $md5 = md5($content);
    $sha256 = hash('sha256', $content);
    $size = filesize($filePath);
    $imgInfo = @getimagesize($filePath);

    $w = $imgInfo ? $imgInfo[0] : 0;
    $h = $imgInfo ? $imgInfo[1] : 0;
    $mime = $imgInfo ? $imgInfo['mime'] : '';

    // JPEG DQT quantization table signature
    $dqtHash = '';
    $dqtPos = strpos($content, "\xFF\xDB");
    if ($dqtPos !== false) {
        $dqtData = substr($content, $dqtPos, 134);
        $dqtHash = md5($dqtData);
    }

    // Binary perceptual structural hash (64 bits)
    $binHash = '';
    $len = strlen($content);
    $step = max(1, (int)($len / 64));
    for ($i = 0; $i < 64; $i++) {
        $pos1 = $i * $step;
        $pos2 = min($len - 1, ($i + 1) * $step);
        $b1 = ord($content[$pos1]);
        $b2 = ord($content[$pos2]);
        $binHash .= ($b1 > $b2) ? '1' : '0';
    }
    $hexHash = sprintf('%08x%08x', bindec(substr($binHash, 0, 32)), bindec(substr($binHash, 32, 32)));

    return [
        'md5' => $md5,
        'sha256' => $sha256,
        'dqt_hash' => $dqtHash,
        'hex_hash' => $hexHash,
        'width' => $w,
        'height' => $h,
        'ratio' => $h > 0 ? round($w / $h, 3) : 1,
        'size' => $size,
        'mime' => $mime
    ];
}

// -----------------------------------------------------------------------------
// GET: Retrieve training dataset & statistics
// -----------------------------------------------------------------------------
if ($method === 'GET') {
    $dataset = getTrainingData($dataFile);

    $aiCount = 0;
    $realCount = 0;

    foreach ($dataset as $item) {
        if (($item['label'] ?? '') === 'ai') $aiCount++;
        else $realCount++;
    }

    echo json_encode([
        'status' => 'success',
        'stats' => [
            'total' => count($dataset),
            'ai_count' => $aiCount,
            'real_count' => $realCount
        ],
        'data' => array_reverse($dataset)
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// POST: Train new image(s) or quick-train existing analyzed file
// -----------------------------------------------------------------------------
if ($method === 'POST') {
    $label = isset($_POST['label']) && strtolower(trim($_POST['label'])) === 'real' ? 'real' : 'ai';
    $sourceNotes = isset($_POST['source_notes']) ? trim($_POST['source_notes']) : '';

    $dataset = getTrainingData($dataFile);
    $added = [];

    // Case A: Quick-train from existing uploaded file path
    if (isset($_POST['file_path']) && !empty($_POST['file_path'])) {
        $relPath = trim($_POST['file_path']);
        // Prevent path traversal
        $fullPath = realpath(__DIR__ . '/../' . ltrim($relPath, '/\\'));
        $allowedBase = realpath(__DIR__ . '/../');

        if ($fullPath && strpos($fullPath, $allowedBase) === 0 && file_exists($fullPath)) {
            $fp = computeImageFingerprint($fullPath);
            if ($fp) {
                // Check if already in dataset
                $exists = false;
                foreach ($dataset as &$existing) {
                    if ($existing['fingerprint']['md5'] === $fp['md5']) {
                        $existing['label'] = $label;
                        if (!empty($sourceNotes)) $existing['source_notes'] = htmlspecialchars($sourceNotes, ENT_QUOTES, 'UTF-8');
                        $existing['updated_at'] = date('Y-m-d H:i:s');
                        $exists = true;
                        $added[] = $existing;
                        break;
                    }
                }
                unset($existing);

                if (!$exists) {
                    $originalName = isset($_POST['file_name']) ? basename($_POST['file_name']) : basename($fullPath);
                    $newItem = [
                        'id' => 'TRN-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
                        'label' => $label,
                        'file_name' => htmlspecialchars($originalName, ENT_QUOTES, 'UTF-8'),
                        'source_notes' => !empty($sourceNotes) ? htmlspecialchars($sourceNotes, ENT_QUOTES, 'UTF-8') : ($label === 'ai' ? 'Pelatihan Foto AI' : 'Pelatihan Foto Asli'),
                        'image_url' => ltrim(str_replace('\\', '/', substr($fullPath, strlen($allowedBase))), '/'),
                        'fingerprint' => $fp,
                        'created_at' => date('Y-m-d H:i:s'),
                        'date_formatted' => date('d M Y, H:i')
                    ];
                    $dataset[] = $newItem;
                    $added[] = $newItem;
                }

                saveTrainingData($dataFile, $dataset);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Foto berhasil ditambahkan ke dataset pelatihan model sebagai ' . ($label === 'ai' ? 'FOTO AI' : 'FOTO ASLI') . '!',
                    'items_added' => count($added)
                ]);
                exit;
            }
        }
    }

    // Case B: Upload file(s) directly via form (Single or Multiple)
    $uploadedFiles = [];
    if (isset($_FILES['images'])) {
        // Multiple files array normalization
        if (is_array($_FILES['images']['name'])) {
            $count = count($_FILES['images']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadedFiles[] = [
                        'name' => $_FILES['images']['name'][$i],
                        'tmp_name' => $_FILES['images']['tmp_name'][$i]
                    ];
                }
            }
        } else if ($_FILES['images']['error'] === UPLOAD_ERR_OK) {
            $uploadedFiles[] = [
                'name' => $_FILES['images']['name'],
                'tmp_name' => $_FILES['images']['tmp_name']
            ];
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadedFiles[] = [
            'name' => $_FILES['image']['name'],
            'tmp_name' => $_FILES['image']['tmp_name']
        ];
    }

    if (empty($uploadedFiles)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada file gambar yang diunggah untuk dilatih.'
        ]);
        exit;
    }

    foreach ($uploadedFiles as $file) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            continue;
        }

        $cleanName = 'trn_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $targetPath = $trainingUploadDir . $cleanName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $fp = computeImageFingerprint($targetPath);
            if ($fp) {
                $newItem = [
                    'id' => 'TRN-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
                    'label' => $label,
                    'file_name' => htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'),
                    'source_notes' => !empty($sourceNotes) ? htmlspecialchars($sourceNotes, ENT_QUOTES, 'UTF-8') : ($label === 'ai' ? 'Contoh Foto AI' : 'Contoh Foto Kamera Asli'),
                    'image_url' => 'uploads/training/' . $cleanName,
                    'fingerprint' => $fp,
                    'created_at' => date('Y-m-d H:i:s'),
                    'date_formatted' => date('d M Y, H:i')
                ];
                $dataset[] = $newItem;
                $added[] = $newItem;
            }
        }
    }

    saveTrainingData($dataFile, $dataset);

    echo json_encode([
        'status' => 'success',
        'message' => 'Berhasil melatih ' . count($added) . ' foto sebagai ' . ($label === 'ai' ? 'FOTO AI' : 'FOTO ASLI') . '! Karakteristiknya kini disimpan ke database pembelajaran.',
        'items_added' => count($added)
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// DELETE: Remove training sample by ID
// -----------------------------------------------------------------------------
if ($method === 'DELETE' || (isset($_GET['action']) && $_GET['action'] === 'delete')) {
    $id = isset($_GET['id']) ? trim($_GET['id']) : (isset($_POST['id']) ? trim($_POST['id']) : '');
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID sampel tidak ditemukan.']);
        exit;
    }

    $dataset = getTrainingData($dataFile);
    $filtered = [];
    $removed = false;

    foreach ($dataset as $item) {
        if ($item['id'] === $id) {
            $removed = true;
            // Optionally delete file
            if (!empty($item['image_url'])) {
                $filePath = __DIR__ . '/../' . ltrim($item['image_url'], '/\\');
                if (file_exists($filePath) && strpos($filePath, 'uploads/training/') !== false) {
                    @unlink($filePath);
                }
            }
        } else {
            $filtered[] = $item;
        }
    }

    if ($removed) {
        saveTrainingData($dataFile, $filtered);
        echo json_encode([
            'status' => 'success',
            'message' => 'Sampel latihan berhasil dihapus dari model.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sampel dengan ID tersebut tidak ditemukan.'
        ]);
    }
    exit;
}

