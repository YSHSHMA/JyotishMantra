function addPoojaProduct(that) {
  that.disabled = true;
  
  var productid = $(that).data('productid');
  var name = $(that).data('name');
  var price = $(that).data('price');
  var image = $(that).data('image');
  var qtyMin = $(that).data('qtymin');
  var leadid = $(that).data('leadid');
  var serviceid = $(that).data('serviceid');
  var poojaprice = $(that).data('poojaprice');

  $.ajax({
      url: addProductCartUrl,
      method: "POST",
      data: {
          productid: productid,
          name: name,
          price: price,
          image: image,
          qtyMin: qtyMin,
          lead: leadid,
          serviceid: serviceid,
          poojaprice: poojaprice,
          _token: csrfToken 
      },
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
          $("#addmore").load(location.href + " #addmore> *");
          $("#checkbutton").load(location.href + " #checkbutton> *");
          $("#productList").load(location.href + " #productList> *");
          $("#price-load").load(location.href + " #price-load >  *");
          $("#checkbutton").load(location.href + " #checkbutton> *");
      },
      error: function(xhr, status, error) {
          that.disabled = false;
          toastr.error('Failed to add product.');
      }
  });
}

function QuantityUpdate(
cartId,
quantity,
updateid,
pprice,
leadid,
poojaprice
) {
var inputBox = $("#cart_quantity_web" + cartId);
var currentQuantity = parseInt(inputBox.val());

if (quantity == -1 && currentQuantity == 1) {
  deleteQuantity(updateid, cartId,leadid, pprice);
  return;
}

var newQuantity = currentQuantity + quantity;

// Ensure minimum quantity is 1
if (newQuantity < 1) {
  return;
}

// Update delete/minus icon logic
if (newQuantity == 1) {
  $("#DeleteIcon" + cartId)
    .addClass("tio-delete text-danger")
    .removeClass("tio-remove");
} else {
  $("#DeleteIcon" + cartId)
    .addClass("tio-remove")
    .removeClass("tio-delete text-danger");
}

// Update quantity in input field
inputBox.val(newQuantity);

// Call function to update backend
ProductQuantity(cartId, newQuantity, updateid, pprice, leadid, poojaprice);
}

function ProductQuantity(
cartId,
newQuantity,
updateid,
pprice,
leadid,
poojaprice
) {
$.ajax({
  url: updateCartQuantityUrl, // Using the global variable from Blade
  method: "POST",
  data: {
    updateid: updateid,
    price: pprice,
    cartId: cartId,
    quantity: newQuantity,
    leadid: leadid,
    poojaprice: poojaprice,
    _token: csrfToken, // Using the global variable for CSRF token
  },
  headers: {
    "X-CSRF-TOKEN": csrfToken,
  },
  success: function (response) {
    var updatedTotal =
      parseInt(poojaprice) + parseInt(response.data.total_amount);
    // $(".productQty"+ updateid).stop(true, true).fadeIn(500).delay(500).fadeOut(500);
    $(".totalProduct" + cartId).text(
      response.data.final_price.final_price + ".00"
    );
    $(".productQty" + cartId).text(newQuantity);
    $("#productCountFinal" + cartId).val(response.data.final_price);
    $("#mainProductPrice").text(updatedTotal + ".00");
    $("#mainProductPriceInput").val(updatedTotal);
    $("#checkbutton").load(location.href + " #checkbutton> *");
    $("#productList").load(location.href + " #productList> *");
    $("#productPrice").load(location.href + " #productPrice> *");
    $("#price-load").load(location.href + " #price-load> *");
    $("#cart-summary").load(location.href + " #cart-summary> *");
    $("#checkbutton").load(location.href + " #checkbutton> *");
  },
  error: function (xhr) {
    console.error(xhr.responseText);
  },
});
}

// Delete Quantity
function deleteQuantity(updateid, cartId, leadid,pprice) {
$.ajax({
  url: deleteCartQuantityUrl,
  method: "POST",
  data: {
    pprice: pprice,
    updateid: updateid,
    leadid: leadid,
    cartId: cartId,
    _token: csrfToken,
  },
  headers: {
    "X-CSRF-TOKEN": csrfToken,
  },
  success: function () {
    window.location.reload();
    toastr.error("Remove the Daan Your Cart.");
  },
  error: function (xhr) {
    toastr.error("Something went wrong!");
  },
});
}