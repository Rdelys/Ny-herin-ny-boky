<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Liste de tous les utilisateurs (clients + vendeurs), avec leur
     * nombre de livres publiés — donnée réelle (withCount).
     */
    public function index(): View
    {
        $users = User::query()
            ->withCount('books')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', ['users' => $users]);
    }
}