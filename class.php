<?php
/* 
Kodlama by Erjan Ulujan
Bot eğer çalışmaz ise tarayıcınızdan instagrama giriş yapın ve EditThisCookie eklentisini kurup csrf token ve mid'i değiştiriniz.
*/
class instaCreator {

	protected $csrf_token = 'oFvlN2FYqUL4zxHy3vzgWhjRVi73E6cw';
	protected $mid_token = 'W3ErsgAEAAESdBKPIGvLPDpGH3Ha';

	private function connectInstagram($username, $password, $email, $full_name, $proxy){

		$channel = curl_init();
		curl_setopt($channel, CURLOPT_URL, "https://www.instagram.com/accounts/web_create_ajax/");
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($channel, CURLOPT_POSTFIELDS, "email=".$email."&password=".$password."&username=".$username."&first_name=".$full_name."&seamless_login_enabled=1&tos_version=row");
		curl_setopt($channel, CURLOPT_POST, 1);
		curl_setopt($channel, CURLOPT_ENCODING, 'gzip, deflate');
		if($proxy){
			curl_setopt($channel, CURLOPT_PROXY, $proxy);
			curl_setopt($channel, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
		}

		$headers = array();
		$headers[] = "Host: www.instagram.com";
		$headers[] = "Cookie: fbm_124024574287414=base_domain=.instagram.com; rur=PRN; csrftoken={$this->csrf_token}; mid={$this->mid_token}; fbm_124024574287414=\"base_domain=.instagram.com\"; mcd=1";
		$headers[] = "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.124 Safari/537.36";
		$headers[] = "Origin: https://www.instagram.com";
		$headers[] = "X-Instagram-Ajax: 8958fe1e75ab";
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
		$headers[] = "Accept: */*";
		$headers[] = "X-Requested-With: XMLHttpRequest";
		$headers[] = "Save-Data: on";
		$headers[] = "X-Csrftoken: {$this->csrf_token}";
		$headers[] = "Referer: https://www.instagram.com/";
		$headers[] = "Accept-Language: tr-TR,en-US,en;q=0.8,id;q=0.6";

		curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($channel);
		if(curl_errno($channel)){
			echo 'Error:' . curl_error($channel);
		}
		curl_close($channel);
		return $result;
	}

	private function getUser(){
		$channel = curl_init();
		curl_setopt($channel, CURLOPT_URL,"http://api.randomuser.me/?nat=tr");
		curl_setopt($channel, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0");
		curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($channel, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($channel);
		curl_close($channel);
		return $result;
	}

	private function replaceTurkish($text){
		$text = trim($text);
		$search = array('Ç','ç','Ğ','ğ','ı','İ','Ö','ö','Ş','ş','Ü','ü',' ');
		$replace = array('c','c','g','g','i','i','o','o','s','s','u','u','-');
		$new_text = str_replace($search,$replace,$text);
		return $new_text;
	}

	private function generateNumbers($char, $length = 3){
		$characters = $char;
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
	}

    private function getProxy(){
        $proxyFile = @fopen('proxylist.txt', 'r');
        if($proxyFile){
            $getProxies = explode(PHP_EOL, fread($proxyFile, filesize('proxylist.txt')));
        }
        $getRandom = (count($getProxies) > 0) ? $getProxies[rand(0, (count($getProxies) - 1))] : NULL;
        return $getRandom;
    }

	public function userCreate($count = 1, $sleep = 10){
		do {
			for($i = 1; $i < $count; $i++){
				$randomUser = $this->getUser();
				$randomUser = json_decode($randomUser);
				$randomUser_First_Name = $randomUser->results[0]->name->first;
				$randomUser_Last_Name = $randomUser->results[0]->name->last;
				$randomUser_Full_Name = $this->replaceTurkish("{$randomUser_First_Name} {$randomUser_Last_Name}");
				$randomUser_User_Name = $this->replaceTurkish("{$randomUser_First_Name}{$randomUser_Last_Name}".$this->generateNumbers('1234567890'));
				$randomUser_Email_Domain = array('hotmail.com','gmail.com','icloud.com','outlook.com');
				$randomUser_Email_Adress = "{$randomUser_User_Name}@".$randomUser_Email_Domain[mt_rand(0, count($randomUser_Email_Domain) - 1)];
				$randomUser_Password = "ankara123";
				$randomUser_Proxy = $this->getProxy();
				$randomUser_Save_Docs = "users.html";

				$userCreate = $this->connectInstagram($randomUser_User_Name, $randomUser_Password, $randomUser_Email_Adress, $randomUser_Full_Name, $randomUser_Proxy);
				$userCreate = json_decode($userCreate);
				if($userCreate->account_created == "true"){
					$file   = fopen(''.$randomUser_Save_Docs.'', 'r+') or die("file not found!");;
					$get    = fgets($file);
					$catat  = fwrite($file, ''.$randomUser_User_Name.':'.$randomUser_Password.'<br>');
					fclose($file);
					echo "[!] ".$i.". hesap olusturma basarili: ".$randomUser_User_Name.":".$randomUser_Password."\n";
				}else{
					echo "[!] ".$i.". hesap olusturma basarisiz: ".$randomUser_User_Name.":".$randomUser_Password."\n";
				}
				sleep($sleep);
			}
			
		}while($count == 'false');
	}
}


