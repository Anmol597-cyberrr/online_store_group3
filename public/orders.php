<?php include 'header.php'; ?>
<h1>📦 Order History</h1>
<table id="ordersTable">
  <thead><tr><th>Order ID</th><th>User</th><th>Total</th><th>Date</th></tr></thead>
  <tbody></tbody>
</table>
<script>
fetch('../api/orders.php')
  .then(res => res.json())
  .then(data => {
    const table = document.querySelector('#ordersTable tbody');
    data.forEach(order => {
      table.innerHTML += `<tr><td>${order.id}</td><td>${order.user}</td><td>$${order.total}</td><td>${order.date}</td></tr>`;
    });
  });
</script>
<?php include 'footer.php'; ?>