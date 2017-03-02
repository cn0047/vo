ValueObject
-

[![Build Status](https://scrutinizer-ci.com/g/cn007b/vo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/cn007b/vo/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cn007b/vo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cn007b/vo/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/cn007b/vo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/cn007b/vo/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f9ae75a5-f16a-4ce9-a194-8df1460ed4f7/mini.png)](https://insight.sensiolabs.com/projects/f9ae75a5-f16a-4ce9-a194-8df1460ed4f7)

#### Install

Via composer: `composer require kint/vo`.

#### Usage

Value object class:

````php
<?php

namespace VO;

use ValueObject\ValueObject;

/**
 * @method string getName Gets name.
 * @method string getEmail Gets email.
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
<br>This rules - Symfony validators! So you have all power of Symfony validation in your VO!
<br>List of all constraints available [here](http://symfony.com/doc/current/validation.html#basic-constraints).

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

More interesting example available [here](https://github.com/cn007b/vo/blob/master/tests/Unit/Stub/SimpleValueObject.php).
