<?php

namespace App\Http\Controllers;

use App\Models\PolicyStage;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiDocsController extends Controller
{
    /**
     * Display the Open API Documentation and Interactive Explorer.
     */
    public function index(Request $request): View
    {
        $stages = PolicyStage::orderBy('sequence_order')->get();
        $states = Region::orderBy('name')->select('id', 'name', 'slug', 'zone', 'iso_code')->get();
        $zones = [
            'North Central',
            'North East',
            'North West',
            'South East',
            'South South',
            'South West',
        ];

        $selectedStateSlug = $request->query('state', 'lagos');
        $activeEndpoint = $request->query('endpoint', 'states');

        return view('api-docs', [
            'stages' => $stages,
            'states' => $states,
            'zones' => $zones,
            'selectedStateSlug' => $selectedStateSlug,
            'activeEndpoint' => $activeEndpoint,
            'baseUrl' => url('/api/v1'),
        ]);
    }
}
