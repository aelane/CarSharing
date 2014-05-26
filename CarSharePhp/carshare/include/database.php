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
    try{
        
    	$dbh = connect();
        
    	$stmt = $dbh->prepare("SELECT COUNT(*) 
    						FROM member 
    						WHERE nickname = :nN AND passwd = :pw");
        
    	$stmt->bindParam(':nN', $name);
        
    	$stmt->bindParam(':pw', $pass);
        
    	$stmt->execute();
        
    	$result = $stmt->fetchColumn();
        
    	$stmt->closeCursor();
    
    }
 catch (PDOException $e) {
            
    	print "Incorrect Username or Password" . $e->getMessage();
            
    	die();
            
    	return FALSE;
        
    }
                              
    
    return ($result == 1);
}

/**
 * Get details of the current user
 * @param string $user login name user
 * @return array Details of user - see index.php
 */
function getUserDetails($user) {
    // STUDENT TODO:
    // Replace lines below with code to validate details from the database
    try{
        
    	$dbh = connect();
        
        
    	//Find user's full name and concatenate
        
    	$stmtname = $dbh->prepare("SELECT givenname || ' ' || familyname FROM member WHERE nickName = :nN");
        
    	$stmtname->bindParam(':nN',$user);
        
    	$stmtname->execute();
        
    	$results['name'] = $stmtname->fetchColumn();
        
    	$stmtname->closeCursor();
 
    	       
        
        
    	//Find user's address
        
    	$stmtaddress = $dbh->prepare("SELECT address FROM member WHERE nickName = :nN");
        
    	$stmtaddress->bindParam(':nN',$user);
        
    	$stmtaddress->execute();
        
    	$results['address'] = $stmtaddress->fetchColumn();
        
    	$stmtaddress->closeCursor();

    	        
        
    	//Find user's homepod (if avaliable)
        
    	$stmtpod = $dbh->prepare("SELECT pod.name FROM member JOIN pod ON homepod = id WHERE nickName = :nN");
        
    	$stmtpod->bindParam(':nN',$user);
        
    	$stmtpod->execute();
        
    	$results['homepod'] = $stmtpod->fetchColumn();
        
    	if (is_null($results['homepod'])){
           
    		$results['homepod'] = 'No homepod';
        
    	}
        
    	$stmtpod->closeCursor();
  
    	      
        
    	//For finding number of bookings user has
        
    	$stmtnbook = $dbh->prepare("SELECT COUNT(*) FROM member JOIN booking ON memberNo = madeBy WHERE nickName = :nN");
        
    	$stmtnbook->bindParam(':nN', $user);
        
    	$stmtnbook->execute();
        
    	$results['nbookings'] = $stmtnbook->fetchColumn();
        
    	$stmtnbook->closeCursor();
    } catch (PDOException $e) {
        
    	print "Unable to obtain user data" . $e->getMessage();
        
   		die();
        
   		return FALSE;
    
   	}
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
	try {
	//Prepare pod name
		$dbh = connect();
 
		$stmt = $dbh->prepare("SELECT name 
								FROM Pod JOIN Member ON id = homePod
								WHERE nickName = :nN");
		$stmt->bindParam(':nN', $user);
		$stmt->execute();
		$result = $stmt->fetchall();
		$stmt->closeCursor();
		return $result;
	} catch (PDOException $e) {
        
    	print "Unable to obtain user data" . $e->getMessage();
        
   		die();
        
   		return FALSE;
    
   	}
	return null;
}

/**
 * Retrieve information on cars located at a pod
 * @param string $pod name of pod
 * @return array Various details of each car - see homepod.php
 * @throws Exception 
 */
function getPodCars($pod) {

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


/**
*Returns the review information for a new car
**/
function getCarReviews($carname) {
    try{
        
    	$dbh = connect();
 
		$stmt = $dbh->prepare("SELECT description, rating, nickname, whendone 
				FROM member M, car C, review R
				WHERE C.name = :cname
				AND M.memberno = R.memberno
				AND C.regno = R.regno
				ORDER BY R.whendone, M.nickname");
	    $stmt->bindParam(':cname', $carname);
        
    	$stmt->execute();
        
    	$results = $stmt->fetchAll();
        
    	$stmt->closeCursor();
    } catch (PDOException $e) {
        
    	print "No reviews could be retrieved for this car" . $e->getMessage();
        
   		die();
        
   		return FALSE;
    
   	}
    return $results;
}


function writeReview($user, $carname, $description, $rating) {
	try {
	$dbh = connect();
 
	//$dbh->beginTransaction();

 	/*$stmt = $dbh->query("INSERT INTO review VALUES 
			((SELECT memberno FROM member WHERE nickname = :nn),
			(SELECT regno FROM car WHERE name = carname),
			CURRENT_DATE, 
			rating,
			:description)");
			*/

 	//$dbh->commit(); 
	$review['status'] == 'success'
    } catch (PDOException $e) {
        
    	print "No reviews could be retrieved for this car" . $e->getMessage();
        
   		die();
        
   		return FALSE;
    
   	}
   	return $review
}
?>