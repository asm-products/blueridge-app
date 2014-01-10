<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Jobs\Push;

use Blueridge\Application;
use Blueridge\Documents\User;
use Mandrill;

class SignUpEmail
{
	/**
	 * Send Signup email
	 * @return Void
	 */
	public function perform()
	{		
		$blueridge= new Application();
		$userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
		$user = $userQr->findOneByEmail($this->args['email']);
		$template = file_get_contents(APP_PATH.'/resources/emails/signup.html');
		$holders =['{{user.firstName}}',"{{plan}}"];
		$values =[$user->firstName,$user->subscription['plan']['name']];
		$message= str_replace($holders, $values, $template);

		try{
			$mandrill = new Mandrill($this->args['postman']['api_key']);  			    
			$message = [
			'html' => $message,
			'subject' => 'Welcome to BlueRidge',
			'from_email' => $this->args['postman']['sender']['email'],
			'from_name' => $this->args['postman']['sender']['name'],
			'to' => [['email' => $this->args['email'],'type' => 'to']],
			'headers' => ['Reply-To' => $this->args['postman']['sender']['reply_to']],
			'tags' => ['new-account','credentials'],
			'subaccount' => 'blueridge',        
			];
			$response = $mandrill->messages->send($message);
		}catch(\Mandrill_Error $e) {
			error_log($e->getMessage());            
		}
	}
}