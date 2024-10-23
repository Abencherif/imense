<?php

namespace App\Classes;

use App\Interfaces\ReadInterface;

class CsvReader implements ReadInterface
{

    public static function read($filePath)
    {
        $productData = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = null;
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    // Grab the header row
                    $header = $row;
                } else {
                    // Combine header with row to create an associative array
                    if (count($row) > count($header)) {
                        // Fix json fields
                        $row = (new self())->fixJsonFields($row);
                    }
                    // Combine header with row to create an associative array
                    $productData[empty($row[2]) ? 'no_sku' : $row[2]][] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $productData;
    }

    protected function fixJsonFields($row)
    {
        $fixedRow = [];
        $jsonField = '';
        $insideJson = false;

        foreach ($row as $value) {
            if (strpos($value, '[{') !== false || $insideJson) {
                // If we detect the start of JSON or we're already inside a JSON string
                $insideJson = true;
                $jsonField .= $value;

                if (strpos($value, '}]') !== false) {
                    // If we detect the end of JSON, stop concatenating
                    $fixedRow[] = $jsonField;
                    $jsonField = '';
                    $insideJson = false;
                }
            } else {
                // If it's a regular field, add it to the fixed row
                $fixedRow[] = $value;
            }
        }

        return $fixedRow;
    }
}
