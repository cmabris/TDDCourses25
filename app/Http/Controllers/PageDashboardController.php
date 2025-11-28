<?php

namespace App\Http\Controllers;

use App\Models\PurchasedCourse;

class PageDashboardController extends Controller
{
    public function __invoke()
    {
        $purchasedCourses = auth()->user()->purchasedCourses;

        return view('dashboard', compact('purchasedCourses'));
    }
}
