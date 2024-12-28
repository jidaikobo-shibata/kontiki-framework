<?php

namespace Jidaikobo\Kontiki\Utils;

use Aura\Session\Session;

class CsrfManager
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * CSRFトークンの取得
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->session->getCsrfToken()->getValue();
    }

    /**
     * CSRFトークンの検証
     *
     * @param string|null $token
     * @return bool
     */
    public function isValid(?string $token): bool
    {
        return $this->session->getCsrfToken()->isValid($token);
    }

    /**
     * CSRFトークンの再生成
     *
     * @return void
     */
    public function regenerate(): void
    {
        $this->session->getCsrfToken()->regenerateValue();
    }
}
