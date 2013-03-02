<?php

function startsWithX($haystack, $needle)
{
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}
	
class LoadIntoData
{

	public function __construct(){	}
	
	public function loadIntoData($results,$location,$lat,$long)
	{
	
	foreach( $results->entry as $result )
	{
		$google = $result->children('http://base.google.com/ns/1.0');
		//get location
		$twitter_location = $google->location;
		//get username
		$username = $result->author->name;
		//get date
		$created_date = $result->published;
		
		    $time = strtotime( $created_date );
		    $myDate = date( 'y-m-d H:i:s', $time );
		
		
		//precompile hashtags for quick querying
		$array = explode(' ',$result->title);
		$array_size = Count($array);
		
		$i = 0;
		$perLineHashTag = '';
		while($i < $array_size)
		{
		    	
			if(startsWithX($array[$i],"#"))
		   	{
		   		$perLineHashTag = $perLineHashTag.$array[$i]." ";
		   	}
		   	else
		   	{		   	 
		   		//not a hashtag
		   	}
		   	 
		    	$i = $i + 1;	 
		}
		     
		
	
   		
		
		//enter data into table
		$query = "INSERT INTO `data` VALUES ('$result->title','$username','$location','$long','$lat','$myDate','$twitter_location','$perLineHashTag')";
		mysql_query($query);
		
		//delete entries over one week old		
		$query2 = "DELETE FROM `data` WHERE `date` < DATE_SUB(NOW(), INTERVAL 1 WEEK)";
		
		mysql_query($query2);
	}
	
	}
	
	
}
	
?>