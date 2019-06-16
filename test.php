<?php

use Minbaby\SwoftClient\Proxy;

require __DIR__ . '/vendor/autoload.php';


$start = microtime(true);


/** @var \App\Rpc\Lib\UserInterface $v */
$v = new Proxy('tcp://127.0.0.1:18307', \App\Rpc\Lib\UserInterface::class, [], "1.3");

$i = 0;
while($i<1000) {
//    $v->getBigContent();
$v->getList(1, 2);
//var_dump($v->exception());
    $i++;
}

echo microtime(true) - $start;
