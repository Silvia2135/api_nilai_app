<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

const FILE_DATA = __DIR__ . '/../data/nilai.json';

function kirim(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function bacaData(): array
{
    if (!file_exists(FILE_DATA)) {
        return [];
    }
    $isi = file_get_contents(FILE_DATA);
    return json_decode($isi, true) ?? [];
}

function tentukanGrade(float $nilai): array
{
    if ($nilai >= 85) {
        return ['grade' => 'A', 'keterangan' => 'Lulus'];
    } elseif ($nilai >= 75) {
        return ['grade' => 'B', 'keterangan' => 'Lulus'];
    } elseif ($nilai >= 65) {
        return ['grade' => 'C', 'keterangan' => 'Lulus'];
    } elseif ($nilai >= 50) {
        return ['grade' => 'D', 'keterangan' => 'Tidak Lulus'];
    } else {
        return ['grade' => 'E', 'keterangan' => 'Tidak Lulus'];
    }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $data = bacaData();
    kirim(200, [
        'status' => 'success',
        'total'  => count($data),
        'data'   => $data,
    ]);
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        kirim(400, ['status' => 'error', 'pesan' => 'Body harus berupa JSON yang valid']);
    }

    $nim   = trim($input['nim'] ?? '');
    $nama  = trim($input['nama'] ?? '');
    $mk    = trim($input['mata_kuliah'] ?? '');
    $nilai = $input['nilai'] ?? null;

    $error = [];
    if ($nim === '') $error[] = 'nim wajib diisi';
    if ($nama === '') $error[] = 'nama wajib diisi';
    if ($mk === '') $error[] = 'mata_kuliah wajib diisi';
    if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
        $error[] = 'nilai harus angka 0 sampai 100';
    }

    if ($error) {
        kirim(400, ['status' => 'error', 'pesan' => 'Data tidak valid', 'detail' => $error]);
    }

    $data = bacaData();
    $idBaru = $data ? max(array_column($data, 'id')) + 1 : 1;
    $hasilGrade = tentukanGrade((float)$nilai);

    $baru = [
        'id'          => $idBaru,
        'nim'         => $nim,
        'nama'        => $nama,
        'mata_kuliah' => $mk,
        'nilai'       => (float)$nilai,
        'keterangan'  => $hasilGrade['keterangan'],
        'grade'       => $hasilGrade['grade'],
    ];

    kirim(201, [
        'status' => 'success',
        'pesan'  => 'Data berhasil diproses',
        'data'   => $baru,
    ]);
}

kirim(405, ['status' => 'error', 'pesan' => 'Method tidak diizinkan']);