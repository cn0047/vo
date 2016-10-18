ValueObject
-

#### Install

Via composer: `composer require kint/vo`.

#### Usage

````php
<?php
/**
 * @method getName
 * @method getEmailAddress
 */
class Partner extends ValueObject\ValueObject
{
     protected function getRules():array
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
} catch (ValueObject\Exception\ValidationException $e) {
    $errors = $e->getMessages();
}

````
