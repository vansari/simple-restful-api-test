<?php
declare(strict_types=1);

namespace Api\System\Http;

class Response
{

    /** @var string|null */
    private $statusCode = null;
    /** @var null|string */
    private $bodyContent = null;

    public function __construct() {
        $this->statusCode = 'HTTP/1.1 200 OK';
        $this->bodyContent = null;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setStatusCode(int $code = 200): self {
        $prefix = 'HTTP/1.1 ';
        switch ($code) {
            case 200:
                $this->statusCode = $prefix . '200 OK';
                break;
            case 201:
                $this->statusCode = $prefix . '201 Created';
                break;
            case 204:
                $this->statusCode = $prefix . '204 No Content';
                break;
            case 404:
                $this->statusCode = $prefix . '404 Not Found';
                break;
            case 405:
                $this->statusCode = $prefix . '405 Method Not Allowed';
                break;
            case 422:
                $this->statusCode = $prefix . '422 Unprocessable Entity';
                break;
        }

        return $this;
    }

    /**
     * @param string|null $content
     * @return $this
     */
    public function setBody(?string $content): self {
        $this->bodyContent = $content;
        return $this;
    }

    /**
     * @return array
     */
    public function getResponse(): array {
        return [
            'status_code_header' => $this->statusCode,
            'body' => $this->bodyContent,
        ];
    }

    /**
     * @return string|null
     */
    public function getStatusCode(): ?string
    {
        return $this->statusCode;
    }

    /**
     * @return string|null
     */
    public function getBodyContent(): ?string
    {
        return $this->bodyContent;
    }
}