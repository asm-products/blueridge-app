<?php
/**
 * Cart
 */

use \BlueRidge\Documents\User;
use \BlueRidge\Utilities\Teller;

$app->post('/app/cart/update-payment/',$authenticate($app), function () use ($app) {

    $token = $app->request->post('paymentToken');
    if(empty($token))
    {
        $app->render("common/error-500.html",['message'=>'Looks like we have a problem updating your payment method',500]);
    }else
    {   

        $qr = $app->dm->getRepository('\BlueRidge\Documents\User');
        $user = $qr->find($_SESSION['user']);
        $payment = Teller::updatePayment($app->config('services')['subscriber'],$user->subscription['customerId'],$token);
        $qr->updatePayment($payment);
        $app->redirect('/app/profile/');
    }
});

$app->post('/app/cart/update-subscription/',$authenticate($app), function () use ($app) {

    $plan = $app->request->post('plan');
    if(empty($plan))
    {
        $app->render("common/error-500.html",['message'=>'We have a problem updating your plan ',500]);
    }else
    {   

        $qr = $app->dm->getRepository('\BlueRidge\Documents\User');
        $user = $qr->find($_SESSION['user']);
        $plan = Teller::updateSubscription($app->config('services')['subscriber'],$user->subscription['customerId'],$plan);
        $qr->updateSubscription($plan);
        $app->redirect('/app/profile/');
            
    } 
});