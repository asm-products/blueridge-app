<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Jobs;

use Blueridge\Application;
use Blueridge\Documents\User;
use Mandrill;

class SendSignUpEmail
{
	/**
	 * Send Signup email
	 * @return Void
	 */
	public function perform()
	{
		$blueridge= new Application();
		$userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
		$user = $userQr->find($this->args['userid']);
		$template = file_get_contents(APP_PATH.'/resources/emails/signup.html');
		$holders =['{{user.firstName}}',"{{plan}}"];
		$values =[$user->firstName,$user->subscription['plan']['name']];
		$message= str_replace($holders, $values, $template);

		$postman = $blueridge['configs']['services']['mandrill'];

		try{
			$mandrill = new Mandrill($postman['api_key']);
			$message = [
			'html' => $message,
			'subject' => 'Welcome to BlueRidge',
			'from_email' => $postman['sender']['email'],
			'from_name' => $postman['sender']['name'],
			'to' => [['email' => $user->email,'type' => 'to']],
			'headers' => ['Reply-To' => $postman['sender']['reply_to']],
			'tags' => ['new-account','credentials'],
			'subaccount' => 'blueridge',
			];
			$response = $mandrill->messages->send($message);
		}catch(\Mandrill_Error $e) {
			error_log($e->getMessage());
		}
	}
}