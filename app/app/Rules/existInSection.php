<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\SectionBean;

class existInSection implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sections = auth()->user()->sections->pluck('id');
        $exists = SectionBean::where('user',$value)->whereIn('section', $sections)->exists();

        if(!$exists) {
            $fail('Users not Existed please Refresh');
        }
    }
}
