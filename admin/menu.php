<?php
//include config
require_once('../includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); }
?>
<h1>Blog</h1>
<ul id='adminmenu'>
	<li><a href='index.php'>Blog</a></li>
	<?php
		// If user is admin  
		if($_SESSION['memberID'] == 1) {
	?>
		<li><a href='users.php'>Users</a></li>
		<li><a href="../" target="_blank">View Website</a></li>
		<li><a href='logout.php'>Logout <?php echo '>>' . $_SESSION['username'] . "(admin)"; ?></a></li>
	<?php } else { ?>
	
	<li><a href="../" target="_blank">View Website</a></li>
	<li><a href='logout.php'>Logout <?php echo '>>' . $_SESSION['username']; ?></a></li>
	<?php } ?>
</ul>
<div class='clear'></div>
<hr />