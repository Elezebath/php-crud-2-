<?php

// Database connection
session_start();
$server = "localhost";
$username = "root";
$password = "";
$dbname = "misc";
$message="";
$conn = new PDO("mysql:host=$server; dbname=$dbname", $username, $password);  
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
	$_SESSION['p_id'] =$_GET['id'];  // get id through query string
	$uid=$_SESSION['user_id'];

if(isset($_POST["cancel"]))  
      { 
		header("location:index.php"); 
	  }
if(isset($_POST["Save"]))  { 
		
	
		$fname=htmlentities($_POST["first_name"]);
		$lname=htmlentities($_POST["last_name"]);
		$headline=htmlentities($_POST["headline"]);
		$email=htmlentities($_POST["email"]);
		$summary=htmlentities($_POST["summary"]);
		$query = "UPDATE profile SET  user_id=:uid ,first_name=:fname,last_name=:lname,email=:email,headline=:headline
		,summary=:summary WHERE profile_id = :id";
		 
		try{
				$statement = $conn->prepare($query);  
				$statement->execute(array( 
								'uid'       =>     $uid,
								'fname'     =>     $fname,  
								'lname'     =>     $lname,
								'email'     =>     $email,
								'headline'  =>     $headline,
								'summary'   =>     $summary,
								'id'        =>     $_SESSION['p_id'])); 
				$statement=null;
				// Clear out the old position entries
				$stmt = $conn->prepare('DELETE FROM Position
				WHERE profile_id=:pid');
				$stmt->execute(array( ':pid' => $_SESSION['p_id']));
			
				// Insert the position entries
				$rank = 1;
				for($i=1; $i<=9; $i++) {
					if ( ! isset($_POST['year'.$i]) ) continue;
					if ( ! isset($_POST['desc'.$i]) ) continue;
					$year = $_POST['year'.$i];
					$desc = $_POST['desc'.$i];
			
					$stmt = $conn->prepare('INSERT INTO Position
						(profile_id, rank, year, description)
					VALUES ( :pid, :rank, :year, :desc)');
					$stmt->execute(array(
						':pid' => $_SESSION['p_id'],
						':rank' => $rank,
						':year' => $year,
						':desc' => $desc)
					);
					$rank++;
				}


				$_SESSION['toast']='profile Edited';
				header("location:index.php"); 
		}
		catch(PDOException $error){
			$_SESSION['error']='Unable to edit';	
		}
		
 }
 ?>
 
 <!DOCTYPE html>
<html>
<head>
<title>Aleena C R</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
<?php
if(isset($_SESSION["user_id"]))  
	{
		
?>

<h1>Adding Profile for <?php echo $_SESSION["name"];
	try{
	$id=$_SESSION['p_id'];
	$sql = "SELECT profile_id,first_name,last_name,email,headline,summary FROM profile WHERE profile_id='$id'";
	$q = $conn->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
	$row = $q->fetch();
	if ( $row !== false ) {
		$quer = "SELECT * FROM position WHERE profile_id= :u_id order by rank";
						$stmt=$conn->prepare($quer);  
						$stmt->execute(array(  
								  'u_id'     =>   $id )); 
						$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
						
?></h1>
 
<form method="POST" >
<label for="frist_name" >Frist Name :</label>
<input type="text" name="first_name" id="f_name" style="width:500px;" value="<?php echo $row['first_name']; ?>"><br/><br/>
<label for="last_name">Last Name :</label>
<input type="text" name="last_name" id="l_name" style="width:500px;" value="<?php echo $row['last_name']; ?>"><br/><br/>
<label for="email">Email :</label>
<input type="text" name="email" id="email" style="width:500px;" value="<?php echo $row['email']; ?>"><br/><br/>
<label for="headline">Headline :</label>
<input type="text" name="headline" id="headline" style="width:500px;" value="<?php echo $row['headline']; ?>"><br/><br/>
<label for="summary">Summary :</label><br><br/>
<textarea  name="summary" id="summary" rows="10" cols="100" >
<?php echo $row['summary']; ?></textarea>
<label for="position">Position :</label>
<input type="submit" name="addPos" id="addPos" value="+" >
<br><br/>
<div id="position_fields">

<?php
if ( $positions >0 ){
$counter=0;
foreach($positions as $pos){
			$counter++;
			echo "<div id='position".$counter."'> 
						<p>Year: <input type='text' name='year".$counter."' value=".htmlspecialchars($pos['year'])." /> 
						<input type='button' value='-' 
							onclick='removeFunction(".$counter.");return false;'></p> 
						<textarea name='desc".$counter."' rows='8' cols='80'>".htmlspecialchars($pos['description'])."</textarea>
						</div>";
			
		}
$_SESSION['counter']=$counter;
			?>
</div><br/><br/>



<input type="submit"  value="Save" name="Save" onclick="return doValidate();" >
<input type="submit" name="cancel" value="Cancel">
</form>
<?php  }  
	else 
	{
		echo "<p> No position</p></div>";
	}
				
} else  
                {  
                     $message = '<label>Invalid Data</label>';  
                } 
} catch(PDOException $error)  
 {  
      echo $error->getMessage();  
 } }?>
</div>
<script>
function removeFunction(x) {
  $('#position'+x).remove();
}


countPos=<?php echo $_SESSION['counter']; ?>; 
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of 9 position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script> 
</body>
</html>
