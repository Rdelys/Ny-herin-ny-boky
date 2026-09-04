<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $books = Book::query()
            ->with(['seller.sellerProfile'])
            ->latest()
            ->take(8)
            ->get();

        $sellers = User::query()
            ->where('role', 'vendeur')
            ->with('sellerProfile')
            ->withCount('books')
            ->latest()
            ->take(3)
            ->get();

        return view('home', ['books' => $books, 'sellers' => $sellers]);
    }
}