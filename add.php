<?php
session_start();
// Database connection attributes 


$server = "localhost";
$username = "root";
$password = "";
$dbname = "misc";
$message="";

//action for "cancel"
if(isset($_POST["cancel"]))  
      { 
		header("location:index.php"); 
	  }

try {
	//DB connection
	$conn = new PDO("mysql:host=$server; dbname=$dbname", $username, $password);  
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  

	//validating the position field
	/*function validatePos() {
		for($i=1; $i<=9; $i++) {
			if ( ! isset($_POST['year'.$i]) ) continue;
			if ( ! isset($_POST['desc'.$i]) ) continue;
			$year = $_POST['year'.$i];
			$desc = $_POST['desc'.$i];
			if ( strlen($year) == 0 || strlen($desc) == 0 ) {
				return "All fields are required !!!";
			}
	
			if ( ! is_numeric($year) ) {
				return "Position year must be numeric!!!";
			}
		}
		return true;
	}
*/
	//Action  for "Add" 
	if(isset($_POST["Add"]))  
	{	 
		
		if( empty(htmlentities($_POST["first_name"])) ||empty(htmlentities($_POST["last_name"])) || empty(htmlentities($_POST["headline"]))
		|| empty(htmlentities($_POST["email"])) || empty(htmlentities($_POST["summary"])))
			{
				$_SESSION['error'] = 'All values are required';
				
				header( "Refresh:1; url=add.php", true,200);
				//header("Location: add.php");
				//echo "<script>alert('".$_SESSION['error']."')</script>";
			}
		/*elseif(! validatePos()){
			  echo "Entered";
				$msg= validatePos();
				if(is_string($msg)){
						$_SESSION['error']= $msg;
						header("location : add.php");
					}
		}*/
			
		else{
			
			$fname=htmlentities($_POST["first_name"]);
			$lname=htmlentities($_POST["last_name"]);
			$headline=htmlentities($_POST["headline"]);
			$email=htmlentities($_POST["email"]);
			$summary=htmlentities($_POST["summary"]);
			$uid=$_SESSION['user_id'];
			$query = "INSERT INTO profile(user_id,first_name,last_name,email,headline,summary) 
			VALUES(:uid,:fname,:lname,:email,:headline,:summary)"; 
			try{
					$statement = $conn->prepare($query);  
					$statement->execute(array( 
									'uid'       =>     $uid,
									'fname'     =>     $fname,  
									'lname'     =>     $lname,
									'email'     =>     $email,
									'headline'  =>     $headline,
									'summary'   =>     $summary)); 
					$prof_id=$conn->lastInsertId();
					
					$rank=1;
					for($i=1;$i<=9;$i++){
							if( ! isset($_POST['year'.$i])) continue;
							if( ! isset($_POST['desc'.$i])) continue;
							$year=$_POST['year'.$i];
							$desc=$_POST['desc'.$i];
							if ( strlen($year) == 0 || strlen($desc) == 0 ) {
								$_SESSION['error'] = "All fields are required !!!";
								header("Location: add.php");//return "All fields are required !!!";
							}
					
							elseif ( ! is_numeric($year) ) {
								$_SESSION['error'] = "Position year must be numeric!!! !!!";
								header("Location: add.php");
								//return "Position year must be numeric!!!";
							}
							$stmt = $conn->prepare('INSERT INTO Position
							(profile_id, rank, year, description) 
							VALUES ( :pid, :rank, :year, :desc)');
							$stmt->execute(array(
								':pid' => $prof_id,
								':rank' => $rank,
								':year' => $year,
								':desc' => $desc)
							);
						$rank++;
					}



					$_SESSION['toast'] = "profile added*";
					header("location:index.php"); 
			}
			catch(PDOException $error){
				$_SESSION['error']= "Unable to insert*";	
			}
		}
		//header("Location: add.php");
	}
}
catch(PDOException $error)  
 {  
      echo $error->getMessage();  
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

<h1>Adding Profile for <?php echo $_SESSION["name"];?></h1>
<hr><div class="error_block">
<?php  
    if(isset($_SESSION['error']))  
    {  
        echo '<p class="error" style="color:red;">'.$_SESSION['error'].'</p>';
		unset($_SESSION['error']);
	}
	
	
?>

<!--form -->
</div><hr>
<form method="POST" >
<label for="frist_name" >Frist Name :</label>
<input type="text" name="first_name" id="f_name" style="width:500px;" ><br/><br/>
<label for="last_name">Last Name :</label>
<input type="text" name="last_name" id="l_name" style="width:500px;" ><br/><br/>
<label for="email">Email :</label>
<input type="text" name="email" id="email" style="width:500px;" ><br/><br/>
<label for="headline">Headline :</label>
<input type="text" name="headline" id="headline" style="width:500px;" ><br/><br/>
<label for="summary">Summary :</label><br><br/>
<textarea  name="summary" id="summary" rows="10" cols="100">

</textarea><br/><br/>

<label for="position">Position :</label>
<input type="submit" name="addPos" id="addPos" value="+" >
<br><br/>
<div id="position_fields">
</div>
<input type="submit"  value="Add" name="Add" id="Add">
<input type="submit" name="cancel" value="Cancel">
</form>

<?php } ?>
</div>
<script>
countPos = 0;

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