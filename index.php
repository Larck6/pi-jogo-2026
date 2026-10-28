<?php
    include "include/conexao.php";
    include "include/id.php";
    include "include/login_php.php";
?>

<?php
    include "include/header.php";
    include "include/login.php";
?>

<main class="home-hero">
  <section class="home-copy">
    <h1>Lendas do Vale</h1>
    <p class="home-description">
      Uma aventura inspirada nas lendas da cidade de União da Vitória, onde histórias locais ganham vida
      em cenários misteriosos, desafios e descobertas pelo vale.
    </p>
    <div class="home-actions">
      <a class="install-button" href="<?= e(app_url('/pages/download.php')) ?>">
        <i class="fa fa-download"></i>
        Instalar o jogo
      </a>
      <a class="home-secondary" href="<?= e(app_url('/pages/tudo.php')) ?>">Ver comunidade</a>
    </div>
  </section>

  <section class="home-showcase">
    <div class="slider-container">
      <button id="prev">◀</button>

      <div class="slider">
        <div class="slides">

          <div class="group">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_15_29.png')) ?>" alt="Cena do jogo">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_17_36.png')) ?>" alt="Cena do jogo">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_21_25.png')) ?>" alt="Cena do jogo">
          </div>

          <div class="group">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_16_48.png')) ?>" alt="Cena do jogo">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_22_55.png')) ?>" alt="Cena do jogo">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_19_18.png')) ?>" alt="Cena do jogo">
          </div>

          <div class="group">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_22_28.png')) ?>" alt="Cena do jogo">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_16_46.png')) ?>" alt="Cena do jogo">
            <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_21_52.png')) ?>" alt="Cena do jogo">
          </div>

        </div>
      </div>

      <button id="next">▶</button>
    </div>
  </section>
</main>

<script src="js/login.js"></script>
<script src="js/slide.js?v=<?= (int) filemtime(__DIR__ . '/js/slide.js') ?>"></script>
<?php include "include/footer.php";?>
