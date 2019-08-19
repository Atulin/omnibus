<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Core\Security\ReCaptcha;

class ReCaptchaHandler
{
    /* @var string $secret */
    private $secret;

    /* @var string $response */
    private $response;

    /* @var string|null $remote_ip */
    private $remote_ip;

    /**
     * ReCaptchaHandler constructor.
     * @param string $secret
     * @param string $response
     * @param string|null $remote_ip
     */
    public function __construct(string $secret, string $response, ?string $remote_ip = null)
    {
        $this->secret = $secret;
        $this->response = $response;
        $this->remote_ip = $remote_ip;
    }

    public function Check(): ReCaptchaResponse
    {
        $data = [
            'secret' => $this->secret,
            'response' => $this->response
        ];

        if ($this->remote_ip !== null) {
            $data['remote-ip'] = $this->remote_ip;
        }

        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $res = json_decode(curl_exec($verify), true);

        return new ReCaptchaResponse(
            $res['success'],
            $res['challenge-ts'] ?? null,
            $res['hostname'] ?? null,
            $res['error-codes'] ?? null
        );
    }
}
