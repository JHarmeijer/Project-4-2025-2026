<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";
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

    <title>Home</title>
</head>
<body>

<!-- ============================================
     HERO SECTIE
     ============================================ -->
<section class="hero">
  <div class="hero-overlay">
    <div class="hero-inhoud">
        <h1>Welkom bij Chef's Choice</h1>
        <p>De beste keukenproducten voor thuis en professioneel gebruik. Ontdek ons assortiment en bestel vandaag nog.</p>
        <a href="../php/product.php" class="hero-knop">Bekijk producten</a>
    </div>
  </div>
</section>

<!-- ============================================
     PRODUCTEN SLIDESHOW
     ============================================ -->
<section class="pagina-inhoud">

    <h2 class="sectie-titel">Onze producten</h2>

    <div class="slideshow-wrapper">
        <button class="slideshow-pijl links" onclick="verschuif(-1)">&#8592;</button>

        <div class="slideshow-venster">
            <div class="slideshow-track" id="slideshow-track"></div>
        </div>

        <button class="slideshow-pijl rechts" onclick="verschuif(1)">&#8594;</button>
    </div>

    <div class="slideshow-puntjes" id="slideshow-puntjes"></div>

</section>

<script>
/* ============================================
   SLIDESHOW STATUS
   ============================================ */
let huidigeSlide  = 0;
let totaalSlides  = 0;


/* ============================================
   PRODUCTEN LADEN EN SLIDES OPBOUWEN
   ============================================ */
function laadProducten() {
    fetch("producten.php")
        .then(response => response.json())
        .then(producten => bouwSlideshow(producten))
        .catch(error => console.error("Producten laden mislukt:", error));
}

function bouwSlideshow(producten) {
    const track   = document.getElementById('slideshow-track');
    const puntjes = document.getElementById('slideshow-puntjes');

    totaalSlides   = producten.length;
    track.innerHTML   = '';
    puntjes.innerHTML = '';

    producten.forEach((product, index) => {
        track.appendChild(maakSlide(product));
        puntjes.appendChild(maakPuntje(index));
    });

    updateSlideshow();
}

function maakSlide(product) {
    const slide = document.createElement('div');
    slide.classList.add('slide');

    const heeftFoto = product.img && product.img.trim() !== '';
    const visueel = heeftFoto
        ? `<img src="${product.img}" alt="${product.product_naam}" class="slide-foto" onerror="this.onerror=null; this.replaceWith(maakFotoFallback());">`
        : `<div class="slide-icoon">🍳</div>`;

    slide.innerHTML = `
        ${visueel}
        <div class="slide-naam">${product.product_naam}</div>
        <div class="slide-prijs">€${parseFloat(product.prijs).toFixed(2)}</div>
        <div class="slide-voorraad">${product.voorraad > 0 ? '✓ Op voorraad' : '✗ Uitverkocht'}</div>
        <a href="../php/bestellen.php" class="product-knop">Bestellen</a>
    `;
    return slide;
}

function maakFotoFallback() {
    const div = document.createElement('div');
    div.classList.add('slide-icoon');
    div.textContent = '🍳';
    return div;
}

function maakPuntje(index) {
    const puntje = document.createElement('span');
    puntje.classList.add('puntje');
    if (index === 0) puntje.classList.add('actief');
    puntje.onclick = () => gaNaarSlide(index);
    return puntje;
}


/* ============================================
   SLIDESHOW NAVIGATIE
   ============================================ */
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

    document.querySelectorAll('.puntje').forEach((puntje, index) => {
        puntje.classList.toggle('actief', index === huidigeSlide);
    });
}


/* ============================================
   INITIALISATIE
   ============================================ */
laadProducten();
setInterval(() => verschuif(1), 4000);
</script>

</body>
</html>