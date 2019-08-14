<?php

namespace Core\Utility;

use JsonSerializable;

class APIMessage implements JsonSerializable
{
    /** @var HttpStatus $status */
    public $status;

    /** @var string $message */
    public $message;

    /** @var array $errors */
    public $errors;

    public $data;

    /**
     * APIMessage constructor.
     * @param HttpStatus $status
     * @param string $message
     * @param array $errors
     * @param $data
     */
    public function __construct(HttpStatus $status, string $message, array $errors, $data = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->errors = $errors;
        $this->data = $data;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $out = [
            'status' => $this->status,
            'message' => $this->message,
            'errors' => $this->errors,
        ];

        if ($this->data) {
            $out['data'] = $this->data;
        }

        return $out;
    }
}
