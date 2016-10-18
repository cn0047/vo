<?php

namespace ValueObject;

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

            $violations = $validator->validate($params[$paramName], $constraints);

            if (0 !== count($violations)) {
                $this->errors[$paramName] = $violations;
            }
        }
    }

    public function __call(string $name, array $arguments )
    {
        $paramName = lcfirst(substr($name, 3));

        return $this->params[$paramName];
    }
}
