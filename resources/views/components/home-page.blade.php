{{-- SEAPEDIA Home Page --}}
<div style="font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; background: #f0f4ff; min-height: 100vh;">

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-18px); }
}
@keyframes floatDelay {
    0%, 100% { transform: translateY(0px) rotate(-2deg); }
    50% { transform: translateY(-12px) rotate(2deg); }
}
@keyframes wave {
    0% { transform: rotate(0deg); }
    15% { transform: rotate(18deg); }
    30% { transform: rotate(-10deg); }
    45% { transform: rotate(14deg); }
    60% { transform: rotate(-6deg); }
    75% { transform: rotate(10deg); }
    100% { transform: rotate(0deg); }
}
@keyframes blink {
    0%, 90%, 100% { transform: scaleY(1); }
    95% { transform: scaleY(0.1); }
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.85); }
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes bobBox {
    0%, 100% { transform: translateY(0) rotate(-1deg); }
    50% { transform: translateY(-8px) rotate(1deg); }
}
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
@keyframes bubbleRise {
    0% {
        transform: translateY(0) translateX(0) scale(0.6);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    50% {
        transform: translateY(-45vh) translateX(18px) scale(1);
    }
    80% {
        opacity: 0.7;
    }
    100% {
        transform: translateY(-110vh) translateX(-10px) scale(1.1);
        opacity: 0;
    }
}
.mascot-float { animation: float 3.5s ease-in-out infinite; }
.mascot-wave-arm { animation: wave 2.5s ease-in-out infinite; transform-origin: 70% 80%; }
.mascot-eye { animation: blink 4s ease-in-out infinite; transform-origin: center; }
.float-card-1 { animation: floatDelay 4s ease-in-out infinite; }
.float-card-2 { animation: floatDelay 3.2s ease-in-out infinite 0.8s; }
.box-bob { animation: bobBox 2.8s ease-in-out infinite; }
</style>

{{-- ===== HERO SECTION ===== --}}
<section style="position: relative; min-height: calc(100vh - 64px); background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 45%, #e0f2fe 100%); overflow: hidden; display: flex; align-items: center;">

    {{-- Dot pattern --}}
    <div style="position: absolute; inset: 0; background-image: radial-gradient(#93c5fd 1.5px, transparent 1.5px); background-size: 32px 32px; opacity: 0.35;"></div>

    {{-- ===== BUBBLES ===== --}}
    <div class="bubbles-container" style="position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 1;">
        @php
        $bubbles = [
            ['size'=>14, 'left'=>5,  'delay'=>0,   'dur'=>7,  'opacity'=>0.5],
            ['size'=>22, 'left'=>12, 'delay'=>1.5, 'dur'=>9,  'opacity'=>0.35],
            ['size'=>10, 'left'=>20, 'delay'=>0.8, 'dur'=>6,  'opacity'=>0.55],
            ['size'=>30, 'left'=>28, 'delay'=>2.2, 'dur'=>11, 'opacity'=>0.25],
            ['size'=>16, 'left'=>36, 'delay'=>0.3, 'dur'=>8,  'opacity'=>0.45],
            ['size'=>8,  'left'=>44, 'delay'=>3.1, 'dur'=>5.5,'opacity'=>0.6],
            ['size'=>24, 'left'=>52, 'delay'=>1.0, 'dur'=>10, 'opacity'=>0.3],
            ['size'=>12, 'left'=>60, 'delay'=>2.7, 'dur'=>7,  'opacity'=>0.5],
            ['size'=>18, 'left'=>68, 'delay'=>0.5, 'dur'=>8.5,'opacity'=>0.4],
            ['size'=>28, 'left'=>75, 'delay'=>1.8, 'dur'=>12, 'opacity'=>0.22],
            ['size'=>10, 'left'=>82, 'delay'=>3.5, 'dur'=>6,  'opacity'=>0.55],
            ['size'=>20, 'left'=>88, 'delay'=>0.9, 'dur'=>9,  'opacity'=>0.38],
            ['size'=>14, 'left'=>93, 'delay'=>2.0, 'dur'=>7.5,'opacity'=>0.48],
            ['size'=>8,  'left'=>97, 'delay'=>1.3, 'dur'=>5,  'opacity'=>0.6],
            ['size'=>32, 'left'=>16, 'delay'=>4.0, 'dur'=>13, 'opacity'=>0.2],
            ['size'=>6,  'left'=>40, 'delay'=>2.5, 'dur'=>5,  'opacity'=>0.65],
            ['size'=>18, 'left'=>56, 'delay'=>3.8, 'dur'=>8,  'opacity'=>0.42],
            ['size'=>26, 'left'=>72, 'delay'=>0.2, 'dur'=>11, 'opacity'=>0.28],
        ];
        @endphp
        @foreach($bubbles as $b)
        @php
            $bs = 'position:absolute;bottom:-60px;left:'.$b['left'].'%;width:'.$b['size'].'px;height:'.$b['size'].'px;border-radius:50%;background:radial-gradient(circle at 35% 35%,rgba(255,255,255,0.9),rgba(147,197,253,'.$b['opacity'].'));border:1.5px solid rgba(255,255,255,0.7);box-shadow:inset 0 -2px 4px rgba(37,99,235,0.15),0 2px 6px rgba(37,99,235,0.1);animation:bubbleRise '.$b['dur'].'s ease-in '.$b['delay'].'s infinite;backdrop-filter:blur(2px)';
        @endphp
        <div style="{{ $bs }}"></div>
        @endforeach
    </div>

    {{-- Blobs --}}
    <div style="position: absolute; top: -100px; right: -100px; width: 500px; height: 500px; background: radial-gradient(circle, rgba(37,99,235,0.10), transparent 70%); border-radius: 50%; pointer-events: none;"></div>
    <div style="position: absolute; bottom: -80px; left: 60px; width: 350px; height: 350px; background: radial-gradient(circle, rgba(14,165,233,0.09), transparent 70%); border-radius: 50%; pointer-events: none;"></div>

    <div style="position: relative; max-width: 1200px; margin: 0 auto; padding: 60px 40px; display: flex; align-items: center; justify-content: space-between; gap: 20px; width: 100%; box-sizing: border-box;">

        {{-- LEFT: Text --}}
        <div style="flex: 1; max-width: 520px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: white; border: 1px solid #bfdbfe; border-radius: 50px; padding: 6px 16px; margin-bottom: 28px; box-shadow: 0 2px 8px rgba(37,99,235,0.1);">
                <span class="pulse-dot" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block; animation: pulse-dot 2s infinite;"></span>
                <span style="font-size: 13px; color: #2563eb; font-weight: 600;">🛍 Marketplace Multi-Toko Terpercaya</span>
            </div>

            <h1 style="font-size: clamp(40px, 5.5vw, 68px); font-weight: 900; line-height: 1.05; color: #0f172a; margin: 0 0 20px 0; letter-spacing: -2px;">
                Belanja Apa<br>Saja, dari<br><span style="color: #2563eb;">Mana Saja.</span>
            </h1>

            <p style="font-size: 16px; color: #475569; line-height: 1.75; margin-bottom: 36px; max-width: 400px;">
                SEAPEDIA menghadirkan ribuan produk dari berbagai toko pilihan — belanja mudah, aman, dan langsung sampai ke tanganmu.
            </p>

            <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
                <a href="{{ route('catalog') }}"
                   style="text-decoration: none; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 14px 32px; border-radius: 50px; font-size: 15px; font-weight: 700; box-shadow: 0 6px 20px rgba(37,99,235,0.35);">
                    🛒 Mulai Belanja
                </a>
                @guest
                <a href="{{ route('login') }}"
                   style="text-decoration: none; background: white; color: #1e293b; padding: 14px 26px; border-radius: 50px; font-size: 14px; font-weight: 600; border: 2px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    Daftar Gratis →
                </a>
                @endguest
            </div>

            <div style="display: flex; gap: 28px; margin-top: 44px; flex-wrap: wrap; align-items: center;">
                <div>
                    <p style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">1.000+</p>
                    <p style="font-size: 12px; color: #64748b; margin: 3px 0 0 0;">Produk</p>
                </div>
                <div style="width: 1px; height: 36px; background: #e2e8f0;"></div>
                <div>
                    <p style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">200+</p>
                    <p style="font-size: 12px; color: #64748b; margin: 3px 0 0 0;">Toko</p>
                </div>
                <div style="width: 1px; height: 36px; background: #e2e8f0;"></div>
                <div>
                    <p style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">5.000+</p>
                    <p style="font-size: 12px; color: #64748b; margin: 3px 0 0 0;">Pembeli</p>
                </div>
            </div>
        </div>

        {{-- RIGHT: Mascot + Floating Cards --}}
        <div style="flex: 0 0 auto; width: 440px; position: relative; height: 500px;">

            {{-- Floating card top-right --}}
            <div class="float-card-1" style="position: absolute; top: 10px; right: 0; background: white; border-radius: 16px; padding: 12px 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.10); display: flex; align-items: center; gap: 10px; z-index: 20; min-width: 180px;">
                <div style="width: 38px; height: 38px; background: #dcfce7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">✅</div>
                <div>
                    <p style="margin: 0; font-size: 12px; font-weight: 700; color: #1e293b;">Pesanan Berhasil!</p>
                    <p style="margin: 2px 0 0 0; font-size: 11px; color: #64748b;">Pembayaran diterima</p>
                </div>
            </div>

            {{-- Floating card bottom-left --}}
            <div class="float-card-2" style="position: absolute; bottom: 60px; left: 0; background: white; border-radius: 16px; padding: 12px 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.10); display: flex; align-items: center; gap: 10px; z-index: 20; min-width: 190px;">
                <div style="width: 38px; height: 38px; background: #fef9c3; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">⚡</div>
                <div>
                    <p style="margin: 0; font-size: 12px; font-weight: 700; color: #1e293b;">Pengiriman Instant</p>
                    <p style="margin: 2px 0 0 0; font-size: 11px; color: #64748b;">3 jam sampai tujuan</p>
                </div>
            </div>

            {{-- ===== MASCOT SVG: Karakter Nelayan/Kurir SEAPEDIA ===== --}}
            <div class="mascot-float" style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 320px;">
                <svg viewBox="0 0 320 480" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto; filter: drop-shadow(0 20px 40px rgba(37,99,235,0.2));">

                    {{-- Shadow --}}
                    <ellipse cx="160" cy="465" rx="70" ry="10" fill="rgba(37,99,235,0.12)"/>

                    {{-- === BODY: Seragam Kurir Biru === --}}
                    {{-- Kaki kiri --}}
                    <rect x="112" y="340" width="38" height="80" rx="18" fill="#1e3a5f"/>
                    {{-- Kaki kanan --}}
                    <rect x="168" y="340" width="38" height="80" rx="18" fill="#1e3a5f"/>
                    {{-- Sepatu kiri --}}
                    <ellipse cx="131" cy="420" rx="24" ry="12" fill="#0f172a"/>
                    {{-- Sepatu kanan --}}
                    <ellipse cx="187" cy="420" rx="24" ry="12" fill="#0f172a"/>

                    {{-- Badan (jaket kurir biru) --}}
                    <rect x="90" y="210" width="140" height="145" rx="30" fill="#2563eb"/>
                    {{-- Garis jaket tengah --}}
                    <rect x="155" y="215" width="6" height="135" rx="3" fill="#1d4ed8"/>
                    {{-- Saku kiri --}}
                    <rect x="100" y="270" width="36" height="28" rx="8" fill="#1d4ed8"/>
                    {{-- Saku kanan --}}
                    <rect x="184" y="270" width="36" height="28" rx="8" fill="#1d4ed8"/>
                    {{-- Label SEAPEDIA di dada --}}
                    <rect x="108" y="228" width="60" height="22" rx="6" fill="white"/>
                    <text x="138" y="244" text-anchor="middle" font-size="9" font-weight="800" fill="#2563eb" font-family="sans-serif">SEAPEDIA</text>

                    {{-- Kerah --}}
                    <polygon points="145,210 160,240 175,210" fill="#1d4ed8"/>

                    {{-- === TANGAN KIRI (memegang kotak) === --}}
                    <rect x="50" y="225" width="42" height="28" rx="14" fill="#2563eb"/>
                    {{-- Telapak tangan kiri --}}
                    <ellipse cx="52" cy="253" rx="16" ry="14" fill="#fbbf8a"/>
                    {{-- Jari-jari kiri --}}
                    <rect x="38" y="252" width="8" height="14" rx="4" fill="#fbbf8a"/>
                    <rect x="48" y="250" width="8" height="16" rx="4" fill="#fbbf8a"/>
                    <rect x="58" y="251" width="8" height="15" rx="4" fill="#fbbf8a"/>

                    {{-- Kotak paket di tangan kiri (dengan animasi bob) --}}
                    <g class="box-bob">
                        <rect x="8" y="200" width="58" height="54" rx="8" fill="#fbbf24"/>
                        <rect x="8" y="200" width="58" height="54" rx="8" fill="none" stroke="#f59e0b" stroke-width="2"/>
                        {{-- Pita --}}
                        <rect x="34" y="200" width="7" height="54" fill="#ef4444" opacity="0.8"/>
                        <rect x="8" y="224" width="58" height="7" fill="#ef4444" opacity="0.8"/>
                        {{-- Ikon ikan di kotak --}}
                        <text x="37" y="218" text-anchor="middle" font-size="10" fill="#1e3a5f" font-family="sans-serif" font-weight="700">🐟</text>
                        {{-- Label --}}
                        <rect x="14" y="230" width="46" height="18" rx="4" fill="white" opacity="0.7"/>
                        <text x="37" y="243" text-anchor="middle" font-size="8" font-weight="700" fill="#1e3a5f" font-family="sans-serif">FRAGILE</text>
                    </g>

                    {{-- === TANGAN KANAN (melambai) === --}}
                    <g class="mascot-wave-arm">
                        <rect x="228" y="215" width="42" height="28" rx="14" fill="#2563eb"/>
                        {{-- Telapak tangan kanan --}}
                        <ellipse cx="268" cy="210" rx="16" ry="14" fill="#fbbf8a"/>
                        {{-- Jari-jari --}}
                        <rect x="256" y="195" width="8" height="18" rx="4" fill="#fbbf8a"/>
                        <rect x="266" y="192" width="8" height="20" rx="4" fill="#fbbf8a"/>
                        <rect x="276" y="194" width="8" height="18" rx="4" fill="#fbbf8a"/>
                        <rect x="285" y="198" width="7" height="14" rx="3.5" fill="#fbbf8a"/>
                    </g>

                    {{-- === LEHER === --}}
                    <rect x="148" y="188" width="26" height="28" rx="10" fill="#fbbf8a"/>

                    {{-- === KEPALA === --}}
                    <ellipse cx="160" cy="158" rx="62" ry="66" fill="#fbbf8a"/>

                    {{-- Telinga kiri --}}
                    <ellipse cx="100" cy="162" rx="12" ry="16" fill="#fbbf8a"/>
                    <ellipse cx="101" cy="162" rx="7" ry="11" fill="#f9a87a"/>

                    {{-- Telinga kanan --}}
                    <ellipse cx="220" cy="162" rx="12" ry="16" fill="#fbbf8a"/>
                    <ellipse cx="219" cy="162" rx="7" ry="11" fill="#f9a87a"/>

                    {{-- === TOPI KURIR BIRU === --}}
                    {{-- Brim topi --}}
                    <ellipse cx="160" cy="106" rx="72" ry="14" fill="#1e3a5f"/>
                    {{-- Body topi --}}
                    <rect x="98" y="68" width="124" height="42" rx="20" fill="#1e40af"/>
                    {{-- Stripe topi --}}
                    <rect x="98" y="98" width="124" height="8" rx="4" fill="#2563eb"/>
                    {{-- Badge topi --}}
                    <rect x="140" y="76" width="40" height="22" rx="6" fill="white"/>
                    <text x="160" y="91" text-anchor="middle" font-size="8" font-weight="900" fill="#2563eb" font-family="sans-serif">SEA</text>
                    {{-- Rambut samping --}}
                    <rect x="98" y="100" width="12" height="22" rx="6" fill="#1a0a00"/>
                    <rect x="210" y="100" width="12" height="22" rx="6" fill="#1a0a00"/>

                    {{-- === WAJAH === --}}
                    {{-- Alis kiri --}}
                    <path d="M132 138 Q142 132 152 136" stroke="#5c3d1e" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                    {{-- Alis kanan --}}
                    <path d="M168 136 Q178 132 188 138" stroke="#5c3d1e" stroke-width="3.5" fill="none" stroke-linecap="round"/>

                    {{-- Mata kiri --}}
                    <g class="mascot-eye">
                        <ellipse cx="143" cy="152" rx="12" ry="13" fill="white"/>
                        <ellipse cx="144" cy="153" rx="7" ry="8" fill="#1a0a00"/>
                        <ellipse cx="146" cy="150" rx="2.5" ry="2.5" fill="white"/>
                        {{-- Bulu mata --}}
                        <path d="M131 146 Q133 142 136 144" stroke="#1a0a00" stroke-width="1.5" fill="none"/>
                        <path d="M154 144 Q157 142 157 146" stroke="#1a0a00" stroke-width="1.5" fill="none"/>
                    </g>

                    {{-- Mata kanan --}}
                    <g class="mascot-eye">
                        <ellipse cx="177" cy="152" rx="12" ry="13" fill="white"/>
                        <ellipse cx="178" cy="153" rx="7" ry="8" fill="#1a0a00"/>
                        <ellipse cx="180" cy="150" rx="2.5" ry="2.5" fill="white"/>
                        {{-- Bulu mata --}}
                        <path d="M165 146 Q167 142 170 144" stroke="#1a0a00" stroke-width="1.5" fill="none"/>
                        <path d="M188 144 Q191 142 191 146" stroke="#1a0a00" stroke-width="1.5" fill="none"/>
                    </g>

                    {{-- Hidung --}}
                    <ellipse cx="160" cy="168" rx="6" ry="5" fill="#f9a87a"/>
                    <ellipse cx="156" cy="170" rx="3" ry="2.5" fill="#e8926a"/>
                    <ellipse cx="164" cy="170" rx="3" ry="2.5" fill="#e8926a"/>

                    {{-- Senyum --}}
                    <path d="M144 180 Q160 196 176 180" stroke="#d97040" stroke-width="3" fill="none" stroke-linecap="round"/>
                    {{-- Gigi --}}
                    <path d="M150 183 Q160 192 170 183" fill="white" opacity="0.9"/>

                    {{-- Pipi merah kiri --}}
                    <ellipse cx="126" cy="176" rx="13" ry="9" fill="#f87171" opacity="0.3"/>
                    {{-- Pipi merah kanan --}}
                    <ellipse cx="194" cy="176" rx="13" ry="9" fill="#f87171" opacity="0.3"/>

                    {{-- === DETAIL TAMBAHAN: Emblem di bahu === --}}
                    <circle cx="100" cy="228" r="10" fill="#fbbf24"/>
                    <text x="100" y="233" text-anchor="middle" font-size="10" fill="#1e3a5f" font-family="sans-serif">★</text>
                    <circle cx="220" cy="228" r="10" fill="#fbbf24"/>
                    <text x="220" y="233" text-anchor="middle" font-size="10" fill="#1e3a5f" font-family="sans-serif">★</text>

                </svg>
            </div>

        </div>{{-- end right --}}
    </div>
</section>

{{-- ===== FITUR ===== --}}
<section style="background: white; padding: 80px 40px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 52px;">
            <span style="background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 50px; letter-spacing: 0.8px; text-transform: uppercase;">Kenapa SEAPEDIA?</span>
            <h2 style="font-size: 34px; font-weight: 900; color: #0f172a; margin: 14px 0 0 0; letter-spacing: -1px;">Pengalaman belanja yang<br><span style="color: #2563eb;">berbeda dari biasanya</span></h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 22px;">
            @php
            $features = [
                ['icon' => '🏪', 'bg' => '#eff6ff', 'title' => 'Multi-Toko', 'desc' => 'Pilih dari ratusan toko dengan ribuan produk berbeda dalam satu platform.'],
                ['icon' => '🚀', 'bg' => '#fef9c3', 'title' => 'Pengiriman Cepat', 'desc' => 'Pilih metode Instant, Next Day, atau Regular sesuai kebutuhanmu.'],
                ['icon' => '💳', 'bg' => '#f0fdf4', 'title' => 'Bayar Mudah', 'desc' => 'Transaksi aman menggunakan sistem wallet digital SEAPEDIA.'],
                ['icon' => '🔒', 'bg' => '#fdf4ff', 'title' => 'Aman & Terpercaya', 'desc' => 'Perlindungan pembeli dengan refund otomatis jika pesanan bermasalah.'],
            ];
            @endphp
            @foreach($features as $f)
            @php $fs = 'background:'.$f['bg'].';border-radius:20px;padding:26px;border:1px solid rgba(0,0,0,0.04)'; 
            @endphp
            <div style="{{ $fs }}">
                <div style="width: 50px; height: 50px; background: white; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">{{ $f['icon'] }}</div>
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">{{ $f['title'] }}</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.65;">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== PRODUK PILIHAN ===== --}}
<section style="background: #f8faff; padding: 80px 40px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 36px; flex-wrap: wrap; gap: 16px;">
            <div>
                <span style="background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.8px;">Produk Terpilih</span>
                <h2 style="font-size: 30px; font-weight: 900; color: #0f172a; margin: 12px 0 0 0; letter-spacing: -1px;">Pilihan Produk Hari Ini</h2>
            </div>
            <a href="{{ route('catalog') }}" style="text-decoration: none; color: #2563eb; font-size: 14px; font-weight: 600;">Lihat Semua →</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 18px;">
            @foreach($products as $product)
            @php $productUrl = $product->id ? route('product.detail', $product->id) : route('catalog'); @endphp
            <a href="{{ $productUrl }}"
               style="text-decoration: none; background: white; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0; display: block; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: box-shadow 0.2s;">
                <div style="height: 150px; background: linear-gradient(135deg, #dbeafe, #eff6ff); display: flex; align-items: center; justify-content: center; font-size: 48px; position: relative; overflow: hidden;">
                    @if(!empty($product->image))
                        <img src="{{ asset('storage/'.$product->image) }}" style="width:100%; height:100%; object-fit:cover; position:absolute; inset:0;">
                    @else
                        📦
                    @endif
                    <div style="position: absolute; top: 10px; right: 10px; background: #2563eb; color: white; font-size: 9px; font-weight: 700; padding: 3px 8px; border-radius: 50px; z-index:1;">BARU</div>
                </div>
                <div style="padding: 14px;">
                    <p style="font-size: 12px; color: #94a3b8; margin: 0 0 4px 0;">🏪 {{ $product->store->name }}</p>
                    <h3 style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0 0 10px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $product->name }}</h3>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 16px; font-weight: 800; color: #2563eb;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <div style="width: 30px; height: 30px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px;">🛒</div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== ULASAN ===== --}}
<section id="ulasan" style="background: white; padding: 80px 40px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 52px;">
            <span style="background: #fef9c3; color: #d97706; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.8px;">Ulasan Pengguna</span>
            <h2 style="font-size: 34px; font-weight: 900; color: #0f172a; margin: 14px 0 0 0; letter-spacing: -1px;">Apa kata mereka tentang<br><span style="color: #2563eb;">SEAPEDIA?</span></h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px; align-items: start;">

            {{-- Form --}}
            <div style="background: #f8faff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 30px;">
                <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 0 0 22px 0;">✍️ Tulis Ulasanmu</h3>

                <form wire:submit.prevent="submitReview" style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nama Anda</label>
                        <input type="text" wire:model="reviewer_name" required
                               style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 14px; outline: none; background: white; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">Rating (1–5) ⭐</label>
                        <input type="number" wire:model="rating" min="1" max="5" required
                               style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 14px; outline: none; background: white; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">Komentar</label>
                        <textarea wire:model="comment" rows="4" required
                                  style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 14px; outline: none; resize: none; background: white; box-sizing: border-box;"></textarea>
                    </div>
                    <button type="submit"
                            style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 13px; border: none; border-radius: 50px; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
                        Kirim Ulasan 🚀
                    </button>
                </form>
            </div>

            {{-- Reviews --}}
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @forelse($reviews as $review)
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; color: #2563eb;">
                                    {{ strtoupper(substr($review->reviewer_name, 0, 1)) }}
                                </div>
                                <span style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ $review->reviewer_name }}</span>
                            </div>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="font-size: 13px; color: {{ $i <= $review->rating ? '#f59e0b' : '#e2e8f0' }};">★</span>
                                @endfor
                            </div>
                        </div>
                        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                        <div style="font-size: 44px; margin-bottom: 10px;">💬</div>
                        <p style="font-size: 14px; margin: 0;">Belum ada ulasan. Jadilah yang pertama!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

</div>{{-- end wrapper --}}
