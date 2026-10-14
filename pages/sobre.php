<?php
    include "../include/conexao.php";
    include "../include/id.php";
    include "../include/login_php.php";
?>

<?php
    include "../include/header.php";
    include "../include/login.php";
?>

<div class="segura2">
    <div class="ladoesq">
        <div class="bloco1">
            <h2 class="titulosobre"></h2>
            <div class="txtsobre1"></div>
        </div>
        <div class="bloco2">
            <h2 class="titulosobre"></h2>
            <div class="txtsobre2"></div>
        </div>
        <div class="bloco3">
            <h2 class="titulosobre"></h2>
            <div class="txtsobre3"></div>
        </div>
    </div>
    <div class="ladodir">
        <div class="txtdireita"></div>

        <!--java script para slide-->
        <div class="slider-degrade">
            <div class="slider_vertical">
                <div class="slides_vertical">
                    <div class="group_vertical">
                        <img src="img1.jpg">
                    </div>

                    <div class="group_vertical">
                        <img src="img4.jpg">
                    </div>

                    <div class="group_vertical">
                        <img src="img7.jpg">
                    </div>
                </div>
            </div>
        </div>

    </div>
</diV>

<div class="seguralendas">
    <h1 class="titulolenda"></h1>

    <div class="lendas">
        <div class="lenda">
            <img src="" class="imglenda">
            <h2 class="nomelenda"></h2>
        </div>
        <div class="lenda">
            <img src="" class="imglenda">
            <h2 class="nomelenda"></h2>
        </div>
        <div class="lenda">
            <img src="" class="imglenda">
            <h2 class="nomelenda"></h2>
        </div>
    </div>
</div>

<div class="slider-container">
  <button id="prev">◀</button>

  <div class="slider">
    <div class="slides">

      <div class="group">
        <img src="img1.jpg">
        <img src="img2.jpg">
        <img src="img3.jpg">
      </div>

      <div class="group">
        <img src="img4.jpg">
        <img src="img5.jpg">
        <img src="img6.jpg">
      </div>

      <div class="group">
        <img src="img7.jpg">
        <img src="img8.jpg">
        <img src="img9.jpg">
      </div>

    </div>
  </div>

  <button id="next">▶</button>
</div>

<script src="../js/slide.js"></script>
<script src="../js/login.js"></script>
<?php include "../include/footer.php";?>