<?php

namespace ValueObject;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use ValueObject\Exception\ParameterNotFoundException;
use ValueObject\Exception\ValidationException;

abstract class ValueObject
{
    /**
     * This array contains VO parameters values which is already validated.
     *
     * @var array $params Validated params, which are available through magic method getParameterNameInCamelCase.
     */
    private $params = [];

    /**
     * This array contains validation errors.
     *
     * @var array $errors Validation errors.
     */
    private $errors = [];

    /**
     * Returns validation rules.
     *
     * This rules describes REQUIRED parameters.
     * Optional parameter must be resolved outside VO instance,
     * so only in this way we can ensure that VO have all parameters and they all valid.
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

    /**
     * Most important method. Here performs all validation stuff.
     *
     * @param array $params Parameters being validated.
     */
    final private function validate(array $params)
    {
        // Initiate instance of Symfony validator.
        $validator = Validation::createValidator();

        /** @var array $rule */
        foreach ($this->getRules() as $paramName => $rule) {

            // One validation rule can have lot of constraints (Symfony constraints).
            // We must check them all,
            // so that's why here we build array with appropriate constraints for particular validation rule.
            // This array contains only constraints for only one validation rule,
            // for only one parameter which must be validated.
            $constraints = [];
            foreach ($rule as $constraintName => $options) {
                $constraintOptions = $options;
                // Constraint can be specified in simple way,
                // like string which is constraint name (for example: `'NotBlank'` etc).
                // Or as array with parameters, like:
                // array kes - is constraint name
                // and array value - constrain options (for example: `'Length' => ['min' => 5]`).
                if (!is_array($options)) {
                    $constraintName = $options;
                    $constraintOptions = [];
                }
                $constraintClassName = 'Symfony\Component\Validator\Constraints\\' . $constraintName;
                $constraints[] = new $constraintClassName($constraintOptions);
            }

            // In the beginning of validation we must be sure that required parameter is passed to VO,
            // otherwise it will be first validation error.
            if (isset($params[$paramName])) {
                $value = $params[$paramName];
                // Performs Symfony validation.
                $violations = $validator->validate($value, $constraints);
                if (0 === count($violations)) {
                    // Parameter valid,
                    // and only in this case we can store this parameter inside VO.
                    // Thereby we ensure that our VO contains only valid parameters with only valid values.
                    $this->params[$paramName] = $value;
                } else {
                    // Parameter invalid,
                    // so we can not store it inside VO,
                    // we only can store error message about validation fail.
                    $this->errors[$paramName] = $violations;
                }
            } else {
                // We can not validate this parameter against validation rules,
                // because this parameter was not passed to VO.
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
