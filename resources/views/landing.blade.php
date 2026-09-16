<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
    <link rel="canonical" href="{{ url('/' . $page->slug) }}">

    {{-- Open Graph (সোশ্যাল মিডিয়ায় শেয়ার করলে সুন্দর প্রিভিউ দেখাবে) --}}
    <meta property="og:title" content="{{ $page->meta_title }}">
    <meta property="og:description" content="{{ $page->meta_description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/' . $page->slug) }}">

    {{-- Structured Data (Google-কে বুঝতে সাহায্য করে এটা একটা স্টাফিং সার্ভিস) --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'EmploymentAgency',
        'name' => 'Manpower Supply KSA',
        'description' => $page->meta_description,
        'url' => url('/' . $page->slug),
        'telephone' => '+966543088658',
        'areaServed' => $page->city_name ?: 'Saudi Arabia',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Riyadh',
            'addressCountry' => 'SA',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @fonts

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eefdf3',
                            100: '#d6f9e2',
                            200: '#aff0c8',
                            300: '#77e2a7',
                            400: '#3ecb80',
                            500: '#18ad61',
                            600: '#0e8c4d',
                            700: '#0d6f40',
                            800: '#0d5735',
                            900: '#0c482d',
                            950: '#042a19',
                        },
                        ink: '#0c1b14',
                    },
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        html { scroll-behavior: smooth; }
        .hero-bg {
            background:
                radial-gradient(1200px 500px at 15% -10%, rgba(24,173,97,.25), transparent 60%),
                radial-gradient(900px 500px at 100% 0%, rgba(13,111,64,.35), transparent 55%),
                linear-gradient(180deg, #0c1b14 0%, #0d2a1c 60%, #0c1b14 100%);
        }
        .card-hover { transition: transform .25s ease, box-shadow .25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(13,111,64,.25); }
        .whatsapp-float { position: fixed; bottom: 22px; right: 22px; z-index: 50; }
        @media (max-width: 640px) { .whatsapp-float { bottom: 16px; right: 16px; } }
        .prose-content p { margin-bottom: 1rem; line-height: 1.75; color: #4b5563; }
        .prose-content strong { color: #0c1b14; }
    </style>
</head>
<body class="antialiased bg-white text-ink font-sans">

    {{-- ============ NAVBAR (হোমপেজের সাথে হুবহু একই) ============ --}}
    <header class="sticky top-0 z-40 bg-ink/95 backdrop-blur border-b border-white/10">
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-brand-500 flex items-center justify-center font-bold text-white text-sm">MS</div>
                    <div class="leading-tight">
                        <p class="text-white font-semibold text-sm lg:text-base">Manpower Supply KSA</p>
                        <p class="text-brand-300 text-[11px] tracking-wide uppercase">Skilled • Semi-Skilled • Unskilled</p>
                    </div>
                </a>

                <nav class="hidden lg:flex items-center gap-8 text-sm text-white/80">
                    <a href="{{ url('/#about') }}" class="hover:text-white transition">About</a>
                    <a href="{{ url('/#categories') }}" class="hover:text-white transition">Workforce</a>
                    <a href="{{ url('/#nationalities') }}" class="hover:text-white transition">Nationalities</a>
                    <a href="{{ url('/#why-us') }}" class="hover:text-white transition">Why Us</a>
                    <a href="{{ url('/#contact') }}" class="hover:text-white transition">Contact</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="https://wa.me/966543088658" target="_blank"
                       class="hidden sm:inline-flex items-center gap-2 rounded-full bg-brand-500 hover:bg-brand-400 text-white text-sm font-medium px-4 py-2 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.335.105 11.892c0 2.096.548 4.14 1.588 5.945L0 24l6.304-1.654a11.86 11.86 0 0 0 5.737 1.463h.005c6.581 0 11.939-5.335 11.939-11.892 0-3.176-1.24-6.163-3.465-8.468zM12.05 21.786h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.981.999-3.648-.235-.374a9.83 9.83 0 0 1-1.512-5.259c0-5.448 4.437-9.878 9.89-9.878 2.642 0 5.125 1.03 6.99 2.898a9.812 9.812 0 0 1 2.895 6.985c0 5.448-4.437 9.878-9.89 9.878z"/></svg>
                        WhatsApp
                    </a>
                    <a href="{{ url('/#contact') }}" class="lg:hidden inline-flex items-center rounded-full border border-white/20 text-white text-sm font-medium px-4 py-2">
                        Contact
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- ============ HERO (ডাইনামিক কনটেন্ট) ============ --}}
    <section class="hero-bg relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-5 lg:px-8 pt-16 pb-20 lg:pt-24 lg:pb-28 relative">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/15 text-brand-200 text-xs font-medium px-4 py-1.5 mb-6">
                    ✅ Trusted Manpower Partner in Saudi Arabia
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">
                    {{ $page->h1_heading }}
                </h1>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="https://wa.me/966543088658" target="_blank"
                       class="inline-flex items-center gap-2 rounded-full bg-brand-500 hover:bg-brand-400 text-white font-semibold px-6 py-3 transition">
                        Chat on WhatsApp
                    </a>
                    <a href="{{ url('/#contact') }}"
                       class="inline-flex items-center gap-2 rounded-full border border-white/25 hover:border-white/50 text-white font-semibold px-6 py-3 transition">
                        Request Workforce
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ INTRO CONTENT + HIGHLIGHTS ============ --}}
    <section class="max-w-7xl mx-auto px-5 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 prose-content">
                {!! $page->intro_content !!}
            </div>

            @if ($page->highlights)
                <div class="rounded-2xl bg-brand-950 p-8 text-white h-fit">
                    <h3 class="font-semibold text-lg mb-6">Key Highlights</h3>
                    <div class="space-y-3">
                        @foreach ($page->highlights as $highlight)
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-brand-500/30 text-brand-300 flex items-center justify-center text-xs">✓</span>
                                <p class="text-sm text-white/85">{{ $highlight }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ============ WORKFORCE CATEGORIES (হোমপেজের সাথে কনসিস্টেন্ট) ============ --}}
    <section class="bg-gray-50 py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-5 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-brand-600 font-semibold text-sm uppercase tracking-wider">Our Workforce Categories</p>
                <h2 class="mt-2 text-2xl lg:text-3xl font-bold text-ink">90+ Job Categories Available</h2>
            </div>

            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    ['title' => 'General & Security', 'items' => ['Cleaners (M/F)', 'Security Guards', 'Watchman', 'Drivers (Heavy/Light)', 'General Labor']],
                    ['title' => 'Construction & Technical', 'items' => ['Mason', 'Carpenter', 'Steel Fixer', 'Painter', 'Electrician', 'Plumber', 'Welder', 'AC Technician', 'Scaffolder']],
                    ['title' => 'Industrial & Factory', 'items' => ['Production Worker', 'Packaging Worker', 'Factory Helper', 'Mechanic', 'Forklift Operator', 'Warehouse Staff', 'Storekeeper']],
                    ['title' => 'Driving & Transport', 'items' => ['Heavy Driver', 'Light Driver', 'Heavy Equipment Operator', 'Forklift Operator']],
                    ['title' => 'Domestic Staff', 'items' => ['Housemaid', 'Nanny/Babysitter', 'Cook (Home)', 'Caregiver', 'House Driver', 'Gardener']],
                    ['title' => 'Hospitality & Food', 'items' => ['Waiter/Waitress', 'Chef', 'Cook (Commercial)', 'Kitchen Helper', 'Barista', 'Baker', 'Butler', 'Cashier']],
                    ['title' => 'Medical & Caregiving', 'items' => ['Nursing Aide', 'Caregiver (Elderly/Patient)', 'Hospital Attendant', 'Hospital Cleaner']],
                    ['title' => 'Retail, Agriculture & Others', 'items' => ['Salesman', 'Shop Assistant', 'Farm Worker', 'Livestock Handler', 'Tailor', 'Landscaper', 'Laundry Worker']],
                ] as $group)
                    <div class="card-hover rounded-2xl bg-white border border-gray-200 p-6">
                        <h3 class="font-semibold text-ink mb-4">{{ $group['title'] }}</h3>
                        <ul class="space-y-2">
                            @foreach ($group['items'] as $item)
                                <li class="text-sm text-gray-600 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CONTACT / CTA (হোমপেজের সাথে হুবহু একই) ============ --}}
    <section id="contact" class="max-w-7xl mx-auto px-5 lg:px-8 py-16 lg:py-24">
        <div class="rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 overflow-hidden">
            <div class="grid lg:grid-cols-2">
                <div class="p-8 lg:p-14 text-white">
                    <h2 class="text-2xl lg:text-3xl font-bold">Need Manpower? Let's Talk Today.</h2>
                    <p class="mt-3 text-white/85">We support both long-term projects and urgent daily requirements for companies across KSA.</p>

                    <div class="mt-8 space-y-4">
                        <a href="https://wa.me/966543088658" target="_blank" class="flex items-center gap-3 group">
                            <span class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center">📱</span>
                            <span class="text-sm">
                                <span class="block text-white/70">WhatsApp</span>
                                <span class="font-semibold group-hover:underline">0543088658</span>
                            </span>
                        </a>
                        <a href="mailto:helaluddinbd6320@gmail.com" class="flex items-center gap-3 group">
                            <span class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center">✉️</span>
                            <span class="text-sm">
                                <span class="block text-white/70">Email</span>
                                <span class="font-semibold group-hover:underline">helaluddinbd6320@gmail.com</span>
                            </span>
                        </a>
                    </div>

                    <a href="https://wa.me/966543088658" target="_blank"
                       class="mt-10 inline-flex items-center gap-2 rounded-full bg-white text-brand-700 font-semibold px-6 py-3 hover:bg-brand-50 transition">
                        Request Workforce Now →
                    </a>
                </div>

                <div class="hidden lg:flex items-center justify-center bg-brand-900/40 p-14">
                    <div class="text-white/80 text-sm leading-relaxed max-w-xs">
                        <p class="text-white font-semibold text-lg mb-3">Manpower Supply KSA</p>
                        <p>Skilled • Semi-Skilled • Unskilled workforce — verified, compliant, and deployed fast, anywhere in Saudi Arabia.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ RELATED PAGES (SEO ইন্টারনাল লিংকিং) ============ --}}
    @if ($relatedPages->isNotEmpty())
        <section class="max-w-7xl mx-auto px-5 lg:px-8 pb-16 lg:pb-24">
            <h3 class="text-lg font-semibold text-ink mb-5">Explore More Locations & Services</h3>
            <div class="flex flex-wrap gap-3">
                @foreach ($relatedPages as $related)
                    <a href="{{ url('/' . $related->slug) }}"
                       class="rounded-full border border-gray-200 hover:border-brand-400 hover:bg-brand-50 text-sm text-gray-700 px-5 py-2 transition">
                        {{ $related->h1_heading }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ FOOTER ============ --}}
    <footer class="bg-ink border-t border-white/10">
        <div class="max-w-7xl mx-auto px-5 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-white/50 text-xs">&copy; {{ date('Y') }} Manpower Supply KSA. All rights reserved.</p>
            <p class="text-white/40 text-xs">Riyadh, KSA &middot; Serving All Over Saudi Arabia</p>
        </div>
    </footer>

    <a href="https://wa.me/966543088658" target="_blank" class="whatsapp-float w-14 h-14 rounded-full bg-brand-500 hover:bg-brand-400 shadow-xl flex items-center justify-center transition">
        <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.335.105 11.892c0 2.096.548 4.14 1.588 5.945L0 24l6.304-1.654a11.86 11.86 0 0 0 5.737 1.463h.005c6.581 0 11.939-5.335 11.939-11.892 0-3.176-1.24-6.163-3.465-8.468zM12.05 21.786h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.981.999-3.648-.235-.374a9.83 9.83 0 0 1-1.512-5.259c0-5.448 4.437-9.878 9.89-9.878 2.642 0 5.125 1.03 6.99 2.898a9.812 9.812 0 0 1 2.895 6.985c0 5.448-4.437 9.878-9.89 9.878z"/></svg>
    </a>

</body>
</html>