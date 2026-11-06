<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:../login.php");
    exit();
}

include "sidebar_header.php";

include '../config.php';

$results = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $bloodType = $_POST['bloodType'];
  $location  = $_POST['location'];

  if ($bloodType === '' && $location === '') {
    $sql = "SELECT bloodtype, units_available, location FROM blood_inventory";
  } elseif ($bloodType !== '' && $location === '') {
    $sql = "SELECT bloodtype, units_available, location FROM blood_inventory WHERE bloodtype = '$bloodType'";
  } elseif ($bloodType === '' && $location !== '') {
    $sql = "SELECT bloodtype, units_available, location FROM blood_inventory WHERE location LIKE '%$location%'";
  } else {
    $sql = "SELECT bloodtype, units_available, location FROM blood_inventory WHERE bloodtype = '$bloodType' AND location LIKE '%$location%'";
  }

  $query = $conn->query($sql);
  if ($query && $query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
      $results[] = $row;
    }
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Search Blood</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body {
      font-family: "Poppins", Arial, sans-serif;
      background: #f4f4f9;
      margin: 0;
      padding: 0px 0;
      color: #333;
    }

    .container {
      width: 90%;
      max-width: 900px;
      margin: 30px auto;
      background: #fff;
      padding: 22px;
      border-radius: 8px;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    }

    .container h1 {
      text-align: center;
      color: crimson;
      font-size: 25px;
      border-bottom: 1px solid #ddd;
      padding-bottom: 8px;
    }

    h1 {
      text-align: center;
      color: crimson;
    }

    form {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      justify-content: center;
      margin-bottom: 8px;
      align-items: flex-end;
    }

    .field {
      min-width: 200px;
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: 500;
      margin-bottom: 6px;
    }

    select,
    input[type="text"] {
      padding: 10px;
      border-radius: 6px;
      border: none;
      background: #eee;
      font-size: 15px;
      outline: none;
    }

    button {
      background: crimson;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      margin-bottom: 13px;
    }

    button:hover {
      background: rgb(253, 80, 80);
    }

    .results-title {
      text-align: center;
      margin-top: 8px;
      color: #cc0000;
      font-weight: 600;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 14px;
    }

    th,
    td {
      padding: 12px;
      border: 1px solid #e6e6e6;
      text-align: center;
    }

    th {
      background: crimson;
      color: white;
      font-weight: 600;
    }

    tr:nth-child(even) {
      background: #fafafa;
    }

    .no-results {
      text-align: center;
      color: darkred;
      margin-top: 12px;
      font-weight: 500;
    }

    @media (max-width:600px) {
      form {
        gap: 8px;
      }

      .field {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Search for Available Blood</h1>

    <form method="POST" action="">
      <div class="field">
        <label for="bloodType">Blood Type</label>
        <select id="bloodType" name="bloodType">
          <option value="">Select Blood Type</option>
          <option value="A+">A+</option>
          <option value="A-">A-</option>
          <option value="B+">B+</option>
          <option value="B-">B-</option>
          <option value="AB+">AB+</option>
          <option value="AB-">AB-</option>
          <option value="O+">O+</option>
          <option value="O-">O-</option>
        </select>
      </div>

      <div class="field">
        <label for="location">Location (city)</label>
        <input type="text" id="location" name="location" placeholder="Enter your city">
      </div>

      <div class="field">
        <button type="submit">Search</button>
      </div>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
      <div class="results">
        <h2 class="results-title">Search Results</h2>

        <?php if (count($results) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>Blood Type</th>
                <th>Available Units</th>
                <th>Location</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $row): ?>
                <tr>
                  <td><?php echo $row['bloodtype']; ?></td>
                  <td><?php echo $row['units_available']; ?></td>
                  <td><?php echo $row['location']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="no-results">No results found.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</body>

</html>