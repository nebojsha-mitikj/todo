<?php

declare(strict_types=1);

namespace App\Http\Requests\Validation;

/**
 * @method static array getRules()
 */
class TaskValidationRules extends ValidationRules
{
    protected array $rules = [
        'description' => 'required|string|min:1|max:255',
        'status' => 'required|in:to-do,in-progress,finished',
        'start_time' => 'nullable|date_format:H:i:s',
        'end_time' => 'nullable|date_format:H:i:s'
    ];
}
