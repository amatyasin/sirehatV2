<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rujukan Kesehatan Siswa</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2a9d8f;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #2a9d8f;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        .meta {
            margin-bottom: 20px;
            font-size: 10px;
            color: #555;
            width: 100%;
        }
        .meta td {
            padding: 2px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #ddd;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #2a9d8f;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        .status-belum { background-color: #e63946; }
        .status-sudah { background-color: #f4a261; }
        .status-tindak { background-color: #457b9d; }
        .status-selesai { background-color: #2a9d8f; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 8px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rujukan Kesehatan Siswa</h2>
        <p>Cek Kesehatan Gratis Online (CekGO)</p>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 15%;">Tanggal Cetak</td>
            <td style="width: 35%;">: {{ now()->translatedFormat('d F Y H:i') }}</td>
            <td style="width: 15%;">Sekolah / Kelas</td>
            <td style="width: 35%;">: {{ $school_name }} / {{ $class_name }}</td>
        </tr>
        <tr>
            <td>Pemeriksaan</td>
            <td>: {{ $pemeriksaan_type }}</td>
            <td>Status Rujukan</td>
            <td>: {{ $status_name }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 16%;">Nama Siswa</th>
                <th style="width: 12%;">NIK / NISN</th>
                <th style="width: 16%;">Sekolah & Kelas</th>
                <th style="width: 10%;">Jenis Pemeriksaan</th>
                <th style="width: 18%;">Alasan Rujukan</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 12%;">Tgl Pemeriksaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($referrals as $index => $referral)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $referral->studentClassHistory?->student?->nama_lengkap }}</strong></td>
                    <td>
                        {{ $referral->studentClassHistory?->student?->nik ?: '-' }} <br>
                        <span style="color: #888;">{{ $referral->studentClassHistory?->student?->nisn ?: '-' }}</span>
                    </td>
                    <td>
                        {{ $referral->studentClassHistory?->school?->nama_sekolah }} <br>
                        <span style="color: #666; font-size: 9px;">Kelas: {{ $referral->studentClassHistory?->schoolClass?->nama_kelas }}</span>
                    </td>
                    <td>{{ $referral->jenis_pemeriksaan }}</td>
                    <td>{{ $referral->alasan_rujukan }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge 
                            @if($referral->status_rujukan == 'Belum Dirujuk') status-belum
                            @elseif($referral->status_rujukan == 'Sudah Dirujuk') status-sudah
                            @elseif($referral->status_rujukan == 'Dalam Tindak Lanjut') status-tindak
                            @elseif($referral->status_rujukan == 'Selesai') status-selesai
                            @endif">
                            {{ $referral->status_rujukan }}
                        </span>
                    </td>
                    <td>{{ $referral->tanggal_pemeriksaan?->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #888; padding: 20px;">Tidak ada data rujukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh CekGO.
    </div>
</body>
</html>
