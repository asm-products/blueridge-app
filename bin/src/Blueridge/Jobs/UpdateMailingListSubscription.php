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
use Drewm\MailChimp;

class UpdateMailingListSubscription
{
    /**
     * Subscribe to Mailchimp BlueRidge Users list
     * @return Void
     */
    public function perform()
    {

        $blueridge= new Application();
        $postman = $blueridge['configs']['services']['mailchimp'];

        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $user = $userQr->find($this->args['userid']);

        if(empty($user)) {
            return;
        }

        try {

            $mailChimpClient = new MailChimp($postman['api_key']);
            $args = [
                'id'=>$postman['list_id'],
                'email' => ['email'=>$user->email],
                'merge_vars' => [
                    'FNAME' => $user->firstName,
                    'LNAME' => $user->lastName,
                    'groupings' => [
                        ['id'=>$postman['groupings_id'], 'groups'=>$this->args['groups']]
                    ]
                ],
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => true
            ];
            if($user->status = 'new' ) {
                $args['send_welcome'] = true;
            }

            $result = $mailChimpClient->call('lists/subscribe',$args);


        } catch(\Exception $e) {

            error_log($e->getMessage());
        }

    }
}