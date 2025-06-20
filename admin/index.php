<?php
require "../config.php";

if (!isset($_SESSION['shop_id'])) {
  header('Location:login.php');
  exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);


$ord_pen = 0;
$ord_con = 0;
$total_pen = 0;
$total_con = 0;
$tot_rev = 0;
$total_ = 0;
$p_id_ = [];

$shop_id = $_SESSION['shop_id'];

$shop_result = $conn->query("SELECT percentage, withdrawal_balance FROM shop WHERE id='$shop_id'");
$shop_data = $shop_result->fetch_assoc();
$percentage = floatval($shop_data['percentage']);
$withdrawal_balance = floatval($shop_data['withdrawal_balance']);

$product_result = $conn->query("SELECT * FROM item WHERE shop_id='$shop_id'");
if ($product_result->num_rows > 0) {
  while ($row = $product_result->fetch_assoc()) {
    $tot_rev += intval($row['reviews']);
    $total_ += 1;
    $p_id_[] = intval($row['id']);
  }
}
$p_id_[] = 0;
$_SESSION['products'] = $p_id_;

$order_sql = 'SELECT * FROM orders WHERE p_id IN (' . implode(',', $p_id_) . ')';
$order_result = $conn->query($order_sql);
if ($order_result->num_rows > 0) {
  while ($row = $order_result->fetch_assoc()) {
    if ($row['status'] == "delivered" || $row['status'] == "picked") {
      $ord_con += $row['qty'];
      $total_con += $row['price'] * $row['qty'];
    } elseif ($row['status'] == "ordered") {
      $ord_pen += $row['qty'];
      $total_pen += $row['price'] * $row['qty'];
    }
  }
}

$platform_cut = ($total_con * $percentage) / 100;
$total_earning = $total_con - $platform_cut;
$total_balance = $total_earning - $withdrawal_balance;

$update_sql = $conn->prepare("UPDATE shop SET total_earning=?, total_balance=? WHERE id=?");
$update_sql->bind_param("ddi", $total_earning, $total_balance, $shop_id);
$update_sql->execute();

// New analytics data queries
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

function runQuery($conn, $sql) {
  $result = $conn->query($sql);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$top_products = runQuery($conn, "
  SELECT i.name, SUM(o.qty) as qty
  FROM orders o JOIN item i ON o.p_id = i.id
  WHERE i.shop_id = '$shop_id' AND STR_TO_DATE(o.order_time, '%d-%m-%Y') BETWEEN '$start_date' AND '$end_date'
  GROUP BY i.id ORDER BY qty DESC LIMIT 10
");

$monthly_sales = runQuery($conn, "
  SELECT DATE_FORMAT(STR_TO_DATE(o.order_time, '%d-%m-%Y'), '%b') as month, SUM(o.price * o.qty) as total
  FROM orders o JOIN item i ON o.p_id = i.id
  WHERE i.shop_id = '$shop_id' AND STR_TO_DATE(o.order_time, '%d-%m-%Y') BETWEEN '$start_date' AND '$end_date'
  GROUP BY MONTH(STR_TO_DATE(o.order_time, '%d-%m-%Y'))
");

$sales_by_category = runQuery($conn, "
  SELECT i.cat as category, SUM(o.price * o.qty) as total
  FROM orders o JOIN item i ON o.p_id = i.id
  WHERE i.shop_id = '$shop_id' AND STR_TO_DATE(o.order_time, '%d-%m-%Y') BETWEEN '$start_date' AND '$end_date'
  GROUP BY i.cat
");

$order_status_monthly = runQuery($conn, "
  SELECT DATE_FORMAT(STR_TO_DATE(o.order_time, '%d-%m-%Y'), '%b') as month, o.status, COUNT(*) as count
  FROM orders o JOIN item i ON o.p_id = i.id
  WHERE i.shop_id = '$shop_id' AND STR_TO_DATE(o.order_time, '%d-%m-%Y') BETWEEN '$start_date' AND '$end_date'
  GROUP BY MONTH(STR_TO_DATE(o.order_time, '%d-%m-%Y')), o.status
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php require "inc/head.php"; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  padding: 0rem;
}

.card {
  background-color: var(--clr-white);
  padding: 1.5rem;
  border-radius: 0px;
  box-shadow: var(--box-shadow);
  text-align: left;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 0.5rem;
  position: relative;
}

.card:hover {
  box-shadow: none;
  transition: cubic-bezier(0.075, 0.82, 0.165, 1);
}

.card .icon {
  position: absolute;
  top: 20px;
  right: 20px;
  background: var(--clr-danger);
  color: rgb(255, 255, 255);
  border-radius: 50%;
  font-size: 50px;
  padding: 0.5rem;
}

.card h2 {
  font-size: 20px;
  color: var(--clr-dark);
  margin-top: 0;
  font-weight: 600;
}

.card .value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--clr-dark);
  margin-top: 0px;
}

.card p {
  font-size: 0.85rem;
  color: var(--clr-dark-variant);
}

.card:nth-child(4n) .icon {
  background: var(--clr-accent2);
}
.card:nth-child(5n) .icon {
  background: var(--clr-accent3);
}

/* Charts styling */
.chart-container {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-top: 40px;
}

.chart-wrapper {
  flex: 1;
  min-width: 200px;
  background: var(--clr-white);
  padding: 1.5rem;
  border-radius: 0px;
  box-shadow: var(--box-shadow);
}

.chart-wrapper h3 {
  margin-top: 0;
  color: var(--clr-dark);
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

/* Recent orders table */
.recent_order {
  margin-top: 40px;
  background: var(--clr-white);
  padding: 1.5rem;
  border-radius: 0px;
  box-shadow: var(--box-shadow);
  overflow-x: auto;
}

.recent_order h2 {
  margin-top: 0;
  color: var(--clr-dark);
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.recent_order table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
}

.recent_order th,
.recent_order td {
  padding: 0.75rem 1rem;
  text-align: left;
  white-space: nowrap;
}

.recent_order th {
  background: #f8f9fa;
  color: var(--clr-dark);
  font-weight: 600;
}

.recent_order td {
  border-bottom: 1px solid #eee;
  color: var(--clr-dark-variant);
}

.recent_order tr:last-child td {
  border-bottom: none;
}

.recent_order a {
  display: inline-block;
  margin-top: 1rem;
  color: var(--clr-primary);
  text-decoration: none;
  font-weight: 500;
}

.recent_order a:hover {
  text-decoration: underline;
}

.warning {
  color: #f39c12;
  font-weight: 500;
}

.success {
  color: #27ae60;
  font-weight: 500;
}

/* Scrollbar for smaller devices */
@media (max-width: 768px) {
  .recent_order {
    overflow-x: auto;
  }

  .recent_order table {
    display: block;
  }
}

/* Analytics container styles */
.analytics-container {
  margin-top: 40px;
  background: var(--clr-white);
  padding: 1.5rem;
  border-radius: 0px;
  box-shadow: var(--box-shadow);
}

.analytics-container h2 {
  margin-top: 0;
  color: var(--clr-dark);
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

/* Date filter form styles */
.filter-form {
  margin-bottom: 2rem;
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.filter-form input,
.filter-form button {
  padding: 0.6rem 1rem;
  border-radius: 4px;
  border: 1px solid var(--clr-info-light);
  font-size: 0.9rem;
  min-width: 150px;
}

.filter-form button {
  background: var(--clr-primary);
  color: white;
  cursor: pointer;
  border: none;
}

/* Chart grid layout */
.analytics-charts {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

/* Individual chart box */
.chart-box {
  background: var(--clr-white);
  padding: 1.5rem;
  border-radius: 0px;
  box-shadow: var(--box-shadow);
  min-height: 400px;
}

.chart-box h3 {
  margin-top: 0;
  color: var(--clr-dark);
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

/* Responsive tweaks */
@media (max-width: 768px) {
  .filter-form {
    flex-direction: column;
  }

  .filter-form input,
  .filter-form button {
    width: 100%;
  }

  .analytics-charts {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .chart-box {
    padding: 1rem;
    min-height: auto;
    min-width: 200px;
  }

  .chart-box h3 {
    font-size: 1rem;
  }

  .analytics-container h2 {
    font-size: 1.2rem;
  }
}

@media (max-width: 600px) {
  .dashboard-grid {
    grid-template-columns: repeat(2, 1fr);
    padding: .5rem;
    gap: 1rem;
  }
  
  .card {
    border-radius: 0px;
    text-align: left;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 0.5rem;
    position: relative;
  }
  
  .card:hover {
    box-shadow: none;
    transition: cubic-bezier(0.075, 0.82, 0.165, 1);
  }
  
  .card .icon {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 30px;
    padding: 0.5rem;
  }
  
  .card h2 {
    font-size: 15px;
    margin-top: 0;
  }
  
  .card .value {
    font-size: 2rem;
    font-weight: 700;
    margin-top: -10px;
  }
  
  .card:nth-child(4n) .icon {
    background: var(--clr-accent2);
  }
  .card:nth-child(5n) .icon {
    background: var(--clr-accent3);
  }
  
  .chart-wrapper {
    min-width: 100%;
  }
  
  .analytics-charts {
    grid-template-columns: 1fr;
  }
}

@media (max-width:470px) {
  .card .value {
    font-size: 1.5rem;
  }
}

@media (max-width:390px) {
  .card h2 {
    font-size: 12px;
  }
  
  .card p {
    font-size: 10px;
    margin-top: -10px;
  }
  
  .card .value {
    font-size: 1.2rem;
  }
  
  .card .icon {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 25px;
    padding: 0.5rem;
  }
}

.chart-wrapper canvas,
.chart-box canvas {
  width: 100% !important;
  height: 300px !important;
  display: block !important;
}

.analytics-charts {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.chart-box {
  background: var(--clr-white);
  padding: 1.5rem;
  border-radius: 0;
  box-shadow: var(--box-shadow);
  min-height: 400px;
}

.filter-form {
  margin: 1rem 0;
}

.filter-form input {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.filter-form button {
  padding: 0.5rem 1rem;
  background: var(--clr-primary);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
  </style>
</head>
<body>

<?php require "inc/sidebar.php"; ?>

<div class="content">
  <h1 style="color: var(--clr-dark);">Dashboard</h1>
  <br>
   
  <div class="dashboard-grid" id="dashboardGrid">
    <div class="card"><div class="icon material-symbols-sharp">trending_up</div><h2>Total <br> Earning</h2><div class="value">৳<?php echo number_format($total_earning, 2); ?></div><p>In Details</p></div>
    <div class="card"><div style="background-color:  rgb(255, 191, 15);" class="icon material-symbols-sharp">account_balance</div><h2>Total <br> Balance</h2><div class="value">৳<?php echo number_format($total_balance, 2); ?></div><p>In Details</p></div>
    <div class="card"><div class="icon material-symbols-sharp">payments</div><h2>Total <br> Withdraw</h2><div class="value">৳<?php echo number_format($withdrawal_balance, 2); ?></div><p>In Details</p></div>
    <div class="card"><div style="background-color: rgba(1, 235, 252, 0.973);" class="icon material-symbols-sharp">inventory_2</div><h2>Total <br> Products</h2><div class="value"><?php echo $total_; ?></div><p>In Details</p></div>
    <div class="card"><div style="background-color:  rgb(255, 191, 15);" class="icon material-symbols-sharp">hourglass_empty</div><h2>Order <br> Panding</h2><div class="value"><?php echo $ord_pen; ?></div><p>In Details</p></div>
    <div class="card"><div class="icon material-symbols-sharp">check_circle</div><h2>Order<br>Confirmed</h2><div class="value"><?php echo $ord_con; ?></div><p>In Details</p></div>
    <div class="card"><div class="icon material-symbols-sharp">done</div><h2>Payment<br>Confirmed</h2><div class="value">৳<?php echo number_format($total_con, 2); ?></div><p>In Details</p></div>
    <div class="card"><div style="background-color: rgb(255, 191, 15);" class="icon material-symbols-sharp">schedule</div><h2>Pangdng<br>Payment</h2><div class="value">৳<?php echo number_format($total_pen, 2); ?></div><p>In Details</p></div>
    <div class="card"><div class="icon material-symbols-sharp">reviews</div><h2>Total<br>Review</h2><div class="value"><?php echo $tot_rev; ?></div><p>In Details</p></div>
    
  </div>

  <!-- Make sure these chart containers have proper dimensions -->
  <div class="chart-container">
    <div class="chart-wrapper">
      <h3>Order Status</h3>
      <canvas id="orderChart" height="300"></canvas>
    </div>
    <div class="chart-wrapper">
      <h3>Payment Status</h3>
      <canvas id="paymentChart" height="300"></canvas>
    </div>
  </div>

  <!-- Analytics Section with proper structure -->
  <div class="analytics-container">
    <h2>Sales Analytics</h2>
    <form class="filter-form" method="get">
      <label>From:
        <input type="date" name="start" value="<?= htmlspecialchars($start_date) ?>">
      </label>
      <label>To:
        <input type="date" name="end" value="<?= htmlspecialchars($end_date) ?>">
      </label>
      <button type="submit">Apply</button>
    </form>
    
    <div class="analytics-charts">
      <div class="chart-box">
        <h3>Top-Selling Products</h3>
        <canvas id="barChart" height="300"></canvas>
      </div>
      <div class="chart-box">
        <h3>Monthly Sales Trend</h3>
        <canvas id="lineChart" height="300"></canvas>
      </div>
      <div class="chart-box">
        <h3>Sales by Category</h3>
        <canvas id="doughnutChart" height="300"></canvas>
      </div>
      <div class="chart-box">
        <h3>Order Status by Month</h3>
        <canvas id="stackedBarChart" height="300"></canvas>
      </div>
    </div>
  </div>

 <div class="recent_order">
    <h2>Recent Orders</h2>
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Product Name</th>
          <th>Order Number</th>
          <th>Price</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $recent_orders_sql = "SELECT o.*, i.name as product_name, i.img1 FROM orders o JOIN item i ON o.p_id = i.id WHERE o.p_id IN (" . implode(',', $p_id_) . ") ORDER BY o.id DESC LIMIT 5";
        $recent_orders = $conn->query($recent_orders_sql);
        if ($recent_orders->num_rows > 0) {
          while ($order = $recent_orders->fetch_assoc()) {
            $img = !empty($order['img1']) ? $order['img1'] : 'placeholder.png';
            echo '<tr>
                    <td><img src="../prod/' . $img . '" width="40" style="border-radius:6px;"></td>
                    <td>' . htmlspecialchars($order['product_name']) . '</td>
                    <td>#' . $order['id'] . '</td>
                    <td>৳' . number_format($order['price'], 2) . '</td>
                    <td class="' . ($order['status'] == 'ordered' ? 'warning' : 'success') . '">' . ucfirst($order['status']) . '</td>
                  </tr>';
          }
        } else {
          echo '<tr><td colspan="5">No recent orders found.</td></tr>';
        }
        ?>
      </tbody>
    </table>
    <a href="orders.php">Show All</a>
  </div>
</div>

<script>
Chart.defaults.devicePixelRatio = window.devicePixelRatio || 1;

const vibrantColors = [
  '#FF6B6B', '#FFD93D', '#6BCB77', '#4D96FF',
  '#F45B69', '#845EC2', '#00C9A7', '#FFC75F',
  '#F9F871', '#2C73D2'
];

// Data from PHP
const orderPending = <?= $ord_pen ?>;
const orderConfirmed = <?= $ord_con ?>;
const paymentPending = <?= $total_pen ?>;
const paymentConfirmed = <?= $total_con ?>;

const barLabels = <?= json_encode(array_column($top_products, 'name')) ?>;
const barData = <?= json_encode(array_column($top_products, 'qty')) ?>;
const lineLabels = <?= json_encode(array_column($monthly_sales, 'month')) ?>;
const lineData = <?= json_encode(array_column($monthly_sales, 'total')) ?>;
const doughnutLabels = <?= json_encode(array_column($sales_by_category, 'category')) ?>;
const doughnutData = <?= json_encode(array_column($sales_by_category, 'total')) ?>;

const stackedLabels = [...new Set(<?= json_encode(array_column($order_status_monthly, 'month')) ?>)];
const allStatus = [...new Set(<?= json_encode(array_column($order_status_monthly, 'status')) ?>)];
const statusColorMap = {
  'ordered': '#FF6B6B',
  'picked': '#6BCB77',
  'delivered': '#4D96FF',
  'cancelled': '#F45B69'
};

// Order Status Chart
new Chart(document.getElementById('orderChart'), {
  type: 'pie',
  data: {
    labels: ['Pending Orders', 'Confirmed Orders'],
    datasets: [{
      data: [orderPending, orderConfirmed],
      backgroundColor: ['#FF6B6B', '#6BCB77'],
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom' },
      tooltip: {
        callbacks: {
          label: ctx => {
            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
            const val = ctx.raw;
            const pct = ((val / total) * 100).toFixed(1);
            return `${ctx.label}: ${val} (${pct}%)`;
          }
        }
      }
    }
  }
});

// Payment Status Chart
new Chart(document.getElementById('paymentChart'), {
  type: 'bar',
  data: {
    labels: ['Pending Payment', 'Confirmed Payment'],
    datasets: [{
      label: '৳ Amount',
      data: [paymentPending, paymentConfirmed],
      backgroundColor: ['#FFD93D', '#2C73D2'],
      borderColor: ['#F1C40F', '#1F618D'],
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => '৳' + value.toLocaleString()
        }
      }
    },
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => '৳' + ctx.raw.toLocaleString()
        }
      }
    }
  }
});

// Top Products - Bar
new Chart(document.getElementById("barChart"), {
  type: 'bar',
  data: {
    labels: barLabels,
    datasets: [{
      label: "Qty Sold",
      backgroundColor: vibrantColors,
      data: barData
    }]
  },
  options: {
    responsive: true,
    plugins: {
      tooltip: {
        callbacks: {
          label: ctx => `Sold: ${ctx.raw}`
        }
      }
    }
  }
});

// Monthly Sales Trend - Line
new Chart(document.getElementById("lineChart"), {
  type: 'line',
  data: {
    labels: lineLabels,
    datasets: [{
      label: "৳ Sales",
      backgroundColor: '#845EC2',
      borderColor: '#845EC2',
      data: lineData,
      fill: false,
      tension: 0.4,
      pointRadius: 5
    }]
  },
  options: { responsive: true }
});

// Sales by Category - Doughnut
new Chart(document.getElementById("doughnutChart"), {
  type: 'doughnut',
  data: {
    labels: doughnutLabels,
    datasets: [{
      label: "৳ Category Sales",
      backgroundColor: vibrantColors,
      data: doughnutData
    }]
  },
  options: {
    responsive: true,
    plugins: {
      tooltip: {
        callbacks: {
          label: function(context) {
            const sum = context.dataset.data.reduce((a, b) => a + b, 0);
            const val = context.raw;
            const pct = ((val / sum) * 100).toFixed(1);
            return `${context.label}: ৳${val} (${pct}%)`;
          }
        }
      }
    }
  }
});

// Order Status by Month - Stacked Bar
const statusDataset = allStatus.map((status, i) => ({
  label: status,
  backgroundColor: statusColorMap[status] || vibrantColors[i % vibrantColors.length],
  data: stackedLabels.map(month => {
    const match = <?= json_encode($order_status_monthly) ?>.find(row => row.month === month && row.status === status);
    return match ? match.count : 0;
  })
}));

new Chart(document.getElementById("stackedBarChart"), {
  type: 'bar',
  data: {
    labels: stackedLabels,
    datasets: statusDataset
  },
  options: {
    responsive: true,
    scales: {
      x: { stacked: true },
      y: { stacked: true }
    }
  }
});
</script>

<script src="inc/script.js"></script>
</body>
</html>