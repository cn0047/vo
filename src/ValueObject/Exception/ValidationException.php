<?php

namespace ValueObject\Exception;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Our custom exception which mean that VO is invalid.
 *
 * The main purpose on this exception
 * is to store validation errors
 * and provide simple way to obtain this errors further.
 */
class ValidationException extends \Exception
{
    /**
     * This array contains validation errors (violations).
     *
     * @var array $violations Validation errors (violations).
     */
    private $violations = [];

    /**
     * Constructor.
     *
     * @param array $violations Array with validation errors (violations).
     */
    public function __construct(array $violations)
    {
        $this->violations = $violations;

        parent::__construct('ValueObject validation failed.');
    }

    /**
     * Provides array with all validation errors (violations).
     *
     * IMPORTANT error message structure:
     * Each invalid parameter have array with error messages.
     *
     * @return array Array with errors messages.
     */
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

    /**
     * Provides array with all validation errors (violations).
     *
     * IMPORTANT error message structure:
     * Each invalid parameter have string with joined all error messages into single message.
     *
     * @return array Array with errors messages.
     */
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
