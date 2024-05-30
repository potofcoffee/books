<?php

namespace App\Console\Commands;

use App\Imports\BooksImport;
use App\Models\Book;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportBookList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import books from CSV';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Excel::import(new BooksImport(), 'books.xlsx');
        dd(Book::all());
        return Command::SUCCESS;
    }
}
