<?php
/**
 * SIREHAT Laravel Application Seeder Script
 * Author: Antigravity AI
 * Year: 2026
 *
 * This script boots Laravel and inserts the generated realistic dummy data
 * directly into the actual tables used by the application, so they are visible
 * in the Filament dashboard.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Set execution limits
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');

echo "Booted Laravel successfully.\n";

// ---------------------------------------------------------
// SEED DATA & LOCATIONS
// ---------------------------------------------------------

$kecamatanKelurahan = [
    'Kecamatan Wowo' => ['Kelurahan Mimi', 'Kelurahan Mumu', 'Kelurahan Momo', 'Kelurahan Pepe', 'Kelurahan Pupu', 'Kelurahan Popo', 'Kelurahan Lili'],
    'Kecamatan Hehe' => ['Kelurahan Haha', 'Kelurahan Hihi', 'Kelurahan Huhu', 'Kelurahan Cici', 'Kelurahan Cucu', 'Kelurahan Coco', 'Kelurahan Didi'],
    'Kecamatan Lala' => ['Kelurahan Lulu', 'Kelurahan Lolo', 'Kelurahan Nana', 'Kelurahan Nene', 'Kelurahan Nono'],
    'Kecamatan Koko' => ['Kelurahan Kiki', 'Kelurahan Kuku', 'Kelurahan Riri', 'Kelurahan Ruru', 'Kelurahan Roro'],
    'Kecamatan Titi' => ['Kelurahan Tutu', 'Kelurahan Toto', 'Kelurahan Yoyo', 'Kelurahan Yuyu', 'Kelurahan Sisi'],
    'Kecamatan Gogo' => ['Kelurahan Gigi', 'Kelurahan Gugu', 'Kelurahan Wawa', 'Kelurahan Wiwi', 'Kelurahan Wowo'],
    'Kecamatan Jojo' => ['Kelurahan Jiji', 'Kelurahan Juju', 'Kelurahan Kaka', 'Kelurahan Keke', 'Kelurahan Koko'],
    'Kecamatan Bibi' => ['Kelurahan Bubu', 'Kelurahan Bobo', 'Kelurahan Fafa', 'Kelurahan Fifi', 'Kelurahan Fofo'],
    'Kecamatan Zaza' => ['Kelurahan Zizi', 'Kelurahan Zuzu', 'Kelurahan Zozo', 'Kelurahan Lele', 'Kelurahan Lala'],
    'Kecamatan Shisha' => ['Kelurahan Shishi', 'Kelurahan Shushu', 'Kelurahan Tatas', 'Kelurahan Titis']
];

$firstNamesMale = [
    'Ahmad', 'Muhammad', 'Budi', 'Joko', 'Andi', 'Agus', 'Hendra', 'Eko', 'Rudi', 'Bambang',
    'Wawan', 'Dedi', 'Heri', 'Rian', 'Aditya', 'Fajar', 'Taufik', 'Aris', 'Dwi', 'Tri',
    'Wahyu', 'Slamet', 'Hadi', 'Mulyadi', 'Iwan', 'Dani', 'Arif', 'Roni', 'Yanto', 'Rizal',
    'Yudi', 'Faisal', 'Eka', 'Guntur', 'Surya', 'Bintang', 'Dian', 'Fikri', 'Galih', 'Indra',
    'Reza', 'Dimas', 'Adit', 'Gilang', 'Raka', 'Aldi', 'Angga', 'Bayu', 'Fahmi', 'Ilham'
];

$firstNamesFemale = [
    'Siti', 'Maria', 'Dewi', 'Indah', 'Rina', 'Sri', 'Putri', 'Sari', 'Anisa', 'Fitri',
    'Wati', 'Endang', 'Yanti', 'Kartika', 'Dian', 'Mega', 'Laras', 'Nia', 'Ayu', 'Intan',
    'Ratna', 'Lestari', 'Utami', 'Ria', 'Maya', 'Nanda', 'Gita', 'Ratih', 'Siska', 'Silvia',
    'Febri', 'Eka', 'Windy', 'Restu', 'Bella', 'Chandra', 'Desi', 'Amalia', 'Safitri', 'Nur'
];

$lastNames = [
    'Prasetyo', 'Wibowo', 'Saputra', 'Kurniawan', 'Hidayat', 'Santoso', 'Susanto', 'Setiawan', 'Pratama', 'Nugroho',
    'Gunawan', 'Wijaya', 'Siregar', 'Lubis', 'Nasution', 'Simanjuntak', 'Sitorus', 'Harahap', 'Ginting', 'Tarigan',
    'Sinaga', 'Panjaitan', 'Pohan', 'Panggabean', 'Tanjung', 'Pasaribu', 'Manurung', 'Mulyono', 'Subagyo', 'Supriadi'
];

$streets = [
    'Jl. Mulawarman', 'Jl. Yos Sudarso', 'Jl. Pahlawan', 'Jl. Bhayangkara', 'Jl. Pemuda',
    'Jl. Ahmad Yani', 'Jl. Gatot Subroto', 'Jl. S. Parman', 'Jl. Juanda', 'Jl. Antasari',
    'Jl. Kadrie Oening', 'Jl. M. Yamin', 'Jl. Wahid Hasyim', 'Jl. Letjen Suprapto', 'Jl. DI Panjaitan'
];

function localGetRandom($arr) {
    return $arr[array_rand($arr)];
}

function localName($gender) {
    global $firstNamesMale, $firstNamesFemale, $lastNames;
    $first = ($gender === 'L') ? localGetRandom($firstNamesMale) : localGetRandom($firstNamesFemale);
    $last = localGetRandom($lastNames);
    return "$first $last";
}

$nikCounter = 1;
function localNIK($gender, $dobString, $kecIndex) {
    global $nikCounter;
    $prov = '64';
    $kab = '72';
    $kec = str_pad($kecIndex + 1, 2, '0', STR_PAD_LEFT);
    $parts = explode('-', $dobString);
    $year = substr($parts[0], 2, 2);
    $month = $parts[1];
    $day = intval($parts[2]);
    if ($gender === 'P') {
        $day += 40;
    }
    $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
    // Use auto-incrementing counter to guarantee uniqueness
    $serial = str_pad($nikCounter++, 4, '0', STR_PAD_LEFT);
    return $prov . $kab . $kec . $dayStr . $month . $year . $serial;
}

function localPhone() {
    return '0812' . rand(10000000, 99999999);
}

// ---------------------------------------------------------
// DATABASE TRUNCATION
// ---------------------------------------------------------
echo "Clearing existing data (except users and roles)...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

DB::table('pemeriksaan_balitas')->truncate();
DB::table('pemeriksaan_matas')->truncate();
DB::table('pemeriksaan_gigis')->truncate();
DB::table('pemeriksaan_gizis')->truncate();
DB::table('pemeriksaan_umums')->truncate();
DB::table('student_class_histories')->truncate();
DB::table('school_classes')->truncate();
DB::table('students')->truncate();
DB::table('children')->truncate();
DB::table('schools')->truncate();
DB::table('posyandus')->truncate();
DB::table('instansis')->truncate();
DB::table('kelurahans')->truncate();
DB::table('kecamatans')->truncate();

// ---------------------------------------------------------
// Regional Seeding
// ---------------------------------------------------------
echo "Seeding Kecamatan & Kelurahan...\n";

$kecamatanIds = [];
$kelurahanIds = []; // [kecamatan_name => [kelurahan_name => id]]

$kecIndex = 0;
foreach ($kecamatanKelurahan as $kecName => $kelList) {
    $kecId = DB::table('kecamatans')->insertGetId([
        'nama_kecamatan' => $kecName,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    $kecamatanIds[$kecIndex] = $kecId;
    
    foreach ($kelList as $kelName) {
        $kelId = DB::table('kelurahans')->insertGetId([
            'kecamatan_id' => $kecId,
            'nama_kelurahan' => $kelName,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $kelurahanIds[$kecName][$kelName] = $kelId;
    }
    $kecIndex++;
}

// ---------------------------------------------------------
// Instansi (Puskesmas) Seeding (10 rows)
// ---------------------------------------------------------
echo "Seeding Instansi (Puskesmas)...\n";
$instansiIds = [];
for ($i = 0; $i < 10; $i++) {
    $kecName = array_keys($kecamatanKelurahan)[$i];
    $instansiId = DB::table('instansis')->insertGetId([
        'nama_instansi' => "Puskesmas " . chr(65 + $i),
        'alamat' => "Jl. Raya " . $kecName . " No. " . rand(1, 100) . ", Kota Dummy",
        'telepon' => "0541" . rand(200000, 999999),
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    $instansiIds[$i] = [
        'id' => $instansiId,
        'kecamatan_name' => $kecName
    ];
}

// ---------------------------------------------------------
// Posyandu Seeding (50 rows)
// ---------------------------------------------------------
echo "Seeding Posyandus...\n";
$posyanduIds = [];
$posyanduIndex = 1;
foreach ($instansiIds as $index => $inst) {
    $kecName = $inst['kecamatan_name'];
    $kelList = $kecamatanKelurahan[$kecName];
    
    for ($k = 0; $k < 5; $k++) {
        $kelName = localGetRandom($kelList);
        $kelId = $kelurahanIds[$kecName][$kelName];
        $alamat = "Gang Mawar RT " . str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT) . ", " . $kelName . ", " . $kecName;
        
        $posyanduId = DB::table('posyandus')->insertGetId([
            'instansi_id' => $inst['id'],
            'kelurahan_id' => $kelId,
            'nama_posyandu' => "Posyandu " . localGetRandom($posyanduNames ?? ['Melati', 'Mawar', 'Anggrek', 'Kenanga', 'Flamboyan']) . " " . $posyanduIndex,
            'alamat' => $alamat,
            'penanggung_jawab' => localName('P'),
            'no_wa' => localPhone(),
            'rt' => str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT),
            'rw' => str_pad(rand(1, 10), 2, '0', STR_PAD_LEFT),
            'kode_pos' => '751' . rand(10, 99),
            'aktif' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $posyanduIds[] = [
            'id' => $posyanduId,
            'instansi_id' => $inst['id'],
            'kecamatan_name' => $kecName,
            'kelurahan_id' => $kelId,
            'kelurahan_name' => $kelName,
            'alamat' => $alamat
        ];
        $posyanduIndex++;
    }
}

// ---------------------------------------------------------
// School Seeding (100 rows)
// ---------------------------------------------------------
echo "Seeding Schools...\n";
$schoolIds = [];
$schoolsSD = ['SD Harvard', 'SD Oxford', 'SD Cambridge', 'SD Stanford', 'SD MIT', 'SD Berkeley', 'SD Princeton', 'SD Yale', 'SD Columbia', 'SD Cornell'];
$schoolsSMP = ['SMP Gaul', 'SMP Keren', 'SMP Hits', 'SMP Kece', 'SMP Pintar', 'SMP Cerdas', 'SMP Bijak', 'SMP Juara', 'SMP Utama'];
$schoolsSMA = ['SMA Koboh', 'SMA Maju', 'SMA Jaya', 'SMA Hebat', 'SMA Unggul', 'SMA Prestasi', 'SMA Harapan'];
$schoolsSMK = ['SMK Kreatif', 'SMK Inovatif', 'SMK Mandiri', 'SMK Bisa', 'SMK Mahir', 'SMK Trampil'];

$schoolIndex = 1;
foreach ($instansiIds as $index => $inst) {
    $kecName = $inst['kecamatan_name'];
    $kelList = $kecamatanKelurahan[$kecName];
    
    for ($k = 0; $k < 10; $k++) {
        if ($k < 5) {
            $jenjang = 'SD';
            $namePattern = localGetRandom($schoolsSD);
        } elseif ($k < 7) {
            $jenjang = 'SMP';
            $namePattern = localGetRandom($schoolsSMP);
        } elseif ($k < 9) {
            $jenjang = 'SMA';
            $namePattern = localGetRandom($schoolsSMA);
        } else {
            $jenjang = 'SMK';
            $namePattern = localGetRandom($schoolsSMK);
        }
        
        $kelName = localGetRandom($kelList);
        $kelId = $kelurahanIds[$kecName][$kelName];
        $kecId = $kecamatanIds[$index];
        
        $schoolId = DB::table('schools')->insertGetId([
            'instansi_id' => $inst['id'],
            'kecamatan_id' => $kecId,
            'kelurahan_id' => $kelId,
            'nama_sekolah' => "$namePattern $kecName $schoolIndex",
            'npsn' => rand(30400000, 30499999),
            'alamat' => localGetRandom($streets) . " No. " . rand(1, 150) . ", " . $kelName . ", " . $kecName,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $schoolIds[] = [
            'id' => $schoolId,
            'instansi_id' => $inst['id'],
            'kecamatan_name' => $kecName,
            'jenjang' => $jenjang
        ];
        $schoolIndex++;
    }
}

// ---------------------------------------------------------
// School Classes Seeding (Classes for each School)
// ---------------------------------------------------------
echo "Seeding School Classes...\n";
$schoolClassesMap = []; // [school_id => [class_name => class_id]]
foreach ($schoolIds as $sch) {
    $sid = $sch['id'];
    $jenjang = $sch['jenjang'];
    
    if ($jenjang === 'SD') {
        $classes = ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'];
    } elseif ($jenjang === 'SMP') {
        $classes = ['Kelas 7', 'Kelas 8', 'Kelas 9'];
    } else {
        $classes = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
    }
    
    foreach ($classes as $idx => $className) {
        $classId = DB::table('school_classes')->insertGetId([
            'school_id' => $sid,
            'nama_kelas' => $className,
            'urutan' => $idx + 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $schoolClassesMap[$sid][$className] = $classId;
    }
}

// ---------------------------------------------------------
// Students Seeding (5,000 students)
// ---------------------------------------------------------
echo "Seeding Students (5,000 rows)...\n";
$students = [];
$classHistories = [];

$academicYearId = 1; // 2028-2029
$semester = 'Ganjil';

for ($s = 1; $s <= 5000; $s++) {
    $sch = localGetRandom($schoolIds);
    $gender = (rand(1, 100) > 50) ? 'L' : 'P';
    
    if ($sch['jenjang'] === 'SD') {
        $umur = rand(6, 12);
        $kelasNo = min(6, $umur - 5);
        $kelasName = "Kelas " . $kelasNo;
    } elseif ($sch['jenjang'] === 'SMP') {
        $umur = rand(12, 15);
        $kelasNo = min(9, $umur - 5); // 7-9
        $kelasName = "Kelas " . $kelasNo;
    } else {
        $umur = rand(15, 18);
        $kelasNo = min(12, $umur - 5); // 10-12
        $kelasName = "Kelas " . $kelasNo;
    }
    
    $birthYear = 2026 - $umur;
    $birthMonth = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $birthDay = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $dob = "$birthYear-$birthMonth-$birthDay";
    
    $nik = localNIK($gender, $dob, rand(0, 9));
    $nisn = rand(3040000000, 3049999999);
    
    $kelName = localGetRandom($kecamatanKelurahan[$sch['kecamatan_name']]);
    $alamat = localGetRandom($streets) . " RT " . str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT) . ", " . $kelName . ", " . $sch['kecamatan_name'];
    
    $studentId = DB::table('students')->insertGetId([
        'instansi_id' => $sch['instansi_id'],
        'school_id' => $sch['id'],
        'nama_lengkap' => localName($gender),
        'nik' => $nik,
        'nisn' => $nisn,
        'jenis_kelamin' => $gender,
        'tempat_lahir' => 'Kota Dummy',
        'tanggal_lahir' => $dob,
        'alamat' => $alamat,
        'aktif' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    $classId = isset($schoolClassesMap[$sch['id']][$kelasName]) 
        ? $schoolClassesMap[$sch['id']][$kelasName] 
        : array_values($schoolClassesMap[$sch['id']])[0];
        
    $historyId = DB::table('student_class_histories')->insertGetId([
        'student_id' => $studentId,
        'school_id' => $sch['id'],
        'school_class_id' => $classId,
        'academic_year_id' => $academicYearId,
        'semester' => $semester,
        'aktif' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    $students[] = [
        'id' => $studentId,
        'gender' => $gender,
        'umur' => $umur,
        'alamat' => $alamat,
        'history_id' => $historyId
    ];
}

// ---------------------------------------------------------
// Balita Seeding (3,000 balita)
// ---------------------------------------------------------
echo "Seeding Balita Children (3,000 rows)...\n";
$children = [];
for ($c = 1; $c <= 3000; $c++) {
    $pos = localGetRandom($posyanduIds);
    $gender = (rand(1, 100) > 50) ? 'L' : 'P';
    $umurBulan = rand(0, 60);
    
    $currentDate = Carbon::create(2026, 6, 23);
    $dob = $currentDate->subMonths($umurBulan)->format('Y-m-d');
    $nik = localNIK($gender, $dob, rand(0, 9));
    
    $childId = DB::table('children')->insertGetId([
        'orang_tua_id' => null, // Nullable
        'instansi_id' => $pos['instansi_id'],
        'posyandu_id' => $pos['id'],
        'nama_lengkap' => localName($gender),
        'nik' => $nik,
        'jenis_kelamin' => $gender,
        'tanggal_lahir' => $dob,
        'alamat' => $pos['alamat'],
        'aktif' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    $children[] = [
        'id' => $childId,
        'gender' => $gender,
        'dob' => $dob,
        'umur_bulan' => $umurBulan
    ];
}

// ---------------------------------------------------------
// Seeding Student Examinations
// ---------------------------------------------------------
echo "Seeding Student Checks (Pemeriksaan Umum, Gizi, Gigi, Mata)...\n";

$checksCount = 0;
$stuntingCount = 0;
$anemiaChecked = 0;
$anemiaCount = 0;
$kariesCount = 0;
$mataCount = 0;

$pUmumData = [];
$pGiziData = [];
$pGigiData = [];
$pMataData = [];

$batchSize = 250;

foreach ($students as $index => $sis) {
    $historyId = $sis['history_id'];
    $gender = $sis['gender'];
    $umur = $sis['umur'];
    
    // Only 1 pemeriksaan per student_class_history_id (unique constraint)
    $year = rand(2025, 2026);
    $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $date = "$year-$month-$day";
    
    {
        $checksCount++;
        
        // 1. Umum
        $sistolik = rand(90 + ($umur * 2), 110 + ($umur * 2));
        $diastolik = rand(60 + $umur, 75 + $umur);
        $nadi = rand(70, 95);
        $napas = rand(16, 22);
        $suhu = round(36.0 + (rand(0, 15) / 10), 1);
        
        $pUmumData[] = [
            'student_class_history_id' => $historyId,
            'jenis_kelamin' => $gender,
            'sudah_menstruasi' => ($gender === 'P' && $umur >= 12) ? 'Y' : 'N',
            'mengalami_keputihan' => 'N',
            'alamat' => $sis['alamat'],
            'tanggal_pemeriksaan' => $date,
            'tekanan_darah' => "$sistolik/$diastolik",
            'denyut_nadi' => (string)$nadi,
            'frekuensi_pernapasan' => (string)$napas,
            'suhu' => (string)$suhu,
            'bising_jantung' => 'Normal',
            'bising_paru' => 'Normal',
            'keadaan_rambut' => 'Sehat',
            'bercak_keputihan' => 'N',
            'bercak_putih_mati_rasa' => 'N',
            'kulit_bersisik' => 'N',
            'kulit_ada_memar' => 'N',
            'kulit_ada_luka_sayatan' => 'N',
            'kulit_ada_luka_koreng' => 'N',
            'luka_koreng_sukar_sembuh' => 'N',
            'bekas_suntikan' => 'N',
            'risiko_merokok' => 'Tidak',
            'merokok_setahun' => 'N',
            'jenis_rokok' => null,
            'jumlah_rokok' => null,
            'lama_merokok' => null,
            'telinga_luar' => 'Normal',
            'sarapan' => 'Ya',
            'kondisi_kuku' => 'Bersih',
            'dirujuk_ke_fasyankes' => 'N',
            'keterangan_rujukan' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        // 2. Gizi
        $avgHeights = [
            6 => 110, 7 => 116, 8 => 121, 9 => 127, 10 => 132, 11 => 138,
            12 => 144, 13 => 150, 14 => 155, 15 => 159, 16 => 162, 17 => 164, 18 => 166
        ];
        $avgWeights = [
            6 => 19, 7 => 21, 8 => 24, 9 => 27, 10 => 31, 11 => 35,
            12 => 40, 13 => 45, 14 => 50, 15 => 54, 16 => 57, 17 => 59, 18 => 62
        ];
        
        $hAvg = isset($avgHeights[$umur]) ? $avgHeights[$umur] : 140;
        $wAvg = isset($avgWeights[$umur]) ? $avgWeights[$umur] : 35;
        
        $isStunted = (rand(1, 100) <= 9);
        if ($isStunted) {
            $stuntingCount++;
            $statusStunting = (rand(1, 10) <= 3) ? 'Sangat Pendek' : 'Pendek';
            $height = round($hAvg * (rand(85, 92) / 100.0), 1);
            $weight = round($wAvg * (rand(75, 85) / 100.0), 1);
        } else {
            $statusStunting = 'Normal';
            $height = round($hAvg * (rand(96, 105) / 100.0), 1);
            $weight = round($wAvg * (rand(92, 115) / 100.0), 1);
        }
        
        $imt = round($weight / (($height / 100.0) ** 2), 2);
        
        if ($imt < 17.0) {
            $statusGizi = 'Sangat Kurus';
        } elseif ($imt < 18.5) {
            $statusGizi = 'Kurus';
        } elseif ($imt < 25.0) {
            $statusGizi = 'Normal';
        } elseif ($imt < 27.0) {
            $statusGizi = 'Gemuk';
        } else {
            $statusGizi = 'Obesitas';
        }
        
        $statusAnemia = 'Normal';
        $hb = null;
        $tandaAnemia = 'N';
        if ($gender === 'P') {
            $anemiaChecked++;
            if (rand(1, 100) <= 12) {
                $anemiaCount++;
                $statusAnemia = 'Anemia';
                $hb = round(rand(80, 115) / 10.0, 1);
                $tandaAnemia = 'Y';
            } else {
                $hb = round(rand(120, 145) / 10.0, 1);
            }
        }
        
        $pGiziData[] = [
            'student_class_history_id' => $historyId,
            'tanggal_pemeriksaan' => $date,
            'berat_badan' => $weight,
            'tinggi_badan' => $height,
            'lingkar_lengan' => round(14.0 + ($umur * 0.7) + (rand(-10, 10) / 10), 1),
            'lingkar_perut' => round(50.0 + ($umur * 1.5) + (rand(-30, 30) / 10), 1),
            'imt' => $imt,
            'status_gizi' => $statusGizi,
            'gula_darah_sewaktu' => round(rand(80, 120), 1),
            'status_gula' => 'Normal',
            'tanda_klinis_anemia' => $tandaAnemia,
            'hemoglobin' => $hb,
            'status_anemia' => $statusAnemia,
            'dirujuk_ke_fasyankes' => 'N',
            'keterangan_rujukan' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        // 3. Gigi
        $hasKaries = (rand(1, 100) <= 20);
        if ($hasKaries) {
            $kariesCount++;
            $lubang = rand(1, 4);
            $karang = 'Y';
            $rujukanGigi = ($lubang >= 3) ? 'Y' : 'N';
        } else {
            $lubang = 0;
            $karang = 'N';
            $rujukanGigi = 'N';
        }
        
        $pGigiData[] = [
            'student_class_history_id' => $historyId,
            'tanggal_pemeriksaan' => $date,
            'celah_bibir_langit' => 'N',
            'sariawan' => 'N',
            'luka_sudut_mulut' => 'N',
            'lidah_kotor' => 'N',
            'luka_lain_di_mulut' => 'N',
            'gigi_berlubang' => $lubang > 0 ? 'Y' : 'N',
            'jumlah_gigi_berlubang' => $lubang,
            'gusi_berdarah' => 'N',
            'gusi_bengkak' => 'N',
            'gigi_kotor_plak' => 'N',
            'karang_gigi' => $karang,
            'susunan_gigi_tidak_teratur' => 'N',
            'penglihatan_loupe' => 'N',
            'pendengaran' => 'N',
            'kursi_roda' => 'N',
            'tongkat_kruk' => 'N',
            'kaki_tangan_mata_protese' => 'N',
            'dirujuk_ke_fasyankes' => $rujukanGigi,
            'keterangan_rujukan' => ($rujukanGigi === 'Y') ? 'Karies gigi parah' : null,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        // 4. Mata
        $hasMata = (rand(1, 100) <= 15);
        if ($hasMata) {
            $mataCount++;
            $visusKanan = localGetRandom(['6/9', '6/12', '6/18']);
            $visusKiri = localGetRandom(['6/6', '6/9', '6/12']);
            $rujukanMata = 'Y';
        } else {
            $visusKanan = '6/6';
            $visusKiri = '6/6';
            $rujukanMata = 'N';
        }
        
        $pMataData[] = [
            'student_class_history_id' => $historyId,
            'tanggal_pemeriksaan' => $date,
            'visus_kanan' => $visusKanan,
            'visus_kiri' => $visusKiri,
            'pakai_kacamata' => 'N',
            'buta_warna' => (rand(1, 100) > 98) ? 'Y' : 'N',
            'mata_merah' => 'N',
            'mata_berair' => 'N',
            'nyeri_mata' => 'N',
            'gatal_mata' => 'N',
            'mata_bengkak' => 'N',
            'mata_belekan' => 'N',
            'dirujuk_ke_fasyankes' => $rujukanMata,
            'keterangan_rujukan' => ($rujukanMata === 'Y') ? 'Gangguan refraksi mata' : null,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        // Batch Inserts to prevent memory bloat and speed up
        if (count($pUmumData) >= $batchSize) {
            DB::table('pemeriksaan_umums')->insert($pUmumData);
            DB::table('pemeriksaan_gizis')->insert($pGiziData);
            DB::table('pemeriksaan_gigis')->insert($pGigiData);
            DB::table('pemeriksaan_matas')->insert($pMataData);
            $pUmumData = $pGiziData = $pGigiData = $pMataData = [];
        }
    }
}

// Write remaining batches
if (count($pUmumData) > 0) {
    DB::table('pemeriksaan_umums')->insert($pUmumData);
    DB::table('pemeriksaan_gizis')->insert($pGiziData);
    DB::table('pemeriksaan_gigis')->insert($pGigiData);
    DB::table('pemeriksaan_matas')->insert($pMataData);
}

// ---------------------------------------------------------
// Seeding Balita Examinations
// ---------------------------------------------------------
echo "Seeding Balita Checks (Pemeriksaan Balita with Immunizations)...\n";
$pBalitaData = [];
$balitaChecksCount = 0;
$balitaStuntingCount = 0;

$imunisasiSchedule = [
    'imunisasi_hepatitis_b' => 0,
    'imunisasi_bcg_bulan_1' => 1,
    'imunisasi_polio_dosis_1' => 1,
    'imunisasi_polio_dosis_2' => 2,
    'imunisasi_polio_dosis_3' => 3,
    'imunisasi_polio_dosis_4' => 4,
    'imunisasi_dpt_hb_hib_dosis_1' => 2,
    'imunisasi_dpt_hb_hib_dosis_2' => 3,
    'imunisasi_dpt_hb_hib_dosis_3' => 4,
    'imunisasi_dpt_hb_hib_dosis_4' => 18,
    'imunisasi_pcv_dosis_1' => 2,
    'imunisasi_pcv_dosis_2' => 3,
    'imunisasi_rotavirus_dosis_1' => 2,
    'imunisasi_rotavirus_dosis_2' => 3,
    'imunisasi_rotavirus_dosis_3' => 4,
    'imunisasi_campak_rubella_dosis_1' => 9,
    'imunisasi_campak_rubella_dosis_2' => 18
];

foreach ($children as $anak) {
    $anakId = $anak['id'];
    $gender = $anak['gender'];
    $dob = new DateTime($anak['dob']);
    $maxAge = $anak['umur_bulan'];
    
    $numChecks = rand(3, 6);
    $checkDates = [];
    
    $attempts = 0;
    while (count($checkDates) < $numChecks && $attempts < 100) {
        $attempts++;
        $ageAtCheck = ($maxAge <= 3) ? rand(0, $maxAge) : rand(1, $maxAge);
        $checkDate = clone $dob;
        $checkDate->add(new DateInterval('P' . $ageAtCheck . 'M'));
        $checkDate->add(new DateInterval('P' . rand(0, 20) . 'D'));
        
        $checkYear = intval($checkDate->format('Y'));
        if ($checkYear < 2025) {
            $checkDate = new DateTime('2025-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT));
            if ($checkDate < $dob) {
                $checkDate = clone $dob;
                $checkDate->add(new DateInterval('P15D'));
            }
        }
        if (intval($checkDate->format('Y')) > 2026) {
            $checkDate = new DateTime('2026-' . str_pad(rand(6, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT));
        }
        
        $dateStr = $checkDate->format('Y-m-d');
        if (!in_array($dateStr, array_column($checkDates, 'date'))) {
            $checkDates[] = [
                'date' => $dateStr,
                'age_months' => $ageAtCheck
            ];
        }
    }
    
    usort($checkDates, function ($item1, $item2) {
        return strcmp($item1['date'], $item2['date']);
    });
    
    foreach ($checkDates as $cd) {
        $balitaChecksCount++;
        $cDate = $cd['date'];
        $cAge = $cd['age_months'];
        
        if ($gender === 'L') {
            $hAvg = 49.0 + (1.8 * $cAge) - (0.014 * $cAge * $cAge);
            $wAvg = 3.3 + (0.5 * $cAge) - (0.0045 * $cAge * $cAge);
        } else {
            $hAvg = 48.0 + (1.75 * $cAge) - (0.0135 * $cAge * $cAge);
            $wAvg = 3.1 + (0.46 * $cAge) - (0.0042 * $cAge * $cAge);
        }
        
        $isStunted = (rand(1, 100) <= 9);
        if ($isStunted) {
            $balitaStuntingCount++;
            $statusStunting = (rand(1, 10) <= 3) ? 'Sangat Pendek' : 'Pendek';
            $height = round($hAvg * (rand(86, 91) / 100.0), 1);
            $weight = round($wAvg * (rand(76, 86) / 100.0), 1);
        } else {
            $statusStunting = 'Normal';
            $height = round($hAvg * (rand(97, 105) / 100.0), 1);
            $weight = round($wAvg * (rand(92, 112) / 100.0), 1);
        }
        
        $imt = round($weight / (($height / 100.0) ** 2), 2);
        
        $weightRatio = $weight / $wAvg;
        if ($weightRatio < 0.75) {
            $statusGizi = 'Gizi Buruk';
        } elseif ($weightRatio < 0.88) {
            $statusGizi = 'Gizi Kurang';
        } elseif ($weightRatio < 1.15) {
            $statusGizi = 'Gizi Baik';
        } else {
            $statusGizi = 'Gizi Lebih';
        }
        
        // Build immunization flags based on age at check
        $imunFlags = [];
        foreach ($imunisasiSchedule as $colName => $reqAge) {
            if ($cAge >= $reqAge) {
                // got it with 90% chance
                $imunFlags[$colName] = (rand(1, 100) <= 90) ? 'Y' : 'N';
            } else {
                $imunFlags[$colName] = 'N';
            }
        }
        
        $record = [
            'child_id' => $anakId,
            'tanggal_pemeriksaan' => $cDate,
            'berat_badan' => $weight,
            'tinggi_badan' => $height,
            'status_tb_u' => $statusStunting,
            'status_stunting' => $statusStunting,
            'status_imt_u' => $statusGizi,
            'dirujuk_ke_fasyankes' => 'N',
            'keterangan_rujukan' => null,
            'catatan' => 'Pertumbuhan terpantau baik.',
            'created_at' => now(),
            'updated_at' => now(),
            'imt' => $imt,
            'status_lingkar_kepala' => 'Normal',
            'disabilitas' => 'N',
            'riwayat_kencing_manis' => 'N',
            'makan_pagi_sudah_banyak' => 'Y',
            'makan_banyak_makanan_manis' => 'N',
            'mengalami_penurunan_berat_badan' => 'N',
            'riwayat_diabetes_orangtua' => 'N',
            'indikasi_gpph' => 'N',
            'hasil_gpph' => null,
            'indikasi_kmpe' => 'N',
            'hasil_kmpe' => null,
            'hasil_kpsp' => 'Sesuai',
            'hasil_perilaku' => null,
            'hasil_tes_daya_dengar' => 'Normal',
            'hasil_pemeriksaan_tes_daya_lihat' => 'Normal',
            'pemeriksaan_mata' => 'Normal',
            'serumen_impaksi' => 'N',
            'infeksi_telinga' => 'N',
            'jumlah_gigi_karies' => '0',
            'tb_batuk' => 'N',
            'tb_bb_turun' => 'N',
            'tb_demam' => 'N',
            'tb_lesu' => 'N',
            'tb_kelenjar' => 'N',
            'tb_rontgen' => 'N',
            'tb_kontak' => 'N',
            'tb_metode' => null,
            'hasil_frambusia' => 'Negatif',
            'hasil_kusta' => 'Negatif',
            'hasil_skabies' => 'Negatif'
        ];
        
        $pBalitaData[] = array_merge($record, $imunFlags);
        
        if (count($pBalitaData) >= $batchSize) {
            DB::table('pemeriksaan_balitas')->insert($pBalitaData);
            $pBalitaData = [];
        }
    }
}

if (count($pBalitaData) > 0) {
    DB::table('pemeriksaan_balitas')->insert($pBalitaData);
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// ---------------------------------------------------------
// SEED PEMERIKSAAN TELINGA (Ear Exams) for ~50% students
// ---------------------------------------------------------
echo "Seeding Pemeriksaan Telinga (Ear Exams)...\n";

$telinga_histories = DB::table('student_class_histories')->pluck('id')->toArray();
$telingaData = [];

foreach ($telinga_histories as $idx => $histId) {
    if ($idx % 2 !== 0) continue; // ~50% of students

    $dirujuk = ($idx % 6 === 0) ? 'Y' : 'N';
    $gangguanKanan = ($idx % 8 === 0) ? 'Y' : 'N';
    $gangguanKiri  = ($idx % 10 === 0) ? 'Y' : 'N';

    $telingaData[] = [
        'student_class_history_id'    => $histId,
        'tanggal_pemeriksaan'         => Carbon::now()->subDays(rand(1, 90))->toDateString(),
        'telinga_luar_kanan'          => ($gangguanKanan === 'Y') ? 'Infeksi' : 'Bersih',
        'telinga_luar_kiri'           => ($gangguanKiri  === 'Y') ? 'Serumen' : 'Bersih',
        'gangguan_pendengaran_kanan'  => $gangguanKanan,
        'gangguan_pendengaran_kiri'   => $gangguanKiri,
        'serumen_kanan'               => ($gangguanKanan === 'Y') ? 'Y' : 'N',
        'serumen_kiri'                => ($gangguanKiri  === 'Y') ? 'Y' : 'N',
        'dirujuk_ke_fasyankes'        => $dirujuk,
        'keterangan_rujukan'          => ($dirujuk === 'Y') ? 'Infeksi telinga luar' : null,
        'created_at'                  => now(),
        'updated_at'                  => now(),
    ];

    if (count($telingaData) >= 200) {
        DB::table('pemeriksaan_telingas')->insert($telingaData);
        $telingaData = [];
    }
}
if (count($telingaData) > 0) {
    DB::table('pemeriksaan_telingas')->insert($telingaData);
}

// ---------------------------------------------------------
// SYNC REFERRALS from all existing examinations
// ---------------------------------------------------------
echo "Syncing Referrals from all examinations...\n";

$referralService = app(\App\Services\Referral\ReferralService::class);
$syncedCount = $referralService->syncAllExisting();

echo "  Synced {$syncedCount} referral records.\n";

// ---------------------------------------------------------
// PRINT STATISTICAL REPORT
// ---------------------------------------------------------

$stuntingPct = $checksCount > 0 ? round(($stuntingCount / $checksCount) * 100, 2) : 0;
$anemiaPct = $anemiaChecked > 0 ? round(($anemiaCount / $anemiaChecked) * 100, 2) : 0;
$kariesPct = $checksCount > 0 ? round(($kariesCount / $checksCount) * 100, 2) : 0;
$mataPct = $checksCount > 0 ? round(($mataCount / $checksCount) * 100, 2) : 0;

$balitaStuntingPct = $balitaChecksCount > 0 ? round(($balitaStuntingCount / $balitaChecksCount) * 100, 2) : 0;

echo "\n============================================\n";
echo "LARAVEL APPLICATION SEEDING COMPLETED SUCCESSFUL!\n";
echo "============================================\n";
echo "Generated and Seeded Counts:\n";
echo "  - Instansi (Puskesmas): 10\n";
echo "  - Posyandus           : 50\n";
echo "  - Schools             : 100\n";
echo "  - Students            : 5,000\n";
echo "  - Children (Balita)   : 3,000\n";
echo "  - School Classes      : " . DB::table('school_classes')->count() . "\n";
echo "  - Class Histories     : 5,000\n";
echo "  - Student Checks      : " . $checksCount . "\n";
echo "  - Balita Checks       : " . $balitaChecksCount . "\n";
echo "  - Referrals Synced    : " . $syncedCount . "\n\n";

echo "Statistical Verification inside Application:\n";
echo "  - Stunting Rate (Siswa)   : " . $stuntingPct . "% (Target: 8-10%)\n";
echo "  - Stunting Rate (Balita)  : " . $balitaStuntingPct . "% (Target: 8-10%)\n";
echo "  - Anemia Rate (Female S.) : " . $anemiaPct . "% (Target: 10-15%)\n";
echo "  - Karies Gigi (Siswa)     : " . $kariesPct . "% (Target: ~20%)\n";
echo "  - Eye Disorders (Siswa)   : " . $mataPct . "% (Target: ~15%)\n";
echo "============================================\n";
