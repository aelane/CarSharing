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
	// read database seetings from config file
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
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM member WHERE nickname = :nN AND passwd = :pw");
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
 //Return no cars if no pod specified
	if (empty($pod)) return array();
	    $results = array(
        array('id'=>1234,'name'=>'Garry the Getz','avail'=>true),
        array('id'=>4563,'name'=>'Larry the Landrover','avail'=>false),
        array('id'=>7789,'name'=>'Harry the Hovercycle','avail'=>true)
    );
    return $results;
	/*
	try{

	//Get the car id and name from the database
	$dhb = connect();
	//Find id of the car 
	$stmtid = $dbh->prepare("SELECT regno FROM Car JOIN Pod On parkedAt = id 
								WHERE id IN (SELECT id 
												FROM Pod JOIN Member ON id = homePod
												WHERE nickname = :nN)");

	$stmtid->bindParam(':nN', $user);
	$stmtid->execute();
	
	$results['id'] = $stmtid->fetchAll();
	
	$stmtid->closeCursor();
	
	//Find name of the car
	
	$stmtname = $dbh->prepare("SELECT C.name FROM Car  c JOIN Pod P ON parkedAt = id
							WHERE id IN (SELECT id 
								
							FROM Pod JOIN Member ON id = homePod
								
							WHERE nickname = :nN)");

	$stmtname->bindParam(':nN', $user);
	$stmtname->execute();
	$results['name'] = $stmtname->fetchAll();
	$stmtname->closeCursor();
	
	//Find list of car names that are unavailable

	$stmtunavail = $dbh->prepare("SELECT name FROM Car JOIN Booking ON regno = car
							WHERE CURRENT_TIMESTAMP > starttime
								AND CURRENT_TIMESTAMP < endTime");						
	$stmtunavail->execute();
	$unavail[] = $stmtunavail->fetchAll();
	
	//Default 'avail' set to true
	$restults['avail'] = true;
	
	//catching exception
	} catch (PDOException $e) {
        
    	print "Unable to get Car Pods" . $e->getMessage();
        
   		die();
        
   		return FALSE;
    
   	}
return $results;
*/
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
	/*
	$outcome = array();
	try {
		$db = connect();
 
		$db->beginTransaction();
		$stmt = $db->prepare("SELECT*
							  FROM CarSharing.Booking JOIN CarSharing.Car On (Booking.car = Car.regno)
							  WHERE name=:car AND ((endTime>:start AND endTime<:end) OR (startTime>:start AND startTime<:end))");
		$stmt->bindValue(':car', $car);
		$d = date('d-m-y', strtotime($tripdate));
		$t = date('h:i:s', strtotime($starttime));
		$start = date('d-m-y h:i:s', $d . ' ' . $t);
        $stmt->bindValue(':start', $start);
		$end = date('d-m-y h-i-s A', strtotime($start) + 60*60*$numhours);
		$stmt->bindValue(':end', $end);
		$stmt->execute();
		$result = $stmt->fetch();
		$stmt->closeCursor();
		if (empty($result)) {
			$stmt2 = $db->prepare("INSERT INTO CarSharing.Booking
									     VALUES (:car, :madeBy, current_timestamp, "ok", :startTime, :endTime)");
			$carId = $db->query("SELECT DISTINCT regno
								 FROM CarSharing.Car
								 WHERE name=$car");
			
			$stmt2->bindValue(':car', $carId);
			$stmt2->bindValue(':madeBy', $user);
			$stmt2->bindValue(':startTime', $start);
			$stmt2->bindValue(':endTime', $end);							
										
			$stmt2->execute();
				$bookingId = query("SELECT count(id)
									FROM CarSharing.Booking");									
				$stmt2->closeCursor();

				$outcome['status']="success";
				$outcome['id']=$bookingId
				$outcome['car']=$car;
				$outcome['start']=$start;
				$outcome['end']=$end;
				$podId=query("SELECT DISTINCT parkedAt
									   FROM CarSharing.Car
									   WHERE regno = $carId");
				$outcome['pod']=query("SELECT DISTINCT name
									   FROM CarSharing.Pod
									   WHERE id = $podId");
				$outcome['address']=query("SELECT DISTINCT addr
										   FROM CarSharing.Pod
										   WHERE id = $podId");
				$timeCharge=query("SELECT timeCharge
									FROM CarSharing.InvoiceLine
									WHERE bookingId = $bookingId");
				$kmCharge=query("SELECT kmCharge
									FROM CarSharing.InvoiceLine
									WHERE bookingId = $bookingId");
				$feeCharge=query("SELECT feeCharge
									FROM CarSharing.InvoiceLine
									WHERE bookingId = $bookingId");			
				$outcome['cost']=$timeCharge + $kmCharge + $feeCharge;					
			} else { 
				$db->rollback(); 
			} 
			
		} else { 
			print "The car you booked is not available."; 
			$db->rollback(); 
		} 
	} catch (PDOException $e) { 
		print "Error making the booking: " . $e->getMessage(); 
	} 
	return $outcome;
	*/
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


//stores review on the database
function writeReview($user, $carname, $description, $rating) {
	try {
	$dbh = connect();
 
	$dbh->beginTransaction();

 	$stmt = $dbh->prepare("INSERT INTO review VALUES 
			((SELECT memberno FROM member WHERE nickname = :nn),
			(SELECT regno FROM car WHERE name = :cn),
			CURRENT_DATE, 
			:r,
			:descr)");
	$stmt->bindParam(':nn',$user);
	$stmt->bindParam(':cn',$carname);
	$stmt->bindParam(':r',$rating);
	$stmt->bindParam(':descr',$description);
	$stmt->execute();
	$stmt2 = $dbh->prepare("UPDATE memberstats SET stat_nrreviews = stat_nrreviews + 1 WHERE (SELECT memberno FROM member WHERE nickname = :nn");

 	$stmt2->bindParam(':nn',$user);
	$stmt2->execute();
	$review['status'] = 'success';
	$dbh->commit(); 

    } catch (PDOException $e) {
        
    	print "No reviews could be retrieved for this car" . $e->getMessage();
        
   		die();
        
   		return FALSE;
    
   	}
   	return $review;
}


?>

