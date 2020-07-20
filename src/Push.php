<?php

/**
 * Mob Push
 */

namespace MobService;

use GuzzleHttp\Client;

class Push
{
    const url = 'http://api.push.mob.com';

    protected $app_key;
    protected $app_secret;
    protected $header = [];

    public function __construct($app_key, $app_secret)
    {
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
        $header['key'] = $this->app_key;
        $header['sign'] = md5($this->app_secret);
        $this->header = $header;
    }

    /**
     * 安卓广播
     * @param $content
     * @param $scheme
     * @param null $scheme_data_key
     * @param null $scheme_data_value
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function broadcastAndroid($content, $scheme, $scheme_data_key = null, $scheme_data_value = null) {
        if (!$content)
            return false;
        $json_param = [
            'appkey'        => $this->app_key,
            'pushTarget'    => ['target' => 1],
            'pushNotify'    => [
                'plats'         => [1],
                'content'       => $content,
                'type'          => 1
            ],
            'pushForward'   => $this->setPushForwardData($scheme, $scheme_data_key, $scheme_data_value)
        ];
        return $this->_post('/v3/push/createPush', $json_param);
    }

    /**
     * 苹果广播
     * @param $content
     * @param int $ios_dev
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function broadcastIos($content, $ios_dev = 0) {
        if (!$content)
            return false;
        $json_param = [
            'appkey'        => $this->app_key,
            'pushTarget'    => ['target' => 1],
            'pushNotify'    => [
                'plats'         => [2],
                'iosProduction' => $ios_dev,
                'content'       => $content,
                'type'          => 1
            ]
        ];
        return $this->_post('/v3/push/createPush', $json_param);
    }

    /**
     * 安卓个推，基于tag或reg_id
     * @param $content
     * @param array $tags
     * @param array $reg_ids
     * @param $scheme
     * @param $scheme_data_key
     * @param $scheme_data_value
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pushTargetAndroid($content, $tags = [], $reg_ids = [], $scheme, $scheme_data_key, $scheme_data_value) {
        if (!$content)
            return false;
        $json_param = [
            'appkey'        => $this->app_key,
            'pushTarget'    => ['target' => 1],
            'pushNotify'    => [
                'plats'         => [1],
                'content'       => $content,
                'type'          => 1
            ],
            'pushForward'   => $this->setPushForwardData($scheme, $scheme_data_key, $scheme_data_value)
        ];
        if (count($tags)) {
            $json_param['pushTarget'] = [
                'target'    => 3,
                'tags'      => $tags
            ];
        }
        if (count($reg_ids)) {
            $json_param['pushTarget'] = [
                'target'    => 4,
                'rids'      => $reg_ids
            ];
        }
        return $this->_post('/v3/push/createPush', $json_param);
    }

    /**
     * @param $content
     * @param array $tags
     * @param array $reg_ids
     * @param int $ios_dev
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pushTargetIos($content, $tags = [], $reg_ids = [], $ios_dev = 0) {
        if (!$content)
            return false;
        $json_param = [
            'appkey'        => $this->app_key,
            'pushTarget'    => ['target' => 1],
            'pushNotify'    => [
                'plats'         => [2],
                'iosProduction' => $ios_dev,
                'content'       => $content,
                'type'          => 1
            ]
        ];
        if (count($tags)) {
            $json_param['pushTarget'] = [
                'target'    => 3,
                'tags'      => $tags
            ];
        }
        if (count($reg_ids)) {
            $json_param['pushTarget'] = [
                'target'    => 4,
                'rids'      => $reg_ids
            ];
        }
        return $this->_post('/v3/push/createPush', $json_param);
    }

    /**
     * post json
     * @param $api_path
     * @param $json_param
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function _post($api_path, $json_param) {
        $client = new Client(['base_uri' => self::url]);
        $json = json_encode($json_param, JSON_UNESCAPED_UNICODE);
        $this->header['sign'] = md5($json . $this->app_secret);
        $response = $client->request('POST', $api_path, [
            'json'      => $json_param,
            'headers'   => $this->header,
        ]);
        $data = $response->getBody()->getContents();
        return $data;
    }

    /**
     * 设置应用内跳转Data
     * @param $scheme
     * @param null $scheme_data_key
     * @param null $scheme_data_value
     * @return array
     */
    private function setPushForwardData($scheme, $scheme_data_key = null, $scheme_data_value = null) {
        return [
            'nextType'      => 2,
            'scheme'        => $scheme,
            'schemeDataList'=> [['key' => $scheme_data_key, 'value' => $scheme_data_value]]
        ];
    }
}
