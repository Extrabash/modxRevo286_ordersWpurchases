<?php

$modx->setLogLevel(modX::LOG_LEVEL_ERROR);

// Получаем стандартные параметры запроса
$userid = $modx->getOption('userid', $scriptProperties, 0);

if($userid == 0)
    return false;

$params = array(
    'class' => 'msOrder',
    'select' => '{
                "msOrder":"*",
                "OrderStatus":"OrderStatus.name as status_name",
                "Delivery":"Delivery.name as delivery_name",
                "Payment":"Payment.name as payment_name"
                }',
    'leftJoin' => '{
                        "OrderStatus": {
                        "class":"msOrderStatus",
                        "alias":"OrderStatus",
                        "on": "OrderStatus.id = msOrder.status"
                    },
                    "Delivery": {
                    "class":"msDelivery",
                    "alias":"Delivery",
                    "on": "Delivery.id = msOrder.delivery"
                    },
                    "Payment": {
                    "class":"msPayment",
                    "alias":"Payment",
                    "on": "Payment.id = msOrder.payment"
                    }
                }',
    'where' => [
                'user_id' => $userid
                ],
    'sortby' => 'createdon',
    'return' => 'data'
);

$orders = $modx->runSnippet('pdoResources', $params);

if($orders)
{
    foreach($orders as &$order)
    {
        $orderParams = array(
            'class'  => 'msOrderProduct',
            'select' => '{"msOrderProduct":"*"}',
            'where' => [
                'order_id' => $order['id']
                ],
            'limit' => 100,
            'sortby' => 'product_id',
            'return' => 'data'
            //,'showLog'=> 1
        );
        
        $order['purchases'] = $modx->runSnippet('pdoResources', $orderParams); 
    }
}

return $orders;
