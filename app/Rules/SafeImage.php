<?php

namespace App\Rules;

use App\Support\SafeImageUpload;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeImage implements ValidationRule
{
    public function __construct(private int $maxKb = SafeImageUpload::MAX_KB)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail(':attribute: file tidak valid.');
            return;
        }

        if ($error = SafeImageUpload::check($value, $this->maxKb)) {
            $fail(':attribute: ' . $error);
        }
    }
}
