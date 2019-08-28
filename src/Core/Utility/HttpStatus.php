<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Core\Utility;

use Exception;


class HttpStatus
{
    /** @var int $code */
    public $code;
    /** @var string $message */
    public $message;

    /**
     * HttpStatus constructor.
     *
     * @param int    $code
     * @param string $message
     */
    private function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws Exception
     */
    public function __set($name, $value)
    {
        throw new Exception('Public getters not available.');
    }

    // Messages

    public static function S100(): HttpStatus
    {
        return new HttpStatus(100, 'Continue');
    }

    public static function S101(): HttpStatus
    {
        return new HttpStatus(101, 'Switching Protocols');
    }

    public static function S102(): HttpStatus
    {
        return new HttpStatus(102, 'Processing');
    }

    public static function S200(): HttpStatus
    {
        return new HttpStatus(200, 'OK');
    }

    public static function S201(): HttpStatus
    {
        return new HttpStatus(201, 'Created');
    }

    public static function S202(): HttpStatus
    {
        return new HttpStatus(202, 'Accepted');
    }

    public static function S203(): HttpStatus
    {
        return new HttpStatus(203, 'Non-Authoritative Information');
    }

    public static function S204(): HttpStatus
    {
        return new HttpStatus(204, 'No Content');
    }

    public static function S205(): HttpStatus
    {
        return new HttpStatus(205, 'Reset Content');
    }

    public static function S206(): HttpStatus
    {
        return new HttpStatus(206, 'Partial Content');
    }

    public static function S207(): HttpStatus
    {
        return new HttpStatus(207, 'Multi-Status');
    }

    public static function S208(): HttpStatus
    {
        return new HttpStatus(208, 'Already Reported');
    }

    public static function S226(): HttpStatus
    {
        return new HttpStatus(226, 'IM Used');
    }

    public static function S300(): HttpStatus
    {
        return new HttpStatus(300, 'Multiple Choices');
    }

    public static function S301(): HttpStatus
    {
        return new HttpStatus(301, 'Moved Permanently');
    }

    public static function S302(): HttpStatus
    {
        return new HttpStatus(302, 'Found');
    }

    public static function S303(): HttpStatus
    {
        return new HttpStatus(303, 'See Other');
    }

    public static function S304(): HttpStatus
    {
        return new HttpStatus(304, 'Not Modified');
    }

    public static function S305(): HttpStatus
    {
        return new HttpStatus(305, 'Use Proxy');
    }

    public static function S306(): HttpStatus
    {
        return new HttpStatus(306, 'Switch Proxy');
    }

    public static function S307(): HttpStatus
    {
        return new HttpStatus(307, 'Temporary Redirect');
    }

    public static function S308(): HttpStatus
    {
        return new HttpStatus(308, 'Permanent Redirect');
    }

    public static function S400(): HttpStatus
    {
        return new HttpStatus(400, 'Bad Request');
    }

    public static function S401(): HttpStatus
    {
        return new HttpStatus(401, 'Unauthorized');
    }

    public static function S402(): HttpStatus
    {
        return new HttpStatus(402, 'Payment Required');
    }

    public static function S403(): HttpStatus
    {
        return new HttpStatus(403, 'Forbidden');
    }

    public static function S404(): HttpStatus
    {
        return new HttpStatus(404, 'Not Found');
    }

    public static function S405(): HttpStatus
    {
        return new HttpStatus(405, 'Method Not Allowed');
    }

    public static function S406(): HttpStatus
    {
        return new HttpStatus(406, 'Not Acceptable');
    }

    public static function S407(): HttpStatus
    {
        return new HttpStatus(407, 'Proxy Authentication Required');
    }

    public static function S408(): HttpStatus
    {
        return new HttpStatus(408, 'Request Timeout');
    }

    public static function S409(): HttpStatus
    {
        return new HttpStatus(409, 'Conflict');
    }

    public static function S410(): HttpStatus
    {
        return new HttpStatus(410, 'Gone');
    }

    public static function S411(): HttpStatus
    {
        return new HttpStatus(411, 'Length Required');
    }

    public static function S412(): HttpStatus
    {
        return new HttpStatus(412, 'Precondition Failed');
    }

    public static function S413(): HttpStatus
    {
        return new HttpStatus(413, 'Request Entity Too Large');
    }

    public static function S414(): HttpStatus
    {
        return new HttpStatus(414, 'Request-URI Too Long');
    }

    public static function S415(): HttpStatus
    {
        return new HttpStatus(415, 'Unsupported Media Type');
    }

    public static function S416(): HttpStatus
    {
        return new HttpStatus(416, 'Requested Range Not Satisfiable');
    }

    public static function S417(): HttpStatus
    {
        return new HttpStatus(417, 'Expectation Failed');
    }

    public static function S418(): HttpStatus
    {
        return new HttpStatus(418, "I'm a teapot");
    }

    public static function S419(): HttpStatus
    {
        return new HttpStatus(419, 'Authentication Timeout');
    }

    public static function S420(): HttpStatus
    {
        return new HttpStatus(420, 'Method Failure');
    }

    public static function S422(): HttpStatus
    {
        return new HttpStatus(422, 'Unprocessable Entity');
    }

    public static function S423(): HttpStatus
    {
        return new HttpStatus(423, 'Locked');
    }

    public static function S424(): HttpStatus
    {
        return new HttpStatus(424, 'Failed Dependency');
    }

    public static function S425(): HttpStatus
    {
        return new HttpStatus(425, 'Unordered Collection');
    }

    public static function S426(): HttpStatus
    {
        return new HttpStatus(426, 'Upgrade Required');
    }

    public static function S428(): HttpStatus
    {
        return new HttpStatus(428, 'Precondition Required');
    }

    public static function S429(): HttpStatus
    {
        return new HttpStatus(429, 'Too Many Requests');
    }

    public static function S431(): HttpStatus
    {
        return new HttpStatus(431, 'Request Header Fields Too Large');
    }

    public static function S444(): HttpStatus
    {
        return new HttpStatus(444, 'No Response');
    }

    public static function S449(): HttpStatus
    {
        return new HttpStatus(449, 'Retry With');
    }

    public static function S450(): HttpStatus
    {
        return new HttpStatus(450, 'Blocked by Windows Parental Controls');
    }

    public static function S451(): HttpStatus
    {
        return new HttpStatus(451, 'Redirect');
    }

    public static function S494(): HttpStatus
    {
        return new HttpStatus(494, 'Request Header Too Large');
    }

    public static function S495(): HttpStatus
    {
        return new HttpStatus(495, 'Cert Error');
    }

    public static function S496(): HttpStatus
    {
        return new HttpStatus(496, 'No Cert');
    }

    public static function S497(): HttpStatus
    {
        return new HttpStatus(497, 'HTTP to HTTPS');
    }

    public static function S499(): HttpStatus
    {
        return new HttpStatus(499, 'Client Closed Request');
    }

    public static function S500(): HttpStatus
    {
        return new HttpStatus(500, 'Internal Server Error');
    }

    public static function S501(): HttpStatus
    {
        return new HttpStatus(501, 'Not Implemented');
    }

    public static function S502(): HttpStatus
    {
        return new HttpStatus(502, 'Bad Gateway');
    }

    public static function S503(): HttpStatus
    {
        return new HttpStatus(503, 'Service Unavailable');
    }

    public static function S504(): HttpStatus
    {
        return new HttpStatus(504, 'Gateway Timeout');
    }

    public static function S505(): HttpStatus
    {
        return new HttpStatus(505, 'HTTP Version Not Supported');
    }

    public static function S506(): HttpStatus
    {
        return new HttpStatus(506, 'Variant Also Negotiates');
    }

    public static function S507(): HttpStatus
    {
        return new HttpStatus(507, 'Insufficient Storage');
    }

    public static function S508(): HttpStatus
    {
        return new HttpStatus(508, 'Loop Detected');
    }

    public static function S509(): HttpStatus
    {
        return new HttpStatus(509, 'Bandwidth Limit Exceeded');
    }

    public static function S510(): HttpStatus
    {
        return new HttpStatus(510, 'Not Extended');
    }

    public static function S511(): HttpStatus
    {
        return new HttpStatus(511, 'Network Authentication Required');
    }

    public static function S598(): HttpStatus
    {
        return new HttpStatus(598, 'Network read timeout error');
    }

    public static function S599(): HttpStatus
    {
        return new HttpStatus(599, 'Network connect timeout error');
    }

}
