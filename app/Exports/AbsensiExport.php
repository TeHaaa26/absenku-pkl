<?php

namespace App\Exports;

use App\Models\User;
use App\Models\PenempatanPkl; // Tambahkan ini
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
    protected $guruId; // Tambahkan property guruId
    protected $absensiService;

    // Tambahkan $guruId = null pada parameter ketiga
    public function __construct($bulan, $tahun, $guruId = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->guruId = $guruId;
        $this->absensiService = new AbsensiService();
    }

    public function collection()
    {
        // 1. Tentukan query dasar
        $query = User::siswa()->aktif();

        // 2. Jika ada guruId (berarti diakses oleh Guru), filter berdasarkan bimbingan
        if ($this->guruId) {
            $siswaIds = PenempatanPkl::where('guru_id', $this->guruId)->pluck('siswa_id');
            $query->whereIn('id', $siswaIds);
        }

        $siswa = $query->orderBy('nama')->get();

        $data = [];
        $no = 1;

        foreach ($siswa as $s) {
            $rekap = $this->absensiService->getRekapBulanan($s, $this->bulan, $this->tahun);
            
            $data[] = [
                'no' => $no++,
                'nisn' => $s->nisn,
                'nama' => $s->nama,
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

    // ... method headings, styles, dan columnWidths tetap sama ...
    public function headings(): array
    {
        return [
            'No', 'NISN', 'Nama Siswa', 'Hadir', 'Terlambat', 'Izin Sakit', 'Izin Dinas', 'Alpha', 'Total Keterlambatan',
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
            'A' => 5, 'B' => 20, 'C' => 30, 'D' => 10, 'E' => 12, 'F' => 12, 'G' => 12, 'H' => 10, 'I' => 20,
        ];
    }
}