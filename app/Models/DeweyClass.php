<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DeweyClass extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getRecord()
    {
        $record = [
            'id' => $this->ddc,
            'label' => trim($this->ddc . ' ' . $this->title),
        ];
        return $record;
    }

    public function getRecordWithChildren($deweyClasses)
    {
        $record = $this->getRecord();
        $children = [];

        $ddc = $this->ddc;
        if (false === strpos($ddc, '.')) {
            $interval = 0;
            if ($ddc[2] == 0) {
                if ($ddc[1] == 0) {
                    $interval = 10;
                    for ($i = 1; $i <= 9; $i++) {
                        if (isset($deweyClasses[$ddc + $i])) $children[] = $deweyClasses[$ddc + $i][0]->getRecordWithChildren($deweyClasses);
                    }
                } else $interval = 1;
            }
            if ($interval) {
                for ($i = $interval; $i <= ($interval * 9); $i += $interval) {
                    if (isset($deweyClasses[$ddc + $i])) $children[] = $deweyClasses[$ddc + $i][0]->getRecordWithChildren($deweyClasses);
                }
            }

            $ddc .= '.';
        }

        for ($i = 0; $i <= 9; $i++) {
            if (isset($deweyClasses[$ddc . $i])) $children[] = $deweyClasses[$ddc . $i][0]->getRecordWithChildren($deweyClasses);
        }

        if (count($children)) {
            $record['children'] = $children;
        }
        return $record;
    }


    public static function buildTree()
    {

        $tree = [];

        $missingParents = [];

        $deweyClasses = DeweyClass::all()->groupBy('ddc')->all();
        // find classes without parent
        foreach ($deweyClasses as $deweyClass) {
            $ddc = $deweyClass[0]->ddc;
            if (!(($ddc[1] == 0) && ($ddc[2] == 0))) {
                $parent = $ddc;
                if (strlen($ddc) == 3) {
                    if ($ddc[2] != 0) {
                        $parent[2] = 0;
                    } else {
                        $parent[1] = 0;
                    }
                } else {
                    $parent = substr($ddc, 0, -1);
                    if (substr($parent, -1) == '.') $parent = substr($ddc, 0, -1);
                }
                if (!isset($deweyClasses[$parent])) {
                    $missingParents[] = $parent;
                }
            }
        }

        foreach ($missingParents as $missingParent) {
            $deweyClasses[$missingParent] = collect([new DeweyClass(['ddc' => $missingParent, 'title' => ''])]);
        }

        // start tree with basic classes x00
        for ($i = 100; $i <= 900; $i += 100) {
            $tree[] = $deweyClasses[$i][0]->getRecordWithChildren($deweyClasses);
        }

        return $tree;
    }


    public static function getTree() {
        if (Storage::exists('dewey_tree.json')) {
            $tree = json_decode(Storage::get('dewey_tree.json'));
        } else {
            $tree = DeweyClass::buildTree();
            Storage::put('dewey_tree.json', json_encode($tree));
        }
        return $tree;
    }
}
