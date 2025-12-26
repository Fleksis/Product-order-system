<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HasEnoughStock implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $index = explode('.', $attribute)[1];
        $itemName = explode('.', $attribute)[0];

        $available = Product::select('available_quantity')
            ->find(request()->input("products.$index.id"))
            ->available_quantity;

        if ($value > $available) {
            $fail("The $itemName is out of stock.");
        }
    }
}
