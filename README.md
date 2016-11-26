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
`getRules` - it is required method,
which return validation rules for properties of your value object.
This rules - Symfony validators! So you have all power of Symfony validation in your VO!
List of all constraints available [here](http://symfony.com/doc/current/validation.html#basic-constraints).

Somewhere in another class you can use your VO class in next way:

````php
<?php

Namespace Domain\Model\SomeDomainModel;

use VO\Partner;
use ValueObject\Exception\ValidationException;

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
