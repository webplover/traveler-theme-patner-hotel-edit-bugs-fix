// check if continue button available then move next
if (document.querySelector(".st-partner-action .st-btn-continue")) {
  let hotel_edit_screen = document.querySelector(".st-inventory");
  let room_edit_screen = document.querySelector(
    ".calendar-wrapper.template-user"
  );

  let post_id =
    (hotel_edit_screen && hotel_edit_screen.getAttribute("data-id")) ||
    (room_edit_screen && room_edit_screen.getAttribute("data-post-id"));

  let edit_screen = hotel_edit_screen ? "hotel" : "room";

  let parent_hotel_id =
    document.querySelector("#st-field-room_parent") &&
    document.querySelector("#st-field-room_parent").value;

  document
    .querySelector(".st-partner-action .st-btn-continue")
    .addEventListener("click", async () => {
      let form_data = new FormData();
      form_data.append("action", "bkr_partner_custom_action");
      form_data.append("nonce", bkr_scripts.nonce);
      form_data.append("post_id", post_id);
      form_data.append("edit_screen", edit_screen);
      form_data.append("parent_hotel_id", parent_hotel_id);

      let response = await fetch(bkr_scripts.ajax_url, {
        method: "POST",
        processData: false,
        contentType: false,
        body: form_data,
      });
    });
}
