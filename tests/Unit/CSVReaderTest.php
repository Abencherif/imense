<?php

namespace Tests\Unit;

use App\Classes\CsvReader;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\TestCase;

class CSVReaderTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_read_csv_file(): void
    {
        $filePath = './storage/csv/test_products.csv';
        $data = CsvReader::read($filePath);
        $this->assertGreaterThan(0, count($data));
    }
    public function test_read_csv_file_with_empty_data(): void
    {
        $filePath = './storage/csv/test_products_empty.csv';
        $data = CsvReader::read($filePath);
        $this->assertEmpty($data);
    }
}
