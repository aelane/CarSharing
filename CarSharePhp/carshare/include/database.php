<?php
/**
 * Database functions. You need to modify each of these to interact with the database and return appropriate results. 
 */

/**
 * Connect to database
 * This function does not need to be edited - just update config.ini with your own 
 * database connection details. 
 * @param string $file Location of configuration data
 * @return PDO database object
 * @throws exception
 */
function connect($file = 'config.ini') {
	// read database settings from config file
    if ( !$settings = parse_ini_file($file, TRUE) ) 
        throw new exception('Unable to open ' . $file);
    
    // parse contents of config.ini
    $dns = $settings['database']['driver'] . ':' .
            'host=' . $settings['database']['host'] .
            ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
            ';dbname=' . $settings['database']['schema'];
    $user= $settings['db_user']['username'];
    $pw  = $settings['db_user']['password'];

	// create new database connection
    try {
        $dbh=new PDO($dns, $user, $pw);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        print "Error Connecting to Database: " . $e->getMessage() . "<br/>";
        die();
    }
    return $dbh;
}

/**
 * Check login details
 * @param string $name Login name
 * @param string $pass Password
 * @return boolean True is login details are correct
 */
function checkLogin($name,$pass) {
    // STUDENT TODO:
    // Replace line below with code to validate details from the database
    //
    $stmt1 = $conn->prepare("SELECT password 
    							FROM member 
    							WHERE nickName = :nN");
    $stmt2 = $conn->prepare("SELECT password
    							FROM member
    							WHERE password = ':pw");
    $stmt1->bindParam(':nN', $name);
    $stmt2->bindParam(':pw', $pass);
    $stmt1->execute();
    
    return true;
    return false;
}

/**
 * Get details of the current user
 * @param string $user login name user
 * @return array Details of user - see index.php
 */
function getUserDetails($user) {
    // STUDENT TODO:
    // Replace lines below with code to validate details from the database
    if ($user != 'testuser') throw new Exception('Unknown user');
    $results = array();
    // Example user data - this should come from a query
    $results['name'] = 'Demo user';
    $results['address'] = 'Demo location, Sydney, Australia';
    $results['homepod'] = 'Demo pod';
    $results['nbookings'] = 17;   
    return $results;
}

/**
 * Get details of the current user
 * @param string $user ID of member
 * @return Name of user's home pod, or null if no home pod exists - see homepod.php
 */
function getHomePod($user) {
    // STUDENT TODO:
    // Change lines below with code to retrieve user's home pod from the database
	if ($user == 'testuser') {
		return 'testpod';
	}
	else return null;
}

/**
 * Retrieve information on cars located at a pod
 * @param string $pod name of pod
 * @return array Various details of each car - see homepod.php
 * @throws Exception 
 */
function getPodCars($pod) {
	// Return no cars if no pod specified
	if (empty($pod)) return array();
	
    // STUDENT TODO:
    // Replace lines below with code to get list of cars from the database
    // Example car info - this should come from a query. Format is
	// (car ID, Car Name, Car currently available)
    $results = array(
        array('id'=>1234,'name'=>'Garry the Getz','avail'=>true),
        array('id'=>4563,'name'=>'Larry the Landrover','avail'=>false),
        array('id'=>7789,'name'=>'Harry the Hovercycle','avail'=>true)
    );
    return $results;
}

/**
 * Retrieve information on active bookings for a user
 * @param string $user ID of member 
 * @return array Various details of each booking - see bookings.php
 * @throws Exception 
 */
function getOpenBookings($user) {
    // STUDENT TODO:
    // Replace lines below with code to get list of bookings from the database
    // Example booking info - this should come from a query. Format is
	// (booking ID, Car Name, Booking start date, booking start time, booking end time)
    $results = array(
        array('id'=>954673,'car'=>'Garry the Getz','date'=>'11/06/14', 'start'=>'8:00 AM', 'end'=>'11:00 PM'),
        array('id'=>409856,'car'=>'Harry the Hovercycle','date'=>'20/07/14', 'start'=>'10:00 AM', 'end'=>'11:00 AM')
    );
    return $results;
}

/**
 * Make a new booking for a car
 * @param string $user Member booking car
 * @param string $car Name of car to book
 * @param string $start
 * @return array Various details of current visit - see newbooking.php
 * @throws Exception 
 */
function makeBooking($user,$car,$tripdate,$starttime,$numhours) {
    // STUDENT TODO:
    // Replace lines below with code to create a booking and return the outcome

    if ($user != 'testuser') throw new Exception('Unknown user');
    return array(
            'status'=>'success',
            'id'=>2,
            'car'=>'Garry the Getz',
			'start'=>'25/07/14 10:00:00',
			'end'=>'25/07/14 19:00:00',
            'pod'=>'School of IT entrance',
			'address'=>'Camperdown, Sydney',
			'cost'=>'21.20'
        );
}

?>