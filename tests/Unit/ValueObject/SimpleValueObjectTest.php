<?php

namespace Test\ValueObject;

use PHPUnit\Framework\TestCase;
use Test\Unit\Stub\SimpleValueObject;
use ValueObject\Exception\ValidationException;

class SimpleValueObjectTest extends TestCase
{
    /**
     * @test
     */
    public function noOneParameterPassed()
    {
        $expectedErrors = [
            'name' => ['This parameter is required.'],
            'email' => ['This parameter is required.'],
            'password' => ['This parameter is required.'],
            'confirmPassword' => ['This parameter is required.'],
        ];
        $actualErrors = [];
        try {
            new SimpleValueObject([]);
        } catch (ValidationException $e) {
            $actualErrors = $e->getMessages();
        }
        $this->assertSame($expectedErrors, $actualErrors);
    }

    /**
     * @test
     */
    public function notPassedPasswordsAndInvalidEmail()
    {
        $expectedErrors = [
            'email' => ['This value is not a valid email address.'],
            'password' => ['This parameter is required.'],
            'confirmPassword' => ['This parameter is required.'],
        ];
        $actualErrors = [];
        try {
            new SimpleValueObject([
                'name' => 'To secret M',
                'email' => 'top-secret-m[at]mi6.com',
            ]);
        } catch (ValidationException $e) {
            $actualErrors = $e->getMessages();
        }
        $this->assertSame($expectedErrors, $actualErrors);
    }

    /**
     * @test
     */
    public function notPassedPasswords()
    {
        $expectedErrors = [
            'password' => ['This parameter is required.'],
            'confirmPassword' => ['This parameter is required.'],
        ];
        $actualErrors = [];
        try {
            new SimpleValueObject([
                'name' => 'Mister Q',
                'email' => 'mister.q@mi6.com',
            ]);
        } catch (ValidationException $e) {
            $actualErrors = $e->getMessages();
        }
        $this->assertSame($expectedErrors, $actualErrors);
    }

    /**
     * @test
     */
    public function differentPasswords()
    {
        $expectedErrors = [
            'confirmPassword' => ['This value must be equal to password value.'],
        ];
        $actualErrors = [];
        try {
            new SimpleValueObject([
                'name' => 'Felix',
                'email' => 'felix@cia.com',
                'password' => '11112222',
                'confirmPassword' => '11113333',
            ]);
        } catch (ValidationException $e) {
            $actualErrors = $e->getMessages();
        }
        $this->assertSame($expectedErrors, $actualErrors);
    }

    /**
     * @test
     */
    public function aFewErrorsAboutConfirmPassword()
    {
        $expectedErrors = [
            'confirmPassword' => [
                'This value is too short. It should have 8 characters or more.',
                'This value must be equal to password value.',
            ],
        ];
        $actualErrors = [];
        try {
            new SimpleValueObject([
                'name' => 'Dominic',
                'email' => 'dominic@green.com',
                'password' => '7777aaaa',
                'confirmPassword' => '8888www',
            ]);
        } catch (ValidationException $e) {
            $actualErrors = $e->getMessages();
        }
        $this->assertSame($expectedErrors, $actualErrors);
    }
    /**
     * @test
     */
    public function aFewErrorsAboutConfirmPasswordInOneString()
    {
        $expectedErrors = [
            'confirmPassword' =>
            'This value is too short. It should have 8 characters or more. This value must be equal to password value.',
        ];
        $actualErrors = [];
        try {
            new SimpleValueObject([
                'name' => 'Matiz',
                'email' => 'matiz@almost-mi6.com',
                'password' => 'MMMMaaaa',
                'confirmPassword' => '9999zzz',
            ]);
        } catch (ValidationException $e) {
            $actualErrors = $e->getJoinedMessages();
        }
        $this->assertSame($expectedErrors, $actualErrors);
    }

    /**
     * @test
     */
    public function allValidAndMagicMethodsWorksCorrect()
    {
        $params = [
            'name' => 'James',
            'email' => 'bond@mi6.com',
            'password' => 'bond_pass_007',
            'confirmPassword' => 'bond_pass_007',
        ];
        $vo = new SimpleValueObject($params);
        $this->assertSame($vo->getName(), $params['name']);
        $this->assertSame($vo->getEmail(), $params['email']);
        $this->assertSame($vo->getPassword(), $params['password']);
        $this->assertSame($vo->getConfirmPassword(), $params['confirmPassword']);
    }

    /**
     * @test
     */
    public function allValidAndToArrayWorks()
    {
        $params = [
            'name' => 'Silva',
            'email' => 'silva@not-mi6.com',
            'password' => 'silvas_secret_pass',
            'confirmPassword' => 'silvas_secret_pass',
        ];
        $vo = new SimpleValueObject($params);
        $this->assertSame($vo->toArray(), $params);
    }
}
