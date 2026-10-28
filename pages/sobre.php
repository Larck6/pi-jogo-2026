<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";
?>

<?php
    include "../include/header.php";
    include "../include/login.php";
?>

<main class="about-page">
<section class="story-section" aria-labelledby="story-title">
    <div class="story-heading">
        <span>Enredo</span>
        <h2 id="story-title">A História</h2>
    </div>

    <div class="story-text">
        <p>
            Em meio às lendas e mistérios de União da Vitória e Porto União, uma antiga cruz que protegia o vale é destruída, dando início a uma chuva interminável. Enquanto as águas sobem e criaturas do folclore despertam, um jovem viajante embarca em uma jornada por lugares marcados pela história e pelo sobrenatural.
        </p>
        <p>
            Entre cidades sombrias, pontes perigosas e encontros misteriosos, ele deverá enfrentar antigas lendas e descobrir como devolver a paz ao vale antes que tudo seja engolido pela enchente.
        </p>
    </div>
</section>

<section class="game-description" aria-labelledby="game-description-title">
    <div class="game-description-heading">
        <span>Conheça o projeto</span>
        <h2 id="game-description-title">Sobre Lendas do Vale</h2>
    </div>

    <div class="game-description-text">
        <p>
            <strong>Lendas do Vale</strong> é um jogo independente de aventura e plataforma 2D, desenvolvido na <strong>Godot Engine</strong>, com gráficos em pixel art e inspirado nas lendas, histórias e paisagens da região de União da Vitória (PR) e Porto União (SC).
        </p>
        <p>
            O projeto nasceu com o objetivo de transformar elementos da cultura e do folclore regional em uma experiência interativa, misturando exploração, desafios e batalhas contra chefes inspirados em lendas locais.
        </p>
        <p>
            O jogo foi desenvolvido utilizando a linguagem <strong>GDScript</strong>, com cenários, personagens, animações e mecânicas criados especialmente para o projeto. Sua identidade visual utiliza pixel art, com ambientes inspirados em locais conhecidos da região, como a Ponte do Arco, o Rio Iguaçu e os morros que cercam as cidades.
        </p>
        <p>
            Durante a aventura, o jogador enfrenta três grandes chefes: o <strong>Diabo do Baile, a Cobra e o Monge João Maria</strong>, enquanto reúne os fragmentos de uma cruz para acabar com uma chuva misteriosa que ameaça a região.
        </p>
        <p>
            Mais do que um jogo, <strong>Lendas do Vale</strong> busca valorizar a cultura local, apresentando suas histórias e tradições de uma maneira divertida e acessível.
        </p>
    </div>
</section>

<section class="instagram-section" aria-labelledby="instagram-title">
    <div class="instagram-copy">
        <span>Redes sociais</span>
        <h2 id="instagram-title">Acompanhe Lendas do Vale</h2>
        <p>Veja novidades, bastidores e atualizações do desenvolvimento no Instagram oficial do projeto.</p>
    </div>

    <a class="instagram-link" href="https://www.instagram.com/lendasdovale.pi2026/" target="_blank" rel="noopener noreferrer">
        <i class="fa fa-instagram" aria-hidden="true"></i>
        <span>@lendasdovale.pi2026</span>
    </a>
</section>

<div class="seguralendas">
    <h1 class="titulolenda">Chefões</h1>
    <p class="chefes-intro">Conheça as lendas que se tornam grandes confrontos em Lendas do Vale. Clique em um card para descobrir sua história.</p>

    <div class="lendas">
        <button type="button" class="lenda" data-chefe="diabo-do-baile">
            <img src="<?= e(app_url('/Prints/Bosses/diabo.png')) ?>" class="imglenda no-lightbox" alt="Diabo do Baile">
            <span class="nomelenda">Diabo do Baile</span>
            <span class="lenda-acao">Conhecer a história</span>
        </button>
        <button type="button" class="lenda" data-chefe="serpente-do-iguacu">
            <img src="<?= e(app_url('/Prints/Bosses/cobra.png')) ?>" class="imglenda no-lightbox" alt="Serpente do Rio Iguaçu">
            <span class="nomelenda">Serpente do Rio Iguaçu</span>
            <span class="lenda-acao">Conhecer a história</span>
        </button>
        <button type="button" class="lenda" data-chefe="monge-joao-maria">
            <img src="<?= e(app_url('/Prints/Bosses/monge.png')) ?>" class="imglenda no-lightbox" alt="Monge João Maria">
            <span class="nomelenda">Monge João Maria</span>
            <span class="lenda-acao">Conhecer a história</span>
        </button>
    </div>
</div>

<dialog class="chefe-modal" id="chefe-modal" aria-labelledby="chefe-modal-title">
    <button type="button" class="chefe-modal-fechar" aria-label="Fechar história">&times;</button>
    <span class="chefe-modal-label">Lenda do Vale</span>
    <h2 id="chefe-modal-title"></h2>
    <p id="chefe-modal-historia"></p>
</dialog>

<section class="credits-section" aria-labelledby="credits-title">
    <div class="credits-heading">
        <span>Quem fez o jogo</span>
        <h2 id="credits-title">Créditos do projeto</h2>
        <p>
            Lendas do Vale foi desenvolvido pela turma do terceirão do Ensino Médio do Sesc/Senac
            de União da Vitória, com a produção dividida entre diferentes áreas do projeto.
        </p>
    </div>

    <div class="credits-grid">
        <article class="credit-group">
            <h3>Programação do Jogo</h3>
            <ul>
                <li>Kauã</li>
                <li>Eduardo</li>
                <li>Isabelly</li>
                <li>Mariane</li>
            </ul>
        </article>

        <article class="credit-group">
            <h3>Programação do Site</h3>
            <ul>
                <li>Guilherme</li>
                <li>Ricardo</li>
                <li>José</li>
                <li>Julia Lima</li>
            </ul>
        </article>

        <article class="credit-group">
            <h3>Design Gráfico</h3>
            <ul>
                <li>Maria</li>
                <li>Saimon</li>
                <li>Márcio</li>
                <li>Lincoln</li>
            </ul>
        </article>

        <article class="credit-group">
            <h3>Sonoro</h3>
            <ul>
                <li>Isabela</li>
                <li>Ryan</li>
                <li>José</li>
                <li>Ricardo</li>
            </ul>
        </article>

        <article class="credit-group">
            <h3>Game Design</h3>
            <ul>
                <li>Mateus</li>
                <li>João</li>
                <li>Thiarles</li>
                <li>Jhonatan</li>
            </ul>
        </article>

        <article class="credit-group">
            <h3>Marketing</h3>
            <ul>
                <li>Anna</li>
                <li>Julia Lúcio</li>
                <li>Alana</li>
                <li>Gabrieli</li>
            </ul>
        </article>

        <article class="credit-group">
            <h3>Hosts</h3>
            <ul>
                <li>Débora</li>
                <li>Anna Laura</li>
            </ul>
        </article>
    </div>
</section>

<div class="slider-container about-slider">
  <button id="prev">◀</button>

  <div class="slider">
    <div class="slides">

      <div class="group">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_20_39.png')) ?>" alt="Cena do jogo">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_16_46.png')) ?>" alt="Cena do jogo">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_22_28.png')) ?>" alt="Cena do jogo">
      </div>

      <div class="group">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_21_52.png')) ?>" alt="Cena do jogo">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_15_29.png')) ?>" alt="Cena do jogo">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_19_18.png')) ?>" alt="Cena do jogo">
      </div>

      <div class="group">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_22_55.png')) ?>" alt="Cena do jogo">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_17_36.png')) ?>" alt="Cena do jogo">
        <img src="<?= e(app_url('/Prints/Scroll/Imagem do ChatGPT 27 de out. de 2026, 14_16_48.png')) ?>" alt="Cena do jogo">
      </div>

    </div>
  </div>

  <button id="next">▶</button>
</div>
</main>

<script src="../js/slide.js?v=<?= (int) filemtime(dirname(__DIR__) . '/js/slide.js') ?>"></script>
<script src="../js/chefes.js?v=<?= (int) filemtime(dirname(__DIR__) . '/js/chefes.js') ?>"></script>
<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>
