<?php

namespace Test\ValueObject;

use PHPUnit\Framework\TestCase;
use Test\Unit\ValueObject\ValueObjectStub;
use ValueObject\Exception\ValidationException;

class ValueObjectTest extends TestCase
{
    public function testNoOneParameterPassed()
    {
        $expectedErrors = [
            'name' => ['This parameter is required.'],
            'email' => ['This parameter is required.'],
            'password' => ['This parameter is required.'],
            'confirmPassword' => ['This parameter is required.'],
        ];
        try {
            $vo = new ValueObjectStub([]);
        } catch (ValidationException $e) {
            $actualErrors = $e->getMessages();
        }
        $this->assertSame($expectedErrors, $actualErrors);
    }
}
