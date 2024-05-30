<?php

namespace App\Sources;

use App\Models\Author;
use Scriptotek\Marc\Collection;

abstract class AbstractZ3950Source extends AbstractSource
{

    protected $host = '';

    protected function trimData($data) {
        $data = trim($data);
        while (in_array(substr($data, -1) ,['/', ',', '.', ':'])) $data = substr($data, 0, -1);

        return trim($data);
    }

    protected function getTitle($marc) {
        $titleField = $marc->getField('245');
        $nonfiling = $titleField->getIndicator(2);

        if ($nonfiling) {
            // Sort using the subset of the $a subfield
            $title = substr($titleField->getSubfield('a')->getData(), $nonfiling);
        } else {
            // Sort using the entire contents of the $a subfield
            $title = $titleField->getSubfield('a')->getData();
        }

        return $this->trimData($title);
    }

    protected function getSubtitle($marc)
    {
        $field = $marc->getField('245')->getSubfield('b');
        if ($field) {
            return $this->trimData($field->getData());
        }
    }

    protected function getSanitizedSubfield ($record, $n, $sub) {
        $field = $record->getField($n);
        if (!$field) return '';
        $contents = $field->getSubfield($sub);
        if (!$contents) return '';
        return $this->trimData($contents->getData());
    }

    public function query($isbn): array
    {

        $connection = yaz_connect($this->host);
        yaz_syntax($connection, "usmarc");
        yaz_range($connection, 1, 10);
        yaz_search($connection, "rpn", $isbn);
        yaz_wait();


        $error = yaz_error($connection);
        $results = [];
        if (empty($error)) {
            $hits = yaz_hits($connection);
            $errorLevel = error_reporting();
            error_reporting(0);
            if ($hits) {
                $raw = yaz_record($connection, 1, 'xml');
                $marcCollection = Collection::fromString($raw);
                foreach ($marcCollection as $record) {
                    $result['book'] = [
                        'title' => $this->getTitle($record),
                        'subtitle' => $this->getSubtitle($record),
                        'isbn' => $isbn,
                        'place' => $this->getSanitizedSubfield($record, 260, 'a'),
                        'publisher' => $this->getSanitizedSubfield($record, 260, 'b'),
                        'year' => $this->getSanitizedSubfield($record, 260, 'c'),
                        'DDC' => str_replace('/', '', $this->getSanitizedSubfield($record, '082', 'a')),
                    ];
                    $result['authors'] = [];
                    $result['authors'][] = Author::normalizeName($this->getSanitizedSubfield($record, 100, 'a'))['name'];
                    foreach ($record->getFields(700) as $field) {
                        if ($sf = $field->getSubfield('a')) $result['authors'][] = Author::normalizeName($this->trimData($sf->getData()))['name'];
                    }
                }
            }
            error_reporting($errorLevel);
            return $result;
        }

        return [];
    }


}
