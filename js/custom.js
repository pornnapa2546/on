$(function () {
  // Main Menu JS
  $(window).scroll(function () {
    if ($(this).scrollTop() < 50) {
      $("nav").removeClass("site-top-nav");
      $("#back-to-top").fadeOut();
    } else {
      $("nav").addClass("site-top-nav");
      $("#back-to-top").fadeIn();
    }
  });

  // Shopping Cart Toggle JS
  $("#shopping-cart").on("click", function () {
    $("#cart-content").toggle("blind", "", 500);
  });

  // Back-To-Top Button JS
  $("#back-to-top").click(function (event) {
    event.preventDefault();
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  // Delete Cart Item JS
  $(document).on("click", ".btn-delete", function (event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    updateTotal();
  });

  // Update Total Price JS
function updateTotal() {
  let total = 0;

  $(".cart-item").each(function () {
    total += parseFloat(
      $(this).find("td:nth-child(5)").text().replace("฿", "")
    );
  });

  $(".total-price").text(total + " ฿");
}


// Shopping Cart Toggle JS
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


$(".clear-cart").on("click", function () {
  $(".cart-item").remove();
  updateTotal();
});


});



