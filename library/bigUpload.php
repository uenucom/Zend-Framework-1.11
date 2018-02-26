<?php


/**
 * Sanitizes a filename replacing whitespace with dashes
 *
 * Removes special characters that are illegal in filenames on certain
 * operating systems and special characters requiring special escaping
 * to manipulate at the command line. Replaces spaces and consecutive
 * dashes with a single dash. Trim period, dash and underscore from beginning
 * and end of filename.
 *
 * @param string $filename The filename to be sanitized
 * @return string The sanitized filename
 */
function sanitizeFileName($filename) {
	// Remove special accented characters - ie. sí.
	$clean_name = strtr($filename, array('Š' => 'S','Ž' => 'Z','š' => 's','ž' => 'z','Ÿ' => 'Y','À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A','Ç' => 'C','È' => 'E','É' => 'E','Ê' => 'E','Ë' => 'E','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I','Ñ' => 'N','Ò' => 'O','Ó' => 'O','Ô' => 'O','Õ' => 'O','Ö' => 'O','Ø' => 'O','Ù' => 'U','Ú' => 'U','Û' => 'U','Ü' => 'U','Ý' => 'Y','à' => 'a','á' => 'a','â' => 'a','ã' => 'a','ä' => 'a','å' => 'a','ç' => 'c','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e','ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ñ' => 'n','ò' => 'o','ó' => 'o','ô' => 'o','õ' => 'o','ö' => 'o','ø' => 'o','ù' => 'u','ú' => 'u','û' => 'u','ü' => 'u','ý' => 'y','ÿ' => 'y'));
	$clean_name = strtr($clean_name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));

	// Enforce ASCII-only & no special characters
//	$clean_name = preg_replace(array('/\s+/', '/[^a-zA-Z0-9_\.\-]/'), array('.', ''), $clean_name);
//	$clean_name = preg_replace(array('/--+/', '/__+/', '/\.\.+/'), array('-', '_', '.'), $clean_name);
//	$clean_name = trim($clean_name, '-_.');

	// Some file systems are case-sensitive (e.g. EXT4), some are not (e.g. NTFS). 
	// We simply assume the latter to prevent confusion later.
	// 
	// Note 1: camelCased file names are converted to dotted all-lowercase: `camel.case`
	// Note 2: we assume all file systems can handle filenames with multiple dots 
	//         (after all only vintage file systems cannot, e.g. VMS/RMS, FAT/MSDOS)
//	$clean_name = preg_replace('/([a-z])([A-Z]+)/', '$1.$2', $clean_name);
//	$clean_name = strtolower($clean_name);

	// And for operating systems which don't like large paths / filenames, clip the filename to the last 64 characters:
//	$clean_name = substr($clean_name, -64);
//	$clean_name = ltrim($clean_name, '-_.');
	return $clean_name;
}

// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code'))
{
	function http_response_code($newcode = NULL)
	{
		static $code = 200;
		if($newcode !== NULL)
		{
			header('X-PHP-Response-Code: '.$newcode, true, $newcode);
			if(!headers_sent())
				$code = $newcode;
		}
		return $code;
	}
}

class BigUpload
{
	/**
	 * Temporary directory for uploading files
	 */
	const TEMP_DIRECTORY = '/tmp/files/tmp/';

	/**
	 * Directory files will be moved to after the upload is completed
	 */
	const MAIN_DIRECTORY = '/tmp/files/';

	/**
	 * Max allowed filesize. This is for unsupported browsers and
	 * as an additional security check in case someone bypasses the js filesize check.
	 *
	 * This must match the value specified in main.js
	 */
	const MAX_SIZE = 3047483648;

	/**
	 * Temporary directory
	 * @var string
	 */
	private $tempDirectory;

	/**
	 * Directory for completed uploads
	 * @var string
	 */
	private $mainDirectory;

	/**
	 * Name of the temporary file. Used as a reference to make sure chunks get written to the right file.
	 * @var string
	 */
	private $tempName;

	/**
	 * Constructor function, sets the temporary directory and main directory
	 */
	public function __construct() {
		$this->setTempDirectory(self::TEMP_DIRECTORY);
		$this->setMainDirectory(self::MAIN_DIRECTORY);
	}

	/**
	 * Create a random file name for the file to use as it's being uploaded
	 * @param string $value Temporary filename
	 */
	public function setTempName($value = null) {
		if ($value) {
			$this->tempName = sanitizeFileName($value);
		}
		else {
			$this->tempName = mt_rand() . '.tmp';
		}
	}

	/**
	 * Return the name of the temporary file
	 * @return string Temporary filename
	 */
	public function getTempName() {
		return $this->tempName;
	}

	/**
	 * Set the name of the temporary directory
	 * @param string $value Temporary directory
	 */
	public function setTempDirectory($value) {
		$this->tempDirectory = $value;
		return true;
	}

	/**
	 * Return the name of the temporary directory
	 * @return string Temporary directory
	 */
	public function getTempDirectory() {
		return $this->tempDirectory;
	}

	/**
	 * Set the name of the main directory
	 * @param string $value Main directory
	 */
	public function setMainDirectory($value) {
		$this->mainDirectory = $value;
	}

	/**
	 * Return the name of the main directory
	 * @return string Main directory
	 */
	public function getMainDirectory() {
		return $this->mainDirectory;
	}

	/**
	 * Function to upload the individual file chunks
	 * @return string JSON object with result of upload
	 */
	public function uploadFile() {
		// Make sure the total file we're writing to hasn't surpassed the file size limit
		$tmpPath = $this->getTempDirectory() . $this->getTempName();
		if (@file_exists($tmpPath)) {
			$fsize = @filesize($tmpPath);
			if ($fsize === false) {
				return array(
					'errorStatus' => 553,
					'errorText' => 'File part access error.'
				);
			}
			if ($fsize > self::MAX_SIZE) {
				$this->abortUpload();
				return array(
					'errorStatus' => 413,
					'errorText' => 'File is too large.'
				);
			}
		}else{
			exec('mkdir -p '.$this->getTempDirectory());
		}

		// Open the raw POST data from php://input
		$fileData = @file_get_contents('php://input');
		if ($fileData === false) {
			return array(
				'errorStatus' => 552,
				'errorText' => 'File part upload error.'
			);
		}

		// Write the actual chunk to the larger file
		$handle = @fopen($tmpPath, 'a');
		if ($handle === false) {
			return array(
				'errorStatus' => 553,
				'errorText' => 'File part access error.'
			);
		}

		$rv = @fwrite($handle, $fileData);
		@fclose($handle);
		if ($rv === false) {
			return array(
				'errorStatus' => 554,
				'errorText' => 'File part write error.'
			);
		}

		return array(
			'key' => $this->getTempName(),
			'errorStatus' => 0
		);
	}

	/**
	 * Function for cancelling uploads while they're in-progress; deletes the temp file
	 * @return string JSON object with result of deletion
	 */
	public function abortUpload() {
		$file = $this->getTempDirectory() . $this->getTempName();
		if (is_file($file) && exec('rm -rf '.$file)) {
			return array(
				'errorStatus' => 0
			);
		}
		else {
			return array(
				'errorStatus' => 405,
				'errorText' => 'Unable to delete temporary file.'
			);
		}
	}

	/**
	 * Function to rename and move the finished file
	 * @param  string $final_name Name to rename the finished upload to
	 * @return string JSON object with result of rename
	 */
	public function finishUpload($finalName) {
		$dstName = sanitizeFileName($finalName);
		$dstPath = $this->getMainDirectory() . $dstName;
		if (@rename($this->getTempDirectory() . $this->getTempName(), $dstPath)) {
			return array(
				'errorStatus' => 0,
				'fileName' => $dstName
			);
		}
		else {
			return array(
				'errorStatus' => 405,
				'errorText' => 'Unable to move file to "' . $dstPath . '" after uploading.'
			);
		}
	}

	/**
	 * Basic php file upload function, used for unsupported browsers.
	 * The output on success/failure is very basic, and it would be best to have these errors return the user to index.html
	 * with the errors printed on the form, but that is beyond the scope of this project as it is very application specific.
	 * @return string Success or failure of upload
	 */
	public function postUnsupported($files) {
		if (empty($files)) {
			$files = $_FILES['bigUploadFile'];
		}
		if (empty($files)) {
			return array(
				'errorStatus' => 550,
				'errorText' => 'No BigUpload file uploads were specified.'
			);
		}
		$name = sanitizeFileName($files['name']);
		$size = $files['size'];
		$tempName = $files['tmp_name'];

		$fsize = @filesize($tempName);
		if ($fsize === false) {
			return array(
				'errorStatus' => 553,
				'errorText' => 'File part access error.'
			);
		}
		if ($fsize > self::MAX_SIZE) {
			return array(
				'errorStatus' => 413,
				'errorText' => 'File is too large.'
			);
		}

		$dstPath = $this->getMainDirectory() . $name;
		if (@move_uploaded_file($tempName, $dstPath)) {
			return array(
				'errorStatus' => 0,
				'fileName' => $dstName,
				'errorText' => 'File uploaded.'
			);
		}
		else {
			return array(
				'errorStatus' => 405,
				'errorText' => 'There was an error uploading the file to "' . $dstPath . '".'
			);
		}
	}
}




function main($action, $tempName, $finalFileName, $files) {
	// Instantiate the class
	$bigUpload = new BigUpload;

	$bigUpload->setTempName($tempName);

	switch($action) {
		case 'upload':
			return $bigUpload->uploadFile();

		case 'abort':
			return $bigUpload->abortUpload();

		case 'finish':
			return $bigUpload->finishUpload($finalFileName);

		case 'post-unsupported':
		case 'vanilla':
			return $bigUpload->postUnsupported($files);

		case 'help':
			return array(
				'errorStatus' => 552,
				'errorText' => "You've reached the BigUpload gateway. Machines will know what to do."
			);

		default:
			return array(
				'errorStatus' => 550,
				'errorText' => 'Unknown action. Internal failure.'
			);
	}
}

// Whatever happens, we always produce a JSON response
header('Content-Type: application/json');

try {
	// Set the preferred temporary filename
	$tempName = null;
	if (isset($_GET['key'])) {
		$tempName = $_GET['key'];
	}
	if (isset($_POST['key'])) {
		$tempName = $_POST['key'];
	}

	// extract the required action from the request parameters
	$action = 'help';
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
	}
	if (isset($_POST['action'])) {
		$action = $_POST['action'];
	}

	// and get the desired filename from the user 
	// 
	// Note: only really applicable for action=='finish' but for simplicity's sake 
	//       we always grab it here and let main() do the rest.
	$realFileName = null;
	if (isset($_GET['name'])) {
		$realFileName = $_GET['name'];
	}
	if (isset($_POST['name'])) {
		$realFileName = $_POST['name'];
	}

	// Vanilla DropZone hack:
	$files = null;
	if (!empty($_FILES['file']) && $action === 'help') {
		$files = $_FILES['file'];
		$action = 'vanilla';
	}


	$response = main($action, $tempName, $realFileName, $files);

	$httpResponseCode = intval($response['errorStatus']);
} catch (Exception $ex) {
	$httpResponseCode = 550;
	$response = array(
		'errorStatus' => $httpResponseCode,
		'errorText' => 'Internal failure: ' . $ex->getMessage()
	);
}

if ($httpResponseCode !== 0 /* HTTP OK */) {
	// Only accept 4xx and 5xx error codes from the BigUpload class and helper functions.
	// Produce the custom HTTP error code 550 when something very much out of the ordinary 
	// occurred.
	if ($httpResponseCode < 400 || $httpResponseCode > 599) {
		$httpResponseCode = 550;
	}
} else {
	$httpResponseCode = 200; // HTTP OK
}

// http://stackoverflow.com/questions/3258634/php-how-to-send-http-response-code
http_response_code($httpResponseCode);

print json_encode($response);
die();

