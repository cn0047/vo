<?php

namespace Test\Unit\ValueObject;

use ValueObject\ValueObject;

/**
 * @method getName
 * @method getEmail
 * @method getPassword
 * @method getConfirmPassword
 */
class ValueObjectStub extends ValueObject
{
    public function getRules()
    {
        return [
            'name' => ['NotBlank', 'Length' => ['min' => 5]],
            'email' => ['NotBlank', 'Email', 'Length' => ['min' => 16]],
            'password' => ['NotBlank', 'Type' => ['type' => 'stirng'], 'Length' => ['min' => 8]],
            'confirmPassword' => ['NotBlank', 'Type' => ['type' => 'stirng'], 'Length' => ['min' => 8]],
        ];
    }

    public function afterValidation(array $params)
    {
        if (
            array_key_exists('password', $params)
            && array_key_exists('confirmPassword', $params)
            && $params['password'] !== $params['confirmPassword']
        ) {
            $this->setError('confirmPassword', 'This value must be equal to password value.');
        }
    }
}
