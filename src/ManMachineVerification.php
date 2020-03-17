<?php

namespace AliyunManMachineVerification;
use DefaultAcsClient;
use DefaultProfile;
use afs\Request\V20180112 as Afs;

class ManMachineVerification
{
    private $iClientProfile;
    private $appKey;

    public function __construct($accessKey,$accessSecrect,$appKey)
    {
        $profile = DefaultProfile::getProfile("cn-hangzhou", $accessKey, $accessSecrect);
        $this->iClientProfile=new DefaultAcsClient($profile);
        $this->appKey=$appKey;
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "afs", "afs.aliyuncs.com");
    }

    /**
     * 滑动验证、智能验证
     * @param $sessionId 会话ID。必填参数，从前端获取，不可更改。
     * @param $token 请求唯一表示。必填参数，从前端获取，不可更改。
     * @param $sig 签名串。必填参数，从前端获取，不可更改。
     * @param $scene 场景标识。必填参数，从前端获取，不可更改。
     * @param $appKey 应用类型标识。必填参数，后端填写。
     * @param $remoteIp 客户端IP。必填参数，后端填写。
     * @return array
     */
    public function slidingVerification($sessionId,$token,$sig,$scene,$remoteIp)
    {
        $request = new Afs\AuthenticateSigRequest();
        $request->setSessionId($sessionId);
        $request->setToken($token);
        $request->setSig($sig);
        $request->setScene($scene);
        $request->setAppKey($this->appKey);
        $request->setRemoteIp($remoteIp);
        $response = $this->iClientProfile->getAcsResponse($request);// 返回code 100表示验签通过，900表示验签失败
        if($response->Code==100){
            return ['code'=>1,'msg'=>$response->Msg];
        }
        return ['code'=>0,'msg'=>$response->Msg];
    }

    /**
     * 无痕验证
     * @param $nvcVal 必填参数，由前端获取getNVCVal方法获得的值。
     * @return array
     */
    public function noTraceValidation($nvcVal)
    {
        $request = new Afs\AnalyzeNvcRequest();
        $request->setData($nvcVal);// 必填参数，由前端获取getNVCVal方法获得的值。
        // 通过setScoreJsonStr方法声明"服务端调用人机验证服务接口得到的返回结果"与"前端执行操作"间的映射关系，并通知验证码服务端进行二次验证授权。
        // 注意：前端页面必须严格按照该映射关系执行相应操作，否则将导致调用异常。
        // 例如，在setScoreJsonStr方法中声明"400":"SC"，则当服务端返回400时，您的前端必须唤醒刮刮卡验证（SC），如果唤醒滑块验证（NC）则将导致失败。
        $request->setScoreJsonStr("{\"200\":\"PASS\",\"400\":\"NC\",\"600\":\"SC\",\"800\":\"BLOCK\"}");// 根据业务需求设置各返回结果对应的客户端处置方式。
        $response = $this->iClientProfile->getAcsResponse($request);
        return ['code'=>$response->BizCode];
    }
}