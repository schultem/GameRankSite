<html>
<body>

<style type="text/css">
fieldset{
 display:inline;
}
DIV.acenter {text-align: center}
</style>

<?php
    if(isset($_GET["data"]) and isset($_GET["playerstable"]))
    {
        echo "<a href=\"http://sundaygamegroup.host22.com/\" STYLE=\"TEXT-DECORATION: NONE\"> <FONT COLOR=\"000000\"><h1>GameGroupDB - ". $_GET["data"] ."</h1></FONT> </a>";
    }
	else
	{
        echo "<a href=\"http://sundaygamegroup.host22.com/\" STYLE=\"TEXT-DECORATION: NONE\"> <FONT COLOR=\"000000\"><h1>GameGroupDB</h1></FONT> </a>";
        echo "<div style=\"color:red\">";
        echo ('Error: Member incorrectly selected!');
        echo "</div>";
	}
?>

<?php
    if(isset($_GET["data"]) and isset($_GET["playerstable"]))
    {
        echo "<form action=\"add_game.php?data=". $_GET["data"] ."&playerstable=". $_GET["playerstable"] ."\" method=\"post\">";
		echo "<fieldset>";
		echo "<legend><b>Add Game</b></legend>";
		echo "Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"text\" name=\"name\"><br>";
        echo "Max players:<input type=\"number\" name=\"numberofplayers\" min=\"2\" max=\"16\">";
        echo "<input type=\"submit\" value=\"Add\"/>";
        echo "</fieldset>";
		echo "</form>";
    }
	else
	{
        echo "<div style=\"color:red\">";
        echo "Error: Entered the page without member and number of players information";
        echo "</div>";
	}
?>

<?php
$con=mysqli_connect("mysql8.000webhost.com","a3604005_games","password","a3604005_games");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  
$result = mysqli_query($con,"SELECT MAX(maxplayers) FROM Games");
$row    = mysqli_fetch_array($result);

echo "<form action=\"edit.php\" method=\"get\">";
echo "<input type=\"hidden\" name=\"data\" value='". $_GET["data"] ."'>";
echo "Switch table to number of players: <select name=\"playerstable\">";
for ($c_i=2;$c_i<=$row['MAX(maxplayers)'];$c_i++)
{
  echo $row['maxplayers'];
  echo "<option value=\"". $c_i ."\">". $c_i ."</option>";
}
echo "</select>";
echo "<input type=\"submit\" value=\"Select\">";
echo "</form>";
?>

<?php
$con=mysqli_connect("mysql8.000webhost.com","a3604005_games","password","a3604005_games");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  
$number_of_rated_games_result = mysqli_query($con,"SELECT MAX(rank) FROM Rankings WHERE membername='". $_GET["data"] ."' AND numberofplayers='".$_GET['playerstable']."'");
$number_of_rated_games        = mysqli_fetch_array($number_of_rated_games_result);

if ($_POST["newrank"]>=1 and $_POST["newrank"]<=($number_of_rated_games['MAX(rank)']+1))
  {
  if(isset($_GET["data"]) and isset($_GET["playerstable"]))
    {
    //rank
    $newrank=$_POST["newrank"];
    
    $oldrank_stmt=mysqli_prepare($con,"SELECT membername,gamename,numberofplayers,rank FROM Rankings WHERE membername=? AND gamename=? AND numberofplayers=?");
    mysqli_stmt_bind_param($oldrank_stmt,'ssi',$_GET["data"],$_POST["gamenametorank"],$_GET["playerstable"]);
    mysqli_stmt_execute($oldrank_stmt);
    mysqli_stmt_bind_result($oldrank_stmt,$col1,$col2,$col3,$col4);
    $oldrank_row=mysqli_stmt_fetch($oldrank_stmt);
    $oldrank=$col4;
    mysqli_stmt_close($oldrank_stmt);
  
    if ($oldrank==0)
      {
      $currentrank_stmt=mysqli_prepare($con,"SELECT membername,gamename,numberofplayers,rank FROM Rankings WHERE membername=? AND numberofplayers=?");
      mysqli_stmt_bind_param($currentrank_stmt,'si',$_GET["data"],$_GET["playerstable"]);
      mysqli_stmt_execute($currentrank_stmt);
      mysqli_stmt_bind_result($currentrank_stmt,$col1,$col2,$col3,$col4);
  	$result=array();
      while(mysqli_stmt_fetch($currentrank_stmt))
        {
  	    if ($col4!=0)
  		  {
  	      $result[]= $col1;
  	      $result[]= $col2;
  	      $result[]= $col3;
  	      $result[]= $col4;
  		  }
  	  }
      mysqli_stmt_close($currentrank_stmt);
      
  	for($c_i=0;$c_i<sizeof($result);$c_i=$c_i+4)
  	  {
  	  if ($result[$c_i+3]>=$newrank)
  	    {
  	    mysqli_query($con,"UPDATE Rankings SET rank='". ($result[$c_i+3]+1) ."' WHERE membername='". $result[$c_i] ."' AND gamename='". $result[$c_i+1] ."' AND numberofplayers='". $result[$c_i+2] ."'");
  	    }
  	  }
  
      $newrank_stmt=mysqli_prepare($con,"UPDATE Rankings SET rank='". $newrank ."' WHERE membername=? AND gamename='".$_POST['gamenametorank']."' AND numberofplayers=?");
      mysqli_stmt_bind_param($newrank_stmt,'si',$_GET['data'],$_GET['playerstable']);
      mysqli_stmt_execute($newrank_stmt);
      mysqli_stmt_close($newrank_stmt);
      }
    elseif ($newrank>$oldrank)
	  {
	  if($newrank<=$number_of_rated_games['MAX(rank)'])
	    {
        $currentrank_stmt=mysqli_prepare($con,"SELECT membername,gamename,numberofplayers,rank FROM Rankings WHERE membername=? AND numberofplayers=?");
        mysqli_stmt_bind_param($currentrank_stmt,'si',$_GET["data"],$_GET["playerstable"]);
        mysqli_stmt_execute($currentrank_stmt);
        mysqli_stmt_bind_result($currentrank_stmt,$col1,$col2,$col3,$col4);
  	    $result=array();
        while(mysqli_stmt_fetch($currentrank_stmt))
          {
  	      if ($col4!=0)
  		    {
  	        $result[]= $col1;
  	        $result[]= $col2;
  	        $result[]= $col3;
  	        $result[]= $col4;
  		    }
  	    }
        mysqli_stmt_close($currentrank_stmt);
        
        for($c_i=0;$c_i<sizeof($result);$c_i=$c_i+4)
  	    {
  	    if ($result[$c_i+3]>$oldrank and $result[$c_i+3]<=$newrank)
  	      {
  	      mysqli_query($con,"UPDATE Rankings SET rank='". ($result[$c_i+3]-1) ."' WHERE membername='". $result[$c_i] ."' AND gamename='". $result[$c_i+1] ."' AND numberofplayers='". $result[$c_i+2] ."'");
  	      }
  	    }
        
        $newrank_stmt=mysqli_prepare($con,"UPDATE Rankings SET rank='". $newrank ."' WHERE membername=? AND gamename=? AND numberofplayers=?");
        mysqli_stmt_bind_param($newrank_stmt,'ssi',$_GET["data"],$_POST["gamenametorank"],$_GET["playerstable"]);
        mysqli_stmt_execute($newrank_stmt);
        mysqli_stmt_close($newrank_stmt);
  	    }
	  else
	    {
        echo "<div style=\"color:red\">";
        echo "Error: Number must be between 1 and ". ($number_of_rated_games['MAX(rank)']) ."";
        echo "</div>";
	    }
	  }
    elseif ($newrank<$oldrank)
	  {
	  if($newrank<=$number_of_rated_games['MAX(rank)'])
	    {
        $currentrank_stmt=mysqli_prepare($con,"SELECT membername,gamename,numberofplayers,rank FROM Rankings WHERE membername=? AND numberofplayers=?");
        mysqli_stmt_bind_param($currentrank_stmt,'si',$_GET["data"],$_GET["playerstable"]);
        mysqli_stmt_execute($currentrank_stmt);
        mysqli_stmt_bind_result($currentrank_stmt,$col1,$col2,$col3,$col4);
  	    $result=array();
        while(mysqli_stmt_fetch($currentrank_stmt))
          {
  	      if ($col4!=0)
  		    {
  	        $result[]= $col1;
  	        $result[]= $col2;
  	        $result[]= $col3;
  	        $result[]= $col4;
  		    }
  	    }
        mysqli_stmt_close($currentrank_stmt);
        
        for($c_i=0;$c_i<sizeof($result);$c_i=$c_i+4)
  	    {
  	    if ($result[$c_i+3]>=$newrank and $result[$c_i+3]<$oldrank)
  	      {
  	      mysqli_query($con,"UPDATE Rankings SET rank='". ($result[$c_i+3]+1) ."' WHERE membername='". $result[$c_i] ."' AND gamename='". $result[$c_i+1] ."' AND numberofplayers='". $result[$c_i+2] ."'");
  	      }
  	    }
        
        $newrank_stmt=mysqli_prepare($con,"UPDATE Rankings SET rank='". $newrank ."' WHERE membername=? AND gamename=? AND numberofplayers=?");
        mysqli_stmt_bind_param($newrank_stmt,'ssi',$_GET["data"],$_POST["gamenametorank"],$_GET["playerstable"]);
        mysqli_stmt_execute($newrank_stmt);
        mysqli_stmt_close($newrank_stmt);
  	    }
	  else
	    {
        echo "<div style=\"color:red\">";
        echo "Error: Number must be between 1 and ". ($number_of_rated_games['MAX(rank)']) ."";
        echo "</div>";
	    }
      }
    else
      {
      echo "<div style=\"color:red\">";
      echo "Error: Entered the page without member and number of players information";
      echo "</div>";
      $error=1;
      }
    }
  }
else
  {
  echo "<div style=\"color:red\">";
  echo "Error: Number must be between 1 and ". ($number_of_rated_games['MAX(rank)']) ."";
  echo "</div>";
  }

?>

<?php
$con=mysqli_connect("mysql8.000webhost.com","a3604005_games","password","a3604005_games");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$number_of_rated_games_result = mysqli_query($con,"SELECT MAX(rank) FROM Rankings WHERE membername='". $_GET["data"] ."' AND numberofplayers='".$_GET['playerstable']."'");
$number_of_rated_games        = mysqli_fetch_array($number_of_rated_games_result);

$error=0;
if(isset($_GET["data"]) and isset($_GET["playerstable"]))
{
	$stmt = mysqli_prepare($con,"SELECT rank,gamename,numberofplayers FROM Rankings WHERE membername=? and numberofplayers=? ORDER BY rank");
    mysqli_stmt_bind_param($stmt,'si',$_GET["data"],$_GET["playerstable"]);
	mysqli_stmt_execute($stmt);
    
	mysqli_stmt_bind_result($stmt,$col1,$col2,$col3);
}
else
{
    echo "<div style=\"color:red\">";
    echo "Error: Entered the page without member and number of players information";
    echo "</div>";
	$error=1;
}
echo "Enter your preferences for ".$_GET["playerstable"]." player games, <b>one at a time</b>:<br><br>";
echo "<table width=\"60%\" border=\"1\">
<tr>
<td><b>New Rank       </b></td>
<td><b>Unranked Games </b></td>
<td><b>Players        </b></td>
</tr>";

if (!$error)
{
	$caught_up=1;
    while(mysqli_stmt_fetch($stmt))
      {
	  if ($col1=='0')
	    {
            echo "<tr>";
            echo "<td>";
            echo "<form action=\"rank_game.php?data=". $_GET["data"] ."&playerstable=". $_GET["playerstable"] ."\" method=\"post\" size=\"5\" >";
            echo "<input type=\"hidden\" name=\"gamenametorank\" value='". $col2 ."'>";
			echo "<input type=\"number\" name=\"newrank\" size=\"5\" min=\"1\" max=\"". ($number_of_rated_games['MAX(rank)']+1) ."\" >";
            echo "<input type=\"submit\" value=\"Submit\"/>";
			echo "</form>";
            echo "</td>";
            echo "<td>" . $col2 . "</td>";
            echo "<td>" . $col3 . "</td>";
            echo "</tr>";
			$caught_up=0;
		}
      }
}
echo "</table>";

if ($caught_up==1)
{
  echo "Member has no unranked ". $_GET["playerstable"] ." player games";
}


mysqli_stmt_close($stmt);
$error=0;
if(isset($_GET["data"]) and isset($_GET["playerstable"]))
{
	$stmt = mysqli_prepare($con,"SELECT rank,gamename,numberofplayers FROM Rankings WHERE membername=? and numberofplayers=? ORDER BY rank");
    mysqli_stmt_bind_param($stmt,'si',$_GET["data"],$_GET["playerstable"]);
	mysqli_stmt_execute($stmt);
    
	mysqli_stmt_bind_result($stmt,$col1,$col2,$col3);
}
else
{
    echo "<div style=\"color:red\">";
    echo "Error: Entered the page without member and number of players information";
    echo "</div>";
	$error=1;
}
echo "</br>";
echo "</br>";
echo "<table width=\"60%\" border=\"1\">
<tr>
<td><b>Rank   </b></td>
<td><b>New Rank   </b></td>
<td><b>Ranked Games   </b></td>
<td><b>Players</b></td>
</tr>";

if (!$error)
{
    while(mysqli_stmt_fetch($stmt))
      {
	  if ($col1!='0')
	    {
            echo "<tr>";
            echo "<td>" . $col1 . "</td>";
            echo "<td>";
            echo "<form action=\"rank_game.php?data=". $_GET["data"] ."&playerstable=". $_GET["playerstable"] ."\" method=\"post\" size=\"5\" >";
            echo "<input type=\"hidden\" name=\"gamenametorank\" value='". $col2 ."'>";
			echo "<input type=\"number\" name=\"newrank\" size=\"5\" min=\"1\" max=\"". ($number_of_rated_games['MAX(rank)']) ."\" >";
            echo "<input type=\"submit\" value=\"Submit\"/>";
			echo "</form>";
            echo "</td>";
            echo "<td>" . $col2 . "</td>";
            echo "<td>" . $col3 . "</td>";
            echo "</tr>";
		}
      }
}
echo "</table>";
mysqli_close($con);
?> 

</body>
</html>