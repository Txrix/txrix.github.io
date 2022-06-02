<?php

session_start();

// Define values
define('OAUTH2_CLIENT_ID', '981607718630604841');
define('REDIRECT', 'http://localhost:84/');
define('OAUTH2_CLIENT_SECRET', 'B4IzBrX2FTq64NuAVu3fYzVCc0jQjdHu');
$GLOBALS['base_url'] = "https://discord.com";

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';
$revokeURL = 'https://discordapp.com/api/oauth2/token/revoke';

if(get('action') == 'login'){
    $params = array(
        'client_id' => OAUTH2_CLIENT_ID,
        'redirect_uri' => REDIRECT,
        'response_type' => 'code',
        //'scope' => 'identify'
        'scope' => 'identify guilds guilds.join'
      );
    
      // Redirect the user to Discord's authorization page
      header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
      die();
}

if(get('code')){

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => REDIRECT,
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;
  //var_dump($logout_token);
  get_user();

  changerole($logout_token, $_SESSION['user_id']);

  //header('Location: ' . $_SERVER['PHP_SELF']);
  die();
}

if(get('action') == 'logout') {
    apiRequest($revokeURL, array(
        'token' => session('access_token'),
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
      ));
 # Starting the session
session_start();

# Closing the session and deleting all values associated with the session
session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    die();
  }

  function apiRequest($url, $post=FALSE, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  
    $response = curl_exec($ch);
  
  
    if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  
    $headers[] = 'Accept: application/json';
  
    if(session('access_token'))
      $headers[] = 'Authorization: Bearer ' . session('access_token');
  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
    $response = curl_exec($ch);
    return json_decode($response);
  }
  # A function to get user information | (identify scope)
function get_user($email = null)
{
    $url = $GLOBALS['base_url'] . "/api/users/@me";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['access_token']);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    $_SESSION['user'] = $results;
    $_SESSION['username'] = $results['username'];
    $_SESSION['discrim'] = $results['discriminator'];
    $_SESSION['user_id'] = $results['id'];
    $_SESSION['user_avatar'] = $results['avatar'];
    # Fetching email 
    if ($email == True) {
        $_SESSION['email'] = $results['email'];
    }
}
  
  
function get($key, $default=NULL) {
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}
  
  
function session($key, $default=NULL) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}


function changerole($access_token, $userDiscord) {

	$data = json_encode(array(
		"access_token" => $access_token,
		"roles" => array('901410980330893343','958440393282842694')
		));
    $url = "https://discord.com/api/guilds/857335091994820648/members/" . $userDiscord;
    $headers = array ('Content-Type: application/json', 'Authorization: Bot OTgxNjA3NzE4NjMwNjA0ODQx.Gj-o3z.zZA5cIDnyb5NRy5rDpDwkTaV1QIOYndiGOPEU8');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
    $response = curl_exec($curl);
    curl_close($curl);
     $results = json_decode($response, true);
}


?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>MetaversoRP | FiveM</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=ABeeZee">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abel">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Actor">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" integrity="sha384-Bfad6CLCknfcloXFOyFnlgtENryhrpZCe29RTifKEixXQZ38WheV+i/6YWSzkz3V" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body onload="open()" style=" background: #170000; user-select: none;">
    <div id="bdy2" style="transition: opacity 600ms, visibility 600ms; opacity: 0; visibility: hidden; display: none;margin: 0;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);width: 340px;height: 250px;border-radius: 10px;background: #261a1a;box-shadow: 0px 0px 9px 1px rgba(0,0,0,0.26);">
        <div class="card-body"><img src="assets/img/METAVERSO_3.png" style="width: 300px;margin-left: auto; text-align: center;left: 50%;margin-top: 10px;">
        <?php if (isset($_SESSION['username'])) :?>
            <a href="?action=logout"> <button class="btn custom_btn" type="button" style="margin-top: 10px"><i class="fab fa-discord"></i> Logout</button></a>
        <?php else : ?>
            <a href="?action=login"> <button class="btn custom_btn" type="button" style="margin-top: 10px"><i class="fab fa-discord"></i> Login</button></a>
        <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['username'])) :?>

<h2 style="color: white"> User Details :</h2>
<p style="color: white"> Name : <?php echo $_SESSION['username'] . '#' . $_SESSION['discrim']; ?></p>
<p style="color: white"> ID : <?php echo $_SESSION['user_id']; ?></p>

<?php endif; ?>

    <div id="bdy" style="transition: opacity 600ms, visibility 600ms; opacity: 0; visibility: hidden; display: none;margin: 0;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);width: 700px;height: 427px;border-radius: 10px;background: #e4e4e400;box-shadow: 0px 0px 9px 1px rgba(0,0,0,0.0);">
        <div class="lds-ellipsis" style="margin: 0;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>

        <p id="Response" style="font-family: Poppins, sans-serif;text-align: center;color: rgb(187,187,187); margin: 0;position: absolute;top: 80%;left: 50%;transform: translate(-50%, -50%);">
            loading...</p>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>

    <script>
        function open() {
            var settingspage = document.getElementById("bdy")
            settingspage.style.visibility = "visible";
            settingspage.style.opacity = "1";
            settingspage.style.animation = "fade 0.5s";

            settingspage.style.display = "block";


            setTimeout(function() {
                var settingspage = document.getElementById("bdy")
                settingspage.style.opacity = "0";
                settingspage.style.visibility = "hidden";

                var settingspage2 = document.getElementById("bdy2")
                settingspage2.style.visibility = "visible";
                settingspage2.style.opacity = "1";
                settingspage2.style.animation = "fade 0.5s";

                settingspage2.style.display = "block";

            }, 6500);

        }
    </script>

</body>

</html>