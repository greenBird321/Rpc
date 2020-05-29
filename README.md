## RPC远程调用接口文档
支持三种格式的接口调用 yar, soap, http  


### 调用格式
http://host/controller/action  
http://host/yar/controller  
http://host/soap/controller  


### 客户端 Yar
参考: [https://github.com/laruence/yar](https://github.com/laruence/yar)  
```php
<?php
$rpc = new Yar_Client("http://host/yar/User");
$result = $rpc->lists("parameter");
```
### 客户端 Soap
参考: [http://php.net/manual/en/class.soapclient.php](http://php.net/manual/en/class.soapclient.php)
```php
<?php
$rpc = new SoapClient(null, array(
    'location' => 'http://host/soap/User',
    'uri'      => 'app',
    'style'    => SOAP_RPC,
    'use'      => SOAP_ENCODED,
    'trace'    => true
));
$result = $rpc->lists("parameter");
```

### 客户端 Http
```php
<?php
$result = file_get_contents('http://host/User/lists');
```

### 附：通用参数规范
参数名 | 描述
:--- |---:
app         | 应用ID
zone        | 区服序号
account		| 平台账号ID
user        | 应用的用户ID
transaction | 订单号
coin        | 货币数量 
vip         | VIP点数
message     | 消息内容
type        | 数据类型
amount		| 金额
currency	| 币种
quantity    | 数量
attach     	| 附件
content     | 内容
custom      | 自定义字段

## 方法说明
一般调用方法：  

* 添加 /CLASS/create
* 列表 /CLASS/lists
* 详情 /CLASS/item
* 修改 /CLASS/modify
* 删除 /CLASS/remove

___

##### 添加活动 /activity/create
##### 活动列表 /activity/lists
参数名 | 类型 | 必选 | 描述
--- | --- |:---:| ---
type        | varchar(16)  | 否 | 活动类型（prepay,spend,login,exp,level）
start_time  | varchar(32)  | 否 | 开始时间
end_time    | varchar(32)  | 否 | 结束时间
search      | varchar(32)  | 否 | 搜索内容（模糊查询关键字）
page        | int(8)       | 否 | 页码（默认1）
size        | int(8)       | 否 | 单页数量（默认200）

返回：
```json
{
    "code": 0,
    "msg": "success",
    "count": 20,
    "data": [
        {
            "id": 1,
            "status": 1,
            "sort": 0,
            "type": "prepay",
            "visible": 1,
            "title": "充值活动",
            "content": "活动内容",
            "url": "",
            "img": "",
            "img_small": "",
            "custom": "",
            "start_time": "2017-04-03 01:00:00",
            "end_time": "2017-05-03 00:00:00"
        }
    ]
}
```
##### 活动详情 /activity/item
##### 修改活动 /activity/modify
##### 删除活动 /activity/remove
