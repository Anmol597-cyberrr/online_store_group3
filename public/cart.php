<?php include 'header.php'; ?>
<h1>🛒 Your Cart</h1>
<table id="cartTable">
  <thead><tr><th>Product</th><th>Qty</th></tr></thead>
  <tbody></tbody>
</table>
<script>
fetch('../api/cart.php')
  .then(res => res.json())
  .then(data => {
    const table = document.querySelector('#cartTable tbody');
    data.forEach(item => {
      table.innerHTML += `<tr><td>${item.product}</td><td>${item.quantity}</td></tr>`;
    });
  });
</script>
<?php include 'footer.php'; ?>