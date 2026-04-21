<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $agents = User::where('role', 'agent')
            ->withCount('properties')
            ->paginate(12);

        return view('agents.index', compact('agents'));
    }

    public function show(User $agent)
    {
        $properties = $agent->properties()
            ->where('status', 'active')
            ->with(['category', 'mainImage'])
            ->paginate(6);

        return view('agents.show', compact('agent', 'properties'));
    }
}
