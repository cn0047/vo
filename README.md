ValueObject
-

[![Build Status](https://scrutinizer-ci.com/g/cn007b/vo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/cn007b/vo/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cn007b/vo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cn007b/vo/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/cn007b/vo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/cn007b/vo/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/085e3fe2470c42968f478d8041f3c176)](https://www.codacy.com/app/cn007b/vo?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=cn007b/vo&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/54f702945d4cab68cca0/maintainability)](https://codeclimate.com/github/cn007b/vo/maintainability)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](http://makeapullrequest.com)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f9ae75a5-f16a-4ce9-a194-8df1460ed4f7/mini.png)](https://insight.sensiolabs.com/projects/f9ae75a5-f16a-4ce9-a194-8df1460ed4f7)
[![Packagist](https://img.shields.io/packagist/dt/kint/vo.svg)](https://packagist.org/packages/kint/vo)

Value Object (VO) - it's container for your parameters,
which knows only about your *parameters* (how to set/get them)
and about *validation rules* (what type of data allowed for certain parameter).
You are not allowed to create VO with invalid parameters (exception will be thrown) -
it provides you opportunity to be sure that your VO is **always valid**.
Also, VO is **immutable** - hence you're on safe side and no one will modify your VO since it created!

Main benefits: now you can use this VO everywhere and you don't have to worry about validation,
you don't have to mix your business logic with validation code on every application layer (controller, service, model, etc),
also you can use it as `type-hint` hence your code will be more clear, interface oriented and more precise.

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
class SecretAgent extends ValueObject
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
`getRules` - required method,
which returns validation rules for properties of your value object.
<br>These rules - Symfony validators! So you have all power of Symfony validation in your VO!
<br>List of all validation rules (constraints) available [here](http://symfony.com/doc/current/validation.html#basic-constraints).

Now you can create new instance of VO:

````php
$secretAgent = new VO\SecretAgent(['name' => 'Bond', 'email' => 'james.bond@mi6.com']);
// Now you can use magic methods and get values from your VO.
$secretAgentName = $secretAgent->getName();
$secretAgentEmail = $secretAgent->getEmail();
// Also you can pass this VO as parameter.
$controller->doSomethingWithSecretAgent($secretAgent);
````

In case of invalid data - you'll receive exception with information about all violated rules:

````php
use VO\SecretAgent;
use ValueObject\Exception\ValidationException;

try {
    $secretAgent = new SecretAgent(['name' => 'Bond', 'email' => 'error']);
} catch (ValidationException $e) {
    $errors = $e->getMessages();
}
````

As result your code will be super simple, just like that:

````php
class SecretAgentController
{
    public function indexAction(array $postData)
    {
        (new SecretAgentService())->doSomethingWithSecretAgent(new VO\SecretAgent($postData));
    }
}

class SecretAgentService
{
    public function doSomethingWithSecretAgent(VO\SecretAgent $secretAgent)
    {
        (new SecretAgentModel())->update($secretAgent);
    }
}

class SecretAgentModel
{
    public function update(VO\SecretAgent $secretAgent)
    {
        $secretAgentName = $secretAgent->getName();
        $secretAgentEmail = $secretAgent->getEmail();
        // Update model.
    }
}
````

ENJOY! 🙂 

Example with custom validation rules and post-validation behavior available
[here](https://github.com/cn007b/vo/blob/master/tests/Unit/Stub/SimpleValueObject.php).

#### Value Object vs Form

Value Object looks bit similar to Form, but the key difference - is that
you can't rely on form everywhere because form may be valid or invalid at any point of time
and you have always to check `$form->isValid();`
but with Value Object you 100% sure it's always valid, hence you can loosely use it everywhere!!!
