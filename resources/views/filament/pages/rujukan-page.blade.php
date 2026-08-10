<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1rem; gap: 2rem; min-height: 60vh;">

        {{-- Icon & Header --}}
        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; text-align: center;">
            <div style="height: 80px; width: 80px; border-radius: 20px; background: linear-gradient(135deg, #0d9488, #0f766e); display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(13, 148, 136, 0.35);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width: 40px; height: 40px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                </svg>
            </div>
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem 0;"> Rujukan Kesehatan Siswa</h2>
                <p style="font-size: 0.875rem; color: #64748b; margin: 0; max-width: 480px;">
                    Kelola seluruh data rujukan siswa berdasarkan hasil pemeriksaan kesehatan
                </p>
            </div>
        </div>

        {{-- Feature Cards --}}
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; width: 100%; max-width: 800px;">
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div style="height: 40px; width: 40px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-align: center;">Dashboard Rujukan</span>
            </div>
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div style="height: 40px; width: 40px; border-radius: 10px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-align: center;">Daftar Rujukan</span>
            </div>
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div style="height: 40px; width: 40px; border-radius: 10px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-align: center;">Rekap Sekolah</span>
            </div>
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div style="height: 40px; width: 40px; border-radius: 10px; background: #faf5ff; color: #9333ea; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c-.621 0-1.125.504-1.125 1.125v1.5m2.25-2.625c.621 0 1.125.504 1.125 1.125v1.5m2.25-2.625c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125M21 12.375v1.5c0 .621-.504 1.125-1.125 1.125h-2.25c-.621 0-1.125-.504-1.125-1.125v-1.5c0-.621.504-1.125 1.125-1.125H19.875c.621 0 1.125.504 1.125 1.125Z" />
                    </svg>
                </div>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-align: center;">Rekap Kelas</span>
            </div>
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div style="height: 40px; width: 40px; border-radius: 10px; background: #f0fdfa; color: #0d9488; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </div>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-align: center;">Ekspor Laporan</span>
            </div>
        </div>

        {{-- Info Notice --}}
        <div style="width: 100%; max-width: 800px; background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: flex-start; gap: 0.75rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#0d9488" style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            <div>
                <p style="font-size: 0.875rem; font-weight: 600; color: #0f766e; margin: 0 0 0.25rem 0;"> Rujukan Interaktif</p>
                <p style="font-size: 0.8125rem; color: #0d9488; margin: 0; line-height: 1.5;">
                    Berjalan sebagai aplikasi SPA terpisah dengan fitur chart analitik, filter lanjutan, 
                    update status, dan ekspor ke Excel/PDF. Klik tombol di bawah untuk membuka Halaman Rujukan Lengkap.
                </p>
            </div>
        </div>

        {{-- Stats Preview --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; width: 100%; max-width: 800px;">
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); text-align: center;">
                <p style="font-size: 0.625rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.25rem 0;">Total Rujukan</p>
                <p style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0;">9.077</p>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Data tersedia</p>
            </div>
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); text-align: center;">
                <p style="font-size: 0.625rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.25rem 0;">Jenis Pemeriksaan</p>
                <p style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0;">5</p>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Gizi, Gigi, Mata, Telinga, Umum</p>
            </div>
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); text-align: center;">
                <p style="font-size: 0.625rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.25rem 0;">Status</p>
                <p style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0;">4</p>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Belum, Dirujuk, Proses, Selesai</p>
            </div>
        </div>

        {{-- CTA Button --}}
        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
            <a 
                href="{{ url('/admin/rujukan') }}" 
                target="_blank"
                rel="noopener noreferrer"
                style="display: inline-flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, #0d9488, #0f766e); color: white; font-size: 0.9375rem; font-weight: 600; padding: 0.875rem 2rem; border-radius: 12px; text-decoration: none; box-shadow: 0 4px 15px rgba(13, 148, 136, 0.4); transition: all 0.2s;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(13,148,136,0.5)'"
                onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 15px rgba(13,148,136,0.4)'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                <span>Buka Rujukan Lengkap</span>
            </a>
            <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Halaman akan terbuka di tab baru</p>
        </div>

    </div>
</x-filament-panels::page>
