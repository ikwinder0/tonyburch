<?php

$old_error_handler = set_error_handler('custom_error_handler');

function custom_error_handler($errno, $errstr, $errfile, $errline)
{
	switch ($errno) {
		case E_USER_WARNING:
			echo "<dl style='color: yellow'>
				<dt>WARNING [$errno]</dt>
				<dd style='text-size: small'>$errfile:$errline</dd>
				<dd>$errstr</dd>
				</dl>";
			break;
		case E_USER_NOTICE:
			echo "<dl style='color: yellow'>
				<dt>NOTICE [$errno]</dt>
				<dd style='text-size: small'>$errfile:$errline</dd>
				<dd>$errstr</dd>
				</dl>";
				break;
		default:
			echo "<dl style='color: red'>
				<dt>ERROR [$errno] $errstr</dt>
				<dd style='text-size: small'>$errfile:$errline</dd>
				<dd>ABORTING!</dd>
				</dl>";
			exit(1);
			break;
	}
}

error_reporting(E_ALL);
?>
