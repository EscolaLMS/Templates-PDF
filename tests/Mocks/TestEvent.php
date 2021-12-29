<?php

namespace EscolaLms\TemplatesPdf\Tests\Mocks;

use EscolaLms\Core\Models\User;

class TestEvent
{
    private User $user;
    private User $friend;

    public function __construct(User $user, User $friend)
    {
        $this->user = $user;
        $this->friend = $friend;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getFriend(): User
    {
        return $this->friend;
    }
}
