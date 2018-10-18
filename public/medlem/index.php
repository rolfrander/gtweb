<?php

$adjektiv = array("liten", "stor", "grønn", "blid", "fin", "rar", "morsom", "ullen", "sur", "glad");
$substantiv = array("hest", "bil", "telefon", "vegg", "stol", "ovn", "lampe", "blokk", "boks", "sokk");

function feilkode($feilmelding) 
{
    global $adjektiv, $substantiv;
    $a = $adjektiv[random_int(0, count($adjektiv)-1)];
    $s = $substantiv[random_int(0, count($substantiv)-1)];
    error_log("feilkode [".$a." ".$s."]: ".$feilmelding);
    header('X-GT-Error: '.$a." ".$s);
}

// only for testing...
header('Access-Control-Allow-Origin: *');
header('Access-Control-Expose-Headers: X-GT-Error');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $felt = array("navn", "etternavn", "adresse", "postnr", "poststed", "tlf", "f1_navn", "f1_etternavn", "f1_adresse", "f1_postnr", "f1_poststed", "f1_tlf", "f1_epost", "f2_navn", "f2_etternavn", "f2_adresse", "f2_postnr", "f2_poststed", "f2_tlf", "f2_epost", "instr1", "instr2", "instr3");
    
    $feil = array();
    $feilfelt = array();
    $pnrfeil = false;
    
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
        $user = "godliruv_web";
        $pass = "web";
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
                $k=feilkode("error executing transaction: ".err[0]." ".err[1]." ".err[2]);
                http_response_code(500);
            }
        } catch (\PDOException $e) {
            $k = feilkode("PDO-error (" . $e->getCode() . "): " . $e->getMessage());
            http_response_code(500);
        }
    }
    
} elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?><h1>hello world!</h1><?php
} else {
    http_response_code(405);
}
?>
