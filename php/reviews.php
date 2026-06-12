<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Reviews</title>
  </head>

  <body>

    <!-- PAGINA INHOUD -->
    <div class="pagina-inhoud">

      <form id="review-form">

        <label>Jouw naam</label>
        <input type="text" id="naam" placeholder="Naam"/>

        <label>Over welk product schrijf je een review?</label>
        <select id="product">
          <option value="">Producten laden...</option>
        </select>

        <label>Jouw beoordeling</label>
        <div class="sterren-invoer" id="sterren">
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

      <h2>Geplaatste reviews</h2>

      <div id="filters">
        <div>
          <label>Filter op product</label>
          <select id="filter-product" onchange="filterReviews()">
            <option value="">Alle producten</option>
          </select>
        </div>
        <div>
          <label>Filter op sterren</label>
          <select id="filter-sterren" onchange="filterReviews()">
            <option value="">Alle beoordelingen</option>
            <option value="5">★★★★★ — 5 sterren</option>
            <option value="4">★★★★☆ — 4 sterren</option>
            <option value="3">★★★☆☆ — 3 sterren</option>
            <option value="2">★★☆☆☆ — 2 sterren</option>
            <option value="1">★☆☆☆☆ — 1 ster</option>
          </select>
        </div>
        <div>
          <label>Sorteren op datum</label>
          <select id="filter-datum" onchange="filterReviews()">
            <option value="nieuwste">Nieuwste eerst</option>
            <option value="oudste">Oudste eerst</option>
          </select>
        </div>
      </div>

      <div id="review-lijst"></div>

    </div>

    <script>
      let geselecteerdeSter = 0;
      let alleReviews = [];

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


      /* REVIEWS LADEN */
      function laadReviews() {
        fetch("http://localhost/Project%204%20Chef's%20Choice/Project-4-2025-2026/php/reviews_ophalen.php")
          .then(response => response.json())
          .then(reviews => {
            alleReviews = reviews;

            const filterProduct = document.getElementById('filter-product');
            const productenGezien = [];
            filterProduct.innerHTML = '<option value="">Alle producten</option>';
            reviews.forEach(review => {
              if (!productenGezien.includes(review.product_naam)) {
                productenGezien.push(review.product_naam);
                const optie = document.createElement('option');
                optie.value = review.product_naam;
                optie.textContent = review.product_naam;
                filterProduct.appendChild(optie);
              }
            });

            filterReviews();
          })
          .catch(error => console.error("Reviews laden mislukt:", error));
      }


      /* FILTEREN EN TONEN */
      function filterReviews() {
        const filterProduct = document.getElementById('filter-product').value;
        const filterSterren = document.getElementById('filter-sterren').value;
        const filterDatum   = document.getElementById('filter-datum').value;

        let gefilterd = [...alleReviews];

        if (filterProduct !== '') {
          gefilterd = gefilterd.filter(r => r.product_naam === filterProduct);
        }

        if (filterSterren !== '') {
          gefilterd = gefilterd.filter(r => r.beoordeling == filterSterren);
        }

        if (filterDatum === 'oudste') {
          gefilterd.sort((a, b) => new Date(a.datum) - new Date(b.datum));
        } else {
          gefilterd.sort((a, b) => new Date(b.datum) - new Date(a.datum));
        }

        const lijst = document.getElementById('review-lijst');

        if (gefilterd.length === 0) {
          lijst.innerHTML = '<p>Geen reviews gevonden voor deze filter.</p>';
          return;
        }

        lijst.innerHTML = '';
        gefilterd.forEach(review => {
          const sterren    = '★'.repeat(review.beoordeling) + '☆'.repeat(5 - review.beoordeling);
          const datum      = new Date(review.datum);
          const datumTekst = datum.toLocaleDateString('nl-NL', {
            day: 'numeric', month: 'long', year: 'numeric'
          });

          const kaart = document.createElement('div');
          kaart.classList.add('review-kaart');
          kaart.innerHTML = `
            <div class="product-naam">${review.product_naam}</div>
            <div class="reviewer-naam">${review.naam}</div>
            <div class="review-sterren">${sterren}</div>
            <div class="tekst">${review.tekst}</div>
            <div class="pros-cons">
              <div class="pro-box"><strong>Pluspunt</strong>${review.pluspunt}</div>
              <div class="con-box"><strong>Minpunt</strong>${review.minpunt}</div>
            </div>
            <div class="datum">${datumTekst}</div>
          `;
          lijst.appendChild(kaart);
        });
      }

      laadReviews();


      /* REVIEW VERSTUREN */
      function verstuurReview() {
        const product  = document.getElementById("product").value;
        const naam     = document.getElementById("naam").value.trim();
        const tekst    = document.getElementById("review-tekst").value.trim();
        const pluspunt = document.getElementById("pluspunt").value.trim();
        const minpunt  = document.getElementById("minpunt").value.trim();

        if (!product) { alert("Kies een product."); return; }
        if (geselecteerdeSter === 0) { alert("Geef een sterrenbeoordeling."); return; }
        if (!naam) { alert("Vul je naam in."); return; }
        if (tekst.length < 20) { alert("Je review moet minimaal 20 tekens bevatten."); return; }

        const formData = new FormData();
        formData.append("product_id",  product);
        formData.append("naam",        naam);
        formData.append("beoordeling", geselecteerdeSter);
        formData.append("tekst",       tekst);
        formData.append("pluspunt",    pluspunt);
        formData.append("minpunt",     minpunt);

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
            laadReviews();
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