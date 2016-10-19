<?php

namespace ValueObject;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use ValueObject\Exception\ValidationException;

abstract class ValueObject
{
    private $params = [];

    private $errors = [];

    /**
     * @return array
     */
    abstract protected function getRules();

    public function __construct(array $params)
    {
        $this->validate($params);
        $this->afterValidation($params);

        if (0 !== count($this->errors)) {
            throw new ValidationException($this->errors);
        }

        $this->params = $params;
    }

    final private function validate(array $params)
    {
        $validator = Validation::createValidator();

        /** @var array $rule */
        foreach ($this->getRules() as $paramName => $rule) {

            $constraints = [];

            foreach ($rule as $constraintName => $options) {

                if (!is_array($options)) {
                    $constraintName = $options;
                    $options = [];
                }

                $constraintClassName = 'Symfony\Component\Validator\Constraints\\' . $constraintName;
                $constraints[] = new $constraintClassName($options);
            }

            if (array_key_exists($paramName, $params)) {

                $violations = $validator->validate($params[$paramName], $constraints);

                if (0 !== count($violations)) {
                    $this->errors[$paramName] = $violations;
                }

            } else {

                $this->setError($paramName, 'This parameter is required.');

            }

        }
    }

    public function setError($paramName, $message)
    {
        $violation = new ConstraintViolation($message, '', [], '', $paramName, null);
        if (array_key_exists($paramName, $this->errors)) {
            $this->errors[$paramName]->add($violation);
        } else {
            $this->errors[$paramName] = new ConstraintViolationList([$violation]);
        }
    }

    public function afterValidation(array $params)
    {}

    public function __call(string $name, array $arguments )
    {
        $paramName = lcfirst(substr($name, 3));

        return $this->params[$paramName];
    }

    public function toArray()
    {
        return $this->params;
    }
}
