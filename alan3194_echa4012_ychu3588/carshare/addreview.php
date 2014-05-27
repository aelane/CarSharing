<?php 
/**
 * Create a review page
 * 
 */
require_once('include/common.php');
require_once('include/database.php');
startValidSession();
htmlHead();
?>

<h1>Write a Review!</h1>
<?php
// Check whether all attributes for booking have been submitted
$submit = !empty($_REQUEST['carname']) 
	&& !empty($_REQUEST['description']) 
	&& !empty($_REQUEST['rating']);
$newreview = null;
	
if ($submit) {
echo 'Submitting review.';
    try {
        $newreview = writeReview($_SESSION['member'], $_REQUEST['carname'], $_REQUEST['description'], $_REQUEST['rating']);
        if($newreview['status'] == 'success') { 
            echo '<h2>Congratulations, you\'ve written a new review!';
        } else {
            echo '<h2>Sorry, couldn\t post your review:</h2>', $newreview['status'];
        }
    } catch (Exception $e) {
            echo 'Couldn\'t submit review. Please try again.';
    }
} else {
	echo 'Please fill in all fields of the review.';
}

if (!$submit || $newreview==null || $newreview['status'] != 'success') {
	// Supply defaults for any unset values	
	$carname = isset($_REQUEST['carname']) ? $_REQUEST['carname'] : '';
	$tripdate = isset($_REQUEST['description']) ? $_REQUEST['description'] : '';
	$starttime = isset($_REQUEST['rating']) ? $_REQUEST['rating'] : 3;

?>
    <form action="addreview.php" id="addreview" method="post">
        <label>Car <input type="text" name="car" value="<?php echo $carname;?>"/></label><br />
		<label>Comments <input type="text" name="comments" /></label><br />
		<label>Rating <br>
		<input type="radio" name="rating" value=1> 1 
		<input type="radio" name="rating" value=2> 2 
		<input type="radio" name="rating" value=3> 3 
		<input type="radio" name="rating" value=4> 4 
		<input type="radio" name="rating" value=5> 5  </label><br />
		<br /><input type=submit value="Submit Review"/>
    </form>
<?php
}
htmlFoot();
?>
