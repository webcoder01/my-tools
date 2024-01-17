<?php

namespace App\Tests\Core\Security\Entity;

use App\Core\Security\Infrastructure\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserHasDefaultRoleOnInstanciation(): void
    {
        $user = new User();

        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testRolesAreUniqueWhenANewAddedRoleAlreadyExists(): void
    {
        $user = new User();
        $user->addRole('ROLE_USER');

        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }
}