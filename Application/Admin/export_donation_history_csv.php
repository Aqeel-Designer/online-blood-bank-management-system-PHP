<?php
// export_donation_history_csv.php
// Put this in admin/ (or adjust include path to config.php)

include "../config.php"; // adjust if your config.php location differs

// Optional: restrict to admin user (uncomment if you use session control)
// session_start();
// if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
//     header('HTTP/1.1 403 Forbidden');
//     exit('Forbidden');
// }

$filename = 'Donation_History_' . date('Y-m-d_His') . '.csv';

// Headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$out = fopen('php://output', 'w');

// Write UTF-8 BOM for Excel to open UTF-8 correctly (optional but recommended)
fwrite($out, "\xEF\xBB\xBF");

// Header row — change column titles if you like
fputcsv($out, [
    'Donation ID',
    'Donor Email',
    'Blood Type',
    'Units Donated',
    'Location',
    'Donation Date',
    'Recorded At',
    'Status'
]);

// Query the table (adjust column names/table name if different)
$sql = "SELECT donation_id, donor_email, bloodtype, unitsdonated, location, donationdate, recorded_at, status
        FROM donation_history
        ORDER BY donation_id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Build a row. Format date if you want:
        $donation_date = $row['donationdate'];
        $recorded_at   = $row['recorded_at'];

        fputcsv($out, [
            $row['donation_id'],
            $row['donor_email'],
            $row['bloodtype'],
            $row['unitsdonated'],
            $row['location'],
            $donation_date,
            $recorded_at,
            $row['status']
        ]);
    }
}

// Close connection and stream
fclose($out);
$conn->close();
exit();
