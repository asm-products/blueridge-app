
<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

use Blueridge\Documents\User;
use Blueridge\Utilities\Doorman;
use Blueridge\Utilities\Teller;
use Blueridge\Authentication\ProviderAdapter;
use Blueridge\Providers\Basecamp\OAuth;
use Blueridge\Providers\Basecamp\BasecampClient as Basecamp;

/**
 * Connect to Basecamp
 */
$app->get('/basecamp/connect/',function() use ($app,$blueridge){

    $provider = new OAuth($blueridge['configs']['providers']['basecamp']);
    $provider->authorize($app);
});

/**
 * Authorize the Basecamp connection
 */
$app->get('/basecamp/auth/',function() use ($app,$blueridge){

    $code = $app->request()->params('code');

    if(empty($code)) {
        $app->redirect('/error/basecamp-connect/');
    }

    try {
        $provider = new OAuth($blueridge['configs']['providers']['basecamp']);
        $token = $provider->getAccessToken($code);
        $authorization = $provider->getAuthorization($token);
        $userDetails = $provider->getUserDetails($authorization);

    } catch(Exception $error) {
        error_log($error->getMessage());
        $view = [
        'route'=>'error',
        'message'=>"Access to your Basecamp account failed",
        'mode'=>$app->mode
        ];
        $app->render("common/error-403.html", $view,403);
        return;
    }



    $userDetails['profile'] = [
    'accounts'=>$authorization['accounts'],
    'projects'=>[]
    ];

    $basecampDetails =  [
    'token'=>$token,
    'accounts'=>$authorization['accounts'],
    'identity'=>$authorization['identity'],
    ];


    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $user = $userQr->findOneByEmail($userDetails['email']);

    if(empty($user)) {

        $activation = Doorman::getCode();
        $userDetails['member_since'] = new \DateTime();
        $userDetails['status']='new';
        $userDetails['roles']='user';
        $userDetails['key']= $activation['key'];

        $user = new User;
        $user->setProperties($userDetails);
        $blueridge['documentManager']->persist($user);
        $blueridge['documentManager']->flush();

        $subscription=Teller::addCustomer($blueridge['configs']['services']['subscriber'],$user->toArray());
        $userQr->setSubscription($user,$subscription);

        Resque::enqueue('mail', 'Blueridge\Jobs\Push\SignUpEmail', ['email'=>$user->email,'postman'=>$blueridge['configs']['services']['mail']['mandrill']]);

    }

    $identifier = Doorman::getCode();

    $userQr->setProvider($user,'basecamp',$basecampDetails);
    $userQr->setIdentifierKey($user,$identifier['key']);

    $providerAdapter = new ProviderAdapter($blueridge['documentManager'],$userDetails['email'],$identifier['code']);
    $result = $blueridge['authenticationService']->authenticate($providerAdapter);

    $app->setCookie('_blrdg_connect',"{$userDetails['email']}:{$identifier['code']}", '14 days');
    if($user->status != 'active'){
        $app->redirect('/app/projects/');
    }
    $app->redirect('/app/todos/');

});