<?php
    //alustame sessiooni
    session_start();
    //kas on sisselogitud
    if(!isset($_SESSION["user_id"])){
        header("Location: page.php");
    }
    //väljalogimine
    if(isset($_GET["logout"])){
        session_destroy();
        header("Location: page.php");
    }
	
    require_once("../../config.php");
    require_once("fnc_photoupload.php");
	require_once("fnc_general.php");
	
    
    $alt_text = null;
	$privacy = 1;
	$notice = null;
	$photo_name = null;
	
	if(isset($_POST["photo_submit"])){
		$notice = store_photo($_POST["photo_name"], $_POST["alt_input"], $_POST["privacy_input"]);
	}
    
    require("page_header.php");
?>
	<h1><?php echo $_SESSION["first_name"] ." " .$_SESSION["last_name"]; ?>, veebiprogrammeerimine</h1>
	<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<hr>
    <ul>
        <li><a href="?logout=1">Logi välja</a></li>
		<li><a href="home.php">Avaleht</a></li>
    </ul>
	<hr>
    <h2>Galeriipiltide üleslaadimine</h2>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
        <label for="photo_input">Vali pildifail</label>
        <input type="file" name="photo_input" id="photo_input">
        <br>
		<label for="photo_name">Pildi nimi:</label>
		<input type="text" name="photo_name" id="photo_name" placeholder="pildi nimi" value="<?php echo $photo_name; ?>">
		<br>
        <label for="alt_input">Alternatiivtekst (alt):</label>
        <input type="text" name="alt_input" id="alt_input" placeholder="alternatiivtekst
        ..." value="<?php echo $alt_text; ?>">
        <br>
        <input type="radio" name="privacy_input" id="privacy_input_1" value="1" <?php if($privacy == 1){echo " checked";}?>>
        <label for="privacy_input_1">Privaatne (ainult mina näen)</label>
        <br>
        <input type="radio" name="privacy_input" id="privacy_input_2" value="2" <?php if($privacy == 2){echo " checked";}?>>
        <label for="privacy_input_2">Sisseloginud kasutajatele</label>
        <br>
        <input type="radio" name="privacy_input" id="privacy_input_3" value="3" <?php if($privacy == 3){echo " checked";}?>>
        <label for="privacy_input_3">Avalik (kõik näevad)</label>
        <br>
        
        <input type="submit" name="photo_submit" value="Lae pilt üles">
    </form>
    <span><?php echo photo_upload(); ?></span>
	<span><?php echo $notice; ?></span>
    
</body>
</html>