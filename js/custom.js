$(function () {

  /* =====================
     LOAD CART
  ===================== */
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  /* =====================
     TOGGLE CART
  ===================== */
  $("#shopping-cart").on("click", function () {
    $("#cart-content").toggle();
  });

  /* =====================
     ADD TO CART (รวมซ้ำ)
  ===================== */
  $(".btn-add").on("click", function () {
    const box = $(this).closest(".food-menu-box");

    const name = box.find("h4").text();
    const price = parseFloat(
      box.find(".food-price").text().replace("฿", "")
    );
    const qty = parseInt(
      box.find("input[type='number']").val()
    );

    const exist = cart.find(item => item.name === name);

    if (exist) {
      exist.qty += qty;
    } else {
      cart.push({ name, price, qty });
    }

    saveCart();
    renderCart();
  });

  /* =====================
     DELETE ITEM
  ===================== */
  $(document).on("click", ".btn-delete", function (e) {
    e.preventDefault();
    const index = $(this).data("index");
    cart.splice(index, 1);
    saveCart();
    renderCart();
  });

  /* =====================
     RENDER CART
  ===================== */
  function renderCart() {
    $(".cart-item").remove();
    let total = 0;
    let badge = 0;

    cart.forEach((item, index) => {
      const itemTotal = item.price * item.qty;
      total += itemTotal;
      badge += item.qty;

      $(".cart-total").before(`
        <tr class="cart-item">
          <td>-</td>
          <td>${item.name}</td>
          <td>${item.price} ฿</td>
          <td>${item.qty}</td>
          <td>${itemTotal} ฿</td>
          <td>
            <a href="#" class="btn-delete" data-index="${index}">×</a>
          </td>
        </tr>
      `);
    });

    $(".total-price").text(total + " ฿");
    $(".badge").text(badge);
  }

  /* =====================
     SAVE CART
  ===================== */
  function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
  }

  // โหลด cart ตอนเปิดเว็บ
  renderCart();
});
