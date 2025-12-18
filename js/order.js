$(function () {

  // ดึง cart จาก localStorage
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  let total = 0;

  // ถ้าไม่มีสินค้า
  if (cart.length === 0) {
    $("#order-items").html(`
      <tr>
        <td colspan="7" style="text-align:center">
          ไม่มีสินค้าในตะกร้า
        </td>
      </tr>
    `);
    return;
  }

  // แสดงสินค้า
  cart.forEach((item, index) => {
    const itemTotal = item.price * item.qty;
    total += itemTotal;

    $("#order-items").append(`
      <tr>
        <td>${index + 1}</td>
        <td>${item.name}</td>
        <td>${item.price} ฿</td>
        <td>${item.qty}</td>
        <td>${itemTotal} ฿</td>
        <td>
          <a href="#" class="remove-item" data-index="${index}">×</a>
        </td>
      </tr>
    `);
  });

  // แสดงราคารวม
  $("#order-total").text(total + " ฿");

  // ลบสินค้าในหน้า order
  $(document).on("click", ".remove-item", function (e) {
    e.preventDefault();
    const index = $(this).data("index");
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    location.reload();
  });

  // ยืนยันออเดอร์
  $("#confirm-order").on("click", function () {
    alert("สั่งซื้อเรียบร้อยแล้ว");
    localStorage.removeItem("cart");
    window.location.href = "index.html";
  });

});

$("#confirm-order").on("click", function () {
  const slip = $("#slip").val();

  if (!slip) {
    alert("กรุณาแนบสลิปการโอนเงิน");
    return;
  }

  alert("ยืนยันคำสั่งซื้อเรียบร้อย 🎉");

  // เคลียร์ตะกร้า
  localStorage.removeItem("cart");
});
