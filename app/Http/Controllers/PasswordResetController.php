<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\MatchingActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    public function requestForm() { return view('auth.forgot-password'); }

    public function send(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'not_regex:/[\r\n]/']]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $token = Str::random(64);
            DB::table('password_resets')->updateOrInsert(['email' => $user->email], ['token' => Hash::make($token), 'created_at' => now()]);
            $url = '/reset-password/'.$token.'?email='.urlencode($user->email);
            $user->notify(new MatchingActivityNotification('パスワード再設定', 'パスワード再設定のリクエストを受け付けました。60分以内に手続きしてください。', $url));
        }
        return back()->with('status', '登録済みの場合、パスワード再設定の案内を送信しました。');
    }

    public function resetForm(string $token, Request $request) { return view('auth.reset-password', ['token' => $token, 'email' => $request->email]); }

    public function reset(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'not_regex:/[\r\n]/'], 'token' => ['required'], 'password' => ['required', 'min:8', 'confirmed']]);
        $record = DB::table('password_resets')->where('email', $data['email'])->first();
        abort_unless($record && Hash::check($data['token'], $record->token) && now()->diffInMinutes(Carbon::parse($record->created_at)) <= 60, 422, '再設定リンクが無効または期限切れです。');
        User::where('email', $data['email'])->update(['password' => Hash::make($data['password']), 'remember_token' => Str::random(60)]);
        DB::table('password_resets')->where('email', $data['email'])->delete();
        return redirect()->route('login')->with('status', 'パスワードを更新しました。');
    }
}
