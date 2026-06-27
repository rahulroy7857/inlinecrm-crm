<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Source extends Model
{
    protected $fillable = [
        'name',
        'status',
        'description',
        'code'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($source) {
            $name = trim($source->name);

            $words = preg_split('/\s+/', $name);

            if (count($words) === 1) {
            // Single word: take first and last letter
            $word = $words[0];
            $code = Str::upper(substr($word, 0, 1) . substr($word, -1));
            } else {
            // Multiple words: take first letter of each word
            $code = '';
            foreach ($words as $w) {
                $code .= substr($w, 0, 1);
            }
            $code = Str::upper($code);
            }

            // Ensure code is unique
            $baseCode = $code;
            $counter = 1;
            while (static::where('code', $code)->exists()) {
            $code = $baseCode . '-' . $counter;
            $counter++;
            }

            $source->code = $code;
        });
    }
}
