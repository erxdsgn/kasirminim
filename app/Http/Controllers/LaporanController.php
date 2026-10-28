<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function generatePDF()
    {
        $data = [
            'title' => 'Laporan Pegawai',
            'tanggal' => date('d-m-Y'),
            'pegawai' => [
                ['nama' => 'Andi', 'jabatan' => 'Manager'],
                ['nama' => 'Budi', 'jabatan' => 'Staff'],
            ]
        ];

        $pdf = Pdf::loadView('laporan.pegawai', $data);
        return $pdf->stream('laporan-pegawai.pdf');
        // Atau gunakan ->stream() jika ingin menampilkannya langsung di browser:
        // return $pdf->stream('laporan-pegawai.pdf');
    }
}
