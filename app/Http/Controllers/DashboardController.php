<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Imports\KinerjaImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());

        $pekerjaans = Pekerjaan::with('user')
            ->whereDate('tanggal', $tanggalPilihan)
            ->get();

        $labels = $pekerjaans->pluck('user.name');
        $totals = $pekerjaans->pluck('total_pekerjaan');

        $logRiwayat = Pekerjaan::select('tanggal', 'file_name')
            ->groupBy('tanggal', 'file_name')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('dashboard', compact('pekerjaans', 'labels', 'totals', 'logRiwayat', 'tanggalPilihan'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
            'tanggal'    => 'required|date'
        ]);

        $file = $request->file('file_excel');
        $fileName = time() . '_' . $file->getClientOriginalName();

        Excel::import(new KinerjaImport($fileName, $request->tanggal), $file);

        return redirect()->route('dashboard', ['tanggal' => $request->tanggal])
            ->with('success', 'Data laporan berhasil diproses!');
    }
}