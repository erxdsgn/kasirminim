<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // 1. Validasi Input Form
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // 2. Logika simpan ke database / kirim email bisa ditaruh di sini

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Pesan Anda berhasil terkirim!');
    }
}
