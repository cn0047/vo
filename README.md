ValueObject
-

#### Install

Via composer: `composer require kint/vo`.

#### Usage

````php
<?php

use ValueObject\Exception\ValidationException;
use ValueObject\ValueObject;

/**
 * @method getName
 * @method getEmailAddress
 */
class Partner extends ValueObject
{
     protected function getRules()
     {
        return [
            'name' => ['NotBlank', 'Length' => ['min' => 5]],
            'email' => ['NotBlank', 'Email', 'Length' => ['min' => 16]],
        ];
     }
}

try {
    $partner = new Partner(['name' => 'Bond', 'email' => 'none']);

    $partnerName = $partner->getName();
    $partnerEmail = $partner->getEmail();
} catch (ValidationException $e) {
    $errors = $e->getMessages();
}

````

List of all constraints available [here](http://symfony.com/doc/current/validation.html#basic-constraints).
