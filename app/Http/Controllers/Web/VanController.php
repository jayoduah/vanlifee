<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Van;
use App\Services\VanService;
use Illuminate\Http\Request;

class VanController extends Controller
{
    public function __construct(private VanService $vanService)
    {
    }

    public function index(Request $request)
    {
        $vans = $this->vanService->getAll($request->all());
        return view('vans.index', compact('vans'));
    }

    public function show(Van $van)
    {
        $van = $this->vanService->getDetails($van);
        return view('vans.show', compact('van'));
    }
}
