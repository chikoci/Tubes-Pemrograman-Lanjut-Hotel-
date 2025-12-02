<?php
// Auth Controller
class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User_model($this->db);
    }

    // halaman login
    public function login() {
        // kalo udah login redirect aja
        if (isLoggedIn()) {
            redirect('home');
            return;
        }

        // Jika form disubmit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Validasi menggunakan helper validate()
            $errors = validate($_POST, [
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            if (empty($errors)) {
                // Coba login
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role_name'] = $user['role_name'];

                    setFlash('success', 'Login berhasil! Selamat datang ' . $user['name']);

                    // Redirect berdasarkan role
                    if ($user['role_name'] === 'Admin') {
                        redirect('admin/dashboard');
                    } else {
                        redirect('home');
                    }
                    return;
                } else {
                    $errors['login'] = 'Email atau password salah';
                }
            }

            // Jika ada error, simpan untuk ditampilkan
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['email' => $email];
        }

        // Load view
        include 'views/auth/login.php';
    }

    // Register - Step 1: Isi form dan kirim kode verifikasi
    public function register() {
        // kalo udah login redirect ke home
        if (isLoggedIn()) {
            redirect('home');
            return;
        }

        // Jika form disubmit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'age' => trim($_POST['age']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password']
            ];

            // Validasi menggunakan helper validate()
            $errors = validate($data, [
                'name' => ['required'],
                'age' => ['required', 'numeric'],
                'email' => ['required', 'email'],
                'phone' => ['required', 'phone'],
                'password' => ['required', 'min:6']
            ]);
            
            // Validasi tambahan: email sudah terdaftar
            if (empty($errors['email']) && $this->userModel->emailExists($data['email'])) {
                $errors['email'] = 'Email sudah terdaftar';
            }
            
            // Validasi konfirmasi password
            if ($data['password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'Konfirmasi password tidak cocok';
            }

            if (empty($errors)) {
                // Generate kode verifikasi 6 digit
                $code = random_int(100000, 999999);
                
                // Simpan data register dan kode ke session
                unset($data['confirm_password']);
                $_SESSION['register_data'] = $data;
                $_SESSION['register_code'] = $code;
                $_SESSION['register_code_expires'] = time() + 15 * 60; // 15 menit
                
                // Kirim email verifikasi
                $subject = 'Kode Verifikasi Registrasi - Hotel System';
                $message = "Halo " . $data['name'] . ",\n\n";
                $message .= "Kode verifikasi registrasi Anda: " . $code . "\n\n";
                $message .= "Kode ini berlaku selama 15 menit.\n";
                $message .= "Jika Anda tidak melakukan registrasi, abaikan email ini.";
                @mail($data['email'], $subject, $message);
                
                redirect('auth/verifyRegister');
                return;
            }

            // Jika ada error, simpan untuk ditampilkan
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
        }

        // Load view
        include 'views/auth/register.php';
    }
    
    // Register - Step 2: Verifikasi kode dari email
    public function verifyRegister() {
        if (isLoggedIn()) {
            redirect('home');
            return;
        }
        
        // Cek apakah ada data register di session
        if (!isset($_SESSION['register_data']) || !isset($_SESSION['register_code'])) {
            setFlash('error', 'Silakan isi form registrasi terlebih dahulu');
            redirect('auth/register');
            return;
        }
        
        $email = $_SESSION['register_data']['email'];
        
        // Jika form verifikasi disubmit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $errors = [];
            
            if (empty($code)) {
                $errors['code'] = 'Kode verifikasi wajib diisi';
            }
            
            if (empty($errors)) {
                $savedCode = $_SESSION['register_code'];
                $expires = $_SESSION['register_code_expires'];
                
                // Cek apakah kode sudah expired
                if (time() > $expires) {
                    $errors['code'] = 'Kode sudah kadaluarsa. Silakan daftar ulang.';
                    // Hapus session register
                    unset($_SESSION['register_data'], $_SESSION['register_code'], $_SESSION['register_code_expires']);
                } elseif ($code != $savedCode) {
                    $errors['code'] = 'Kode verifikasi salah';
                } else {
                    // Kode benar, simpan user ke database
                    $data = $_SESSION['register_data'];
                    $userId = $this->userModel->register($data);
                    
                    if ($userId) {
                        // Hapus session register
                        unset($_SESSION['register_data'], $_SESSION['register_code'], $_SESSION['register_code_expires']);
                        
                        setFlash('success', 'Registrasi berhasil! Silakan login.');
                        redirect('auth/login');
                        return;
                    } else {
                        $errors['code'] = 'Terjadi kesalahan saat registrasi';
                    }
                }
            }
            
            $_SESSION['errors'] = $errors;
        }
        
        // Load view
        include 'views/auth/verify_register.php';
    }
    
    // Kirim ulang kode verifikasi register
    public function resendRegisterCode() {
        if (!isset($_SESSION['register_data'])) {
            redirect('auth/register');
            return;
        }
        
        $data = $_SESSION['register_data'];
        
        // Generate kode baru
        $code = random_int(100000, 999999);
        $_SESSION['register_code'] = $code;
        $_SESSION['register_code_expires'] = time() + 15 * 60;
        
        // Kirim email
        $subject = 'Kode Verifikasi Registrasi - Hotel System';
        $message = "Halo " . $data['name'] . ",\n\n";
        $message .= "Kode verifikasi registrasi Anda: " . $code . "\n\n";
        $message .= "Kode ini berlaku selama 15 menit.";
        @mail($data['email'], $subject, $message);
        
        setFlash('success', 'Kode verifikasi baru telah dikirim ke email Anda');
        redirect('auth/verifyRegister');
    }

    // logout user
    public function logout() {
        session_destroy();
        redirect('auth/login');
    }

    // halaman profil
    public function profile() {
        requireLogin();
        $user = currentUser();
        $this->view('auth/profile', ['user' => $user]);
    }

    // update profile user
    public function updateProfile() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'age' => trim($_POST['age']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'password' => $_POST['password'],
                'current_password' => $_POST['current_password'] ?? ''
            ];

            // Validasi menggunakan helper validate()
            $errors = validate($data, [
                'name' => ['required'],
                'age' => ['required', 'numeric'],
                'email' => ['required', 'email'],
                'phone' => ['required', 'numeric']
            ]);
            
            // Validasi tambahan: email sudah digunakan
            if (empty($errors['email']) && $this->userModel->emailExists($data['email'], $_SESSION['user_id'])) {
                $errors['email'] = 'Email sudah digunakan';
            }

            // Jika user ingin mengganti password, wajib isi password lama & valid
            if (!empty($data['password'])) {
                if (empty($data['current_password'])) {
                    $errors['current_password'] = 'Password lama wajib diisi untuk mengganti password';
                } else {
                    $user = $this->userModel->find($_SESSION['user_id']);
                    if (!$user || !password_verify($data['current_password'], $user['password'])) {
                        $errors['current_password'] = 'Password lama tidak sesuai';
                    }
                }
            }

            if (empty($errors)) {
                // Update profile
                $updated = $this->userModel->updateProfile($_SESSION['user_id'], $data);

                if ($updated) {
                    // Update session
                    $_SESSION['user_name'] = $data['name'];
                    $_SESSION['user_email'] = $data['email'];
                    
                    setFlash('success', 'Profile berhasil diupdate');
                    redirect('auth/profile');
                    return;
                } else {
                    $errors['update'] = 'Terjadi kesalahan saat update profile';
                }
            }

            $_SESSION['errors'] = $errors;
        }

        redirect('auth/profile');
    }

    // Tampilkan form lupa password
    public function forgotPassword() {
        if (isLoggedIn()) {
            redirect('home');
        }

        include 'views/auth/forgot_password.php';
    }

    // Proses kirim kode reset password
    public function sendResetCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/forgotPassword');
        }

        $email = trim($_POST['email'] ?? '');
        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email wajib diisi';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid';
        } else {
            $user = $this->userModel->getByEmail($email);
            if (!$user) {
                $errors['email'] = 'Email tidak terdaftar';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            redirect('auth/forgotPassword');
        }

        // Generate kode verifikasi 6 digit
        $code = random_int(100000, 999999);

        // Simpan ke session (untuk keperluan tugas; di production sebaiknya ke database)
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_code_expires'] = time() + 15 * 60; // 15 menit

        // Kirim email sederhana (butuh konfigurasi mail di server agar benar-benar terkirim)
        $subject = 'Kode Reset Password - Hotel System';
        $message = "Kode verifikasi reset password Anda: " . $code;
        @mail($email, $subject, $message);

        setFlash('success', 'Kode verifikasi telah dikirim ke email Anda.');
        redirect('auth/resetPassword');
    }

    // Tampilkan & proses form reset password dengan kode
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirmation'] ?? '';
            $errors = [];

            if (empty($code)) {
                $errors['code'] = 'Kode verifikasi wajib diisi';
            }
            if (empty($password)) {
                $errors['password'] = 'Password baru wajib diisi';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Password minimal 6 karakter';
            }
            if ($password !== $confirm) {
                $errors['password_confirmation'] = 'Konfirmasi password tidak sama';
            }

            if (empty($errors)) {
                $savedCode = $_SESSION['reset_code'] ?? null;
                $expires = $_SESSION['reset_code_expires'] ?? 0;
                $email = $_SESSION['reset_email'] ?? null;

                if (!$savedCode || !$email || time() > $expires) {
                    $errors['code'] = 'Kode sudah kadaluarsa. Silakan minta ulang.';
                } elseif ($code != $savedCode) {
                    $errors['code'] = 'Kode verifikasi salah';
                } else {
                    // Update password user
                    $user = $this->userModel->getByEmail($email);
                    if ($user) {
                        $this->userModel->updateProfile($user['id'], [
                            'name' => $user['name'],
                            'age' => $user['age'],
                            'email' => $user['email'],
                            'phone' => $user['phone'],
                            'password' => $password
                        ]);

                        // Bersihkan session reset
                        unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_code_expires']);

                        setFlash('success', 'Password berhasil direset. Silakan login.');
                        redirect('auth/login');
                        return;
                    } else {
                        $errors['code'] = 'Terjadi kesalahan. User tidak ditemukan.';
                    }
                }
            }

            $_SESSION['errors'] = $errors;
        }

        include 'views/auth/reset_password.php';
    }
}
?>
