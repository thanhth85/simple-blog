<?php require('includes/config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Blog</title>
    <link rel="stylesheet" href="style/normalize.css">
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/pagination.css">
</head>
<body>

	<div id="wrapper">

		<h1>Blog</h1>
		<hr />

		<?php
			try {
				//instantiate the class
				$pages = new Paginator('5','p');

				//collect all records fro the next function
				$stmt = $db->query('SELECT postID FROM blog_posts WHERE postStatus = 1');

				//determine the total number of records
				$pages->set_total($stmt->rowCount());

				$stmt = $db->query('SELECT postID, postTitle, postDesc, postDate FROM blog_posts WHERE postStatus = 1 ORDER BY postID DESC '.$pages->get_limit());
				while($row = $stmt->fetch()){
					
					echo '<div>';
						echo '<h1><a href="viewpost.php?id='.$row['postID'].'">'.$row['postTitle'].'</a></h1>';
						echo '<p>Posted on '.date('jS M Y H:i:s', strtotime($row['postDate'])).'</p>';
						echo '<p>'.$row['postDesc'].'</p>';				
						echo '<p><a href="viewpost.php?id='.$row['postID'].'">Read More</a></p>';				
					echo '</div>';

				}
				echo $pages->page_links();

			} catch(PDOException $e) {
			    echo $e->getMessage();
			}
		?>

	</div>


</body>
</html>