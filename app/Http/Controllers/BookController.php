<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\DeweyClass;
use App\Sources\Sources;
use Biblys\Isbn\Isbn;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $bookCount = Book::count();
        return Inertia::render('Dashboard', compact('bookCount'));
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'isbn' => 'nullable|string',
            'title' => 'nullable|string',
            'author' => 'nullable|string',
        ]);

        $query = Book::with('authors');

        if (isset($data['isbn']) && ($data['isbn'] != '')) {
            $query->where('isbn', $data['isbn']);
            if (strlen($data['isbn']) == 10) {
                $query->orWhere('isbn', Isbn::convertToEan13($data['isbn']));
            } else {
                $query->orWhere('isbn', str_replace('-', '', Isbn::convertToIsbn10($data['isbn'])));
            }
        }

        if (isset($data['title']) && ($data['title'] != '')) $query->where('title', 'like', '%'.$data['title'].'%');

        if (isset($data['author']) && ($data['author'] != '')) {
            $query->whereHas('authors',function ($q) use ($data) {
                $q->where('name', 'like', '%'.$data['author'].'%');
                $q->orWhere('last_name', 'like', '%'.$data['author'].'%');
                $q->orWhere('first_name', 'like', '%'.$data['author'].'%');
            });
        }

        $ct = $query->count();
        if ($ct == 0) return redirect()->route('dashboard')->withErrors(['error' => 'Buch nicht gefunden']);
        if ($ct == 1) {
            return redirect()->route('book.edit', $query->first()->id);
        }

        $books = $query->get();
        return Inertia::render('Books/Search', compact('books'));

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $isbn = null)
    {
        $added = null;
        if ($request->has('added')) $added = Book::find($request->get('added'));

        $booksToPrint = Book::where('printed', 0)->orderBy('created_at', 'DESC')->get();

        return Inertia::render('Books/Add', compact('added', 'isbn', 'booksToPrint'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'isbn' => 'nullable|string',
            'author' => 'nullable|string',
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'publisher' => 'nullable|string',
            'place' => 'nullable|string',
            'year' => 'nullable|string',
            'ddc' => 'nullable|string',
        ]);

        $authors = $data['author'] ?? '';
        if (isset($data['author'])) unset($data['author']);

        $book = Book::create($data);
        foreach (explode(';', $authors) as $authorName) {
            $author = Author::fromName($authorName);
            if ($author) $book->authors()->attach($author->id);
        }

        return redirect()->route('book.add', ['added' => $book])->with('success', 'Buch hinzugefügt mit ID #'.$book->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return Inertia::render('Books/Show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'ISBN' => 'nullable|string',
            'author' => 'nullable|string',
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'publisher' => 'nullable|string',
            'place' => 'nullable|string',
            'year' => 'nullable|string',
            'ddc' => 'nullable|string',
        ]);

        $authors = $data['author'] ?? '';
        if (isset($data['author'])) unset($data['author']);

        $book->update($data);
        return redirect()->route('dashboard');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('dashboard');
    }

    public function query($isbn)
    {
        $data = Sources::query($isbn);
        return response()->json($data);
    }
}
