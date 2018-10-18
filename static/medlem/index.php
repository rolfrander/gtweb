<?php

include 'Config.php';

$adjektiv = array("liten", "stor", "grønn", "blid", "fin", "rar", "morsom", "ullen", "sur", "glad");
$substantiv = array("hest", "bil", "telefon", "vegg", "stol", "ovn", "lampe", "blokk", "boks", "sokk");
$felt = array("navn", "etternavn", "adresse", "postnr", "poststed", "tlf",
              "f1_navn", "f1_etternavn", "f1_adresse", "f1_postnr", "f1_poststed", "f1_tlf", "f1_epost",
              "f2_navn", "f2_etternavn", "f2_adresse", "f2_postnr", "f2_poststed", "f2_tlf", "f2_epost",
              "instr1", "instr2", "instr3");

function feilkode($feilmelding)
{
    global $adjektiv, $substantiv;
    $a = $adjektiv[rand(0, count($adjektiv)-1)];
    $s = $substantiv[rand(0, count($substantiv)-1)];
    error_log("feilkode [".$a." ".$s."]: ".$feilmelding);
    header('X-GT-Error: '.$a." ".$s);
}

// only for testing...
header('Access-Control-Allow-Origin: *');
header('Access-Control-Expose-Headers: X-GT-Error');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $feil = array();
    $feilfelt = array();
    $pnrfeil = false;

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
        array_push($feil, "er du en person?");
    }

    if($_POST["navn"]      == "" ||
       $_POST["etternavn"] == "" ||
       $_POST["adresse"]   == "" ||
       $_POST["postnr"]    == "") {
        array_push($feil, "navn og adresse må oppgis");
        array_push($feilfelt, "navn", "etternavn", "adresse", "postnr");
    }
    if($_POST["f1_navn"] == "" && $_POST["f2_navn"] == "") {
        array_push($feil, "minst en foresatt må oppgis");
        array_push($feilfelt, "f1_navn", "f2_navn");
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
        http_response_code(200);
        header('Content-Type: application/json');
        $ret = [ 'feil' => $feil, 'feilfelt' => $feilfelt ];
        echo json_encode($ret);
    } else {
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
                $stmt->bindValue(":".$f, isset($_POST[$f]) ? $_POST[$f] : 'NULL');
            }

            if($stmt->execute()) {
                http_response_code(200);
                header('Content-Type: application/json');
                $ret = [ 'id' => $conn->lastInsertId() ];
                echo json_encode($ret);
            } else {
                $err = $stmt->errorInfo();
                //$k=feilkode("error executing transaction: ".err[0]." ".err[1]." ".err[2]);
                $k=feilkode("error executing transaction: ".err);
                http_response_code(500);
            }
        } catch (\PDOException $e) {
            $k = feilkode("PDO-error (" . $e->getCode() . "): " . $e->getMessage());
            http_response_code(500);
        }

        // Multiple recipients
        $to = 'medlem@godliatrasop.no'; 

        // Subject
        $subject = '[GT] Nytt medlem';

        // Message
        $message = '
        <html>
        <head>
          <title>Godlia/Trasop søknad om medlemskap</title>
        </head>
        <body>
        <table>';
        $message = $message . '<tr><td>Navn</td><td>' . $_POST["navn"] . "</td></tr>\n";
        $message = $message . '<tr><td>Etternavn</td><td>' . $_POST["etternavn"] . "</td></tr>\n";
        $message = $message . '<tr><td>Adresse</td><td>' . $_POST["adresse"] . "</td></tr>\n";
        $message = $message . '<tr><td>Postnummer</td><td>' . $_POST["postnr"] . "</td></tr>\n";
        $message = $message . '<tr><td>Poststed</td><td>' . $_POST["poststed"] . "</td></tr>\n";
        $message = $message . '<tr><td>Telefon</td><td>' . $_POST["tlf"] . "</td></tr>\n";
        $message = $message . '<tr><td colspan="2">Foresatt 1</td><td>\n';
        $message = $message . '<tr><td>Navn</td><td>' . $_POST["f1_navn"] . "</td></tr>\n";
        $message = $message . '<tr><td>Etternavn</td><td>' . $_POST["f1_etternavn"] . "</td></tr>\n";
        $message = $message . '<tr><td>Adresse</td><td>' . $_POST["f1_adresse"] . "</td></tr>\n";
        $message = $message . '<tr><td>Postnummer</td><td>' . $_POST["f1_postnr"] . "</td></tr>\n";
        $message = $message . '<tr><td>Poststed</td><td>' . $_POST["f1_poststed"] . "</td></tr>\n";
        $message = $message . '<tr><td>Telefon</td><td>' . $_POST["f1_tlf"] . "</td></tr>\n";
        $message = $message . '<tr><td>Epost</td><td>' . $_POST["f1_epost"] . "</td></tr>\n";
        $message = $message . '<tr><td colspan="2">Foresatt 2</td><td>\n';
        $message = $message . '<tr><td>Navn</td><td>' . $_POST["f2_navn"] . "</td></tr>\n";
        $message = $message . '<tr><td>Etternavn</td><td>' . $_POST["f2_etternavn"] . "</td></tr>\n";
        $message = $message . '<tr><td>Adresse</td><td>' . $_POST["f2_adresse"] . "</td></tr>\n";
        $message = $message . '<tr><td>Postnummer</td><td>' . $_POST["f2_postnr"] . "</td></tr>\n";
        $message = $message . '<tr><td>Poststed</td><td>' . $_POST["f2_poststed"] . "</td></tr>\n";
        $message = $message . '<tr><td>Telefon</td><td>' . $_POST["f2_tlf"] . "</td></tr>\n";
        $message = $message . '<tr><td>Epost</td><td>' . $_POST["f2_epost"] . "</td></tr>\n";
        $message = $message . '<tr><td>Instrument førstevalg</td><td>' . $_POST["instr1"] . "</td></tr>\n";
        $message = $message . '<tr><td>Instrument andrevalg</td><td>' . $_POST["instr2"] . "</td></tr>\n";
        $message = $message . '<tr><td>Instrument tredjevalg</td><td>' . $_POST["instr3"] . "</td></tr>\n";
        $message = $message . '</table>
        </body>
        </html>
        ';

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        // Additional headers
        $headers[] = 'To: GT Medlemsansvarlig <medlem@godliatrasop.no>';
        $headers[] = 'From: '$_POST["f1_navn"].' '$_POST["f1_etternavn"].' <'.$_POST["f1_epost"].'>';

        // Mail it
        mail($to, $subject, $message, implode("\r\n", $headers));

    }

} elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?><h1><?php echo Config::HELLO ?></h1><?php
} else {
    http_response_code(405);
}
?>
