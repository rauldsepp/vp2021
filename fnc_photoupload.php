<?php
    $database = "if21_raul_ra";
    
    function store_photo($filename, $alttext, $privacy){
        $notice = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		//vaatame, kas on profiil olemas
		$stmt = $conn->prepare("SELECT id FROM vpr_userprofiles WHERE userid = ?");
		echo $conn->error;
		$stmt->bind_param("i", $_SESSION["user_id"]);
		$stmt->bind_result($id_from_db);
		$stmt->execute();
		if($stmt->fetch()){
			$stmt->close();
			//lisan
			$stmt = $conn->prepare("INSERT INTO vpr_photos(userid, filename, alttext, privacy) VALUES(?,?,?,?)");
			echo $conn->error;
			$stmt->bind_param("issi", $_SESSION["user_id"], $filename, $alttext, $privacy);
		}
		if($stmt->execute()){
			$_SESSION["filename"] = $_POST["photo_name"];
			$_SESSION["alttext"] = $_POST["alt_input"];
			$_SESSION["privacy"] = $_POST["privacy_input"];
			$notice = "Pilt lisatud!";
		} else {
			$notice = "Pildi lisamisel tekkis viga: " .$stmt->error;
		}
		$stmt->close();
		$conn->close();
		return $notice;
	}
    
    function save_image($image, $file_type, $target){
        $notice = null;
        
        if($file_type == "jpg"){
            if(imagejpeg($image, $target, 90)){
                $notice = "Foto salvestamine õnnestus!";
            } else {
                $notice = "Foto salvestamine ei õnnestunud!";
            }
        }
        
        if($file_type == "png"){
            if(imagepng($image, $target, 6)){
                $notice = "Foto salvestamine õnnestus!";
            } else {
                $notice = "Foto salvestamine ei õnnestunud!";
            }
        }
        
        if($file_type == "gif"){
            if(imagegif($image, $target)){
                $notice = "Foto salvestamine õnnestus!";
            } else {
                $notice = "Foto salvestamine ei õnnestunud!";
            }
        }
        
        return $notice;
    }
	
	function photo_upload(){
		$photo_upload_notice = null;
		$photo_upload_thumb_dir = "upload_photos_thumb/";
		$photo_upload_orig_dir = "upload_photos_orig/";
		$photo_upload_normal_dir = "upload_photos_normal/";
		$photo_file_name_prefix = "vp_";
		$photo_file_size_limit = 1.2 * 1024 * 1024;
		$photo_width_limit = 600;
		$photo_height_limit = 400;
		$thumb_width_limit = 100;
		$thumb_height_limit = 100;
		$image_size_ratio = 1;
		$thumb_size_ratio = 1;
		$file_type = null;
		$file_name = null;
		$watermark_file = "pics/vp_logo_color_w100_overlay.png";
		
		if(isset($_POST["photo_submit"])){
			//var_dump($_POST);
			//var_dump($_FILES);
			
			if(isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])){
				$image_check = getimagesize($_FILES["photo_input"]["tmp_name"]);
				if($image_check !== false){
					if($image_check["mime"] == "image/jpeg"){
						$file_type = "jpg";
					}
					if($image_check["mime"] == "image/png"){
						$file_type = "png";
					}
					if($image_check["mime"] == "image/gif"){
						$file_type = "gif";
					}
					
					//move_uploaded_file($_FILES["photo_input"]["tmp_name"], $person_photo_dir .$_FILES["photo_input"]["name"]);
					
				} else {
					$photo_upload_notice .= "Valitud fail ei ole pilt!!";
				}
			} else {
				$photo_upload_notice .= " Pilt on valimata!";
			}
			
			if(empty($photo_upload_notice) and $_FILES["photo_input"]["size"] > $photo_file_size_limit){
				$photo_upload_notice .= " Pildifail on liiga suur!";
			}
			
			
			if(empty($photo_upload_notice)){
				//teeme failinime
				//genereerin ajatempli
				$time_stamp = microtime(1) * 10000;
				$file_name = $photo_file_name_prefix .$time_stamp ."." .$file_type; 
				
				//muudame pildi suurust
				//loome image objekti ehk pikslikogumi
				if($file_type == "jpg"){
					$my_temp_image = imagecreatefromjpeg($_FILES["photo_input"]["tmp_name"]);
				}
				if($file_type == "png"){
					$my_temp_image = imagecreatefrompng($_FILES["photo_input"]["tmp_name"]);
				}
				if($file_type == "gif"){
					$my_temp_image = imagecreatefromgif($_FILES["photo_input"]["tmp_name"]);
				}
				//pildi originaalmõõdud
				$image_width = imagesx($my_temp_image);
				$image_height = imagesy($my_temp_image);
				if($image_width / $photo_width_limit > $image_height / $photo_height_limit){
					$image_size_ratio = $image_width / $photo_width_limit;
				} else {
					$image_size_ratio = $image_height / $photo_height_limit;
				}
				$image_new_width = round($image_width / $image_size_ratio);
				$image_new_height = round($image_height / $image_size_ratio);
				//loome uue, väiksema pildiobjekti
				$my_new_temp_image = imagecreatetruecolor($image_new_width, $image_new_height);
				imagecopyresampled($my_new_temp_image, $my_temp_image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_width, $image_height);
				
				
				$thumb_width = imagesx($my_temp_image);
				$thumb_height = imagesy($my_temp_image);
				if($thumb_width / $thumb_width_limit > $thumb_height / $thumb_height_limit){
					$thumb_size_ratio = $thumb_width / $thumb_width_limit;
				} else {
					$thumb_size_ratio = $thumb_height / $thumb_height_limit;
				}
				$thumb_new_width = round($thumb_width / $thumb_size_ratio);
				$thumb_new_height = round($thumb_height / $thumb_size_ratio);
				//loome uue, väiksema pildiobjekti
				$my_new_temp_thumb = imagecreatetruecolor($thumb_new_width, $thumb_new_height);
				imagecopyresampled($my_new_temp_thumb, $my_temp_image, 0, 0, 0, 0, $thumb_new_width, $thumb_new_height, $thumb_width, $thumb_height);
				
				
				//lisan vesimärgi
				$watermark = imagecreatefrompng($watermark_file);
				$watermark_width = imagesx($watermark);
				$watermark_height = imagesy($watermark);
				$watermark_x = $image_new_width - $watermark_width - 10;
				$watermark_y = $image_new_height - $watermark_height - 10;
				imagecopy($my_new_temp_image, $watermark, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height);
				imagedestroy($watermark);
							
				//salvestamine
				$photo_upload_notice = save_image($my_new_temp_image, $file_type, $photo_upload_normal_dir .$file_name);
				$photo_upload_notice = save_image($my_new_temp_thumb, $file_type, $photo_upload_thumb_dir .$file_name);
				//kõrvaldame piklsikogumi, et mälu vabastada
				imagedestroy($my_new_temp_image);
				
				imagedestroy($my_temp_image);
				
				
				if(move_uploaded_file($_FILES["photo_input"]["tmp_name"], $photo_upload_orig_dir .$file_name)){
					//pildi info andmebaasi
					
				}
			}
		}//photo_submit
	}