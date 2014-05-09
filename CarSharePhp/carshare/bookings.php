<?php 
/**
 * Web page to display users active bookings
 */
require_once('include/common.php');
require_once('include/database.php');
startValidSession();
htmlHead();
?>
<h1>Active Bookings</h1>
<?php 
try {
    $bookings = getOpenBookings($_SESSION['member']);
    echo '<table>';
    echo '<thead>';
    echo '<tr><th>Car</th><th>Date</th><th>Start</th><th>End</th></tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($bookings as $booking) {
        echo '<tr><td>',$booking['car'],'</td>',
                '<td>',$booking['date'],'</td><td>',$booking['start'],'</td>',
                '<td>',$booking['end'],'</td></tr>';
    }
    echo '</tbody>';
    echo '</table>';
} catch (Exception $e) {
        echo 'Cannot get available bookings';
}
htmlFoot();
?>
