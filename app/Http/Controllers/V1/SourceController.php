<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SourceResource;
use App\Models\Source;

class SourceController extends Controller
{
    public function index()
    {
        return SourceResource::collection(
            Source::paginate()
        );
    }
}
