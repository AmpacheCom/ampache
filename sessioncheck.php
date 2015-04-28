<?php
	$ampache_path = dirname(__FILE__);
	$prefix = realpath($ampache_path);
	require_once $prefix . '/lib/init-tiny.php';
	require_once $prefix . '/lib/class/session.class.php';
	$results = @parse_ini_file($configfile);
	AmpConfig::set_by_array($results, true);
	Session::_auto_init();

	$session_name = AmpConfig::get('session_name');
	$session_id = $_COOKIE[$session_name];

	$username = Session::username($session_id);
	$valid_session = !empty($username);
	$output = array(
		"valid_session" => $valid_session,
	);

	if ($valid_session) {
		$time = time();
		$domain = $_SERVER['SERVER_NAME'];
		$salt = "C1h2R3i4S5b6A7i8L9e0Y1i2S3g4A5y6";
		$data = $username . $time . $domain. $salt;
		$hash = hash("sha256", $data);

		$output["username"] = $username;
		$output["time"] = $time;
		$output["hash"] = $hash;
		$output["domain"] = $domain;
	}

	header('content-type: application/json; charset=utf-8');
	$json = json_encode($output);
	//For supporting json and jsonp
	echo isset($_GET['callback'])
	    ? "{$_GET['callback']}($json)"
	    : $json;