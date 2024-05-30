<?php

namespace App\Imports;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class BooksImport implements WithHeadingRow, OnEachRow
{

    public function onRow(Row $excelRow)
    {
        $row = $excelRow->toArray();

        $book = Book::create([
            'title' => $row['title'],
            'publisher' => strpos($row['publication'], '(') ? trim(substr($row['publication'], 0, strpos($row['publication'], '(') - 1)) : $row['publication'],
            'year' => $row['date'],
            'ISBN' => strtr($row['isbn'], ['[' => '', ']' => '']),
            'ddc' => str_replace(',', '.', $row['dewey_decimal']),
        ]);

        $authors = array_merge([$row['primary_author']], explode('|', $row['secondary_author']));
        foreach ($authors as $authorImport) {
            if (trim($authorImport)) {
                if (false !== strpos($authorImport, ',')) {
                    list($lastName, $firstName) = explode(',', $authorImport);
                } else {
                    $lastName = trim($authorImport);
                    $firstName = '';
                }
                $author = Author::firstOrCreate([
                    'name' => trim($firstName) . ' ' . trim($lastName),
                    'last_name' => trim($lastName),
                    'first_name' => trim($firstName)
                ]);
                $book->authors()->attach($author->id);
            }
        }
    }


}
