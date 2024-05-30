<?php

namespace App\Sources;

use App\Models\Author;
use App\Sources\Schemas\QueryResponse;
use Scriptotek\Marc\Collection;
use Scriptotek\Sru\Record;

class DnbSource extends AbstractSruSource
{

    protected $sourceTitle = 'Deutsche Nationalbibliothek';
    protected $url = 'https://services.dnb.de/sru/dnb';
    protected $schema = 'MARC21-xml';

    public function getAuthors(Record $record): array {
        $authors = parent::getAuthors($record);

        $line = QueryResponse::getDataField($record, 245, 'c');
        $separator = ', ';
        if (false !== strpos($line, '/')) $separator = '/';
        foreach (explode($separator, $line) as $author) {
            $normalized = Author::normalizeName($author)['name'];
            if (!in_array($normalized, $authors)) $authors[] = $normalized;
        }

        return $authors;
    }


}

