<html>
<body>

<a href="http://sundaygamegroup.host22.com/" STYLE="TEXT-DECORATION: NONE"> <FONT COLOR="000000"><h1>GameGroupDB</h1></FONT> </a> 

<?php

$con=mysqli_connect("mysql8.000webhost.com","a3604005_games","password","a3604005_games");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$sql="INSERT INTO Members (membername)
VALUES
('$_POST[name]')";

$error=0;
if (!mysqli_query($con,$sql))
  {
  echo "<div style=\"color:red\">";
  echo ('Error: ' . mysqli_error($con));
  echo "</div>";
  $error=1;
  }
  
if (!$error)
  {
  $result=array();
  $games_result = mysqli_query($con,"SELECT * FROM Games");
  while($r = mysqli_fetch_assoc($games_result)) 
    {
    $result[] = $r;
    }

  foreach($result as $row)
    {
    for ( $c_i=$row['maxplayers']; $c_i>1 ; $c_i--)
      {
      $stmt = mysqli_prepare($con,"INSERT INTO Rankings (membername,gamename,numberofplayers,rank) VALUES (?,?,?,0)");
      mysqli_stmt_bind_param($stmt,'ssi',$_POST[name],$row['gamename'],$c_i);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
      }
    }
  }
mysqli_close($con);
?>

<form action="add_member.php" method="post">
Add Member: <input type="text" name="name">
<input type="submit" style="position: absolute; left: -9999px"/>
</form>

<?php
$con=mysqli_connect("mysql8.000webhost.com","a3604005_games","password","a3604005_games");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$result = mysqli_query($con,"SELECT * FROM Members ORDER BY membername");


while($row = mysqli_fetch_array($result))
  {
  echo "<a href=\"http://sundaygamegroup.host22.com/edit.php?data=". $row['membername'] ."&playerstable=2\">" . $row['membername'] . "</a> &nbsp;";
  }

mysqli_close($con);
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

echo "<form action=\"default.php\" method=\"get\">";
echo "<input type=\"hidden\" name=\"data\" value='". $_GET["data"] ."'>";
echo "Switch table to number of players:<br><select name=\"playerstable\">";
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
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
$result = mysqli_query($con,"SELECT * FROM Members");
echo "Or select a specifc group:";
echo "<form action=\"default.php\" method=\"post\">";
$i = 0;
while ($row = mysqli_fetch_array($result))
{
  echo "
    <tr>
      <td>
        ". $row['membername'] ." <input type='checkbox' name='membernamecheckbox[".$i."]' value='".$row['membername']."' />
      </td>
    </tr>
  ";
  $i++;
}
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

if(isset($_GET["playerstable"]))
{
	$stmt = mysqli_prepare($con,"SELECT (SUM(Rankings.rank)) AS \"TotalRank\", Rankings.gamename FROM Rankings WHERE Rankings.numberofplayers=? AND Rankings.rank!=0 GROUP BY Rankings.gamename ORDER BY Rankings.rank");
    mysqli_stmt_bind_param($stmt,'i',$_GET["playerstable"]);
	mysqli_stmt_execute($stmt);
    
	mysqli_stmt_bind_result($stmt,$col1,$col2);
	$playerstable=$_GET["playerstable"];
}
else
  {
  $membernamestr="";
  if(isset($_POST['membernamecheckbox']))
  {
  $membernamestr .=" AND (";
  foreach($_POST['membernamecheckbox'] as $value)
    {
    $membernamestr .="Rankings.membername='";
    $membernamestr .= $value;
	$membernamestr .= "' OR ";
    }
  $membernamestr .= " 'False') ";
  }

  $stmt = mysqli_prepare($con,"SELECT (SUM(Rankings.rank)) AS \"TotalRank\", Rankings.gamename FROM Rankings WHERE Rankings.numberofplayers=2 AND Rankings.rank!=0".$membernamestr." GROUP BY Rankings.gamename ORDER BY Rankings.rank");
  mysqli_stmt_execute($stmt);
    
  mysqli_stmt_bind_result($stmt,$col1,$col2);
  $playerstable=2;
  }

echo "<table width=\"50%\" border=\"1\">
<tr>
<td><b>Rank   </b></td>
<td><b>Game   </b></td>
<td><b>Players</b></td>
</tr>";

while(mysqli_stmt_fetch($stmt))
  {
  if ($col1!='0')
    {
        echo "<tr>";
        echo "<td>" . $col1 . "</td>";
        echo "<td>" . $col2 . "</td>";
        echo "<td>" . $playerstable . "</td>";
        echo "</tr>";
	}

  }
mysqli_close($con);
?> 
</body>
</html>

</body>
</html>