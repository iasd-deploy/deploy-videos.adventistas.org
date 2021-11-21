<?php
ob_start();

function redirect()
{
	setcookie("LangRedirect", "TRUE", 0, "", "", "true", "true");
	$lang = substr(getenv('HTTP_ACCEPT_LANGUAGE'), 0, 2);
	switch ($lang) {
		case 'es':
			$redir_url = "/es/";
			break;
		default:
		case 'pt':
			$redir_url = "/pt/";
			break;
	}
	header("Location: $redir_url");
}

redirect();
