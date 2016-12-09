<?php

namespace ValueObject;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use ValueObject\Exception\ParameterNotFoundException;
use ValueObject\Exception\ValidationException;

abstract class ValueObject
{
    private $params = [];

    private $errors = [];

    /**
     * Returns validation rules.
     *
     * @return array Array with validation rules.
     */
    abstract protected function getRules();

    /**
     * Constructor.
     *
     * Most important method.
     * Here we validate received parameters against rules defined in method getRules,
     * and call after validation lifecycle callback.
     *
     * @param array $params Parameters being validated.
     *
     * @throws ValidationException In case when validation failed.
     */
    public function __construct(array $params)
    {
        $this->validate($params);
        $this->afterValidation($params);
        // In case when we obtain invalid parameter we throw error here,
        // this approach ensures that our VO have only valid parameters.
        if (0 !== count($this->errors)) {
            throw new ValidationException($this->errors);
        }
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

            if (isset($params[$paramName])) {

                $value = $params[$paramName];
                $violations = $validator->validate($value, $constraints);

                if (0 === count($violations)) {
                    $this->params[$paramName] = $value;
                } else {
                    $this->errors[$paramName] = $violations;
                }

            } else {

                $this->setError($paramName, 'This parameter is required.');

            }

        }
    }

    /**
     * Sets error message for certain parameter.
     *
     * @param string $paramName Parameter name (key in array $parameters which is passed to __constructor method).
     * @param string $message Custom error message.
     */
    public function setError($paramName, $message)
    {
        $violation = new ConstraintViolation($message, '', [], '', $paramName, null);
        if (isset($this->errors[$paramName])) {
            // Adds error message to ConstraintViolationList.
            $this->errors[$paramName]->add($violation);
        } else {
            // Creates ConstraintViolationList and puts into it first error message.
            $this->errors[$paramName] = new ConstraintViolationList([$violation]);
        }
    }

    /**
     * Lifecycle callback - after validation.
     *
     * This method can be used for custom validation purposes.
     * This method will be called each time after validation rules (validate method).
     *
     * @param array $params Array with parameters passed to VO.
     */
    public function afterValidation(array $params)
    {}

    /**
     * Returns parameter value by parameter name.
     *
     * @param string $name Parameter name (key in array $parameters which is passed to __constructor method).
     * @param array $arguments Array with arguments.
     *
     * @throws ParameterNotFoundException In case when VO don't have needed parameter.
     *
     * @return mixed Parameter value.
     */
    public function __call($name, array $arguments)
    {
        $paramName = lcfirst(substr($name, 3));
        if (!isset($this->params[$paramName])) {
            throw new ParameterNotFoundException();
        }
        return $this->params[$paramName];
    }

    /**
     * Gets VO parameter as array.
     *
     * @return array Array which contains all VO parameters.
     */
    public function toArray()
    {
        return $this->params;
    }
}
