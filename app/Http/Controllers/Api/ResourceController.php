<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::query();

        if ($request->type) {
            $query->ofType($request->type);
        }

        if (!auth()->user()->isAdmin()) {
            $query->published();
        }

        $resources = $query->orderBy('order')->get();

        return response()->json($resources);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:schedule,document,presentation,guideline',
            'file' => 'required_without:url|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
            'url' => 'required_without:file|nullable|url',
            'is_published' => 'boolean',
            'order' => 'integer'
        ]);

        $data = $request->except('file');

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('resources', 'public');
            $data['file_path'] = $path;
            $data['file_type'] = $request->file('file')->getClientOriginalExtension();
        }

        $resource = Resource::create($data);

        return response()->json($resource, 201);
    }

    public function show(Resource $resource)
    {
        if (!auth()->user()->isAdmin() && !$resource->is_published) {
            abort(404);
        }

        return response()->json($resource);
    }

    public function update(Request $request, Resource $resource)
    {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'type' => 'in:schedule,document,presentation,guideline',
            'file' => 'file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
            'url' => 'nullable|url',
            'is_published' => 'boolean',
            'order' => 'integer'
        ]);

        $data = $request->except('file');

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $path = $request->file('file')->store('resources', 'public');
            $data['file_path'] = $path;
            $data['file_type'] = $request->file('file')->getClientOriginalExtension();
        }

        $resource->update($data);

        return response()->json($resource);
    }

    public function destroy(Resource $resource)
    {
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return response()->json(null, 204);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'resources' => 'required|array',
            'resources.*.id' => 'required|exists:resources,id',
            'resources.*.order' => 'required|integer'
        ]);

        foreach ($request->resources as $item) {
            Resource::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Resources reordered successfully']);
    }
}
