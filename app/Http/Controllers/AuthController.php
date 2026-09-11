<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\MatchingActivityNotification;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'not_regex:/[\r\n]/'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (in_array(Auth::user()->status, ['rejected', 'suspended'], true)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'このアカウントは現在利用できません。運営へお問い合わせください。']);
            }
            Auth::user()->update(['last_login_at' => now()]);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'not_regex:/[\r\n]/', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'role' => ['required', 'in:coach,organization'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'member_type' => $data['role'] === 'coach' ? 'coach_member' : 'browsing_member',
            'status' => 'pending',
        ]);

        Auth::login($user);
        $user->notify(new MatchingActivityNotification('登録申請を受け付けました', 'プロフィール登録後、運営による確認をお待ちください。'));
        User::where('role', 'admin')->get()->each->notify(new MatchingActivityNotification('新しい会員登録申請', $user->name.' 様から登録申請が届きました。', '/admin/users'));
        return redirect('/dashboard')->with('status', '会員登録が完了しました。プロフィール登録後、運営承認をお待ちください。');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
