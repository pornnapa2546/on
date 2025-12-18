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
    $("#cart-content tr").each(function () {
      const rowTotal = parseFloat($(this).find("td:nth-child(5)").text().replace("$", ""));
      if (!isNaN(rowTotal)) {
        total += rowTotal;
      }
    });
    $("#cart-content th:nth-child(5)").text("$" + total.toFixed(2));
    $(".tbl-full th:nth-child(6)").text("$" + total.toFixed(2));
  }

// Shopping Cart Toggle JS
$(".btn-add").on("click", function () {
  const box = $(this).closest(".food-menu-box");

  const name = box.find("h4").text();
  const priceText = box.find(".food-price").text();
  const price = parseFloat(priceText);
  const qty = parseInt(box.find("input[type='number']").val());

  const total = price * qty;

  const row = `
    <tr>
      <td>-</td>
      <td>${name}</td>
      <td>${price} ฿</td>
      <td>${qty}</td>
      <td>${total} ฿</td>
      <td><a href="#" class="btn-delete">&times;</a></td>
    </tr>
  `;

  $(".cart-table tr:last").before(row);
  updateTotal();
});


});



