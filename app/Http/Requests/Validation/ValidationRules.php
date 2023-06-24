<?php

declare(strict_types=1);

namespace App\Http\Requests\Validation;


class ValidationRules
{
    /**
     * @var array
     */
    protected array $rules = [];

    /**
     * Get the validation rules.
     *
     * @return array
     */
    protected function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Handle static method calls.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $instance = new static();
        return call_user_func_array([$instance, $name], $arguments);
    }
}
