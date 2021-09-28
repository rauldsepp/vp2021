<?php
	$database = "if21_raul_ra";
	
	function sign_up($firstname, $surname, $email, $gender, $birth_date, $password){
		$notify = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		$stmt = $conn->prepare("INSERT INTO vpr_users (firstname, lastname, birthdate, gender, email, password) VALUES (?,?,?,?,?,?)");
		echo $conn->error;
		//krüpteerime salasõna
		$option = ["cost"=>12];
		$pwd_hash = password_hash($password, PASSWORD_BCRYPT, $option);
		$stmt->bind_param("sssiss", $firstname, $surname, $birth_date, $gender, $email, $pwd_hash);
		if($stmt->execute()){
			$notify = "Uus kasutaja edukalt loodud!";
		} else {
			$notify = "Uue kasutaja loomisel tekkis viga: " .$stmt->error;
		}
		$stmt->close();
		$conn->close();
		return $notify;
	}
	
	function sign_in($email, $password){
		$notify = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		$stmt = $conn->prepare("SELECT id, firstname, lastname, password FROM vpr_users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->bind_result($id_from_db, $firstname_from_db, $lastname_from_db, $password_from_db);
		echo $conn->error;
		$stmt->execute();
		if($stmt->fetch()){
			//kasutaja on olemas, kontrollime parooli
			if(password_verify($password, $password_from_db)){
				//ongi õige
				$stmt->close();
				$conn->close();
				header("Location: home.php");
				exit();
			} else {
				$notify = "Kasutajanimi või salasõna oli vale!";
			}
		} else {
			$notify = "Kasutajanimi või salasõna oli vale!";
		}
		
		
		
		$stmt->close();
		$conn->close();
		return $notify;
	}