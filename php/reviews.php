<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../css/style.css">
    <title>Reviews</title>
  </head>

  <body>
    <form id="review-form">

      <label>Jouw naam</label>
      <input type="text" id="naam" placeholder="Naam"/>

      <label>Over welk product schrijf je een review?</label>
      <select id="product">
        <option value="">Producten laden...</option>
      </select>

      <label>Jouw beoordeling</label>
      <div class="sterren" id="sterren">
        <span onclick="setSter(1)">★</span>
        <span onclick="setSter(2)">★</span>
        <span onclick="setSter(3)">★</span>
        <span onclick="setSter(4)">★</span>
        <span onclick="setSter(5)">★</span>
      </div>

      <label>Jouw review</label>
      <textarea id="review-tekst" placeholder="Wat vind je van het product?"></textarea>

      <label>Pluspunt</label>
      <input type="text" id="pluspunt" placeholder="Wat vond je goed aan het product?"/>

      <label>Minpunt</label>
      <input type="text" id="minpunt" placeholder="Wat vond je minder aan het product?"/>

      <button type="button" onclick="verstuurReview()">Review plaatsen</button>

    </form>

<script>
let geselecteerdeSter = 0;

/* PRODUCTEN LADEN */
fetch("http://localhost/Project%204%20Chef's%20Choice/Project-4-2025-2026/php/producten.php")
  .then(response => response.json())
  .then(producten => {
    const select = document.getElementById("product");

    select.innerHTML = '<option value="">Kies een product...</option>';

    producten.forEach(product => {
      const optie = document.createElement("option");
      optie.value = product.product_ID;
      optie.textContent = `${product.product_naam} — €${product.prijs}`;
      select.appendChild(optie);
    });
  })
  .catch(error => console.error("Producten laden mislukt:", error));


/* STERREN SELECTEREN */
function setSter(aantal) {
  geselecteerdeSter = aantal;

  const sterren = document.querySelectorAll("#sterren span");

  sterren.forEach((ster, index) => {
    ster.classList.toggle("actief", index < aantal);
  });
}


/* REVIEW VERSTUREN */
function verstuurReview() {
  const product  = document.getElementById("product").value;
  const naam     = document.getElementById("naam").value.trim();
  const tekst    = document.getElementById("review-tekst").value.trim();
  const pluspunt = document.getElementById("pluspunt").value.trim();
  const minpunt  = document.getElementById("minpunt").value.trim();

  if (!product) {
    alert("Kies een product.");
    return;
  }

  if (geselecteerdeSter === 0) {
    alert("Geef een sterrenbeoordeling.");
    return;
  }

  if (!naam) {
    alert("Vul je naam in.");
    return;
  }

  if (tekst.length < 20) {
    alert("Je review moet minimaal 20 tekens bevatten.");
    return;
  }

  const formData = new FormData();
  formData.append("product_id", product);
  formData.append("naam", naam);
  formData.append("beoordeling", geselecteerdeSter);
  formData.append("tekst", tekst);
  formData.append("pluspunt", pluspunt);
  formData.append("minpunt", minpunt);

  fetch("http://localhost/Project%204%20Chef's%20Choice/Project-4-2025-2026/php/opslaan.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.succes) {
      alert("Bedankt! Je review is geplaatst.");

      document.getElementById("review-form").reset();
      geselecteerdeSter = 0;

      document.querySelectorAll("#sterren span").forEach(ster => {
        ster.classList.remove("actief");
      });

    } else {
      alert("Fout: " + data.bericht);
    }
  })
  .catch(error => {
    console.error("Server error:", error);
    alert("Er ging iets mis met de server.");
  });
}
  </script>
  </body>
</html>