<?php

namespace beck\mysqlvs;

use think\facade\Db;

/**
 * 用于向服务端发起请求
 */
class Client extends Unit
{
    protected $url = '';
    protected $token = '';

    /**
     * @param $app MysqlVersion
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->url   = $app->config['url'] ?? '';
        $this->token = $app->config['token'] ?? '';
    }

    public function getList($id = 0)
    {

        $str = $this->generateRandomString(99);

        $result = $this->sendHttpRequest($this->url, null, [
            'id'   => $id,
            'str'  => $str,
            'sign' => md5($str . $this->token)
        ], true);

        return $result;
    }

    /**
     * 验证 签名是否正确
     * @param $sign
     * @param $str
     * @return bool
     */
    public function verifySign($sign, $str)
    {
        if (strlen($str) != 99) {
            throw new \Exception('str参数错误');
        }

        return md5($str . $this->token) == $sign;
    }


    protected function generateRandomString($length)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }


    public function sendHttpRequest($url, $url_param = null, $body_param = null, $is_post = true)
    {
        if ($url_param) {
            $url_param = '?' . http_build_query($url_param);
        }
        if ($body_param) {
            $body_param = json_encode($body_param, JSON_UNESCAPED_UNICODE);
        }
        $ch = curl_init($url . $url_param);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body_param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        curl_close($ch);
        $array_data = json_decode($data, true);

        return $array_data;
    }
}