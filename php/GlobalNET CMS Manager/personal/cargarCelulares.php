<?php

include_once("includes.php");

$dbc = new conexion("Web");
$db = $dbc->db;

$csv_filename = "celulares_homologados.csv";

$csv_datos = file($csv_filename);


foreach($csv_datos as $linea) {
    $datos = explode(";", $linea);

    $ua = str_replace(".*", "%", $datos[1]);
    $ua = trim(stripcslashes($ua));
    $ua_pattern = str_replace("%", "", $ua);

    $marca_modelo = stripslashes($datos[0]);
    
    $sql = "SELECT *
            FROM MCM.celulares_ua_wurfl
            where pk_descripcion like '$ua'";
     echo "<br>".$sql;
    $rs = mysql_query($sql, $db);
    if($rs) {
        while($row = mysql_fetch_assoc($rs)) {
            $sql = "INSERT INTO personalArg.celulares_homologados_marcablanca
                    (id_wurfl, marca_modelo, ua_pattern)
                    VALUES
                    ('".$row['pk_fk_celulares_modelos_wurfl']."', '".$marca_modelo."', '$ua_pattern')";
            $rs_in = mysql_query($sql, $db);
            if(!$rs_in) {
                echo "<br>Error insertando (errcode: " . mysql_errno().")::" . mysql_error()."::" . $sql;
            }
        }
    }
}
echo "== Proceso terminado ==";




?>
