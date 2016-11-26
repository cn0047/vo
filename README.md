ValueObject
-

#### Install

Via composer: `composer require kint/vo`.

#### Usage

Value object class:

````php
<?php

namespace VO;

use ValueObject\Exception\ValidationException;
use ValueObject\ValueObject;

/**
 * @method getName
 * @method getEmail
 */
class Partner extends ValueObject
{
     protected function getRules()
     {
        return [
            'name' => ['NotBlank', 'Length' => ['min' => 5]],
            'email' => ['NotBlank', 'Email', 'Length' => ['min' => 15]],
        ];
     }
}
````

Somewhere in another class you can use your VO class in next way:

````php
<?php

use VO\Partner;

try {
    $partner = new Partner(['name' => 'Bond', 'email' => 'error']);

    // Now you can use magic methods and get values from your VO.
    $partnerName = $partner->getName();
    $partnerEmail = $partner->getEmail();

} catch (ValidationException $e) {

    // Here you can obtain your VO validation errors. 
    $errors = $e->getMessages();

}

````

List of all constraints available [here](http://symfony.com/doc/current/validation.html#basic-constraints).
