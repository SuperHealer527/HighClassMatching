<?php

namespace App\Http\Controllers;

use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProjectMemberController extends Controller
{
    public function index()
    {
        return view('admin.project-members.index', [
            'members' => ProjectMember::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        $member = new ProjectMember([
            'sort_order' => (int) ProjectMember::max('sort_order') + 1,
            'is_active' => true,
        ]);

        return view('admin.project-members.form', compact('member'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['image_path'] = $request->file('image')->store('project-members', 'public');
        $data['is_active'] = $request->boolean('is_active');

        ProjectMember::create($data);

        return redirect()->route('admin.project-members.index')->with('status', 'メンバーを追加しました。');
    }

    public function edit(ProjectMember $projectMember)
    {
        return view('admin.project-members.form', ['member' => $projectMember]);
    }

    public function update(Request $request, ProjectMember $projectMember)
    {
        $data = $this->validated($request, false);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $this->deleteManagedImage($projectMember->image_path);
            $data['image_path'] = $request->file('image')->store('project-members', 'public');
        }

        $projectMember->update($data);

        return redirect()->route('admin.project-members.index')->with('status', 'メンバー情報を更新しました。');
    }

    public function destroy(ProjectMember $projectMember)
    {
        $this->deleteManagedImage($projectMember->image_path);
        $projectMember->delete();

        return redirect()->route('admin.project-members.index')->with('status', 'メンバーを削除しました。');
    }

    private function validated(Request $request, bool $imageRequired): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:40'],
            'role' => ['required', 'string', 'max:40'],
            'affiliation' => ['required', 'string', 'max:20'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        unset($data['image']);

        return $data;
    }

    private function deleteManagedImage(?string $path): void
    {
        if ($path && Str::startsWith($path, 'project-members/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
