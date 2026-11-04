<?php
session_start();

/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}*/

include "sidebar_header.php";
include "../config.php";

$email = $_SESSION['email'];

// Fetch donor username
$userQuery = "SELECT username FROM users WHERE email = '$email'";
$userResult = $conn->query($userQuery);
$username = ($userResult && $userResult->num_rows > 0)
  ? $userResult->fetch_assoc()['username']
  : "User";

/* SECTION 1: Next Eligible Date*/
$eligibleDateQuery = "
    SELECT donationdate
    FROM donation_requests
    WHERE donor_email = '$email' AND status = 'approved'
    ORDER BY donationdate DESC
    LIMIT 1
";
$eligibleResult = $conn->query($eligibleDateQuery);
$nextEligibleDate = null;
if ($eligibleResult && $eligibleResult->num_rows > 0) {
  $nextEligibleDate = $eligibleResult->fetch_assoc()['donationdate'];
}

/* SECTION 2: Donation Requests */
$requestsQuery = "
    SELECT donation_id, bloodtype, unitsdonated, location, donationdate, requested_at, status
    FROM donation_requests
    WHERE donor_email = '$email'
    ORDER BY donation_id ASC
";
$requestsResult = $conn->query($requestsQuery);

/* SECTION 3: Donation History */
$historyQuery = "
    SELECT donation_id,
           bloodtype,
           unitsdonated,
           location,
           donationdate,
           recorded_at,
           status
    FROM donation_history
    WHERE donor_email = '$email'
    ORDER BY donationdate ASC
";
$historyResult = $conn->query($historyQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Donor Dashboard</title>
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

  <!-- Dashboard Section -->
  <section id="dashboard">
    <h2>Hello <?php echo htmlspecialchars($username); ?>, Welcome to the Donor Dashboard</h2>
    <p style="text-align: center; font-size:17px;">Use the sidebar to navigate.</p>
  </section>

  <!-- 1) Next Eligible Donation Date (donation_requests) -->
  <section id="next-eligible">
    <h2>Next Eligible Donation Date</h2>
    <?php if ($nextEligibleDate): ?>
      <div class="info-box">
        Your latest **approved** donation date is <strong><?php echo htmlspecialchars($nextEligibleDate); ?></strong>.
        <div>If an admin approved a future date for you, it will appear here.</div>
      </div>
    <?php else: ?>
      <p class="no-results">You currently have no approved donation requests.</p>
    <?php endif; ?>
  </section>

  <!-- 2) Donation Requests (donation_requests) -->
  <section id="donation-requests">
    <h2>My Donation Requests</h2>

    <?php if ($requestsResult && $requestsResult->num_rows > 0): ?>
      <table class="requests-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Blood Type</th>
            <th>Units</th>
            <th>Location</th>
            <th>Requested Date</th>
            <th>Submitted At</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $counter = 1;
          while ($row = $requestsResult->fetch_assoc()):
            $statusClass = strtolower(trim($row['status']));
          ?>
            <tr>
              <td>#<?php echo str_pad($counter++, 3, '0', STR_PAD_LEFT); ?></td>
              <td><?php echo htmlspecialchars($row['bloodtype']); ?></td>
              <td><?php echo htmlspecialchars($row['unitsdonated']); ?></td>
              <td><?php echo htmlspecialchars($row['location']); ?></td>
              <td><?php echo htmlspecialchars($row['donationdate']); ?></td>
              <td><?php echo htmlspecialchars($row['requested_at']); ?></td>
              <td><span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($statusClass); ?></span></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="no-results">You have not made any donation requests yet.</p>
    <?php endif; ?>
  </section>

  <!-- 3) Donation History (donation_history) -->
  <section id="donation-history">
    <h2>My Donation History</h2>

    <?php if ($historyResult && $historyResult->num_rows > 0): ?>
      <table class="requests-table">
        <thead>
          <tr>
            <th>#</th>
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
      <p class="no-results">You have no donation history yet.</p>
    <?php endif; ?>
  </section>

</body>

</html>