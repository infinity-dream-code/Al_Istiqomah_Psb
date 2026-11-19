<?php	
date_default_timezone_set("Asia/Bangkok");
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
class JWT
{
	/**
	 * Decodes a JWT string into a PHP object.
	 *
	 * @param string      $jwt    The JWT
	 * @param string|null $key    The secret key
	 * @param bool        $verify Don't skip verification process 
	 *
	 * @return object      The JWT's payload as a PHP object
	 * @throws UnexpectedValueException Provided JWT was invalid
	 * @throws DomainException          Algorithm was not provided
	 * 
	 * @uses jsonDecode
	 * @uses urlsafeB64Decode
	 */
	public static function decode($jwt, $key = null, $verify = true)
	{
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			throw new UnexpectedValueException('Wrong number of segments');
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;
		if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		$sig = JWT::urlsafeB64Decode($cryptob64);
		if ($verify) {
			if (empty($header->alg)) {
				throw new DomainException('Empty algorithm');
			}
			if ($sig != JWT::sign("$headb64.$bodyb64", $key, $header->alg)) {
				throw new UnexpectedValueException('Signature verification failed');
			}
		}
		return $payload;
	}
	/**
	 * Converts and signs a PHP object or array into a JWT string.
	 *
	 * @param object|array $payload PHP object or array
	 * @param string       $key     The secret key
	 * @param string       $algo    The signing algorithm. Supported
	 *                              algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string      A signed JWT
	 * @uses jsonEncode
	 * @uses urlsafeB64Encode
	 */
	public static function encode($payload, $key, $algo = 'HS256')
	{
		$header = array('typ' => 'JWT', 'alg' => $algo);
		$segments = array();
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
		$signing_input = implode('.', $segments);
		$signature = JWT::sign($signing_input, $key, $algo);
		$segments[] = JWT::urlsafeB64Encode($signature);
		return implode('.', $segments);
	}
	/**
	 * Sign a string with a given key and algorithm.
	 *
	 * @param string $msg    The message to sign
	 * @param string $key    The secret key
	 * @param string $method The signing algorithm. Supported
	 *                       algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string          An encrypted message
	 * @throws DomainException Unsupported algorithm was specified
	 */
	public static function sign($msg, $key, $method = 'HS256')
	{
		$methods = array(
			'HS256' => 'sha256',
			'HS384' => 'sha384',
			'HS512' => 'sha512',
		);
		if (empty($methods[$method])) {
			throw new DomainException('Algorithm not supported');
		}
		return hash_hmac($methods[$method], $msg, $key, true);
	}
	/**
	 * Decode a JSON string into a PHP object.
	 *
	 * @param string $input JSON string
	 *
	 * @return object          Object representation of JSON string
	 * @throws DomainException Provided string was invalid JSON
	 */
	public static function jsonDecode($input)
	{
		$obj = json_decode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($obj === null && $input !== 'null') {
			throw new DomainException('Null result with non-null input');
		}
		return $obj;
	}
	/**
	 * Encode a PHP object into a JSON string.
	 *
	 * @param object|array $input A PHP object or array
	 *
	 * @return string          JSON representation of the PHP object or array
	 * @throws DomainException Provided object could not be encoded to valid JSON
	 */
	public static function jsonEncode($input)
	{
		$json = json_encode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($json === 'null' && $input !== null) {
			throw new DomainException('Null result with non-null input');
		}
		return $json;
	}
	/**
	 * Decode a string with URL-safe Base64.
	 *
	 * @param string $input A Base64 encoded string
	 *
	 * @return string A decoded string
	 */
	public static function urlsafeB64Decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}
	/**
	 * Encode a string with URL-safe Base64.
	 *
	 * @param string $input The string you want encoded
	 *
	 * @return string The base64 encode of what you passed in
	 */
	public static function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
	/**
	 * Helper method to create a JSON error.
	 *
	 * @param int $errno An error number from json_last_error()
	 *
	 * @return void
	 */
	private static function _handleJsonError($errno)
	{
		$messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
		throw new DomainException(
			isset($messages[$errno])
			? $messages[$errno]
			: 'Unknown JSON error: ' . $errno
		);
	}
}
//=========================================
// Billing VA → SIKEU modern (siswa/tagihan), format respons bank tetap sama.
$transaksi = new JWT();
$key = 'TokenJWT_BMI_ICT';

require_once __DIR__ . '/include/config_Nasabah.php';
require_once __DIR__ . '/include/SikeuBilling.php';

if (!isset($_GET['token'])) {
	$response = [
		'ERR' => '12',
		'METHOD' => 'REVERSAL',
		'DESCRIPTION' => 'Declined - Invalid transaction Query',
		'CUSTNAME' => '',
		'DESCRIPTION2' => '',
		'BILL' => '0',
		'CCY' => '360',
	];
	echo JWT::encode($response, $key);
	exit;
}

$decoded = $transaksi->decode($_GET['token'], $key, ['HS256']);
$decoded_array = (array) $decoded;
$METHOD = $decoded_array['METHOD'] ?? null;
$CLIENT_NAME = 'Kuningan_Yayasan_Al_Istiqomah';

if ($METHOD === 'INQUIRY') {
	$VANO = (string) ($decoded_array['VANO'] ?? '');
	$siswaList = sikeu_find_siswa($dbhandle, $VANO);
	$adanim = count($siswaList);

	if ($adanim === 1) {
		$siswa = $siswaList[0];
		$summary = sikeu_inquiry_summary($dbhandle, $siswa);
		$nama = strtoupper(rtrim(str_replace("'", '', (string) $summary['nama'])));

		if ($summary['has_bill']) {
			$response = [
				'ERR' => '00',
				'METHOD' => 'INQUIRY',
				'DESCRIPTION' => $CLIENT_NAME,
				'CUSTNAME' => $nama,
				'DESCRIPTION2' => rtrim((string) $summary['nama_tagihan']),
				'BILL' => ($summary['nominal'] + 2000) * 100,
				'CCY' => '360',
			];
		} else {
			$response = [
				'ERR' => '00',
				'METHOD' => 'INQUIRY',
				'DESCRIPTION' => 'TOP UP',
				'CUSTNAME' => $nama,
				'DESCRIPTION2' => $CLIENT_NAME,
				'BILL' => '0',
				'CCY' => '360',
			];
		}
	} elseif ($adanim > 1) {
		$response = [
			'ERR' => '12',
			'METHOD' => 'INQUIRY',
			'DESCRIPTION' => 'Declined - Invalid transaction',
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => '0',
			'CCY' => '360',
		];
	} else {
		$response = [
			'ERR' => '15',
			'METHOD' => 'INQUIRY',
			'DESCRIPTION' => 'Nomor Identitas Pembayaran tidak ditemukan di basis data Billing Provider',
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => '0',
			'CCY' => '360',
		];
	}

	sikeu_log_va(
		$dbhandle,
		'INQUIRY',
		$VANO,
		$adanim === 1 ? (int) $siswaList[0]['id'] : null,
		isset($summary['nominal']) ? (float) $summary['nominal'] : 0,
		(string) $response['ERR'],
		(string) $response['DESCRIPTION'],
		$response['ERR'] === '00' ? 'success' : 'failed',
		$decoded_array,
		$response
	);

	echo JWT::encode($response, $key);
	exit;
}

if ($METHOD === 'PAYMENT') {
	$VANO = (string) ($decoded_array['VANO'] ?? '');
	$REFNO = (string) ($decoded_array['REFNO'] ?? '');
	$CHANNELID = (string) ($decoded_array['CHANNELID'] ?? '');
	$PAYMENT = ((float) ($decoded_array['PAYMENT'] ?? 0)) - 2000;
	$TRXDATE = $decoded_array['TRXDATE'] ?? '';
	$dt = date_create((string) $TRXDATE);
	$TRXDATE = $dt ? date_format($dt, 'Y-m-d H:i:s') : '';

	if ($VANO === '' || $TRXDATE === '' || $REFNO === '' || $CHANNELID === '' || $PAYMENT == 0) {
		$response = [
			'ERR' => '30',
			'METHOD' => 'PAYMENT',
			'DESCRIPTION' => 'Salah format message',
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => '0',
			'CCY' => '360',
		];
	} else {
		$result = sikeu_apply_payment($dbhandle, $VANO, $REFNO, $TRXDATE, $CHANNELID, (float) $PAYMENT);
		$response = [
			'ERR' => $result['err'],
			'METHOD' => 'PAYMENT',
			'DESCRIPTION' => $result['message'],
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => $result['ok'] ? $PAYMENT : '0',
			'CCY' => '360',
		];
		sikeu_log_va(
			$dbhandle,
			'PAYMENT',
			$VANO,
			$result['siswa_id'] ?? null,
			(float) $PAYMENT,
			(string) $response['ERR'],
			(string) $response['DESCRIPTION'],
			$result['ok'] ? 'success' : 'failed',
			$decoded_array,
			$response
		);
	}

	echo JWT::encode($response, $key);
	exit;
}

if ($METHOD === 'REVERSAL') {
	$VANO = (string) ($decoded_array['VANO'] ?? '');
	$REFNO = (string) ($decoded_array['REFNO'] ?? '');
	$PAYMENT = (float) ($decoded_array['PAYMENT'] ?? 0);
	$TRXDATE = $decoded_array['TRXDATE'] ?? '';
	$PYMTDATE = $decoded_array['PYMTDATE'] ?? '';
	$dt = date_create((string) $TRXDATE);
	$TRXDATE = $dt ? date_format($dt, 'Y-m-d H:i:s') : '';
	$pd = date_create((string) $PYMTDATE);
	$PYMTDATE = $pd ? date_format($pd, 'Y-m-d H:i:s') : '';

	if ($VANO === '' || $REFNO === '' || $PYMTDATE === '') {
		$response = [
			'ERR' => '30',
			'METHOD' => 'PAYMENT',
			'DESCRIPTION' => 'Salah format message',
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => '0',
			'CCY' => '360',
		];
	} else {
		$result = sikeu_reverse_payment($dbhandle, $VANO, $REFNO, $TRXDATE, (float) $PAYMENT);
		$response = [
			'ERR' => $result['err'],
			'METHOD' => 'REVERSAL',
			'DESCRIPTION' => $result['message'],
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => $result['ok'] ? $PAYMENT : '0',
			'CCY' => '360',
		];
		sikeu_log_va(
			$dbhandle,
			'REVERSAL',
			$VANO,
			$result['siswa_id'] ?? null,
			(float) $PAYMENT,
			(string) $response['ERR'],
			(string) $response['DESCRIPTION'],
			$result['ok'] ? 'success' : 'failed',
			$decoded_array,
			$response
		);
	}

	echo JWT::encode($response, $key);
	exit;
}

$response = [
	'ERR' => '12',
	'METHOD' => 'REVERSAL',
	'DESCRIPTION' => 'Declined - Invalid transaction Query',
	'CUSTNAME' => '',
	'DESCRIPTION2' => '',
	'BILL' => '0',
	'CCY' => '360',
];
echo JWT::encode($response, $key);
?>