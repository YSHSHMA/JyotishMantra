$("#NewNumberAdd").change(function () {
  if ($(this).is(":checked")) {
    $("#newPhoneAdd").show();
    $("#newPhoneAdd input[name='newPhone']").prop("required", true);
  } else {
    $("#newPhoneAdd").hide();
    $("#newPhoneAdd input[name='newPhone']").prop("required", false);
  }
});
// Check the Gutra
$(document).ready(function () {
  $("#gotraCheck").change(function () {
    if ($(this).is(":checked")) {
      $("#GotraId").prop("readonly", true).val("Kashyapa");
    } else {
      $("#GotraId").prop("readonly", false).val("");
    }
  });
});
// add the condition button YES ANd NO
var isPrashad = $("#is_prashad").val();
if (isPrashad == 1) {
  $(".hideable-div").show();
  $("button.yes-btn")
    .removeClass("bg-transparent text-black")
    .addClass("bg-warning text-white");
  $("button.no-btn")
    .removeClass("bg-warning text-white")
    .addClass("bg-transparent text-black border-dark");
} else {
  $(".hideable-div").hide();
  $("button.no-btn")
    .removeClass("bg-transparent text-black")
    .addClass("bg-warning text-white");
  $("button.yes-btn")
    .removeClass("bg-warning text-white")
    .addClass("bg-transparent text-black border-dark");
}
// Click event for Yes button
$("button.yes-btn").click(function () {
  $("#is_prashad").val(1);
  $(".hideable-div").show();
  $(this)
    .removeClass("bg-transparent text-black border-dark")
    .addClass("bg-warning text-white");
  $("button.no-btn")
    .removeClass("bg-warning text-white")
    .addClass("bg-transparent text-black border-dark");
});

// Click event for No button
$("button.no-btn").click(function () {
  $("#is_prashad").val(0);
  $(".hideable-div").hide();
  $(this)
    .removeClass("bg-transparent text-black border-dark")
    .addClass("bg-warning text-white");
  $("button.yes-btn")
    .removeClass("bg-warning text-white")
    .addClass("bg-transparent text-black border-dark");
});

// ---------------------------------------------------------------------------------------
$("#editButton").click(function () {
  $("#sankalp_check").toggle();
});
