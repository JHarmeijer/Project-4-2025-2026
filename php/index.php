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
    <title>Chef's Choice — Home</title>
  </head>

  <body>

    <!-- HERO SECTIE -->
    <div class="hero">
      <div class="hero-inhoud">
        <h1>Welkom bij Chef's Choice</h1>
        <p>De beste keukenproducten voor thuis en professioneel gebruik. Ontdek ons assortiment en bestel vandaag nog.</p>
        <a href="../php/bestellen.php" class="hero-knop">Bekijk producten</a>
      </div>
    </div>

    <!-- PRODUCTEN SLIDESHOW -->
    <div class="pagina-inhoud">

      <h2 class="sectie-titel">Onze producten</h2>

      <div class="slideshow-wrapper">
        <button class="slideshow-pijl links" onclick="verschuif(-1)">&#8592;</button>
        <div class="slideshow-venster">
          <div class="slideshow-track" id="slideshow-track"></div>
        </div>
        <button class="slideshow-pijl rechts" onclick="verschuif(1)">&#8594;</button>
      </div>

      <!-- Puntjes navigatie -->
      <div class="slideshow-puntjes" id="slideshow-puntjes"></div>

    </div>

    <script>
      let huidigeSlide = 0;
      let totaalSlides = 0;

      /* PRODUCTEN LADEN */
      fetch("producten.php")
        .then(response => response.json())
        .then(producten => {
          const track  = document.getElementById('slideshow-track');
          const puntjes = document.getElementById('slideshow-puntjes');
          totaalSlides = producten.length;

          producten.forEach((product, index) => {
            // Slide aanmaken
            const slide = document.createElement('div');
            slide.classList.add('slide');
            slide.innerHTML = `
              <div class="slide-icoon">🍳</div>
              <div class="slide-naam">${product.product_naam}</div>
              <div class="slide-prijs">€${parseFloat(product.prijs).toFixed(2)}</div>
              <div class="slide-voorraad">${product.voorraad > 0 ? '✓ Op voorraad' : '✗ Uitverkocht'}</div>
              <a href="../php/bestellen.php" class="product-knop">Bestellen</a>
            `;
            track.appendChild(slide);

            // Puntje aanmaken
            const puntje = document.createElement('span');
            puntje.classList.add('puntje');
            if (index === 0) puntje.classList.add('actief');
            puntje.onclick = () => gaNaarSlide(index);
            puntjes.appendChild(puntje);
          });

          updateSlideshow();
        })
        .catch(error => console.error("Producten laden mislukt:", error));

      function verschuif(richting) {
        huidigeSlide = (huidigeSlide + richting + totaalSlides) % totaalSlides;
        updateSlideshow();
      }

      function gaNaarSlide(index) {
        huidigeSlide = index;
        updateSlideshow();
      }

      function updateSlideshow() {
        const track = document.getElementById('slideshow-track');
        track.style.transform = `translateX(-${huidigeSlide * 100}%)`;

        // Puntjes bijwerken
        document.querySelectorAll('.puntje').forEach((p, i) => {
          p.classList.toggle('actief', i === huidigeSlide);
        });
      }

      // Automatisch doorschuiven elke 4 seconden
      setInterval(() => verschuif(1), 4000);
    </script>

  </body>
</html>