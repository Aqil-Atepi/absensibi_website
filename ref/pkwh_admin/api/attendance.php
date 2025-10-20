<?php
// api/attendance.php
session_start();
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $date = $_GET['date'] ?? date('Y-m-d');
    $stmt = $pdo->prepare("SELECT a.*, s.nama AS nama_siswa FROM attendance a LEFT JOIN siswa s ON s.id=a.student_id WHERE a.tanggal = :t ORDER BY a.jam_masuk");
    $stmt->execute(['t'=>$date]);
    echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    // body: student_id, jam_masuk (HH:MM:SS), status (Hadir/Telat/Izin/Alpha), tanggal
    $in = json_decode(file_get_contents('php://input'), true);
    $student_id = (int)($in['student_id'] ?? 0);
    $jam_masuk = $in['jam_masuk'] ?? date('H:i:s');
    $status = $in['status'] ?? 'Hadir';
    $tanggal = $in['tanggal'] ?? date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO attendance (student_id,jam_masuk,status,tanggal) VALUES (:sid,:jm,:st,:tg)");
    $stmt->execute(['sid'=>$student_id,'jm'=>$jam_masuk,'st'=>$status,'tg'=>$tanggal]);
    echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    $in = json_decode(file_get_contents('php://input'), true);
    $id = (int)$in['id'];
    $status = $in['status'] ?? null;
    $jam_masuk = $in['jam_masuk'] ?? null;

    $parts = [];
    $params = ['id'=>$id];
    if ($status !== null) { $parts[] = "status = :st"; $params['st'] = $status; }
    if ($jam_masuk !== null) { $parts[] = "jam_masuk = :jm"; $params['jm'] = $jam_masuk; }

    if (!$parts) { echo json_encode(['ok'=>false,'msg'=>'No fields']); exit; }

    $sql = "UPDATE attendance SET " . implode(',', $parts) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['ok'=>true]);
    exit;
}

http_response_code(405);
echo json_encode(['ok'=>false,'msg'=>'Method not allowed']);
