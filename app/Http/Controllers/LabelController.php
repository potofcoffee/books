<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use PDF;

class LabelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return Response
     */
    public function form(): Response
    {
        $books = Book::where('printed', 'not', true)->get();
        return Inertia::render('Labels/Form', compact('books'));
    }

    /**
     * @param $skipLabels
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function print($skipLabels)
    {
        $books = Book::where('printed', 'not', true)->limit(24-$skipLabels)->get();
        /** @var \niklasravnsborg\LaravelPdf\Pdf $pdf */
        $pdf = PDF::loadView('pdf.labels', compact('books', 'skipLabels'));
        return response($pdf->download('labels.pdf'))
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="labels.pdf"')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate')
            ->header('Pragma', 'public');
    }

    /**
     * @param $skipLabels
     * @return RedirectResponse
     */
    public function printed($skipLabels): RedirectResponse
    {
        Book::where('printed', false)->limit(24-$skipLabels)->update(['printed' => true]);
        return redirect()->route('book.add');
    }
}
