<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Contracts\Pagination\Paginator;

class DashboardController extends Controller
{
    public function index()
    {
        if(Auth::check() && \in_array(Auth::user()->role->name, ['master', 'seller'])){
            return view('dashboard.index');
        } else {
            return view('errors.index', ['message' => 'unauthorized']);
        }
    }

    public function showCategories(){
        $categories = Category::paginate(5);

        return view('dashboard.categories.index', ['categories' => $categories]);
    }

    public function showUsers(){
        return view('dashboard.user.index');
    }

    public function showProfile(){
        return view('dashboard.profile.index');
    }

    public function buyerPage(){
        return view('buyer.index');
    }
}
