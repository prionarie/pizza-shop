<?php
$pizzaTypes = [
    "Hawaiian" => 100,
    "Ham & Cheese" => 150,
    "Beef Supreme" => 270,
    "Cheese Supreme" => 250
];
$pizzaSizes = [
    "Solo" => 0,
    "Double" => 50,
    "Family" => 70,
    "Barkada"=> 90
];
$toppings = [
    "Cheese"=> 10,
    "Pepper"=> 15,
    "Ham"=> 27,
    "Pineapple"=> 25,
    "Ground Beef"=> 30
];

$total = 0;
$selectedType = $selectedSize = "";
$selectedToppings = [];
$showReceipt = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedType = $_POST["type"]  ?? "";
    $selectedSize = $_POST["size"]  ?? "";
    $selectedToppings = $_POST["toppings"]  ?? [];
    $showReceipt = true;

    if (isset($pizzaTypes[$selectedType])) {
        $total += $pizzaTypes[$selectedType];
    }

    if (isset($pizzaSizes[$selectedSize])) {
        $total += $pizzaSizes[$selectedSize];
    }

    foreach ($selectedToppings as $top) {
        if (isset($toppings[$top])) {
            $total += $toppings[$top];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pizza Shop</title>
  <link rel="stylesheet" href="css/pizzashop.css">
</head>
<body>

  <div class="container">
<div class="title-container">
  <div class="title-box">
    <h1>Pizza Shop</h1>
  </div>
</div>
    <form method="post" class="order-form">
      <div class="columns">

        <div class="column left">
          <h3>Pizza Type:</h3>
          <?php foreach ($pizzaTypes as $type => $price): ?>
            <label>
              <input type="radio" name="type" value="<?= $type ?>" required <?= $selectedType == $type ? 'checked' : '' ?>>
              <?= $type ?> (₱<?= number_format($price, 2) ?>)
            </label><br>
          <?php endforeach; ?>

          <h3>Size:</h3>
<?php foreach ($pizzaSizes as $size => $price): ?>
  <label>
    <input type="radio" name="size" value="<?= $size ?>" required <?= $selectedSize == $size ? 'checked' : '' ?>>
    <?= $size ?> 
    <?php if ($price > 0): ?>
      (add ₱<?= number_format($price, 2) ?>)
    <?php endif; ?>
  </label><br>
<?php endforeach; ?>
        </div>

<div class="column right">
  <h3>Extra Toppings:</h3>
  <?php foreach ($toppings as $topping => $price): ?>
    <label>
      <input type="checkbox" class="topping" data-price="<?= $price ?>" 
             name="toppings[]" value="<?= $topping ?>" 
             <?= in_array($topping, $selectedToppings) ? 'checked' : '' ?>>
      <?= $topping ?> (add ₱<?= number_format($price, 2) ?>)
    </label><br>
  <?php endforeach; ?>

  <h3>Order Summary</h3>
  <p>Price: ₱<span id="basePrice">0.00</span></p>
  <p>Quantity: <input type="number" id="quantity" value="1" min="1" style="width:60px;"></p>
  <p>Toppings: ₱<span id="toppingsTotal">0.00</span></p>
  <p><strong>TOTAL: ₱<span id="grandTotal">0.00</span></strong></p>
  </div>
  </div>

<div class="submit-container">
  <input type="submit" value="Submit Order" class="btn">
</div>

  <?php if ($showReceipt): ?>
    <div class="receipt-box">
      <h3>Order Receipt</h3>
      <p><strong>Pizza:</strong> <?= htmlspecialchars($selectedType) ?></p>
      <p><strong>Size:</strong> <?= htmlspecialchars($selectedSize) ?></p>
      <p><strong>Extra Toppings:</strong> <?= implode(", ", $selectedToppings) ?: "None" ?></p>
      <p><strong>Quantity:</strong> 1</p>
      <hr>
      <p><strong>Price:</strong> ₱<?= number_format($pizzaTypes[$selectedType] + $pizzaSizes[$selectedSize], 2) ?></p>
      <p><strong>Toppings:</strong> ₱<?php 
          $topsum = 0;
          foreach ($selectedToppings as $top) { $topsum += $toppings[$top]; }
          echo number_format($topsum, 2);
      ?></p>
      <p class="total">TOTAL: ₱<?= number_format($total, 2) ?></p>
    </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const radiosType = document.querySelectorAll("input[name='type']");
  const radiosSize = document.querySelectorAll("input[name='size']");
  const checkToppings = document.querySelectorAll(".topping");
  const qtyInput = document.getElementById("quantity");

  const basePriceEl = document.getElementById("basePrice");
  const toppingsTotalEl = document.getElementById("toppingsTotal");
  const grandTotalEl = document.getElementById("grandTotal");

  function computeTotal() {
    let base = 0;

    let type = document.querySelector("input[name='type']:checked");
    if (type) {
      let priceText = type.nextSibling.textContent.match(/₱([\d.,]+)/);
      if (priceText) base += parseFloat(priceText[1].replace(",", ""));
    }

    let size = document.querySelector("input[name='size']:checked");
    if (size) {
      let sizeText = size.nextSibling.textContent.match(/₱([\d.,]+)/);
      if (sizeText) base += parseFloat(sizeText[1].replace(",", ""));
    }

    let toppingsTotal = 0;
    checkToppings.forEach(chk => {
      if (chk.checked) toppingsTotal += parseFloat(chk.dataset.price);
    });

    let qty = parseInt(qtyInput.value) || 1;

    basePriceEl.textContent = base.toFixed(2);
    toppingsTotalEl.textContent = toppingsTotal.toFixed(2);
    grandTotalEl.textContent = ((base + toppingsTotal) * qty).toFixed(2);
  }
  [...radiosType, ...radiosSize, ...checkToppings].forEach(el => {
    el.addEventListener("change", computeTotal);
  });
  qtyInput.addEventListener("input", computeTotal);

  computeTotal(); // initial load
});
</script>

</body>
</html>