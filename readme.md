[![Latest Stable Version](http://poser.pugx.org/zengzhonggang/php-jwt/v)](https://packagist.org/packages/zengzhonggang/php-jwt) 
[![Total Downloads](http://poser.pugx.org/zengzhonggang/php-jwt/downloads)](https://packagist.org/packages/zengzhonggang/php-jwt) 
[![Latest Unstable Version](http://poser.pugx.org/zengzhonggang/php-jwt/v/unstable)](https://packagist.org/packages/zengzhonggang/php-jwt) 
[![License](http://poser.pugx.org/zengzhonggang/php-jwt/license)](https://packagist.org/packages/zengzhonggang/php-jwt) 
[![PHP Version Require](http://poser.pugx.org/zengzhonggang/php-jwt/require/php)](https://packagist.org/packages/zengzhonggang/php-jwt)
# PHP-JWT
一个jwt库，能简便的生成jwt规范的token。

## 安装
使用composer
```shell
composer require zengzhonggang/php-jwt
```

## 用例
### 简单用例
```php
use ZZG\JWT\JWT;

$jwtService = JWT::init([JWT::HS256,'123']);
//生成
$payload = [
    'user' => 'zzg',
    'role' => 'admin'
];

$token = $jwtService->generateToken($payload)
        ->setKindex(0)
        ->toString();
        
//解析
$tokenResolver = $jwtService->analyticToken($token);

$payload = $tokenResolver->getPayload();
echo $payload->getExpirationTime();
echo $payload->get('user');
var_export($payload->toArray());
```
### 初始化
```php
//单个加密算法
$jwtService = JWT::init([JWT::HS256,'123']);
//多套加密算法
$jwtService = JWT::init([[JWT::HS256,'123'],[JWT::HS256,'456']]);
//自定义算法unique_id,
$jwtService = JWT::init([JWT::HS256,'123','kid']);

//openssl
$privateKey = '';
$publicKey = '';
$jwtService = JWT::init([JWT::RS256,[$publicKey,$privateKey]]);
```
### 生成token
```php
//数组payload
$payload = [
    'user' => 'zzg',
    'role' => 'admin'
];
//使用对象
$payload = new \ZZG\JWT\Payload\Claim();
$payload->setExpirationTime(time()+24*60*60);
$payload->setPublicClaim('user','zzg');

$tokenGenerator = $jwtService->generateToken($payload);
//添加payload
$tokenGenerator->setPayload('role','admin');
//选择token加密算法
//只初始了一个加密算法，也需要设置；多个加密算法，设置算法数组下标
$tokenGenerator->setKindex(0);
//如果加密算法自定义了unique_id,可以使用自定义unique_id
//$jwtService = JWT::init([JWT::HS256,'123','kid-1']);
$tokenGenerator->setKid('kid-1');
```

### 解析token

```php
$tokenResolver = $jwtService->analyticToken($token);
try {
    $tokenResolver->verify();
 }catch (Exception $exception) {
    //todo
 }
 
 //在使用payload之前没有验证操作，可能会抛出TokenInvalidException异常
 //token的生效和有效期不会验证
$payload = $tokenResolver->getPayload();
echo $payload->getExpirationTime();
echo $payload->get('user');
var_export($payload->toArray());
```
### 验证处理
```php
//验证通过 code 为 0
switch ($tokenResolver->errorCode()) {
    case \ZZG\JWT\Exception\TokenInvalidException::CODE:
        //todo
        break;
    case \ZZG\JWT\Exception\SignatureInvalidException::CODE:
        //todo
        break;
    case \ZZG\JWT\Exception\BeforeValidException::CODE:
        //todo
        break;
    case \ZZG\JWT\Exception\ExpiredException::CODE:
        //todo
        break;
}
    
try {
    $tokenResolver->verify();
 } catch (\ZZG\JWT\Exception\BeforeValidException $e) {
    //todo token还未生效
 } catch (\ZZG\JWT\Exception\ExpiredException $e) {
    //todo token过期
 } catch (\ZZG\JWT\Exception\SignatureInvalidException $e) {
    //todo 签名验证失败
 } catch (RuntimeException $e) {
    //todo 其他异常
 }
```