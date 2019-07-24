<?php
namespace Core\Security\ReCaptcha;

use DateTime;

/**
 * Class ReCaptcha
 * @package Core\Security
 */
class ReCaptchaResponse
{
    /** @var bool $success */
    private $success;

    /** @var DateTime|null $challenge_timestamp */
    private $challenge_timestamp;

    /** @var string|null $hostname */
    private $hostname;

    /** @var array|null $error_codes */
    private $error_codes;

    /**
     * ReCaptchaResponse constructor.
     * @param bool $success
     * @param DateTime|null $challenge_timestamp
     * @param string|null $hostname
     * @param array|null $error_codes
     */
    public function __construct(bool $success, ?DateTime $challenge_timestamp, ?string $hostname, ?array $error_codes)
    {
        $this->success = $success;
        $this->challenge_timestamp = $challenge_timestamp;
        $this->hostname = $hostname;
        $this->error_codes = $error_codes;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return DateTime|null
     */
    public function getChallengeTimestamp(): ?DateTime
    {
        return $this->challenge_timestamp;
    }

    /**
     * @return string|null
     */
    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    /**
     * @return array|null
     */
    public function getErrorCodes(): ?array
    {
        return $this->error_codes;
    }


}
