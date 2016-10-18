<?php

namespace ValueObject\Exception;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationException extends \Exception
{
    private $violations = [];

    public function __construct(array $violations)
    {
        $this->violations = $violations;

        parent::__construct('ValueObject validation failed.');
    }

    public function getMessages()
    {
        $messages = [];

        /** @var ConstraintViolationList $violationList */
        foreach ($this->violations as $paramName => $violationList) {
            /** @var ConstraintViolation $violation */
            foreach ($violationList as $violation) {
                $messages[$paramName][] = $violation->getMessage();
            }
        }

        return $messages;
    }

    public function getJoinedMessages()
    {
        $messages = [];

        /** @var ConstraintViolationList $violationList */
        foreach ($this->violations as $paramName => $violationList) {
            /** @var ConstraintViolation $violation */
            foreach ($violationList as $violation) {
                $messages[$paramName][] = $violation->getMessage();
            }
            $messages[$paramName] = implode(' ', $messages[$paramName]);
        }

        return $messages;
    }
}
