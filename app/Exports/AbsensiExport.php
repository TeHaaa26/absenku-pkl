<?php

namespace App\Exports;

use App\Models\User;
use App\Services\AbsensiService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AbsensiExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $bulan;
    protected $tahun;
    protected $absensiService;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->absensiService = new AbsensiService();
    }

    public function collection()
    {
        $guru = User::guru()->aktif()->orderBy('nama')->get();

        $data = [];
        $no = 1;

        foreach ($guru as $g) {
            $rekap = $this->absensiService->getRekapBulanan($g, $this->bulan, $this->tahun);
            
            $data[] = [
                'no' => $no++,
                'nip' => $g->nip,
                'nama' => $g->nama,
                'hadir' => $rekap['hadir'],
                'terlambat' => $rekap['terlambat'],
                'izin_sakit' => $rekap['izin_sakit'],
                'izin_dinas' => $rekap['izin_dinas'],
                'alpha' => $rekap['alpha'],
                'total_terlambat' => $rekap['total_terlambat_menit'] . ' menit',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        $namaBulan = Carbon::create()->month($this->bulan)->translatedFormat('F');
        
        return [
            'No',
            'NIP',
            'Nama Guru',
            'Hadir',
            'Terlambat',
            'Izin Sakit',
            'Izin Dinas',
            'Alpha',
            'Total Keterlambatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 30,
            'D' => 10,
            'E' => 12,
            'F' => 12,
            'G' => 12,
            'H' => 10,
            'I' => 20,
        ];
    }
}