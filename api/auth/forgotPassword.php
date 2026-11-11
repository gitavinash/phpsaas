<?php

/** Forgot Password module */

function forgotPassword($connection, $data)
{
	$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
	if (file_exists($ssoFile)) {
		require_once $ssoFile;
	}

	extract($data);

	$email = isset($email) ? trim((string) $email) : '';
	$toAddress = $email;

	$lookup = function_exists('iswFindUserAndSiteAccessByIdentifier')
		? iswFindUserAndSiteAccessByIdentifier($connection, $email, (int) SITE_ID, 'email')
		: ['reason' => 'not_found', 'resolved' => null];

	if (!empty($lookup['resolved']) && !empty($lookup['resolved']['has_access'])) {
		$resolvedUser = $lookup['resolved']['user'] ?? [];
		$user_salt_key = (string) ($resolvedUser['user_salt_key'] ?? '');
		$user_id = (int) ($resolvedUser['user_id'] ?? 0);
		$userEmail = (string) ($resolvedUser['user_email'] ?? $email);

		$passwordResetURL = SITE_URL . '/verify/password/?transaction=' . $user_salt_key . '&email=' . base64_encode($userEmail);
		$toAddress = $userEmail;

		require_once EMAIL_TEMPLATE . 'forgot-password.php';

		if (isset($emailIsNotDelivered)) {
			$status = 'warning';
			$msg = $emailIsNotDelivered;
		} else {
			$status = 'success';
			$msg = 'Email has been sent to your register email.';
		}
	} elseif (($lookup['reason'] ?? '') === 'disabled') {
		$status = 'error';
		$msg = 'Your workspace access has been deactivated.';
	} elseif (($lookup['reason'] ?? '') === 'no_access') {
		$status = 'error';
		$msg = 'You do not have access to this workspace.';
	} else {
		$status = 'warning';
		$msg = 'No User Found';
	}

	$result = array(
		'status' => ((isset($status)) ? $status : 'error'),
		'msg' => ((isset($msg)) ? $msg : 'Unable to find any user')
	);

	echo json_encode($result, TRUE);
}
