## 阿里云人机验证

## 安装

```shell
composer require liuyexing/man_machine_verification


## 使用以tp5为例：

```php
<?php
/**
 * Created by: PhpStorm
 * User: lyx
 * DateTime: 2020/3/17 11:34
 */

namespace app\index\controller;


use think\Controller;
use AliyunManMachineVerification\ManMachineVerification;
use think\Request;

class Verify extends Controller
{

    private $aliKey;
    private $aliSecrect;
    private $appKey;
    public function _initialize()
    {
        $this->aliKey='YOUR ACCESSKEY';
        $this->aliSecrect='YOUR ACCESS_SECRET';
        $this->appKey='YOUR AppKey';
    }

    /**
     * 滑动验证/智能验证
     * @param Request $request
     * @return \think\response\Json
     */
    public function slidingVerification(Request $request)
    {
        $sessionId=$request->param('sessionId');//会话ID。必填参数，从前端获取，不可更改。
        $token=$request->param('token');//请求唯一表示。必填参数，从前端获取，不可更改。
        $sig=$request->param('sig');// 签名串。必填参数，从前端获取，不可更改。
        $scene=$request->param('scene');// 场景标识。必填参数，从前端获取，不可更改。
        $remoteIp=$request->ip();//客户端ip
        $service=new ManMachineVerification($this->aliKey,$this->aliSecrect,$this->appKey);
        $result=$service->slidingVerification($sessionId,$token,$sig,$scene,$remoteIp);
        return json($result);
    }

    /**
     * 无痕验证
     * @param Request $request
     * @return \think\response\Jsonp
     */
    public function noTraceValidation(Request $request)
    {
        $nvcVal=$request->param('nvcVal');
        $service=new ManMachineVerification($this->aliKey,$this->aliSecrect,$this->appKey);
        $result=$service->noTraceValidation($nvcVal);
        return jsonp(['result'=>$result]);
    }
}
```
