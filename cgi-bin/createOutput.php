<?php

function startsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    return (substr($haystack, 0, $length) === $needle);
	}	

class CreateOutput
{
		

	public function __construct(){	}
	
	public function createOutput($location, $timeNum, $timeUnit,$resultLimit, $session)
	{
		$con = mysql_connect(localhost,"slidat_carlos","cortes299940");
		mysql_select_db("slidat_cps630") or die( "Unable to select database");
	
		
		//get hashtag rows from data;
		$query = "SELECT Hashtags FROM data WHERE Location = '$location' AND DATE > DATE_SUB( NOW( ) , INTERVAL $timeNum $timeUnit)";
		$response = mysql_query($query);
		
		$table_name = "table".$session."";
		//echo $session;
		
		//temporary table
		$sql = "CREATE TEMPORARY TABLE $table_name (Text varchar(160), Count int)";
			
		// Execute query
		mysql_query($sql);
		
		mysql_query("ALTER TABLE $table_name ADD UNIQUE (`Text`)");
			
		while ($row = mysql_fetch_array($response, MYSQL_ASSOC))
		{
			$HashtagsPerRow = $row["Hashtags"];
			$array = explode(' ',$HashtagsPerRow);
			$array_size = Count($array);
			    
			$i = 0;
			    
			while($i < $array_size)
			{
				if(startsWith($array[$i],"#"))
			   	{
			   	  
			   		if(mysql_num_rows((mysql_query("SELECT Text FROM $table_name WHERE Text LIKE '$array[$i]'"))) > 0)
			   	 	{
			   	  
				   		$gethashtag = "UPDATE $table_name SET Count=Count+1 WHERE Text LIKE '$array[$i]'";
				           	mysql_query($gethashtag);
				   	
			   	 	}
				   	else
				   	{		   	 
				   		mysql_query("INSERT INTO $table_name VALUES ('$array[$i]',1)");
				   	}
			    		 
			   	 }
			         $i = $i + 1;
			}
			
		}
		
		
		$sql2 = "SELECT Text, Count FROM $table_name ORDER BY Count DESC LIMIT 0, $resultLimit";
		// Execute query
		
		$response2 = mysql_query($sql2);
		
		$row3 = array();
		
		while($row2 = mysql_fetch_array($response2, MYSQL_ASSOC))
		{
		
			$row3[] = $row2;
		
		}
		
		$deletetable = "DROP TABLE $table_name";
		mysql_query($deletetable);
			
		//mkdir("images/folder$session", 0777);
			
		//custom query returned to main
		mysql_close(); 
		return $row3;
		
	}
	
}

?>