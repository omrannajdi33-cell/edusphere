<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::query()
            ->with('author')
            ->latest()
            ->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'publish_now' => ['sometimes', 'boolean'],
        ]);

        Announcement::query()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at' => $request->boolean('publish_now') ? now() : null,
            'author_id' => Auth::id(),
        ]);

        return redirect()->route('admin.annonces.index')->with('success', 'Annonce enregistrée.');
    }

    public function edit(Announcement $annonce): View
    {
        return view('admin.announcements.edit', ['announcement' => $annonce]);
    }

    public function update(Request $request, Announcement $annonce): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'publish_now' => ['sometimes', 'boolean'],
        ]);

        $annonce->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at' => $request->boolean('publish_now')
                ? ($annonce->published_at ?? now())
                : null,
        ]);

        return redirect()->route('admin.annonces.index')->with('success', 'Annonce mise à jour.');
    }

    public function destroy(Announcement $annonce): RedirectResponse
    {
        $annonce->delete();

        return redirect()->route('admin.annonces.index')->with('success', 'Annonce supprimée.');
    }
}
