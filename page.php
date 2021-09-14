<?php
	$author_name = "Raul Raudsepp";
	//echo $author_name;	//print
	//vaatan praegust ajahetke
	$full_time_now = date("d.m.Y H:i:s");
	//vaatan nädalapäeva
	$weekday_now = date("N");
	$weekday_names_et = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
	//küsime ainult tunde
	$hour_now = date("H");
	$time_category = "aeg";
	
	$day_category = "tavaline päev";
	if($weekday_now <= 5){
		$day_category = "koolipäev";
		if($hour_now >= 8 and $hour_now <= 18){
			$time_category = "tundide aeg";
		}//if lõpeb
		if($hour_now > 18 and $hour_now < 23){
			$time_category = "vaba aeg";
		}//if lõpeb
		if($hour_now <8 or $hour_now >=23){
			$time_category = "uneaeg";
		}
	} else {
		$day_category = "puhkepäev";
		if($hour_now >= 8 and $hour_now < 23){
			$time_category = "vaba aeg";
		}//if lõpeb
		if($hour_now <8 or $hour_now >=23){
			$time_category = "uneaeg";
		}
	}
	
	//lisan lehele juhusliku foto
	$photo_dir = "photos/";
	//loen kataloogi sisu
	//$all_files = ;
	$all_files = array_slice(scandir($photo_dir), 2);
	//var_dump($all_files);
	
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
	//echo $photo_num;
	//<img src="photo/pilt.jpg" alt="Tallinna Ülikool">
	$photo_html = '<img src="' .$photo_dir .$all_photos[$photo_num] .'" alt="Tallinna Ülikool">';

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title><?php echo $author_name; ?>, veebiprogrammeerimine</title>
</head>
<body>
	<center>
	<h1><?php echo $author_name; ?>, veebiprogrammeerimine</h1>
	<p>See leht on valminud õppetöö raames ja ei sisalda mingit tõsiselt võetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<img src="3700x1100_pildivalik181.jpg" alt="Tallinna Ülikool Mare hoone peauks" width="600">
	<p>Olin tubli ja tegin kodutöö ära!</p>
	<p>Lehe avamise hetk: <span style="color:red;font-weight:bold"><?php echo $weekday_names_et[$weekday_now - 1] .", ".$full_time_now .", on ".$day_category .". Hetkel on " .$time_category . "."; ?></span></p>
	<?php echo $photo_html; ?>
	</center>
</body>
</html>