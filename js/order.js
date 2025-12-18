$(function () {

  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  let total = 0;

  cart.forEach(item => {
    const itemTotal = item.price * item.qty;
    total += itemTotal;

    $("#order-table").append(`
      <tr>
        <td>${item.name}</td>
        <td>${item.price} ฿</td>
        <td>${item.qty}</td>
        <td>${itemTotal} ฿</td>
      </tr>
    `);
  });

  $("#order-total").text("Total: " + total + " ฿");

  $("#confirm-order").on("click", function () {
    alert("Order Complete!");
    localStorage.removeItem("cart");
    window.location.href = "index.html";
  });
});
