<?php

class GetSession
{
		

	public function __construct(){	}
	
	public function getSession()
	{

		$con = mysql_connect(localhost,"slidat_carlos","cortes299940");
		mysql_select_db("slidat_cps630") or die( "Unable to select database");
			
		
		$query = "SELECT ID FROM Session";
		
		$response = mysql_query($query);
		
		$row = mysql_fetch_array($response, MYSQL_ASSOC);
		
		
		
		//session will be a range from 1-99
		$session_id = $row["ID"];
		
		if($session_id >= 100){
		
			$session_id = 1;
			$update_sessions = "UPDATE Session SET ID=1 ";
		}
		else{
			//session number for next user
			$update_sessions = "UPDATE Session SET ID=ID+1";
			
		}
		mysql_query($update_sessions);
		
		
		
		mysql_close(); 
		 
		return $session_id;
	}

}

?>