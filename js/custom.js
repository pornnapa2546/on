$(function () {

  /* ===============================
     GLOBAL CART (หัวใจระบบ)
  =============================== */
  let cart = JSON.parse(localStorage.getItem("cart")) || [];


  /* ===============================
     MAIN MENU / SCROLL
  =============================== */
  $(window).scroll(function () {
    if ($(this).scrollTop() < 50) {
      $("nav").removeClass("site-top-nav");
      $("#back-to-top").fadeOut();
    } else {
      $("nav").addClass("site-top-nav");
      $("#back-to-top").fadeIn();
    }
  });


  /* ===============================
     SHOPPING CART TOGGLE
  =============================== */
  $("#shopping-cart").on("click", function () {
    $("#cart-content").toggle("blind", "", 500);
  });


  /* ===============================
     BACK TO TOP
  =============================== */
  $("#back-to-top").click(function (e) {
    e.preventDefault();
    $("html, body").animate({ scrollTop: 0 }, 1000);
  });


  /* ===============================
     ADD TO CART (รวมสินค้าซ้ำ)
  =============================== */
  $(".btn-add").on("click", function () {
    const box = $(this).closest(".food-menu-box");
    const name = box.find("h4").text();
    const price = parseFloat(box.find(".food-price").text());
    const qty = parseInt(box.find("input[type='number']").val());

    const existing = cart.find(item => item.name === name);

    if (existing) {
      existing.qty += qty;
    } else {
      cart.push({ name, price, qty });
    }

    saveCart();
    renderCart();
  });


  /* ===============================
     DELETE ITEM
  =============================== */
  $(document).on("click", ".btn-delete", function (e) {
    e.preventDefault();
    const index = $(this).data("index");
    cart.splice(index, 1);
    saveCart();
    renderCart();
  });


  /* ===============================
     CLEAR CART
  =============================== */
  $(".clear-cart").on("click", function () {
    cart = [];
    saveCart();
    renderCart();
  });


  /* ===============================
     RENDER CART
  =============================== */
  function renderCart() {
    $(".cart-item").remove();

    let total = 0;

    cart.forEach((item, index) => {
      const itemTotal = item.price * item.qty;
      total += itemTotal;

      const row = `
        <tr class="cart-item">

          <td>${item.name}</td>
          <td>${item.price} ฿</td>
          <td>${item.qty}</td>
          <td>${itemTotal} ฿</td>
          <td>
            <a href="#" class="btn-delete" data-index="${index}">&times;</a>
          </td>
        </tr>
      `;

      $(".cart-total").before(row);
    });

    $(".total-price").text(total + " ฿");
    updateBadge();
  }


  /* ===============================
     BADGE COUNT
  =============================== */
  function updateBadge() {
    let count = 0;
    cart.forEach(item => count += item.qty);
    $(".badge").text(count);
  }


  /* ===============================
     LOCAL STORAGE
  =============================== */
  function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
  }


  /* ===============================
     INIT (โหลดตอนเปิดเว็บ)
  =============================== */
  renderCart();

});
