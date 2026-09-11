<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use App\Models\Inquiry;
use App\Models\User;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        return view('inquiries.index', [
            'inquiries' => auth()->user()->inquiries()->with('coachProfile')->latest()->paginate(10),
        ]);
    }

    public function create(Request $request)
    {
        $coach = $request->filled('coach')
            ? CoachProfile::publiclyVisible()->findOrFail($request->integer('coach'))
            : null;
        abort_if($coach && !auth()->user()->isOrganization() && !auth()->user()->isAdmin(), 403);

        return view('inquiries.create', compact('coach'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'coach_profile_id' => ['nullable', 'exists:coach_profiles,id'],
            'category' => ['required', 'in:consultation,trouble,account,service,other'],
            'subject' => ['required', 'max:255'],
            'body' => ['required', 'max:5000'],
        ]);
        abort_if(!empty($data['coach_profile_id']) && !auth()->user()->isOrganization() && !auth()->user()->isAdmin(), 403);
        $data['user_id'] = auth()->id();
        $data['status'] = 'open';

        $inquiry = Inquiry::create($data);
        User::where('role', 'admin')->get()->each->notify(new MatchingActivityNotification(
            '新しい問い合わせ',
            auth()->user()->name.' 様から「'.$inquiry->subject.'」を受け付けました。',
            '/admin/inquiries/'.$inquiry->id
        ));

        return redirect()->route('inquiries.show', $inquiry)->with('status', '問い合わせを送信しました。');
    }

    public function show(Inquiry $inquiry)
    {
        abort_unless(auth()->user()->isAdmin() || $inquiry->user_id === auth()->id(), 403);
        $inquiry->load('user', 'coachProfile', 'repliedBy');

        return view(auth()->user()->isAdmin() ? 'admin.inquiries.show' : 'inquiries.show', compact('inquiry'));
    }

    public function adminIndex(Request $request)
    {
        $query = Inquiry::with('user', 'coachProfile');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('subject', 'like', "%{$keyword}%")
                    ->orWhere('body', 'like', "%{$keyword}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$keyword}%"));
            });
        }

        return view('admin.inquiries.index', [
            'inquiries' => $query->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function reply(Request $request, Inquiry $inquiry)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'admin_reply' => ['nullable', 'required_if:status,resolved', 'max:5000'],
            'admin_note' => ['nullable', 'max:5000'],
        ]);
        $data['replied_by'] = auth()->id();
        $data['replied_at'] = filled($data['admin_reply'] ?? null) ? now() : $inquiry->replied_at;
        $inquiry->update($data);

        if ($inquiry->user && filled($data['admin_reply'] ?? null)) {
            $inquiry->user->notify(new MatchingActivityNotification(
                '問い合わせへの返信',
                '「'.$inquiry->subject.'」に運営から返信が届きました。',
                '/inquiries/'.$inquiry->id
            ));
        }

        return back()->with('status', '問い合わせを更新しました。');
    }
}
