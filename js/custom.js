// Delete Cart Item
$(document).on("click", ".btn-delete", function (e) {
  e.preventDefault();
  $(this).closest("tr").remove();
  updateTotal();
});

// Update Total
function updateTotal() {
  let total = 0;

  $(".cart-item").each(function () {
    total += parseFloat(
      $(this).find("td:nth-child(5)").text().replace("฿", "")
    );
  });

  $(".total-price").text(total + " ฿");
}

// Add To Cart
$(".btn-add").on("click", function () {
  const box = $(this).closest(".food-menu-box");
  const name = box.find("h4").text();
  const price = parseFloat(box.find(".food-price").text());
  const qty = parseInt(box.find("input[type='number']").val());
  const total = price * qty;

  const row = `
    <tr class="cart-item">
      <td>-</td>
      <td>${name}</td>
      <td>${price} ฿</td>
      <td>${qty}</td>
      <td>${total} ฿</td>
      <td><a href="#" class="btn-delete">&times;</a></td>
    </tr>
  `;

  $(".cart-total").before(row);
  updateTotal();
});

// Clear Cart
$(".clear-cart").on("click", function () {
  $(".cart-item").remove();
  updateTotal();
});
