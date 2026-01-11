<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\AbsensiService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $absensiService;

    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    public function index()
    {
        $user = Auth::user();
        $statusHariIni = $this->absensiService->getStatusHariIni($user);
        $rekapBulanIni = $this->absensiService->getRekapBulanan(
            $user, 
            Carbon::now()->month, 
            Carbon::now()->year
        );

        // Greeting berdasarkan waktu
        $jam = Carbon::now()->hour;
        if ($jam >= 5 && $jam < 12) {
            $greeting = 'Selamat Pagi';
            $emoji = '🌅';
        } elseif ($jam >= 12 && $jam < 15) {
            $greeting = 'Selamat Siang';
            $emoji = '☀️';
        } elseif ($jam >= 15 && $jam < 18) {
            $greeting = 'Selamat Sore';
            $emoji = '🌇';
        } else {
            $greeting = 'Selamat Malam';
            $emoji = '🌙';
        }

        return view('guru.dashboard', compact(
            'user',
            'statusHariIni',
            'rekapBulanIni',
            'greeting',
            'emoji'
        ));
    }
}