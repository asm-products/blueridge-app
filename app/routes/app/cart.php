<?php
/**
 * Cart
 */

use \Blueridge\Documents\User;
use \Blueridge\Utilities\Teller;

$app->post('/app/cart/update-payment/',$authenticate($app), function () use ($app,$blueridge) {

    $token = $app->request->post('paymentToken');
    if(empty($token))
    {
        $app->render("common/error-500.html",['message'=>'Looks like we have a problem updating your payment method',500]);
    }else
    {   

        $qr = $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $user = $qr->find(base64_decode($_SESSION['user']));
        $payment = Teller::updatePayment($blueridge['configs']['services']['subscriber'],$user->subscription['customerId'],$token);
        $qr->updatePayment($user,$payment);
        $app->redirect('/app/profile/');
    }
});

$app->post('/app/cart/update-subscription/',$authenticate($app), function () use ($app,$blueridge) {

    $plan = $app->request->post('plan');
    if(empty($plan))
    {
        $app->render("common/error-500.html",['message'=>'We have a problem updating your plan ',500]);
    }else
    {   

        $qr = $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $user = $qr->find(base64_decode($_SESSION['user']));
        $plan = Teller::updateSubscription($blueridge['configs']['services']['subscriber'],$user->subscription['customerId'],$plan);
        $updated= $qr->updateSubscription($user,$plan);
        if(empty($updated['err'])){
            $app->flash('success', 'Your subscription has been updated successfully');
        }        
        $app->redirect('/app/profile/');
            
    } 
});