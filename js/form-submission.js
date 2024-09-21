// select elements
let form_contact = document.querySelector(".contact-form");
let form_input = document.querySelector(".name-input");
let form_email = document.querySelector(".email-input");
let text_area = document.querySelector(".textarea-input");
form_contact.addEventListener("submit", (event) => {
  event.preventDefault();
  //send post request to endpoint
  fetch(`${wpApiSettings.root}form-submissions-api/submit`, {
    method: "POST",

    body: JSON.stringify({
      name: form_input.value,
      email: form_email.value,
      query: text_area.value,
    }),

    headers: {
      "Content-Type": "application/json",
      "X-WP-Nonce": wpApiSettings.nonce,
    },
  })
    .then((response) => {
      if (!response.ok) {
        return response.json().then((err) => {
          throw err;
        });
      } else {
        form_contact.innerHTML = `
        <div class="success-message">We appreciate your support and will get back to you very soon!</div>
        `;
        return response.json();
      }
    })
    .catch((error) => {
      console.error(error);
    });
});
