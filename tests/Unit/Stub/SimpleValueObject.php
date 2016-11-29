<?php

namespace Test\Unit\Stub;

use ValueObject\ValueObject;

/**
 * @method getName
 * @method getEmail
 * @method getPassword
 * @method getConfirmPassword
 */
class SimpleValueObject extends ValueObject
{
    public function getRules()
    {
        $password = ['NotBlank', 'Type' => ['type' => 'string'], 'Length' => ['min' => 8]];
        return [
            'name' => ['NotBlank', 'Length' => ['min' => 5]],
            'email' => ['NotBlank', 'Email', 'Length' => ['min' => 10]],
            'password' => $password,
            'confirmPassword' => $password,
        ];
    }

    public function afterValidation(array $params)
    {
        if (
            // Array $params - it is array which is passed to ValueObject __constructor
            // hence we can not guarantee that this array is valid and contains all required fields,
            // so we need here to check is parameters "password" and "confirmPassword" present in array $params.
            // We don't need to rise errors in case when this parameters wasn't passed,
            // because for such purposes we have method getRules, which will handle errors for required fields.
            array_key_exists('password', $params) && array_key_exists('confirmPassword', $params)
            // And here we have our custom validation, which is ensures that passwords are equal.
            && $params['password'] !== $params['confirmPassword']
        ) {
            $this->setError('confirmPassword', 'This value must be equal to password value.');
        }
    }
}
