<?php
    include "include/conexao.php";
    include "include/id.php";
    include "include/login_php.php";
?>

<?php
    include "include/header.php";
    include "include/login.php";
?>

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

<script src="js/login.js"></script>
<script src="js/slide.js"></script>
<?php include "include/footer.php";?>
