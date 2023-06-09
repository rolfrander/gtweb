<?php

$success = include 'Config.php';
if(!$success) {
    class Config
    {
        const DB_USER = 'godliruv_web';
        const DB_PASSWORD = 'web';
        const RECAPTCHA_SECRET = null;
        const HELLO = 'hello world!';
        const EPOST_MEDLEMSANSVARLIG = 'rolfrander@gmail.com';
    }
}
$adjektiv = array("liten", "stor", "gul", "blid", "fin", "rar", "morsom", "ullen", "sur", "glad");
$substantiv = array("hest", "bil", "telefon", "vegg", "stol", "ovn", "lampe", "blokk", "boks", "sokk");
$felt = array("navn", "etternavn", "fdato", "adresse", "postnr", "poststed", "tlf",
              "f1_navn", "f1_etternavn", "f1_adresse", "f1_postnr", "f1_poststed", "f1_tlf", "f1_epost",
              "f2_navn", "f2_etternavn", "f2_adresse", "f2_postnr", "f2_poststed", "f2_tlf", "f2_epost",
              "instr1", "instr2", "instr3", "samtykke_registrering", "samtykke_reklame", "samtykke_soknad", "skole", "kommentarer");

function feilkode($feilmelding)
{
    global $adjektiv, $substantiv;
    $a = $adjektiv[rand(0, count($adjektiv)-1)];
    $s = $substantiv[rand(0, count($substantiv)-1)];
    error_log("feilkode [".$a." ".$s."]: ".$feilmelding);
    header('X-GT-Error: '.$a." ".$s);
    return $a." ".$s;
}

function tr($f, $v)
{
  global $_POST;
  return '<tr><td>' . $f . '</td><td>' . strip_tags($_POST[$v]) . "</td></tr>\n";
}

// only for testing...
//header('Access-Control-Allow-Origin: *');
header('Access-Control-Expose-Headers: X-GT-Error');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $feil = array();
    $feilfelt = array();
    $pnrfeil = false;

    if(Config::RECAPTCHA_SECRET) {
      #
      # Verify captcha
      $post_data = http_build_query(
        array(
          'secret' => Config::RECAPTCHA_SECRET,
          'response' => $_POST['g-recaptcha-response'],
          'remoteip' => $_SERVER['REMOTE_ADDR']
        )
      );
      $opts = array('http' =>
        array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => $post_data
        )
      );
      $context  = stream_context_create($opts);
      $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
      $result = json_decode($response);
      if (!$result->success) {
        array_push($feil, "Kryss av for 'jeg er ikke en robot'");
      }
    }

    if($_POST["navn"]      == "" ||
       $_POST["etternavn"] == "" ||
       $_POST["adresse"]   == "" ||
       $_POST["postnr"]    == "") {
        array_push($feil, "navn og adresse må oppgis");
        array_push($feilfelt, "navn", "etternavn", "adresse", "postnr");
    }

    if($_POST["samtykke_registrering"] == "") {
        array_push($feil, "du må akseptere personvernerklæringen");
        array_push($feilfelt, "samtykke_registrering");
    }

    $fdato = null;
    try {
      $fdato = new DateTime($_POST["fdato"]);
    } catch(Exception $e) {
      feilkode("Feil fra datoparsing: " . $e->getMessage());
      $_POST["kommentarer"] .= " Oppgitt fødselsdato: " . $_POST["fdato"];
    }

    if($fdato) {
        $now   = new DateTime("now");
        $alder = $fdato->diff($now)->y;
        //array_push($feil, "logget dato: " . $fdato->format('d. M Y'));
        if($alder < 6) {
            array_push($feil, "alder under 6 år");
            array_push($feilfelt, "fdato");
        } else if($alder > 18) {
            array_push($feil, "alder over 18 år");
            array_push($feilfelt, "fdato");
        } else {
            // riktig formattert dato for database
            $_POST["fdato"] = $fdato->format('Y-m-d');
        }
    } else {
        array_push($feil, "mangler fødselsdato");
        array_push($feilfelt, "fdato");
    }

    if($_POST["f1_navn"] == "" && $_POST["f2_navn"] == "") {
        array_push($feil, "minst en foresatt må oppgis");
        array_push($feilfelt, "f1_navn", "f2_navn");
    }
    
    if($_POST["f1_navn"] != "" && ($_POST["f1_tlf"] == "" || $_POST["f1_epost"] == "")) {
        array_push($feil, "vi må ha kontaktinfo til foresatte");
        array_push($feilfelt, "f1_tlf", "f1_epost");
    }

    if($_POST["f2_navn"] != "" && ($_POST["f2_tlf"] == "" || $_POST["f2_epost"] == "")) {
        array_push($feil, "vi må ha kontaktinfo til foresatte");
        array_push($feilfelt, "f2_tlf", "f2_epost");
    }

    if($_POST["postnr"] != "" && (strlen($_POST["postnr"]) != 4)) {
        $pnrfeil = true;
        array_push($feilfelt, "postnr");
    }
    if($_POST["f1_postnr"] != "" && (strlen($_POST["f1_postnr"]) != 4)) {
        $pnrfeil = true;
        array_push($feilfelt, "f1_postnr");
    }
    if($_POST["f2_postnr"] != "" && (strlen($_POST["f2_postnr"]) != 4)) {
        $pnrfeil = true;
        array_push($feilfelt, "f2_postnr");
    }
    if($pnrfeil) {
        array_push($feil, "postnummer må være fire siffer");
    }

    if(count($feil) > 0 || count($feilfelt) > 0) {
        error_log("feilmeldinger returnert til bruker: " . join(", ", $feil) . join(", ", $feilfelt));
        http_response_code(200);
        header('Content-Type: application/json');
        $ret = [ 'feil' => $feil, 'feilfelt' => $feilfelt ];
        echo json_encode($ret);
    } else {
        // default søknadsid
        $id = 0;
        $insert_sql = "insert into soknader set " .
                    join(",", array_map(function($item) {
                        return $item . "= :" . $item;
                    }, $felt));

        $server = "localhost";
        $user = Config::DB_USER;
        $pass = Config::DB_PASSWORD;
        $db = "godliruv_soknader";

        $dsn = "mysql:host=$server;dbname=$db;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $conn = new PDO($dsn, $user, $pass, $options);

            $stmt = $conn->prepare($insert_sql);

            foreach($felt as $f) {
                $stmt->bindValue(":".$f, isset($_POST[$f]) ? strip_tags($_POST[$f]) : 'NULL');
            }

            if($stmt->execute()) {
                $id = $conn->lastInsertId();
                http_response_code(200);
                header('Content-Type: application/json');
                $ret = [ 'id' => $id ];
                echo json_encode($ret);
            } else {
                $err = $stmt->errorInfo();
                //$k=feilkode("error executing transaction: ".err[0]." ".err[1]." ".err[2]);
                $k=feilkode("error executing transaction: ".err);
                http_response_code(500);
            }
        } catch (PDOException $e) {
            $k = feilkode("PDO-error (" . $e->getCode() . "): " . $e->getMessage());
            http_response_code(500);
        }

        // Multiple recipients
        $to = CONFIG::EPOST_MEDLEMSANSVARLIG;
        // Subject
        $subject = '[GT] Nytt medlem, søknad ' . $id;

        // Message
        $message = '
        <html>
        <head>
          <title>Godlia/Trasop søknad om medlemskap</title>
        </head>
        <body>
        <table>';
        $message = $message . tr("Navn", "navn");
        $message = $message . tr("Etternavn", "etternavn");
        $message = $message . tr("Fødselsdato", "fdato");
        $message = $message . tr("Adresse", "adresse");
        $message = $message . tr("Postnummer", "postnr");
        $message = $message . tr("Poststed", "poststed");
        $message = $message . tr("Telefon", "tlf");
        $message = $message . tr("Instrument førstevalg", "instr1");
        $message = $message . tr("Instrument andrevalg",  "instr2");
        $message = $message . tr("Instrument tredjevalg", "instr3");

        $message = $message . "<tr><th colspan=\"2\">Foresatt 1</td><td>\n";
        $message = $message . tr("Navn",      "f1_navn");
        $message = $message . tr("Etternavn", "f1_etternavn");
        $message = $message . tr("Adresse",   "f1_adresse");
        $message = $message . tr("Postnummer","f1_postnr");
        $message = $message . tr("Poststed",  "f1_poststed");
        $message = $message . tr("Telefon",   "f1_tlf");
        $message = $message . tr("Epost",     "f1_epost");

        $message = $message . "<tr><th colspan=\"2\">Foresatt 2</td><td>\n";
        $message = $message . tr("Navn",      "f2_navn");
        $message = $message . tr("Etternavn", "f2_etternavn");
        $message = $message . tr("Adresse",   "f2_adresse");
        $message = $message . tr("Postnummer","f2_postnr");
        $message = $message . tr("Poststed",  "f2_poststed");
        $message = $message . tr("Telefon",   "f2_tlf");
        $message = $message . tr("Epost",     "f2_epost");

        $message = $message . tr("Samtykke registering", "samtykke_registrering");
        $message = $message . tr("Samtykke bilder i profilering", "samtykke_reklame");
        $message = $message . tr("Samtykke bilder i søknader", "samtykke_soknad");

        $message = $message . tr("Skole", "skole");

        $message = $message . "<tr><th colspan=\"2\">Kommentarer</td><td>\n";
        $message = $message . tr("Kommentar", "kommentarer");

        $message = $message . '</table>
        </body>
        </html>
        ';

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';

        // Additional headers
        // $headers[] = 'To: GT Medlemsansvarlig <medlem@godliatrasop.no>';
        $headers[] = 'From: '.$_POST["f1_navn"].' '.$_POST["f1_etternavn"].' <'.$_POST["f1_epost"].'>';

        // Mail it
        mail($to, $subject, $message, implode("\r\n", $headers));
    }
} elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>
    <html><head><title>Hello</title></head>
<body><h1><?php echo Config::HELLO ?></h1>
update 1
    </body></html><?php
} else {
    http_response_code(405);
}
?>
