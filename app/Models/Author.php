<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public static function normalizeName($authorName) {
        $authorName = trim($authorName);
        if (false != strpos( $authorName, ',')) {
            $tmp = explode(',', $authorName);
            $lastName = trim($tmp[0]);
            $firstName = trim($tmp[1]);
        } elseif(false != strpos( $authorName, ' ')) {
            $tmp = explode(' ', $authorName);
            $lastName = trim($tmp[count($tmp)-1]);
            unset($tmp[count($tmp)-1]);
            $firstName = join(' ', $tmp);
        } else {
            $lastName = $authorName;
            $firstName = '';
        }
        return [
            'name' => $lastName.', '.$firstName,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];

    }

    public static function fromName($authorName) {
        return self::firstOrCreate(self::normalizeName($authorName));
    }
}
