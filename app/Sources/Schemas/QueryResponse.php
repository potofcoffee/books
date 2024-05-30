<?php

namespace App\Sources\Schemas;

use Scriptotek\Sru\Record;

class QueryResponse
{

    public static function getDataField(Record $record, $field, $subField = null) {
        $results = [];
        foreach ($record->data->el->record->datafield as $df) {
            if ((string)$df['tag'] == $field) {
                if (!$subField) $results[] = (string)$df;
                foreach($df->subfield as $sf) {
                    if ((string)$sf['code'] == $subField) $results[] = (string)$sf;
                }
            }
        };
        if (!count($results)) return '';
        if (count($results) == 1) return $results[0];
        return $results;
    }

    public static function fromSruRecord($isbn, Record $record, $filters, $authorCallback, $stringFilter = null) {
        $response = [
            'book' => [
                'isbn' => $isbn,
            ],
            'authors' => call_user_func($authorCallback, $record),
        ];

        foreach ($filters as $key => $filter) {
            $raw = self::getDataField($record, $filter[0], $filter[1]);
            if (isset($filter[3]) && is_array($raw)) $raw = join($filter[3], $raw);
            if (isset($filter[2])) $raw = strtr($raw, $filter[2]);
            $response['book'][$key] = $stringFilter ? call_user_func($stringFilter, $raw) : $raw;
        }
        return $response;
    }
}
