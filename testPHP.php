<?php
// - reecive two arrays from FileA and FileB
$csv = array_map('str_getcsv', file('/var/www/html/FileA.csv'));
$csv1 = array_map('str_getcsv', file('/var/www/html/FileB.csv'));

// - checking arrays
$csvlength = count($csv);
for($x = 0; $x < $csvlength; $x++) {
	for($y = 0; $y < count($csv[$x]); $y++) {
  		echo $csv[$x][$y];
  		echo " ";
	}
echo "\n";		
}
echo "\n";
echo "\n";
for($x = 0; $x < count($csv1); $x++) {
	for($y = 0; $y < count($csv1[$x]); $y++) {
  		echo $csv1[$x][$y];
  		echo " ";
	}
echo "\n";
}
echo "\n";
echo "\n";

//merge two arrays
$csv_last = count($csv);
$csv1_last = count($csv1);
$x = 1;
while ($x < $csv_last) {
	$y = 1;
	while ($y < $csv1_last) {
		if ((string)$csv[$x][0] == (string)$csv1[$y][0]) {
  			$csv_m[$x][0] = $csv[$x][0];
  			$csv_m[$x][1] = $csv[$x][1];
  			$csv_m[$x][2] = $csv1[$y][1];
  			$csv_m[$x][3] = $csv1[$y][2];
		}
		$y++;
	}
	$x++;	
}
// - checking the result
for($x = 1; $x <= count($csv_m); $x++) {
	echo $csv_m[$x][0]; echo " ";
	echo $csv_m[$x][1]; echo " ";
	echo $csv_m[$x][2]; echo " ";
	echo $csv_m[$x][3]; echo " ";
	echo "\n";	
}
echo "\n";
echo "\n";


//receive the user list from API
$url = 'https://sandbox.tinypass.com/api/v3/publisher/user/list';
$data = array('api_token' => 'zziNT81wShznajW2BD5eLA4VCkmNJ88Guye7Sw4D', 'aid' => 'o1sRRZSLlw', 'offset' => '0');

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }

//echo $result;
$csv_r = json_decode($result, true);


//change UID from API result
$csv_m_last = count($csv_m);
$csv_r_last = count($csv_r["users"]);
$x = 1;
while ($x < $csv_m_last) {
	$y = 0;
	while ($y < $csv_r_last) {
		if ((string)$csv_m[$x][1] == (string)$csv_r["users"][$y]["email"]) {
  			$csv_m[$x][0] = $csv_r["users"][$y]["uid"];
		}
		$y++;
	}
	$y = 0;
	while ($y < $csv_r_last) {
		if (((string)$csv_m[$x][2] == (string)$csv_r["users"][$y]["first_name"]) and ((string)$csv_m[$x][3] == (string)$csv_r["users"][$y]["last_name"])) {
  			$csv_m[$x][0] = $csv_r["users"][$y]["uid"];
		}
		$y++;
	}
	$x++;	
}

// - checking the result
for($x = 1; $x <= count($csv_m); $x++) {
	echo $csv_m[$x][0]; echo " ";
	echo $csv_m[$x][1]; echo " ";
	echo $csv_m[$x][2]; echo " ";
	echo $csv_m[$x][3]; echo " ";
	echo "\n";	
}
echo "\n";
echo "\n";

// - write to CSV the results
$myfile = fopen("/var/www/html/Finalphp.csv", "w") or die("Unable to open file!");
for($x = 1; $x <= count($csv_m); $x++) {
	$txt = (string)$csv_m[$x][0].", ".(string)$csv_m[$x][1].", ".(string)$csv_m[$x][2].", ".(string)$csv_m[$x][3]."\n";
	echo $txt;
	fwrite($myfile, $txt);
}
fclose($myfile);


?>