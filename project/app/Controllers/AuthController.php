<?php

namespace App\Controllers;

use App\Models\User;
use Core\CSRF;
use Core\Cache;
use Core\Controller;
use Core\Logger;
use Core\Response;
use Core\Security;
use Core\Session;

class AuthController extends Controller
{
    protected Cache $rateLimiter;
    protected User $users;

    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . '/../../config/mail.php';
        $this->rateLimiter = new Cache();
        $this->users = new User();
    }

    public function showLoginForm(array $params = []): void
    {
        Session::clearOldInput();
        $this->render('giris', ['title' => 'Giriş Yap']);
    }

    public function login(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/giris');
        }

        $input = $this->input();
        $email = Security::filterEmail($input['email'] ?? '');
        $password = $input['password'] ?? '';
        Session::pushOldInput(['email' => $email]);

        $key = 'login:' . md5(($email ?: 'guest') . ':' . ($_SERVER['REMOTE_ADDR'] ?? 'cli'));
        $attempts = (int)$this->rateLimiter->get($key, 0);
        if ($attempts >= 5) {
            Logger::warning('Giriş rate limit aşıldı', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
            ]);
            Session::flash('error', 'Çok sayıda başarısız giriş denemesi. Lütfen 15 dakika sonra tekrar deneyin.');
            Response::redirect('/giris');
        }

        if (!$email || !$password) {
            $this->rateLimiter->increment($key, 1, 900);
            Logger::warning('Eksik giriş bilgisi', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
            ]);
            Session::flash('error', 'E-posta ve parola zorunludur.');
            Response::redirect('/giris');
        }

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->rateLimiter->increment($key, 1, 900);
            Logger::warning('Başarısız giriş denemesi', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
            ]);
            Session::flash('error', 'Kimlik bilgileri hatalı.');
            Response::redirect('/giris');
        }

        $this->rateLimiter->delete($key);
        Session::clearOldInput();

        if (!empty($user['twofa_enabled'])) {
            $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            Session::set('pending_user', ['id' => $user['id'], 'role' => $user['role'], 'email' => $user['email']]);
            Session::set('otp_challenge', ['code' => $otp, 'expires' => time() + 300]);
            Logger::info('OTP doğrulaması gönderildi', [
                'user_id' => $user['id'],
                'email' => $user['email'],
            ]);
            if (class_exists('\\MailerStub')) {
                \MailerStub::send($user['email'], 'OTP Kodunuz', 'Giriş kodunuz: ' . $otp);
            }
            Session::flash('info', 'OTP kodu e-posta adresinize gönderildi. Lütfen 5 dakika içinde doğrulayın.');
            Response::redirect('/otp-dogrula');
        }

        $this->completeLogin($user);
    }

    protected function completeLogin(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_email', $user['email']);
        Session::flash('success', 'Hoş geldiniz, ' . ($user['name'] ?? ''));

        Logger::info('Kullanıcı giriş yaptı', [
            'user_id' => $user['id'],
            'role' => $user['role'],
        ]);

        Session::remove('pending_user');
        Session::remove('otp_challenge');

        $redirect = match ($user['role']) {
            'admin', 'editor', 'support' => '/admin',
            'customer' => '/hesabim',
            default => '/',
        };

        Response::redirect($redirect);
    }

    public function showRegisterForm(array $params = []): void
    {
        Session::clearOldInput();
        $this->render('kayit', ['title' => 'Kayıt Ol']);
    }

    public function register(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/kayit');
        }

        $input = $this->input();
        $email = Security::filterEmail($input['email'] ?? '');
        $name = Security::sanitize($input['name'] ?? '');
        $password = $input['password'] ?? '';
        $confirm = $input['password_confirmation'] ?? '';
        $phone = Security::sanitize($input['phone'] ?? '');
        Session::pushOldInput([
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
        ]);

        if (!$email || !$name || !$password || !$confirm) {
            Session::flash('error', 'Tüm alanlar zorunludur.');
            Response::redirect('/kayit');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'E-posta adresi geçersiz.');
            Response::redirect('/kayit');
        }

        if ($password !== $confirm) {
            Session::flash('error', 'Parola doğrulaması eşleşmiyor.');
            Response::redirect('/kayit');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Parola en az 8 karakter olmalıdır.');
            Response::redirect('/kayit');
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
            Session::flash('error', 'Parola en az bir büyük harf, bir küçük harf ve bir rakam içermelidir.');
            Response::redirect('/kayit');
        }

        if ($this->users->findByEmail($email)) {
            Session::flash('error', 'Bu e-posta ile kayıtlı hesap mevcut.');
            Response::redirect('/kayit');
        }

        $userId = $this->users->create([
            'email' => $email,
            'password_hash' => Security::hashPassword($password),
            'name' => $name,
            'phone' => $phone,
            'role' => 'customer',
            'status' => 'active',
            'twofa_enabled' => 0,
        ]);

        if (!$userId) {
            Session::flash('error', 'Kayıt işlemi başarısız oldu.');
            Response::redirect('/kayit');
        }

        $user = $this->users->findById($userId);
        Session::clearOldInput();
        Logger::info('Yeni kullanıcı kaydı', [
            'user_id' => $userId,
            'email' => $email,
        ]);
        $this->completeLogin($user);
    }

    public function logout(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/');
        }

        $userId = Session::get('user_id');
        Session::destroy();
        Logger::info('Kullanıcı çıkış yaptı', [
            'user_id' => $userId,
        ]);
        Response::redirect('/');
    }

    public function showResetForm(array $params = []): void
    {
        Session::clearOldInput();
        $this->render('sifre_sifirla', ['title' => 'Şifre Sıfırla']);
    }

    public function resetPassword(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/sifre-sifirla');
        }

        Session::flash('success', 'Şifre sıfırlama talimatları kayıtlı e-posta adresinize gönderildi.');
        Response::redirect('/giris');
    }

    public function showOtpForm(array $params = []): void
    {
        if (!Session::has('pending_user')) {
            Response::redirect('/giris');
        }

        $this->render('otp', ['title' => 'OTP Doğrulama']);
    }

    public function verifyOtp(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz CSRF belirteci.');
            Response::redirect('/otp-dogrula');
        }

        $challenge = Session::get('otp_challenge');
        $pending = Session::get('pending_user');
        if (!$challenge || !$pending) {
            Session::flash('error', 'OTP oturumu bulunamadı.');
            Response::redirect('/giris');
        }

        if (($challenge['expires'] ?? 0) < time()) {
            Session::flash('error', 'OTP süresi doldu. Lütfen tekrar giriş yapın.');
            Session::remove('otp_challenge');
            Response::redirect('/giris');
        }

        $input = $this->input();
        $code = preg_replace('/\D/', '', $input['otp'] ?? '');

        if ($code !== ($challenge['code'] ?? '')) {
            Logger::warning('OTP doğrulama başarısız', [
                'user_id' => $pending['id'] ?? null,
                'email' => $pending['email'] ?? null,
            ]);
            Session::flash('error', 'OTP kodu hatalı.');
            Response::redirect('/otp-dogrula');
        }

        $user = $this->users->findById($pending['id']);
        if (!$user) {
            Session::flash('error', 'Kullanıcı bulunamadı.');
            Response::redirect('/giris');
        }

        $this->completeLogin($user);
    }
}
