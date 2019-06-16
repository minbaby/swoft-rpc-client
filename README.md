# swoft-rpc-client
swoft 客户端，非swoft框架内调用

```php
<?php

use Minbaby\SwoftClient\Proxy;

require __DIR__ . '/vendor/autoload.php';


$start = microtime(true);


/** @var \App\Rpc\Lib\UserInterface $v */
$v = new Proxy('tcp://127.0.0.1:18307', \App\Rpc\Lib\UserInterface::class, [], "1.3"); // 这里 UserInterface不是必须的，php默认可以使用 class_name::class 来获取对应的类字符串，即使类不存在。

$i = 0;
while($i<1000) {
//    $v->getBigContent();
$v->getList(1, 2);
//var_dump($v->exception());
    $i++;
}

echo microtime(true) - $start;
```

## 核心思路

默认消息协议是 `json-rpc`， 所以我们按照这个格式就可以了，需要注意的是，默认消息协议是以 `\r\n\r\n` 结尾的。

这里 `method` 的格式为 `"{version}::{class_name}::{method_name}"`

```json
{
    "jsonrpc": "2.0",
    "method": "",
    "params": [],
    "id": "",
    "ext": []
}
```