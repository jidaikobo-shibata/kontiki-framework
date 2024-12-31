<?php

namespace Jidaikobo\Kontiki\Services;

use Aura\Session\Session;

class AuthService
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * 現在のユーザー情報を取得
     *
     * @return array|null ログイン中のユーザー情報（未ログインの場合はnull）
     */
    public function getCurrentUser(): ?array
    {
        $segment = $this->session->getSegment('jidaikobo\kontiki\auth');
        return $segment->get('user');
    }

    /**
     * ユーザーがログインしているかを確認
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->getCurrentUser() !== null;
    }
}
