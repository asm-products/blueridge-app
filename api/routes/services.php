<?php
/**
 * Service
 */
$app->get('/api/services/subscriber', function () use ($app) {

    $subscriber = new stdClass();
    $subscriber->name= $app->subscriber->name;
    $subscriber->key = $app->subscriber->publishable_key;
    echo json_encode($subscriber);
});