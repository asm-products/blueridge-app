<?php
/**
 * Cart
 */

use \Blueridge\Documents\User;
use \Blueridge\Utilities\Teller;

$app->post('/app/cart/update-payment/',function () use ($app,$blueridge) {

    $token = $app->request->post('paymentToken');
    if(empty($token))
    {
        $app->render("common/error-500.html",['message'=>'Looks like we have a problem updating your payment method',500]);
    }else
    {

        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $user = $userQr->findOneById($blueridge['authenticationService']->getIdentity());
        $payment = Teller::updatePayment($blueridge['configs']['services'],$user->subscription['customerId'],$token);
        $userQr->updatePayment($user,$payment);
        $app->redirect('/app/profile/');
    }
});

$app->post('/app/cart/update-subscription/',function () use ($app,$blueridge) {

    $plan = $app->request->post('plan');

    if(empty($plan))
    {
        $app->render("common/error-500.html",['message'=>'We have a problem updating your plan ',500]);
    }else
    {

        switch($plan) {
            case 'br-pro':
            $planDescription = 'Pro';
            break;

            case 'br-manager':
            $planDescription = 'Manager';
            break;

            case 'br-solo':
            $planDescription = 'Solo';
            break;

            case 'br-free': default:
            $planDescription = 'Forever Free';
            break;
        }

        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $user = $userQr->findOneById($blueridge['authenticationService']->getIdentity());

        $plan = Teller::updateSubscription($blueridge['configs']['services'],$user->subscription['customerId'],$plan);
        $updated= $userQr->updateSubscriptionPlan($user,$plan);

        if(empty($updated['err'])){
            $app->flash('success', 'Your subscription has been updated successfully');
            Resque::enqueue('mail', 'Blueridge\Jobs\UpdateMailingListSubscription', ['userid'=>$user->id,'groups'=>[$planDescription]]);
        }
        $app->redirect('/app/profile/');

    }
});