<?php
$keys = file_get_contents("data/keys.txt");
$keys = explode("\n", $keys);

$upload_dir_short = "uploads/"; // relative to this file, leave a trailing slash ('/')
$max_file_size = 20000000; // in bytes

$current_dir = substr($_SERVER["SCRIPT_FILENAME"], 0, -(strlen($_SERVER["PHP_SELF"])) + 1);
$upload_dir = $current_dir . $upload_dir_short;
$key_id = -1;

function generate_filename() {
	$date = date_create();
	$timestamp = date_timestamp_get($date);

	// generate base filename with as much randomness as possible
	$ret = $timestamp . $_POST["key"] . $_FILES["file"]["name"] . $_FILES["file"]["size"] . rand(0, 100000000);

	// sha256 hash filename to make it harder to find/identify
	$ret = hash("sha256", $ret);

	// find extension of uploaded file
	$split = preg_split("/\./", $_FILES["file"]["name"]);
	$ext = $split[sizeof($split)-1];

	// use start of the hash and the original extension for the final filename
	$ret = substr($ret, 0, 10) . "." . $ext;

	// generate new filename if this one already used
	if (file_exists($upload_dir . $ret)) {
		return generate_filename();
	}
	return $ret;
}

if (!isset($_POST["key"]) ||
	!isset($_FILES["file"]) ||
	$_FILES["file"]["size"] > $max_file_size ||
	$_SERVER["REQUEST_METHOD"] !== "POST" ||
	strpos($_FILES["file"]["name"], ".php") !== false ||
	strpos($_FILES["file"]["name"], ".phtml") !== false // this security alone will not work
) {
	http_response_code(400);
} else {
	for ($i = 0; $i < sizeof($keys); $i++) {
		if ($_POST["key"] == preg_split("/\|/", $keys[$i])[1]) $key_id = $i;
	}

	if (strlen($_POST["key"]) < 32) $key_id = -1;

	if ($key_id !== -1) {
		$filename = generate_filename();
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $upload_dir . $filename)) {
			// print url
			echo $_SERVER["HTTP_REFERER"] . $upload_dir_short . $filename;

			// create log entry
			$date = date_create();

			$log_entry = "[" . date_format($date, "Y-m-d, H:i:s") . "] "
			. $_SERVER["REMOTE_ADDR"] . " - "
			. $keys[$key_id] . ", "
			. "uploaded filename: " . $filename . ", "
			. "original filename: " . $_FILES["file"]["name"] . ", "
			. "file size: " . $_FILES["file"]["size"] . "\n";

			// write to ./data/log.txt
			file_put_contents($current_dir . "data/log.txt", $log_entry, FILE_APPEND);
		} else {
			http_response_code(500);
		}
	} else {
		http_response_code(401);
	}
}
?>
