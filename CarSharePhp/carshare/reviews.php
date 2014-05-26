<?php 
/**
 * Reviews and Ratings Page
 * Links to search new cars
 */
require_once('include/common.php');
require_once('include/database.php');
startValidSession();
htmlHead();
?>

<h1>Reviews and Ratings</h1>

<form action="reviews.php" method="get">
<label>Car<input type=text value="<?php echo $_GET['carname'];?>"name="carname" /></label><br />
<input type=submit value="Search"/>
<?php 
try {
	$reviews = getCarReviews($_GET['carname']);
?>
</form>
<table>
<thead>
<tr><th>Comments</th><th>Rating</th><th>Username</th><th>Date Written</th></tr>
</thead>
<tbody>
<?php
foreach($reviews as $review) {
    echo '<tr><td>',$review['description'],'</td><td>',$review['rating'],'</td>',
            '<td>',$review['nickname'],'</td><td>',$review['whendone'],'</td></tr>';
}
?>
</tbody>
</table>
<?php
} catch (Exception $e) {
        echo 'Cannot find any reviews for this car';
}
htmlFoot();
?>