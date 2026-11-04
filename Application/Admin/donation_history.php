<?php
session_start();

/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}*/

include "sidebar_header.php";
include "../config.php";



$historyQuery = "
    SELECT donation_id,
           username,
           donor_email,
           bloodtype,
           unitsdonated,
           location,
           donationdate,
           recorded_at,
           status
    FROM donation_history
    ORDER BY donationdate ASC
";
$historyResult = $conn->query($historyQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Donation History</title>
  <link rel="stylesheet" href="recipient_style.css">
  <style>
    /* styles for messages */
    .info-box {
      background: #fff;
      border-radius: 8px;
      padding: 12px 16px;
      margin-bottom: 12px;
      box-shadow: 0 0 6px rgba(0, 0, 0, 0.04);
      text-align: center;
    }

    .info-box strong {
      color: crimson;
    }
  </style>
</head>

<body>
  <!--  Donation History (donation_history) -->
  <section id="donation-history" style="max-width : 1000px;">
    <h2>Donation History</h2>

    <?php if ($historyResult && $historyResult->num_rows > 0): ?>
      <table class="requests-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>Donor Email</th>
            <th>Blood Type</th>
            <th>Units Donated</th>
            <th>Location</th>
            <th>Donation Date</th>
            <th>Recorded At</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $counter2 = 1;
          while ($h = $historyResult->fetch_assoc()):
            $statusClass = strtolower(trim($h['status']));
          ?>
            <tr>
              <td>#<?php echo str_pad($counter2++, 3, '0', STR_PAD_LEFT); ?></td>
              <td><?php echo ucfirst($h['username']); ?></td>
              <td><?php echo htmlspecialchars($h['donor_email']); ?></td>
              <td><?php echo htmlspecialchars($h['bloodtype']); ?></td>
              <td><?php echo htmlspecialchars($h['unitsdonated']); ?></td>
              <td><?php echo htmlspecialchars($h['location']); ?></td>
              <td><?php echo htmlspecialchars($h['donationdate']); ?></td>
              <td><?php echo htmlspecialchars($h['recorded_at']); ?></td>
              <td><span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($statusClass); ?></span></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="no-results">There is no donation history yet.</p>
    <?php endif; ?>

    <form action="export_donation_history_csv.php" method="post" style="display:inline-block;">
      <div><button type="submit" class="download-btn">Download Donation Report (CSV)</button></div>
    </form>

  </section>

</body>

</html>