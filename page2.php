<?php
	$author_name = "Raul Raudsepp";
	
	//vaatan, mida post meetodil saadeti
	//var_dump($_POST);
	
	$today_html = null;
	$adjective_error = null;
	$todays_adjective = null;
	//kontrollin, kas klikiti subit
	if(isset($_POST["submit_todays_adjective"])){
		//echo "Klikiti nuppu!";
		if(!empty($_POST["todays_adjective_input"])){
			$today_html = "<p> Tänane päev on " .$_POST["todays_adjective_input"] .".</p>";
			$todays_adjective = $_POST["todays_adjective_input"];
		} else {
			$adjective_error = "Palun kirjutage tänase kohta omadussõna!";
		}
	}
	
	//lisan lehele juhusliku foto
	$photo_dir = "photos/";
	$all_files = array_slice(scandir($photo_dir), 2);
	
	//kontrollin ja võtan ainult fotod
	$allowed_photo_types = ["image/jpeg", "image/png"];
	$all_photos = [];
	foreach($all_files as $file){
		$file_info = getimagesize($photo_dir .$file);
		if(isset($file_info["mime"])){
			if(in_array($file_info["mime"], $allowed_photo_types)){
				array_push($all_photos, $file);				
			}//if in_array lõpeb
		}//if isset lõpeb
	}// foreach lõpeb
	
	$file_count = count($all_photos);
	$photo_num = mt_rand(0, $file_count - 1);
	$photo_html = '<img src="' .$photo_dir .$all_photos[$photo_num] .'" alt="Tallinna Ülikool">';
	
	$photo_list_html = "\n<ul> \n";
	//tsükkel
	//for($i=algväärtus; $i < piirväärtus; $i muutumine){...}

	//<ul>
	//<li>pildifail1.jpg</li>
	//...
	//<li>pildifailn.jpg</li>
	//</ul>

	for($i = 0; $i < $file_count; $i++){
		$photo_list_html .= "<li>" .$all_photos[$i] ."</li> \n";
	}
	$photo_list_html .= "</ul> \n";
	
	$photo_select_html = '<select name="photo_select"' ."\n";
	for($i = 0; $i < $file_count; $i++){
		$photo_select_html .= '<option value="' .$i .'" ' .$all_photos[$i] ."</option> \n";
	}
	$photo_select_html .= "</select> \n";

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title><?php echo $author_name; ?>, veebiprogrammeerimine</title>
</head>
<body>
	<h1><?php echo $author_name; ?>, veebiprogrammeerimine</h1>
	<p>See leht on valminud õppetöö raames ja ei sisalda mingit tõsiselt võetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<hr>
	<!--ekraanivorm-->
	<form method="POST">
		<input type="text" name="todays_adjective_input" placeholder="tänase päeva ilma omadus" value="<?php echo $todays_adjective; ?>">
		<input type="submit" name="submit_todays_adjective" value="Saada ära">
		<span><?php echo $adjective_error; ?></span>
	</form>
	<?php echo $today_html; ?>
	<hr>
	
	<form method="_POST">
		<?php echo $photo_select_html; ?>
	</form>
	
	<?php 
		echo $photo_html; 
		echo $photo_list_html;
	?>
</body>
</html>