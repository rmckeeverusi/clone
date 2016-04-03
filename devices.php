 <?php
    $link = mysql_pconnect("localhost", "root", "") or die("Unable To Connect To Database Server");
    mysql_select_db("municlondb") or die("Unable To Connect To to your database");
      
    $arr = array();
     $rs = mysql_query("SELECT ticketID, device, component,severity,type,owner,created,updated,list FROM ticket");
      
    while($obj = mysql_fetch_object($rs)) {
         $arr[] = $obj;
   }
  echo "{\"data\":" .json_encode($arr). "}";
    ?>
	
<!--
ticketID
component						
device
severity
type
owner
created
updated
list
-->
