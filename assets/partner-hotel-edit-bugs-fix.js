let post_id = document.querySelector(".st-inventory").getAttribute("data-id");

document
  .querySelector(".st-partner-action .st-btn-continue")
  .addEventListener("click", async () => {
    let form_data = new FormData();
    form_data.append("action", "bkr_partner_custom_action");
    form_data.append("nonce", bkr_scripts.nonce);
    form_data.append("post_id", post_id);

    let response = await fetch(bkr_scripts.ajax_url, {
      method: "POST",
      processData: false,
      contentType: false,
      body: form_data,
    });

    console.log(response);
  });
