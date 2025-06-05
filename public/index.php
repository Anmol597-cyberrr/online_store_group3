<?php include 'header.php'; ?>
<h1>🛍️ All Products</h1>
<div class="grid" id="productList"></div>
<script>
fetch('../api/products.php')
  .then(res => res.json())
  .then(data => {
    const list = document.getElementById('productList');
    data.forEach(p => {
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <img src="${p.image}" alt="${p.description}" />
        <h3>${p.description}</h3>
        <p>Price: $${p.price}</p>
        <p>Shipping: $${p.shipping_cost}</p>
      `;
      list.appendChild(card);
    });
  });
</script>
<?php include 'footer.php'; ?>
