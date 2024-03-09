<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule as RuleI;
use Illuminate\Validation\Rule;
use App\Models\Locale;

class UniqueLocale implements RuleI
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private string $type, private $ignore=null, private string $ignore_column='id', private $where=null)
    {
    }

    public function passes($attribute, $value)
    {
        $lang = explode(".", $attribute)[1];
        $attr = explode(".", $attribute)[2];

        $query = Locale::where([
          $attr => $value,
          'localizable_type' => $this->type,
          'locale' => $lang,
        ]);

        if ($this->ignore) {
            $query->where($this->ignore_column, '!=', $this->ignore);
        }

        if ($this->where) {
            $query->where($this->where);
        }

        $count = $query->count();

        return !$count;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique');
    }
}
