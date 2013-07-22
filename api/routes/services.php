<?php
/**
 * Service
 */
$app->get('/api/services/cashier', function () use ($app) {

    $cashier = new stdClass();
    $cashier->name= $app->cashier->name;
    $cashier->key = $app->cashier->publishable_key;
    echo json_encode($cashier);
});