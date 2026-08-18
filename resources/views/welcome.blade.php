<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data x-bind:class="$store.theme?.dark ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedSwift Express — Medical Courier &amp; Logistics</title>
    <meta name="description" content="South Africa's leading AI-powered medical courier service. Reliable cold-chain specimen transit, urgent dispatch, and laboratory logistics.">
    <link rel="icon" type="image/png" href="/favicon.png">
    {{-- Apply dark class synchronously to prevent flash --}}
    <script>if(localStorage.getItem('medswift-theme')==='dark'||(localStorage.getItem('medswift-theme')===null&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .glass-dark { backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .hero-bg { background: linear-gradient(135deg, #0d1719 0%, #142225 50%, #0f2a2e 100%); }
        .text-gradient { background: linear-gradient(135deg, #1697a9, #1da287); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-shine::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,0.08) 0%,transparent 60%); pointer-events:none; border-radius:inherit; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .float { animation: float 4s ease-in-out infinite; }
        @keyframes pulse-ring { 0%{transform:scale(0.9);opacity:1} 100%{transform:scale(1.4);opacity:0} }
        .pulse-ring { animation: pulse-ring 2s ease-out infinite; }
    </style>
</head>
<body class="font-sans antialiased bg-[#0d1719] text-white overflow-x-hidden" x-data="{ mobileOpen: false, trackingNumber: '' }">

{{-- ═══════════════════════════════════════════════════════
     NAVIGATION
═══════════════════════════════════════════════════════ --}}
<nav class="fixed top-0 inset-x-0 z-50 glass bg-[#0d1719]/70 border-b border-white/10"
     x-data="{ scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
     :class="scrolled ? 'shadow-2xl' : ''">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-18 py-3">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 shrink-0">
                <img src="/images/logo.png" alt="MedSwift Express" class="h-10 w-auto">
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#services" class="text-white/70 hover:text-teal-light transition-colors">Services</a>
                <a href="#how-it-works" class="text-white/70 hover:text-teal-light transition-colors">How It Works</a>
                <a href="#features" class="text-white/70 hover:text-teal-light transition-colors">Features</a>
                <a href="#about" class="text-white/70 hover:text-teal-light transition-colors">About</a>
            </div>

            {{-- CTAs --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="rounded-lg px-4 py-2 text-sm font-medium text-teal-light border border-teal/40 hover:bg-teal/10 transition-colors">
                        Dashboard
                    </a>
                {{-- Dark mode toggle — always visible --}}
                <button id="theme-toggle"
                        class="rounded-full p-2 text-white/60 hover:bg-white/10 transition-colors"
                        aria-label="Toggle dark mode">
                    <svg id="icon-sun" class="h-5 w-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 0 0 10A5 5 0 0 0 12 7z"/>
                    </svg>
                    <svg id="icon-moon" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="rounded-lg px-4 py-2 text-sm font-medium text-teal-light border border-teal/40 hover:bg-teal/10 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-white/80 hover:text-white transition-colors px-3 py-2">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}"
                       class="rounded-lg px-5 py-2.5 text-sm font-semibold
                              bg-gradient-to-r from-teal to-emerald text-white
                              hover:from-teal-dark hover:to-emerald-dark
                              shadow-lg shadow-teal/25 transition-all">
                        Get Started
                    </a>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-white/70 hover:bg-white/10 transition-colors">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-collapse
             class="md:hidden border-t border-white/10 py-4 space-y-2">
            <a href="#services" @click="mobileOpen=false" class="block px-3 py-2 text-white/70 hover:text-white rounded-lg hover:bg-white/5">Services</a>
            <a href="#how-it-works" @click="mobileOpen=false" class="block px-3 py-2 text-white/70 hover:text-white rounded-lg hover:bg-white/5">How It Works</a>
            <a href="#features" @click="mobileOpen=false" class="block px-3 py-2 text-white/70 hover:text-white rounded-lg hover:bg-white/5">Features</a>
            <div class="pt-3 flex flex-col gap-2 border-t border-white/10">
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-2.5 text-center text-sm font-medium text-white border border-white/20 hover:bg-white/5">Sign In</a>
                <a href="{{ route('register') }}" class="rounded-lg px-4 py-2.5 text-center text-sm font-semibold bg-gradient-to-r from-teal to-emerald text-white">Get Started</a>
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center overflow-hidden">
    {{-- Background image + overlay --}}
    <div class="absolute inset-0">
        <img src="/images/Gemini_Generated_Image_iqx8bpiqx8bpiqx8.jpeg"
             alt="MedSwift courier at Lancet Laboratories"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0 bg-gradient-to-r from-[#0d1719]/95 via-[#0d1719]/75 to-[#0d1719]/40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0d1719]/60 via-transparent to-transparent"></div>
    </div>

    {{-- Decorative orbs --}}
    <div class="absolute top-1/4 left-1/3 w-96 h-96 bg-teal/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-emerald/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-20">
        <div class="max-w-2xl">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 glass bg-teal/15 border border-teal/30 rounded-full px-4 py-2 text-sm text-teal-light font-medium mb-8">
                <span class="relative flex h-2 w-2">
                    <span class="pulse-ring absolute inline-flex h-full w-full rounded-full bg-emerald opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald"></span>
                </span>
                Now serving 6 provinces across South Africa
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight mb-6">
                Medical Logistics,<br>
                <span class="text-gradient">Delivered with<br>Precision.</span>
            </h1>

            <p class="text-lg text-white/70 leading-relaxed mb-10 max-w-xl">
                South Africa's most trusted AI-powered courier for biological specimens, cold-chain samples, and medical supplies — from pickup to lab, tracked in real time.
            </p>

            {{-- Tracking form --}}
            <div class="glass card-shine relative bg-white/8 border border-white/15 rounded-2xl p-5 mb-8 max-w-lg">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-light mb-3">Track Your Shipment</p>
                <form action="{{ route('login') }}" method="GET" class="flex gap-2">
                    <input type="text"
                           x-model="trackingNumber"
                           placeholder="Enter tracking number  e.g. MS-A1B2C3D4"
                           class="flex-1 bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-sm text-white placeholder-white/40
                                  focus:outline-none focus:ring-2 focus:ring-teal/50 focus:border-teal/50 transition-all">
                    <button type="submit"
                            class="rounded-xl bg-gradient-to-r from-teal to-emerald px-5 py-3 text-sm font-semibold text-white
                                   hover:from-teal-dark hover:to-emerald-dark shadow-lg shadow-teal/30 transition-all whitespace-nowrap">
                        Track →
                    </button>
                </form>
            </div>

            {{-- CTA buttons --}}
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('register') }}"
                   class="rounded-xl bg-gradient-to-r from-teal to-emerald px-7 py-3.5 text-base font-semibold text-white
                          hover:from-teal-dark hover:to-emerald-dark shadow-xl shadow-teal/30 transition-all">
                    Book a Pickup
                </a>
                <a href="#services"
                   class="rounded-xl glass bg-white/10 border border-white/20 px-7 py-3.5 text-base font-semibold text-white
                          hover:bg-white/15 transition-all">
                    Explore Services
                </a>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/40">
        <span class="text-xs tracking-widest uppercase">Scroll</span>
        <div class="w-px h-10 bg-gradient-to-b from-white/40 to-transparent"></div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     STATS BAR
═══════════════════════════════════════════════════════ --}}
<section class="relative py-12 bg-gradient-to-r from-teal/20 via-[#142225] to-emerald/20">
    <div class="absolute inset-0 glass bg-black/30"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach ([
                ['500+',   'Labs &amp; Clinics Served'],
                ['50 000+','Specimens Delivered'],
                ['99.8%',  'On-Time Delivery Rate'],
                ['24 / 7', 'Emergency Dispatch'],
            ] as [$num, $label])
                <div>
                    <div class="text-4xl font-black text-gradient mb-1">{!! $num !!}</div>
                    <div class="text-sm text-white/60">{!! $label !!}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     SERVICES
═══════════════════════════════════════════════════════ --}}
<section id="services" class="py-24 bg-[#0d1719]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <p class="text-teal-light text-sm font-semibold uppercase tracking-widest mb-3">Our Services</p>
            <h2 class="text-4xl lg:text-5xl font-black mb-4">Built for Medical Logistics</h2>
            <p class="text-white/60 max-w-xl mx-auto">Every service is designed around the strict chain-of-custody and cold-chain requirements of the medical industry.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Service 1 --}}
            <div class="group relative overflow-hidden rounded-3xl card-shine">
                <img src="/images/Gemini_Generated_Image_60vqho60vqho60vq.jpeg"
                     alt="Specimen transit at hospital" class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0d1719] via-[#0d1719]/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="glass bg-white/10 border border-white/15 rounded-2xl p-5">
                        <div class="text-teal-light text-2xl mb-2">🧬</div>
                        <h3 class="font-bold text-lg mb-1">Specimen &amp; Sample Transit</h3>
                        <p class="text-white/60 text-sm">Direct lab-to-lab transport with full chain-of-custody documentation and digital sign-off.</p>
                    </div>
                </div>
            </div>

            {{-- Service 2 --}}
            <div class="group relative overflow-hidden rounded-3xl card-shine">
                <img src="/images/Gemini_Generated_Image_23o7zp23o7zp23o7.jpeg"
                     alt="Cold chain logistics" class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0d1719] via-[#0d1719]/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="glass bg-white/10 border border-white/15 rounded-2xl p-5">
                        <div class="text-cyan text-2xl mb-2">❄️</div>
                        <h3 class="font-bold text-lg mb-1">Cold Chain Logistics</h3>
                        <p class="text-white/60 text-sm">Temperature-monitored transport for refrigerated (2–8°C) and frozen (≤–20°C) specimens.</p>
                    </div>
                </div>
            </div>

            {{-- Service 3 --}}
            <div class="group relative overflow-hidden rounded-3xl card-shine">
                <img src="/images/Gemini_Generated_Image_c35lcnc35lcnc35l.jpeg"
                     alt="Urgent medical dispatch" class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0d1719] via-[#0d1719]/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="glass bg-white/10 border border-white/15 rounded-2xl p-5">
                        <div class="text-red-400 text-2xl mb-2">🚨</div>
                        <h3 class="font-bold text-lg mb-1">Urgent Medical Dispatch</h3>
                        <p class="text-white/60 text-sm">Same-day emergency courier for critical specimens, blood products, and surgical supplies.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════════════════ --}}
<section id="how-it-works" class="py-24 bg-[#0f1e21]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <p class="text-teal-light text-sm font-semibold uppercase tracking-widest mb-3">Simple Process</p>
            <h2 class="text-4xl lg:text-5xl font-black mb-4">How It Works</h2>
            <p class="text-white/60 max-w-xl mx-auto">From booking to delivery in three seamless steps — with real-time visibility at every stage.</p>
        </div>

        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Connector lines --}}
            <div class="hidden md:block absolute top-14 left-1/3 right-1/3 h-px bg-gradient-to-r from-teal/40 to-emerald/40"></div>

            @foreach ([
                ['01', '📋', 'Book Your Pickup', 'Submit a pickup request via the portal or ask Swiftie, our AI assistant. Specify temperature class, priority, and special handling.', 'Book Now', '/register'],
                ['02', '📡', 'Real-Time Tracking', 'Your specimen is collected, cold-chain validated, and tracked at every waypoint. You receive status updates instantly.', 'Track a Shipment', '/login'],
                ['03', '✅', 'Delivered &amp; Signed Off', 'Digital proof of delivery with recipient signature, timestamp, and optional temperature audit trail for compliance.', 'Learn More', '#features'],
            ] as [$step, $icon, $title, $desc, $cta, $href])
                <div class="relative text-center">
                    {{-- Step number --}}
                    <div class="relative inline-flex items-center justify-center w-28 h-28 mb-6">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-teal/20 to-emerald/20 blur-xl"></div>
                        <div class="relative glass bg-white/8 border border-white/15 rounded-full w-28 h-28 flex flex-col items-center justify-center">
                            <span class="text-3xl">{{ $icon }}</span>
                            <span class="text-xs text-teal-light font-bold mt-1">STEP {{ $step }}</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-3">{!! $title !!}</h3>
                    <p class="text-white/60 text-sm leading-relaxed mb-4 max-w-xs mx-auto">{{ $desc }}</p>
                    <a href="{{ $href }}"
                       class="text-sm text-teal-light font-semibold hover:text-teal transition-colors">{!! $cta !!} →</a>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     OPERATIONS SHOWCASE
═══════════════════════════════════════════════════════ --}}
<section class="relative py-0 overflow-hidden">
    <img src="/images/Gemini_Generated_Image_1vl1ol1vl1ol1vl1.jpeg"
         alt="MedSwift operations hub" class="w-full h-[500px] lg:h-[600px] object-cover object-center">
    <div class="absolute inset-0 bg-gradient-to-r from-[#0d1719]/90 via-[#0d1719]/60 to-transparent"></div>
    <div class="absolute inset-0 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-xl">
                <p class="text-teal-light text-sm font-semibold uppercase tracking-widest mb-4">Our Operations</p>
                <h2 class="text-4xl lg:text-5xl font-black mb-5 leading-tight">
                    A Network Built for<br>Medical Excellence
                </h2>
                <p class="text-white/70 text-lg leading-relaxed mb-8">
                    From our regional hub, we coordinate couriers, routes, and cold-chain compliance across Gauteng, the Western Cape, KwaZulu-Natal, and beyond.
                </p>
                <div class="flex flex-wrap gap-3">
                    <div class="glass bg-white/10 border border-white/15 rounded-xl px-5 py-3 text-sm">
                        <span class="font-bold text-teal-light">Gauteng</span>
                    </div>
                    <div class="glass bg-white/10 border border-white/15 rounded-xl px-5 py-3 text-sm">
                        <span class="font-bold text-teal-light">Western Cape</span>
                    </div>
                    <div class="glass bg-white/10 border border-white/15 rounded-xl px-5 py-3 text-sm">
                        <span class="font-bold text-teal-light">KwaZulu-Natal</span>
                    </div>
                    <div class="glass bg-white/10 border border-white/15 rounded-xl px-5 py-3 text-sm text-white/60">
                        + 3 more provinces
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FEATURES / WHY MEDSWIFT
═══════════════════════════════════════════════════════ --}}
<section id="features" class="py-24 bg-[#0d1719]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <p class="text-teal-light text-sm font-semibold uppercase tracking-widest mb-3">Why MedSwift Express</p>
            <h2 class="text-4xl lg:text-5xl font-black mb-4">Technology Meets Compliance</h2>
            <p class="text-white/60 max-w-xl mx-auto">Purpose-built for the unique demands of medical logistics — no generic courier can match this.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ([
                ['🤖', 'Swiftie AI Assistant', 'Ask anything in plain language — track a shipment, get a quote, or request compliance guidance instantly.', 'from-teal/20 to-teal/5'],
                ['🌡️', 'Cold-Chain Assurance', 'Ambient, refrigerated, and frozen transport with real-time temperature logging and SANS compliance.', 'from-cyan/20 to-cyan/5'],
                ['📍', 'Real-Time Tracking', 'Live status updates from pickup to delivery. Every waypoint logged with GPS location and timestamp.', 'from-emerald/20 to-emerald/5'],
                ['☣️', 'Biohazard Compliance', 'Certified handling for Category B UN 3373 specimens with full IATA P650 packaging protocols.', 'from-orange-900/30 to-orange-900/5'],
                ['⚡', 'Same-Day Dispatch', 'Urgent pickups confirmed within minutes. 24/7 emergency courier network across major metros.', 'from-teal/20 to-teal/5'],
                ['🔐', 'Chain of Custody', 'Every transfer is digitally signed off. Tamper-evident records for audit and compliance.', 'from-emerald/20 to-emerald/5'],
                ['📊', 'Admin Operations View', 'Full visibility dashboard for ops teams — active routes, exceptions, KPIs, and courier load.', 'from-cyan/20 to-cyan/5'],
                ['🧾', 'Auto Invoicing', 'Itemised invoices with VAT, fuel levy, and cold-chain surcharges generated automatically.', 'from-teal/20 to-teal/5'],
            ] as [$icon, $title, $desc, $gradient])
                <div class="relative card-shine glass bg-gradient-to-br {{ $gradient }} border border-white/10 rounded-2xl p-6 hover:border-teal/40 hover:-translate-y-1 transition-all duration-300">
                    <div class="text-3xl mb-4">{{ $icon }}</div>
                    <h3 class="font-bold text-base mb-2">{{ $title }}</h3>
                    <p class="text-white/55 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     PARTNERS / TRUSTED BY
═══════════════════════════════════════════════════════ --}}
<section id="about" class="py-20 bg-[#0f1e21]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <p class="text-center text-white/40 text-sm font-semibold uppercase tracking-widest mb-12">Trusted by leading healthcare institutions</p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
            @foreach (['Lancet Laboratories', 'Netcare', 'Mediclinic', 'National Health Laboratory Service'] as $partner)
                <div class="glass bg-white/5 border border-white/10 rounded-2xl p-6 text-center hover:border-teal/30 transition-colors">
                    <p class="font-semibold text-white/70 text-sm">{{ $partner }}</p>
                </div>
            @endforeach
        </div>

        {{-- Photo collage --}}
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-8 rounded-3xl overflow-hidden h-72">
                <img src="/images/Gemini_Generated_Image_gcuxmogcuxmogcux.jpeg"
                     alt="MedSwift courier handing specimen" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
            </div>
            <div class="col-span-6 md:col-span-4 rounded-3xl overflow-hidden h-72">
                <img src="/images/Gemini_Generated_Image_tjpq4ftjpq4ftjpq.jpeg"
                     alt="MedSwift van" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
            </div>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     CTA BANNER
═══════════════════════════════════════════════════════ --}}
<section class="relative py-24 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-teal/30 via-[#0d1719] to-emerald/20"></div>
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-teal/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-emerald/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="glass card-shine bg-white/5 border border-white/15 rounded-3xl p-12 lg:p-16">
            <div class="text-5xl mb-6">🚀</div>
            <h2 class="text-4xl lg:text-5xl font-black mb-5 leading-tight">
                Ready to Move Your<br><span class="text-gradient">Medical Logistics Forward?</span>
            </h2>
            <p class="text-white/65 text-lg mb-10 max-w-xl mx-auto">
                Join 500+ labs and clinics across South Africa who trust MedSwift Express for reliable, compliant, and AI-powered medical courier services.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('register') }}"
                   class="rounded-xl bg-gradient-to-r from-teal to-emerald px-8 py-4 text-base font-semibold text-white
                          hover:from-teal-dark hover:to-emerald-dark shadow-2xl shadow-teal/30 transition-all">
                    Create Free Account
                </a>
                <a href="{{ route('ai.chat') }}"
                   class="rounded-xl glass bg-white/10 border border-white/20 px-8 py-4 text-base font-semibold text-white
                          hover:bg-white/15 transition-all flex items-center gap-2">
                    <svg class="h-5 w-5 text-teal-light" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                    </svg>
                    Ask Swiftie AI
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════ --}}
<footer class="bg-[#080f10] border-t border-white/8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <img src="/images/logo.png" alt="MedSwift Express" class="h-12 w-auto mb-4">
                <p class="text-white/50 text-sm leading-relaxed">
                    South Africa's leading AI-powered medical courier. Reliable. Traceable. Cold-chain assured.
                </p>
                <div class="flex gap-3 mt-5">
                    @foreach (['LinkedIn', 'Facebook', 'Instagram'] as $social)
                        <a href="#" class="glass bg-white/8 border border-white/10 rounded-lg p-2 text-white/50 hover:text-teal-light hover:border-teal/30 transition-colors text-xs font-medium px-3">
                            {{ $social }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Services --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-white/40 mb-5">Services</p>
                <ul class="space-y-3 text-sm text-white/60">
                    <li><a href="#services" class="hover:text-teal-light transition-colors">Specimen Transit</a></li>
                    <li><a href="#services" class="hover:text-teal-light transition-colors">Cold Chain Logistics</a></li>
                    <li><a href="#services" class="hover:text-teal-light transition-colors">Urgent Dispatch</a></li>
                    <li><a href="#services" class="hover:text-teal-light transition-colors">Medical Supplies</a></li>
                </ul>
            </div>

            {{-- Portal --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-white/40 mb-5">Portal</p>
                <ul class="space-y-3 text-sm text-white/60">
                    <li><a href="{{ route('login') }}" class="hover:text-teal-light transition-colors">Client Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-teal-light transition-colors">Register</a></li>
                    <li><a href="{{ route('ai.chat') }}" class="hover:text-teal-light transition-colors">Swiftie AI</a></li>
                    <li><a href="#" class="hover:text-teal-light transition-colors">Track Shipment</a></li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-white/40 mb-5">Company</p>
                <ul class="space-y-3 text-sm text-white/60">
                    <li><a href="#about" class="hover:text-teal-light transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-teal-light transition-colors">Compliance</a></li>
                    <li><a href="#" class="hover:text-teal-light transition-colors">Contact</a></li>
                    <li><a href="#" class="hover:text-teal-light transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/35">
            <p>© {{ date('Y') }} MedSwift Express (Pty) Ltd. All rights reserved.</p>
            <p>VAT Registered · SANS/ISO Compliant · IATA P650 Certified</p>
        </div>
    </div>
</footer>

{{-- ═══════════════════════════════════════════════════════
     FLOATING SWIFTIE BUTTON
═══════════════════════════════════════════════════════ --}}
<a href="{{ route('ai.chat') }}"
   class="fixed bottom-6 right-6 z-50 group flex items-center gap-0 overflow-hidden
          glass bg-gradient-to-br from-teal to-emerald
          border border-white/20 rounded-2xl shadow-2xl shadow-teal/40
          px-4 py-3.5
          hover:gap-3 hover:pr-5 transition-all duration-300 hover:shadow-teal/60"
   title="Chat with Swiftie AI">

    {{-- Pulse ring --}}
    <span class="absolute -top-1 -right-1 flex h-3 w-3">
        <span class="pulse-ring absolute inline-flex h-full w-full rounded-full bg-emerald-light opacity-60"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald"></span>
    </span>

    {{-- Icon --}}
    <svg class="h-5 w-5 text-white shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
    </svg>

    {{-- Label — slides in on hover --}}
    <span class="max-w-0 group-hover:max-w-xs overflow-hidden whitespace-nowrap transition-all duration-300 text-white text-sm font-semibold">
        Chat with Swiftie
    </span>
</a>

<script>
    // Pure JS dark mode — no Alpine dependency
    const THEME_KEY = 'medswift-theme';
    const html = document.documentElement;
    const sunIcon  = document.getElementById('icon-sun');
    const moonIcon = document.getElementById('icon-moon');
    const toggleBtn = document.getElementById('theme-toggle');

    function isDark() {
        const stored = localStorage.getItem(THEME_KEY);
        return stored === 'dark' || (stored === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
    }

    function applyTheme(dark) {
        html.classList.toggle('dark', dark);
        if (sunIcon && moonIcon) {
            sunIcon.classList.toggle('hidden', !dark);
            moonIcon.classList.toggle('hidden', dark);
        }
        // Sync Alpine store if Alpine is loaded
        if (window.Alpine && Alpine.store('theme')) {
            Alpine.store('theme').dark = dark;
        }
    }

    // Apply on load
    applyTheme(isDark());

    // Toggle on click
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const next = !isDark();
            localStorage.setItem(THEME_KEY, next ? 'dark' : 'light');
            applyTheme(next);
        });
    }

    // Also init Alpine store for authenticated app pages
    document.addEventListener('alpine:init', () => {
        if (window.Alpine) {
            Alpine.store('theme', {
                dark: isDark(),
                toggle() {
                    const next = !this.dark;
                    this.dark = next;
                    localStorage.setItem(THEME_KEY, next ? 'dark' : 'light');
                    html.classList.toggle('dark', next);
                    applyTheme(next);
                },
            });
        }
    });
</script>

</body>
</html>