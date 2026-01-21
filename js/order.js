$(function () {

  /* ===============================
     LOAD CART
  =============================== */
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let totalPrice = 0;

  /* ===============================
     EMPTY CART
  =============================== */
  if (cart.length === 0) {
    $("#order-items").html(`
      <tr>
        <td colspan="7" style="text-align:center">
          ไม่มีสินค้าในตะกร้า
        </td>
      </tr>
    `);
    $("#order-total").text("0 ฿");
    return;
  }

  /* ===============================
     RENDER ORDER ITEMS
  =============================== */
  cart.forEach((item, index) => {
    const itemTotal = item.price * item.qty;
    totalPrice += itemTotal;

    $("#order-items").append(`
      <tr>
        <td>${index + 1}</td>
        <td>
          ${item.name}<br>
          <small>🌡 ${item.temp} | 🍬 ${item.sweet}</small>
        </td>
        <td>${item.price} ฿</td>
        <td>${item.qty}</td>
        <td>${itemTotal} ฿</td>
        <td>
          <a href="#" class="remove-item" data-index="${index}">×</a>
        </td>
      </tr>
    `);
  });

  $("#order-total").text(totalPrice + " ฿");

  /* ===============================
     REMOVE ITEM
  =============================== */
  $(document).on("click", ".remove-item", function (e) {
    e.preventDefault();
    const index = $(this).data("index");
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    location.reload();
  });

  /* ===============================
     CONFIRM ORDER
  =============================== */
  $("#confirm-order").on("click", function () {

    const slip = document.getElementById("slip")?.files[0];
    if (!slip) {
      alert("กรุณาอัปโหลดสลิป");
      return;
    }

    const customerName = $("input[name='customer_name']").val();
    const phone        = $("input[name='phone']").val();

    if (!customerName || !phone) {
      alert("กรุณากรอกชื่อและเบอร์โทร");
      return;
    }

    let formData = new FormData();
    formData.append("slip", slip);
    formData.append("total", totalPrice);
    formData.append("customer_name", customerName);
    formData.append("phone", phone);
    formData.append("cart", JSON.stringify(cart));

    $.ajax({
      url: "save-order.php",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function () {
        alert("สั่งซื้อสำเร็จ รอแอดมินตรวจสอบ");
        localStorage.removeItem("cart");
        window.location.href = "index.php";
      },
      error: function () {
        alert("เกิดข้อผิดพลาด กรุณาลองใหม่");
      }
    });
  });

  /* ===============================
     PROMPTPAY QR (ถ้ามีฟังก์ชัน)
  =============================== */
  if (typeof generatePromptPayQR === "function") {
    generatePromptPayQR(totalPrice);
  }

});
