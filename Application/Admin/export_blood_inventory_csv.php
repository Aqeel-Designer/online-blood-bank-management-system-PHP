<?php
// export_blood_inventory_csv.php
// Place this inside /admin/ folder (adjust config path if needed)

include "../config.php";

// Optional: restrict to admin user
/*
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}
*/

$filename = 'Blood_Inventory_' . date('Y-m-d_His') . '.csv';

// Headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$out = fopen('php://output', 'w');

// UTF-8 BOM (Excel friendly)
fwrite($out, "\xEF\xBB\xBF");

// Header row — adjust names if needed
fputcsv($out, [
    'Inventory ID',
    'Blood Type',
    'Units Available',
    'Location'
]);

// Fetch data
$sql = "SELECT id, bloodtype, units_available, location
        FROM blood_inventory
        ORDER BY id ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($out, [
            $row['id'],
            $row['bloodtype'],
            $row['units_available'],
            $row['location']
        ]);
    }
}

fclose($out);
$conn->close();
exit();
