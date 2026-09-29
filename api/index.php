<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Menentukan lokasi file nilai.json
$possiblePaths = [
    __DIR__ . '/../data/nilai.json',
    $_SERVER['DOCUMENT_ROOT'] . '/data/nilai.json',
    'data/nilai.json'
];

$filePath = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $filePath = $path;
        break;
    }
}

if ($filePath === null) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'File data/nilai.json tidak ditemukan'
    ]);
    exit;
}

$jsonData = file_get_contents($filePath);
http_response_code(200);
echo $jsonData;