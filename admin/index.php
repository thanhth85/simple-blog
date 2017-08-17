<?php
//include config
require_once('../includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); }

//show message from add / edit page
if(isset($_GET['delpost'])){ 

	$stmt = $db->prepare('DELETE FROM blog_posts WHERE postID = :postID') ;
	$stmt->execute(array(':postID' => $_GET['delpost']));

	header('Location: index.php?action=deleted');
	exit;
}

//show message from  page
if(isset($_POST['postID']) && isset($_POST['postStatus'])){ 

	//insert into database
	$stmt = $db->prepare('UPDATE blog_posts SET postStatus = :postStatus WHERE postID = :postID') ;
	$stmt->execute(array(
		':postStatus' => $_POST['postStatus'],
		':postID' => $_POST['postID']
	));

	header('Location: index.php?action=approved');
	exit;
}  

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin</title>
  <link rel="stylesheet" href="../style/normalize.css">
  <link rel="stylesheet" href="../style/main.css">
  <link rel="stylesheet" href="../style/pagination.css">
  <script language="JavaScript" type="text/javascript">
  function delpost(id, title)
  {
	  if (confirm("Are you sure you want to delete '" + title + "'"))
	  {
	  	window.location.href = 'index.php?delpost=' + id;
	  }
  }

  function appropost(id, v_status)
  {
  	var xmlhttp;

  	if (window.XMLHttpRequest) {
	    // code for modern browsers
	    xmlhttp = new XMLHttpRequest();
	 } else {
	    // code for old IE browsers
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	      alert('Approved post on public!');
	      document.getElementById("appropost").style.display = "none";
	      window.location = "index.php";
	    }
	 };
	xmlhttp.open("POST", "index.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("postID="+ id + "&postStatus=" + v_status);
 
  }

  function unappropost(id, v_status)
  {
  	var xmlhttp;

  	if (window.XMLHttpRequest) {
	    // code for modern browsers
	    xmlhttp = new XMLHttpRequest();
	 } else {
	    // code for old IE browsers
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	      alert('Unapproved post on public!');
	      document.getElementById("unappropost").style.display = "none";
	      window.location = "index.php";
	    }
	 };
	xmlhttp.open("POST", "index.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("postID="+ id + "&postStatus=" + v_status);
 
  }
  </script>
</head>
<body>

	<div id="wrapper">

	<?php include('menu.php');?>

	<?php 
	//show message from add / edit page
	if(isset($_GET['action'])){ 
		echo '<h3>Post '.$_GET['action'].'.</h3>'; 
	} 
	?>

	<table>
	<tr>
		<th>Title</th>
		<th>Author</th>
		<th>Status</th>
		<th>Date</th>
		<th>Action</th>
	</tr>
	<?php
		try {
			// If user is admin  
			if($_SESSION['memberID'] == 1) {
				//instantiate the class
				$pages = new Paginator('5','p');

				//collect all records fro the next function
				$stmt = $db->query('SELECT postID FROM blog_posts');

				//determine the total number of records
				$pages->set_total($stmt->rowCount());

				// posts
				$stmt = $db->query('SELECT postID, postTitle, postDate, postStatus,postMemberID, username FROM blog_members LEFT JOIN blog_posts ON memberID = postMemberID ORDER BY postID DESC ' .$pages->get_limit());

				//list posts
				while($row = $stmt->fetch()){
					
					echo '<tr>';
					echo '<td>'.$row['postTitle'].'</td>';
					echo '<td>'.(($row['postMemberID'] == 1)? $_SESSION['username']:$row['username']).'</td>';
                    echo '<td>'.(($row['postStatus'] == 1)? "Approved" : "Unapproved").'</td>';
					echo '<td>'.date('jS M Y', strtotime($row['postDate'])).'</td>';
					?>

					<td>
						<a href="edit-post.php?id=<?php echo $row['postID'];?>">Edit</a> | 
						<a href="javascript:delpost('<?php echo $row['postID'];?>','<?php echo $row['postTitle'];?>')">Delete</a>

						<?php if($row['postStatus'] == 1) {?>
							| <a id="unappropost" href="javascript:unappropost('<?php echo $row['postID'];?>',0)">Unapproved</a>
						<?php } else { ?>
							| <a id="appropost" href="javascript:appropost('<?php echo $row['postID'];?>',1)">Approved</a>
						<?php } ?>

					</td>
					
					<?php 
					echo '</tr>';

				}// end posts
			} else {
				//instantiate the class
				$pages = new Paginator('5','p');

				//collect all records fro the next function
				$stmt = $db->prepare('SELECT postID FROM blog_posts WHERE postMemberID = :postMemberID');
				$stmt->execute(array(':postMemberID' => $_SESSION['memberID']));

				//determine the total number of records
				$pages->set_total($stmt->rowCount());

				// posts
				$stmt = $db->prepare('SELECT postID, postTitle, postDate, postStatus,postMemberID, username FROM blog_members LEFT JOIN blog_posts ON memberID = postMemberID WHERE postMemberID = :postMemberID ORDER BY postID DESC ' .$pages->get_limit());
				$stmt->execute(array(':postMemberID' => $_SESSION['memberID']));
				//list posts
				while($row = $stmt->fetch()){
					
					echo '<tr>';
					echo '<td>'.$row['postTitle'].'</td>';
					echo '<td>'.(($row['postMemberID'] == 1)? $_SESSION['username']:$row['username']).'</td>';
                    echo '<td>'.(($row['postStatus'] == 1)? "Approved" : "Unapproved").'</td>';
					echo '<td>'.date('jS M Y', strtotime($row['postDate'])).'</td>';
					?>

					<td>
						<a href="edit-post.php?id=<?php echo $row['postID'];?>">Edit</a> | 
						<a href="javascript:delpost('<?php echo $row['postID'];?>','<?php echo $row['postTitle'];?>')">Delete</a>
					</td>
					
					<?php 
					echo '</tr>';

				}// end posts
			}

		} catch(PDOException $e) {
		    echo $e->getMessage();
		}
	?>
	</table>
	<?php
		echo '<center>' . $pages->page_links() . '</center>'; 
	?>

	<p><a href='add-post.php'>Add Post</a></p>

</div>

</body>
</html>
