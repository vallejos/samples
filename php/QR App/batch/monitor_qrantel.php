<?
ini_set('max_execution_time',200);
include("receivemail.class.php");
// Create Object For reciveMail Class
$obj= new receiveMail('qrantel@domain.uy','password','qrantel@domain.uy','10.0.0.201','pop3','110'); //200.40.206.110
//Connect to the Mail Box
$obj->connect();
// Get Total Number of Unread Email in mail box
$tot=$obj->getTotalMails(); //Total Mails in inbox Return interger value
echo "Total Mails:: $tot<br>";
global $path;

$local = false;
$app = "770";
$path = "/var/www/tmp/fotos/";
if ($local) $path = "";

function tolog($cel,$msg){
    $fp = fopen($GLOBALS['path']."monitor_qrantel.log","a");
    fwrite($fp,date("Y-m-d H:i:s")." | ".$cel." | ".$msg."\n");
    fclose($fp);
    echo $msg."\n";
}

for($i=1;$i<=$tot;$i++)
{
	$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Key Are (subject,to,toOth,toNameOth,from,fromName)
    $error = true;
    $delete = false;
    // FORMATO ORIGINAL : +59899923340/type=plmn@mms.ancelutil.com.uy
    if ((strpos($head['from'],"@mms.ancelutil.com.uy")>0 || strpos($head['from'],"@mms.ancel.net.uy")>0) &&
        (substr($head['from'],0,3)!="598" || substr($head['from'],0,4)!="+598")){ // Es un mail desde un celular

	    $body = $obj->getBody($i);  // Get Body Of Mail Number Return String Get Mail id in interger
	    $str=$obj->GetAttech($i,$path); // Get Attached File from Mail Return name of file in comasepreted string  args (mailid, Path to store file)
	    $ar=explode(",",$str);
	    $jpg = "";
	    $texto = "";
		$id = "";
	    $cel = $head['from'];
	    $cel = str_replace("@mms.ancelutil.com.uy","",$cel);
	    $cel = str_replace("@mms.ancel.net.uy","",$cel);
        if (substr($head['from'],0,3)!="598") { // Viene formato +59899923340/type=plmn@mms.ancelutil.com.uy
            $cel = explode("/",$cel);
            $cel = $cel[0];
        } else { // Viene formato 59899923340@mms.ancelutil.com.uy

        }
        $cel = "09".substr($cel,-7);
        echo $cel;
	    foreach($ar as $key=>$value){
		    $ext = substr(strtolower($value),-4);
            echo " - archivo: ".$value;
            if ($ext!=".jpg" && $ext!=".gif" && $ext!=".png"){
                $info = @getimagesize($path.$value);
                $mime = @image_type_to_mime_type($info[2]);
                if ($mime=="image/jpeg") $ext = ".jpg";
                if ($mime=="image/gif") $ext = ".gif";
                if ($mime=="image/png") $ext = ".png";
            }
		    switch($ext){
			    case ".jpg":
			        $jpg = $path.$value;
					$imgsize = filesize($path.$value);
                    $nombre = str_replace($ext,"",$value);
				    break;
			    case ".gif":
				    $img = imagecreatefromgif($path.$value);
				    if (!empty($img)){
					    imagejpeg($img,$path.$value,80);
						$imgsize = filesize($path.$value);
					    $jpg = $path.$value;
                        $nombre = str_replace($ext,"",$value);
				    }
				    break;
			    case ".png":
				    $img = imagecreatefrompng($path.$value);
				    if (!empty($img)){
					    imagejpeg($img,$path.$value,80);
						$imgsize = filesize($path.$value);
					    $jpg = $path.$value;
                        $nombre = str_replace($ext,"",$value);
				    }
				    break;
			    case ".txt":
				    $fp = fopen($path.$value,"r");
				    $texto = fread($fp,255);
				    fclose($fp);
				    @unlink($path.$value);
				    break;
			    default:
				    @unlink($path.$value);
		    }
	    }
		$id = 0;
	    if ($jpg!="" && file_exists($jpg)) {
            if (empty($texto)) {
                $texto = substr(trim($body),0,255);
            }
			$db = mysql_connect("10.0.0.240", "user", "password");
			mysql_select_db("qrapp_antel");
			$sql = "INSERT INTO qrapp_antel.imagenes(celular,app,idcliente,fecha_alta,hora_alta) ";
			$sql .="Values('$cel','$app','0',CURDATE(),CURTIME())";


		    if (!$local) mysql_query($sql,$db);
		    $id = intval(@mysql_insert_id($db));
			mysql_close($db);
			$sqlDelete = "DELETE FROM qrapp_antel.imagenes WHERE id=".$id;

			$source_file = $jpg;
			$destination_file = "/var/www/tmp/mm7/qrantel/".$id.".jpg";

			$source_file_2 = str_replace(" ", "-", $source_file);
			if($source_file_2 != $source_file){
				rename($source_file, $source_file_2);
				$source_file = $source_file_2;
			}
			if(copy($source_file,$destination_file)){
				$error = false;
				unlink($source_file);
			}else{
				$error = true;
				tolog($cel, "Error al copiar a mm7: ".$source_file." | ".$destination_file);
				echo "<br/>Error al copiar a mm7: ".$source_file." | ".$destination_file;
			}


	    } else {
            tolog($cel, "No hay JPG");
			echo '<br/> No exite JPG';
            $error = true;
            $delete = true;
        }
    } else {
        tolog($cel,"Borro mail que no es desde un cel | ".$head['from']);
		echo "<br/>Borro mail que no es desde un cel";
        $error = true;
        $delete = true;
    }
	if (!$error) {
		$delete = true;
		echo "<br/>ningun error<br/>";
	} else { // Si hubo problemas borro el registro
		echo "<br/>error borra registro<br/>";
		if (!$local && $id != 0) {
			$db = mysql_connect("10.0.0.240", "pablo", "pablok4");
			mysql_select_db("qrapp_antel");
			mysql_query($sqlDelete, $db);
			mysql_close($db);
			echo $sqlDelete.'<br/>';
		}
	}

	if ($delete && !$local) {
		$obj->deleteMails($i);
	}
}
$obj->close_mailbox();   //Close Mail Box

?>