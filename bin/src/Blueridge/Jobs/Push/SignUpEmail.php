<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelbas.com>
 */

namespace Blueridge\Jobs\Push;

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
		try{
			$mandrill = new Mandrill($this->args['postman']['api_key']);  			    
			$message = [
			'html' => "
			<p><strong>Your BlueRidge account info:</strong><br>Email: <code>{$this->args['email']}</code> <br>Password: <code>{$this->args['password']}</code></p>",
			'subject' => 'Please confirm your Mapster Account email address',
			'from_email' => $this->args['postman']['sender']['email'],
			'from_name' => $this->args['postman']['sender']['name'].' - Welcome Committee',
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