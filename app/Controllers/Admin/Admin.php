<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Admin extends BaseController
{
    public function __construct()
    {
        $this->m_admin    = new AdminModel();
        $this->validation = \config\Services::validation();
        helper('cookie');
        helper('global_fungsi_helper');
    }
    public function login()
    {
        $data = [];
        if (get_cookie('cookie_username') && get_cookie('cookie_password')) {
            $username = get_cookie('cookie_username');
            $password = get_cookie('cookie_password');

            $dataAkun = $this->m_admin->getData($username);
            if ($password != $dataAkun['password']) {
                $error = ["Akun yang anda masukan tidak sesuai"];
                session()->setFlashdata('username', $username);
                session()->setFlashdata('warning', $error);

                delete_cookie('cookie_username');
                delete_cookie('cookie_password');
                return redirect()->to('admin/login');
            }
            $akun = [
                'akun_username'     => $username,
                'akun_nama_lengkap' => $dataAkun['nama_lengkap'],
                'akun_email'        => $dataAkun['email'],
            ];
            session()->set($akun);
            return redirect()->to('admin/sukses');
        }
        if ($this->request->getMethod() == "post") {
            $rules = [
                'username' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Username wajib diisi!',
                    ],
                ],
                'password' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Password Wajib diisi!',
                    ],
                ],
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata("warning", $this->validation->getErrors());
                return redirect()->to('admin/login');
            }
            $username    = $this->request->getVar('username');
            $password    = $this->request->getVar('password');
            $remember_me = $this->request->getVar('remember_me');

            $dataAkun = $this->m_admin->getData($username);
            if (!$dataAkun || !password_verify($password, $dataAkun['password'])) {
                $error = ["Password Salah"];
                session()->setFlashdata('username', $username);
                session()->setFlashdata('warning', $error);
                return redirect()->to(base_url('admin/login'));
            }
            if ('1' == $remember_me) {
                set_cookie("cookie_username", $username, 3600 * 24 * 30);
                set_cookie("cookie_password", $dataAkun['password'], 3600 * 24 * 30);

            }
            $akun = [
                'akun_username'     => $dataAkun['username'],
                'akun_nama_lengkap' => $dataAkun['nama_lengkap'],
                'akun_email'        => $dataAkun['email'],
            ];

            session()->set($akun);
            return redirect()->to("admin/sukses")->withCookies();
        }
        echo view('admin/v_login', $data);
    }
    public function sukses()
    {
        return redirect()->to("/admin/dashboard");
        // print_r(session()->get());
        // echo "isian data cookie username" . get_cookie("cookie_username") . "dan password" . get_cookie("cookie_password");
    }

    public function logout()
    {
        delete_cookie('cookie_username');
        delete_cookie('cookie_password');
        session()->destroy();
        if (session()->get('akun_username') != '') {
            session()->setFlashdata("success", "Anda berhasil logout");
        }
        echo view("admin/v_login");
    }
    public function lupapassword()
    {
        $error = [];
        if ($this->request->getMethod() == 'post') {
            $username = $this->request->getVar('username');
            if ('' == $username) {
                $error[] = 'Silahkan masukkan username Anda!';
            }
            if (empty($error)) {
                $data = $this->m_admin->getData($username);
                if (empty($data)) {
                    $error[] = 'Akun yang anda masukkan tidak terdaftar!';
                }
            }
            if (empty($error)) {
                $email = $data['email'];
                $token = md5(date('ymdhis'));

                $link       = site_url("admin/resetpassword/?email=$email&token=$token");
                $attachment = "";
                $to         = $email;
                $title      = "Reset Password";
                $message    = "Berikut ini adalah link untuk melakukan reset password anda";
                $message .= "Silahkan klik link berikut ini $link";

                kirim_email($attachment, $to, $title, $message);
                $dataUpdate = [
                    'email' => $email,
                    'token' => $token,
                ];
                $this->m_admin->updateData($dataUpdate);
                session()->setFlashdata("success", "Email telah dikirim");
            }
            if ($error) {
                session()->setFlashdata("username", $username);
                session()->setFlashdata("warning", $error);
            }
            return redirect()->to("admin/lupapassword");
        }
        echo view('admin/v_lupapassword');
    }
    public function resetpassword()
    {
        $error = [];
        $email = $this->request->getVar('email');
        $token = $this->request->getVar('token');

        if ('' != $email && '' != $token) {
            $dataAkun = $this->m_admin->getData($email);
            if ($dataAkun && $dataAkun['token'] != $token) {
                $error[] = "Token tidak valid";
            }
        } else {
            $error[] = "Parameter yang dikirimkan tidak valid";
        }

        if ($error) {
            session()->setFlashdata("warning", $error);
        }

        if ($this->request->getMethod() == 'post') {
            $aturan = [
                'password'            => [
                    'rules'  => 'required|min_length[5]',
                    'errors' => [
                        'required'   => 'Password wajib diisi',
                        'min_length' => 'Panjang karakter minimum 5',
                    ],
                ],
                'konfirmasi_password' => [
                    'rules'  => 'required|min_length[5]|matches[password]',
                    'errors' => [
                        'required'   => 'Konfirmasi password wajib diisi',
                        'min_length' => 'Panjang karakter minimum 5',
                        'matches'    => 'Password tidak sesuai',
                    ],
                ],
            ];

            if (!$this->validate($aturan)) {
                session()->setFlashdata('warning', $this->validator->getErrors());
            } else {
                $dataUpdate = [
                    'email'    => $email,
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                    'token'    => null,
                ];
                delete_cookie('cookie_username');
                delete_cookie('cookie_password');
                $this->m_admin->updateData($dataUpdate); // Memperbaiki pemanggilan metode updateData
                session()->setFlashdata('success', 'Password berhasil diubah');
                return redirect()->to('admin/login')->withCookies();
            }
        }
        echo view("admin/v_resetpassword");
    }

}
