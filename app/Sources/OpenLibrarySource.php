<?php

namespace App\Sources;

use App\Models\Author;

class OpenLibrarySource extends AbstractSource
{
    protected $sourceTitle = 'OpenLibrary Initiative';


    protected function joinData($data, $key, $separator = ' / ') {
        if (!isset($data[$key])) return '';
        $r = [];
        foreach ($data[$key] as $record) {
            $r[] = $record['name'];
        }
        return join($separator, $record);
    }

    public function query($isbn): array
    {
        $results = [];
        $books = json_decode(file_get_contents('https://openlibrary.org/api/books?bibkeys=ISBN:'.$isbn.'&format=json&jscmd=data'), true);

        foreach ($books as $data) {
            if (isset($data['classifications']['dewey_decimal_class'])) {
                $ddc = is_array($data['classifications']['dewey_decimal_class']) ? $data['classifications']['dewey_decimal_class'][0] : $data['classifications']['dewey_decimal_class'];
            } else {
                $ddc = '';
            }

            $record = [];
            $record['book'] = [
                'title' => $data['title'] ?? '',
                'subtitle' => $data['subtitle'] ?? '',
                'ddc' =>  $ddc,
                'publisher' => $this->joinData($data, 'publishers'),
                'place' => $this->joinData($data, 'publish_places'),
                'year' => $data['publish_date'] ?? '',
            ];

            $authors = [];
            foreach ($data['authors'] as $author) {
                $authors[] = Author::normalizeName($author['name'])['name'];
            }
            $record['authors'] = join('; ', $authors);

            $results[] = $record;
        }

        return $results;
    }
}
