<?php
namespace Core\Utility;

class Gravatar
{
    /** @var string $gravatar */
    private $gravatar;

    /**
     * Gravatar constructor.
     * @param string $email
     * @param int|null $size
     */
    public function __construct(string $email, int $size = null)
    {
        $hash = md5( strtolower( trim( $email ) ) );
        $this->gravatar = "https://www.gravatar.com/avatar/$hash" . ($size ? "?s=$size" : '');
    }

    /**
     * @return string
     */
    public function getGravatar(): string
    {
        return $this->gravatar;
    }

}
