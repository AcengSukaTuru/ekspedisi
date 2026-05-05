<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sistem Informasi Ekspedisi Online</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            display: ['Space Grotesk', 'sans-serif'],
                            body: ['Manrope', 'sans-serif'],
                        },
                        colors: {
                            accent: '#d81f32',
                            ink: '#111111',
                            fog: '#f4f5f7',
                        },
                    },
                },
            };
        </script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-fog font-body text-ink">
        @php
            $cityBlocks = [28, 42, 35, 30, 50, 26, 34, 24, 46, 38, 28, 52, 30, 40, 24, 36, 29, 47, 33, 27, 44, 31, 39, 22];
            $serviceData = [
                'road' => [
                    'title' => 'Road Freight',
                    'desc' => 'Fast and flexible ground transportation for your cargo to any destination across the country safely.',
                    'image' => 'https://images.pexels.com/photos/2199293/pexels-photo-2199293.jpeg?auto=compress&cs=tinysrgb&w=1200',
                ],
                'warehouse' => [
                    'title' => 'Warehousing',
                    'desc' => 'Secure warehousing and inventory control with real-time stock visibility to keep your shipments on schedule.',
                    'image' => 'https://images.pexels.com/photos/4481327/pexels-photo-4481327.jpeg?auto=compress&cs=tinysrgb&w=1200',
                ],
                'door' => [
                    'title' => 'Door-to-Door Delivery',
                    'desc' => 'From pickup location directly to destination address, we handle the whole route with live tracking updates.',
                    'image' => 'https://images.pexels.com/photos/4246120/pexels-photo-4246120.jpeg?auto=compress&cs=tinysrgb&w=1200',
                ],
                'ocean' => [
                    'title' => 'Ocean Freight',
                    'desc' => 'Reliable sea shipping for high-volume goods, supported by route planning and transparent document handling.',
                    'image' => 'https://images.pexels.com/photos/262353/pexels-photo-262353.jpeg?auto=compress&cs=tinysrgb&w=1200',
                ],
            ];
        @endphp

        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_85%_5%,rgba(216,31,50,0.12),transparent_40%)]"></div>

            <header class="relative z-10 mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between border border-zinc-200 bg-zinc-100/95 px-4 py-3">
                    <a href="#" class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-sm bg-black text-xs font-bold text-white">S</span>
                        <span class="font-display text-sm font-bold tracking-wide">SHIPIT</span>
                    </a>

                    <nav class="hidden items-center gap-6 text-sm font-medium text-zinc-600 md:flex">
                        <a href="#about" class="hover:text-black">About</a>
                        <a href="#services" class="hover:text-black">Services</a>
                        <a href="#pricing" class="hover:text-black">Pricing</a>
                        <a href="#faq" class="hover:text-black">FAQ</a>
                    </nav>

                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center border border-zinc-200 bg-white text-zinc-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M4 7h16"></path>
                            <path d="M9 12h11"></path>
                            <path d="M4 17h16"></path>
                        </svg>
                    </button>
                </div>
            </header>

            <main class="relative z-10">
                <section class="mx-auto mt-10 max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="grid items-center gap-10 lg:grid-cols-[1fr_1.1fr]">
                        <div>
                            <h1 class="font-display text-5xl font-bold leading-[0.95] sm:text-6xl">
                                We Ship It.<br>
                                You Relax.
                            </h1>
                            <p class="mt-5 max-w-lg text-base leading-7 text-zinc-600">
                                Say goodbye to logistics headaches. Sistem ekspedisi kita bantu admin, customer, dan kurir bekerja cepat dalam satu alur yang jelas.
                            </p>

                            <div class="mt-7 flex flex-wrap gap-3">
                                <a href="{{ route('register') }}" class="inline-flex items-center bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#bf1828]">
                                    Ship With Us
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center border border-zinc-300 bg-white px-5 py-3 text-sm font-semibold text-zinc-800 transition hover:bg-zinc-100">
                                    Login Dashboard
                                </a>
                            </div>
                        </div>

                        <div class="relative rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-8">
                            <div class="absolute -right-3 -top-3 rounded-xl bg-black px-3 py-2 text-xs font-semibold text-white">Live Tracking</div>
                            <div class="relative h-[320px] overflow-hidden rounded-2xl bg-zinc-100 p-6">
                                <div class="absolute inset-x-5 top-8 h-40 rounded-[2rem] bg-zinc-200"></div>
                                <div class="absolute inset-x-8 top-10 grid grid-cols-6 gap-2">
                                    @foreach ($cityBlocks as $height)
                                        <div class="rounded-sm bg-zinc-400" style="height: {{ $height }}px"></div>
                                    @endforeach
                                </div>
                                <div class="absolute left-6 right-20 top-52 h-1 rounded-full bg-accent"></div>
                                <div class="absolute left-6 top-[198px] h-6 w-6 rounded-full bg-accent/30 ring-4 ring-accent/20"></div>
                                <div class="absolute left-8 top-[200px] h-2 w-2 rounded-full bg-accent"></div>
                                <div class="absolute right-16 top-[197px] h-3 w-3 rounded-full bg-accent"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="about" class="mx-auto mt-20 max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-8 lg:grid-cols-[0.9fr_1fr_0.7fr]">
                        <div class="space-y-5">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">About Us</div>
                            <h2 class="font-display text-4xl font-bold leading-tight">Logistics Without the Headaches</h2>
                            <p class="text-zinc-600">
                                Forget complicated logistics nightmares. We handle the heavy lifting so you can focus on growing your business.
                            </p>
                            <div class="rounded-xl border border-zinc-200 bg-white p-4 text-sm text-zinc-600">
                                <p class="font-semibold text-zinc-900">No fluff, no fuss.</p>
                                <p class="mt-1">Paket kamu diproses cepat, tracking jelas, status pembayaran transparan.</p>
                            </div>
                            <a href="{{ route('login') }}" class="inline-flex bg-black px-4 py-2 text-sm font-semibold text-white">Learn More</a>
                        </div>

                        <div class="relative overflow-hidden border border-zinc-200 bg-white p-3">
                            <img
                                src="https://images.pexels.com/photos/4246120/pexels-photo-4246120.jpeg?auto=compress&cs=tinysrgb&w=1200"
                                alt="Courier with shipment packages"
                                class="h-full min-h-[300px] w-full object-cover"
                            >
                        </div>

                        <div class="grid gap-3">
                            <div class="border border-zinc-200 bg-white p-5">
                                <p class="text-3xl font-extrabold">576+</p>
                                <p class="mt-1 text-sm text-zinc-500">Project Completed</p>
                            </div>
                            <div class="border border-zinc-200 bg-white p-5">
                                <p class="text-3xl font-extrabold">687+</p>
                                <p class="mt-1 text-sm text-zinc-500">Happy Customers</p>
                            </div>
                            <div class="border border-zinc-200 bg-white p-5">
                                <p class="text-3xl font-extrabold">890+</p>
                                <p class="mt-1 text-sm text-zinc-500">Delivered In Time</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="services" class="mx-auto mt-20 max-w-6xl px-4 sm:px-6 lg:px-8" x-data="{ service: 'road' }">
                    <div class="grid gap-6 md:grid-cols-[1fr_0.8fr]">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Services</div>
                            <h2 class="mt-3 font-display text-5xl font-bold leading-[0.95]">Every Shipping Solution You Need</h2>
                        </div>
                        <div class="md:pl-8">
                            <p class="text-zinc-600">
                                Air, sea, road, rail, you name it, we ship it. We build our services around what your business actually needs.
                            </p>
                            <a href="{{ route('register') }}" class="mt-5 inline-flex bg-black px-4 py-2 text-sm font-semibold text-white">View All Services</a>
                        </div>
                    </div>

                    <div class="mt-8 border border-zinc-200 bg-white">
                        <div class="grid grid-cols-2 border-b border-zinc-200 text-sm font-semibold text-zinc-600 sm:grid-cols-4">
                            <button type="button" @click="service = 'road'" :class="service === 'road' ? 'border-b-2 border-accent text-ink' : ''" class="px-4 py-3 text-left">Road Freight</button>
                            <button type="button" @click="service = 'warehouse'" :class="service === 'warehouse' ? 'border-b-2 border-accent text-ink' : ''" class="px-4 py-3 text-left">Warehousing</button>
                            <button type="button" @click="service = 'door'" :class="service === 'door' ? 'border-b-2 border-accent text-ink' : ''" class="px-4 py-3 text-left">Door-to-Door</button>
                            <button type="button" @click="service = 'ocean'" :class="service === 'ocean' ? 'border-b-2 border-accent text-ink' : ''" class="px-4 py-3 text-left">Ocean Freight</button>
                        </div>

                        <div class="grid gap-6 p-5 lg:grid-cols-[1fr_0.9fr]">
                            @foreach ($serviceData as $key => $service)
                                <div x-show="service === '{{ $key }}'" x-cloak>
                                    <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}" class="h-60 w-full border border-zinc-200 object-cover">
                                </div>
                            @endforeach

                            <div>
                                @foreach ($serviceData as $key => $service)
                                    <div x-show="service === '{{ $key }}'" x-cloak>
                                        <h3 class="font-display text-3xl font-bold">{{ $service['title'] }}</h3>
                                        <p class="mt-3 text-zinc-600">{{ $service['desc'] }}</p>
                                        <div class="mt-5 border-t border-zinc-200 pt-4 text-sm text-zinc-500">
                                            Fast response team, clear timeline, and easy monitoring for every shipment.
                                        </div>
                                        <a href="{{ route('login') }}" class="mt-5 inline-flex bg-black px-4 py-2 text-sm font-semibold text-white">Let's Work Together</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-20 bg-[#e7eaee] py-16">
                    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Benefits</div>
                            <h2 class="mt-2 font-display text-5xl font-bold leading-[0.95]">Perks of Shipping With Us</h2>
                            <p class="mt-4 text-zinc-600">
                                Ketika kirim pakai platform kita, semuanya lebih teratur. Real-time, aman, dan bisa dipantau dari dashboard.
                            </p>
                        </div>

                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="border border-zinc-200 bg-white p-4">
                                <p class="text-sm font-semibold text-accent">1 • 24/7 Customer Support</p>
                                <p class="mt-2 text-sm text-zinc-600">Get help anytime, day or night.</p>
                            </div>
                            <div class="border border-zinc-200 bg-white p-4">
                                <p class="text-sm font-semibold text-accent">2 • Competitive Pricing</p>
                                <p class="mt-2 text-sm text-zinc-600">Affordable rates with no hidden fees.</p>
                            </div>
                            <div class="border border-zinc-200 bg-white p-4">
                                <p class="text-sm font-semibold text-accent">3 • On-Time Delivery</p>
                                <p class="mt-2 text-sm text-zinc-600">Your packages arrive on time.</p>
                            </div>
                            <div class="border border-zinc-200 bg-white p-4">
                                <p class="text-sm font-semibold text-accent">4 • Global Network</p>
                                <p class="mt-2 text-sm text-zinc-600">Ship to over 200 countries worldwide.</p>
                            </div>
                        </div>

                        <div class="mt-8 overflow-hidden border border-zinc-300 bg-white">
                            <img
                                src="https://images.pexels.com/photos/358319/pexels-photo-358319.jpeg?auto=compress&cs=tinysrgb&w=1600"
                                alt="Cargo airplane"
                                class="h-72 w-full object-cover"
                            >
                        </div>
                    </div>
                </section>

                <section class="relative mt-0">
                    <img
                        src="https://images.pexels.com/photos/2199293/pexels-photo-2199293.jpeg?auto=compress&cs=tinysrgb&w=1800"
                        alt="Truck on road"
                        class="h-[360px] w-full object-cover"
                    >
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 mx-auto flex max-w-6xl items-end px-4 pb-10 sm:px-6 lg:px-8">
                        <div class="max-w-xl text-white">
                            <p class="font-display text-6xl leading-none">“</p>
                            <p class="mt-2 text-3xl font-extrabold leading-tight">
                                Switching to this company was the best decision for my business.
                            </p>
                            <p class="mt-4 text-white/85">
                                My packages always arrive on time and my customers are happy.
                            </p>
                            <p class="mt-6 text-sm font-semibold uppercase tracking-[0.2em]">John Carter • Satisfied Client</p>
                        </div>
                    </div>
                </section>

                <section class="mx-auto mt-16 max-w-6xl px-4 sm:px-6 lg:px-8">
                    <h2 class="text-center font-display text-5xl font-bold leading-[0.95]">3 Easy Steps To Deliver</h2>
                    <div class="mt-10 grid gap-6 text-center md:grid-cols-3">
                        <div class="relative border border-zinc-200 bg-white p-6">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-accent text-white">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 7h18"></path>
                                    <path d="M6 7v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7"></path>
                                    <path d="M9 12h6"></path>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-bold text-accent">Book Your Shipment</h3>
                            <p class="mt-2 text-sm text-zinc-600">Enter your package details and destination.</p>
                        </div>
                        <div class="border border-zinc-200 bg-white p-6">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-zinc-100 text-accent">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 16h13"></path>
                                    <path d="M3 8h10"></path>
                                    <path d="M16 8h5v8h-5"></path>
                                    <circle cx="8" cy="18" r="2"></circle>
                                    <circle cx="18" cy="18" r="2"></circle>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-bold">We Pick Up & Ship</h3>
                            <p class="mt-2 text-sm text-zinc-600">We collect and transport your package.</p>
                        </div>
                        <div class="border border-zinc-200 bg-white p-6">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-zinc-100 text-accent">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 7h16"></path>
                                    <path d="M7 7v10h10V7"></path>
                                    <path d="M9 11h6"></path>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-bold">Receive Your Package</h3>
                            <p class="mt-2 text-sm text-zinc-600">Your package arrives safely at destination.</p>
                        </div>
                    </div>
                </section>

                <section id="pricing" class="mx-auto mt-16 max-w-6xl px-4 pb-16 sm:px-6 lg:px-8" x-data="{ billing: 'monthly' }">
                    <div class="grid gap-6 md:grid-cols-[1fr_0.8fr]">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Pricing</div>
                            <h2 class="mt-3 font-display text-5xl font-bold leading-[0.95]">Pick a Plan, Start Shipping</h2>
                        </div>
                        <p class="md:pl-8 text-zinc-600">
                            No hidden surprises. Just straightforward pricing that makes sense for businesses like yours.
                        </p>
                    </div>

                    <div class="mt-8 border border-zinc-200 bg-white p-5">
                        <div class="mx-auto mb-5 inline-flex border border-zinc-200 bg-zinc-100 p-1 text-sm font-semibold">
                            <button type="button" @click="billing = 'monthly'" :class="billing === 'monthly' ? 'bg-white text-ink' : 'text-zinc-600'" class="px-4 py-1.5">Monthly</button>
                            <button type="button" @click="billing = 'yearly'" :class="billing === 'yearly' ? 'bg-white text-ink' : 'text-zinc-600'" class="px-4 py-1.5">Yearly</button>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-[1fr_1fr]">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between border border-zinc-200 bg-white px-4 py-4 text-sm">
                                    <span class="font-semibold">Basic Plan</span>
                                    <span class="font-extrabold">$<span x-text="billing === 'monthly' ? '49' : '39'"></span>/month</span>
                                </div>
                                <div class="flex items-center justify-between border border-accent bg-accent px-4 py-4 text-sm text-white">
                                    <span class="font-semibold">Professional Plan</span>
                                    <span class="font-extrabold">$<span x-text="billing === 'monthly' ? '149' : '129'"></span>/month</span>
                                </div>
                                <div class="flex items-center justify-between border border-zinc-200 bg-white px-4 py-4 text-sm">
                                    <span class="font-semibold">Enterprise Plan</span>
                                    <span class="font-extrabold">Custom</span>
                                </div>
                            </div>

                            <div class="border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-600">
                                <p class="font-semibold text-zinc-900">Includes:</p>
                                <ul class="mt-3 space-y-3">
                                    <li class="flex items-center justify-between border-b border-zinc-200 pb-2"><span>Up to 300 shipments per month</span><span class="text-accent">✓</span></li>
                                    <li class="flex items-center justify-between border-b border-zinc-200 pb-2"><span>All delivery options</span><span class="text-accent">✓</span></li>
                                    <li class="flex items-center justify-between border-b border-zinc-200 pb-2"><span>Priority support 24/7</span><span class="text-accent">✓</span></li>
                                    <li class="flex items-center justify-between"><span>Free warehousing (limited)</span><span class="text-accent">✓</span></li>
                                </ul>
                                <a href="{{ route('register') }}" class="mt-5 inline-flex bg-black px-4 py-2 font-semibold text-white">Choose Plan</a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-black py-14">
                    <div class="mx-auto grid max-w-6xl items-center gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                        <div class="text-white">
                            <h2 class="font-display text-5xl font-bold leading-[0.95]">Thousands of companies trust us to deliver safely and on time.</h2>
                        </div>
                        <img
                            src="https://images.pexels.com/photos/262353/pexels-photo-262353.jpeg?auto=compress&cs=tinysrgb&w=1200"
                            alt="Container shipping"
                            class="h-60 w-full border border-zinc-700 object-cover"
                        >
                    </div>

                    <div class="mx-auto mt-10 grid max-w-6xl grid-cols-2 border-y border-zinc-800 text-center text-sm font-semibold text-zinc-300 sm:grid-cols-5">
                        <div class="border-r border-zinc-800 py-4">Base</div>
                        <div class="border-r border-zinc-800 py-4">Pixelone</div>
                        <div class="border-r border-zinc-800 py-4">Lift</div>
                        <div class="border-r border-zinc-800 py-4">ShipIT</div>
                        <div class="py-4">Siane</div>
                    </div>
                </section>

                <section class="mx-auto mt-16 max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Blog</div>
                            <h2 class="mt-2 font-display text-5xl font-bold leading-[0.95]">Learn More About Logistics</h2>
                            <p class="mt-4 text-zinc-600">
                                Discover tips, best practices, and insights to help you ship smarter and save money.
                            </p>
                            <a href="{{ route('login') }}" class="mt-5 inline-flex bg-black px-4 py-2 text-sm font-semibold text-white">View Articles</a>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <article class="border border-zinc-200 bg-white p-3">
                                <img src="https://images.pexels.com/photos/262353/pexels-photo-262353.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Article 1" class="h-44 w-full object-cover">
                                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-accent">Cost Saving • May 21, 2026</p>
                                <h3 class="mt-2 text-lg font-bold">How To Reduce Your Shipping Costs Without Sacrificing Quality</h3>
                            </article>
                            <article class="border border-zinc-200 bg-white p-3">
                                <img src="https://images.pexels.com/photos/262353/pexels-photo-262353.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Article 2" class="h-44 w-full object-cover">
                                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-accent">Shipping • May 30, 2026</p>
                                <h3 class="mt-2 text-lg font-bold">International Shipping Made Simple: A Beginner's Guide</h3>
                            </article>
                        </div>
                    </div>
                </section>

                <section id="faq" class="mt-16 bg-[#e7eaee] py-16" x-data="{ open: 1 }">
                    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                        <div class="text-center">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">FAQ</div>
                            <h2 class="mt-2 font-display text-5xl font-bold leading-[0.95]">Frequently Asked Questions</h2>
                        </div>

                        <div class="mt-8 space-y-3">
                            <div class="border border-zinc-300 bg-white">
                                <button type="button" @click="open = open === 1 ? 0 : 1" class="flex w-full items-center justify-between px-4 py-3 text-left font-semibold">How long does shipping take? <span>+</span></button>
                                <div x-show="open === 1" x-cloak class="border-t border-zinc-200 px-4 py-3 text-sm text-zinc-600">Biasanya 1-3 hari kerja untuk rute reguler antar kota besar, tergantung jenis layanan yang dipilih.</div>
                            </div>
                            <div class="border border-zinc-300 bg-white">
                                <button type="button" @click="open = open === 2 ? 0 : 2" class="flex w-full items-center justify-between px-4 py-3 text-left font-semibold">Do you ship internationally? <span>+</span></button>
                                <div x-show="open === 2" x-cloak class="border-t border-zinc-200 px-4 py-3 text-sm text-zinc-600">Ya, ocean freight dan layanan tertentu mendukung pengiriman internasional dengan dokumen lengkap.</div>
                            </div>
                            <div class="border border-zinc-300 bg-white">
                                <button type="button" @click="open = open === 3 ? 0 : 3" class="flex w-full items-center justify-between px-4 py-3 text-left font-semibold">What if my package is damaged or lost? <span>+</span></button>
                                <div x-show="open === 3" x-cloak class="border-t border-zinc-200 px-4 py-3 text-sm text-zinc-600">Tim support akan bantu investigasi cepat dan proses claim sesuai ketentuan layanan.</div>
                            </div>
                            <div class="border border-zinc-300 bg-white">
                                <button type="button" @click="open = open === 4 ? 0 : 4" class="flex w-full items-center justify-between px-4 py-3 text-left font-semibold">Can I change my delivery address after shipping? <span>+</span></button>
                                <div x-show="open === 4" x-cloak class="border-t border-zinc-200 px-4 py-3 text-sm text-zinc-600">Bisa, selama paket belum masuk status out for delivery. Perubahan bisa diajukan lewat dashboard admin.</div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="relative mt-0 overflow-hidden bg-black text-white">
                <img
                    src="https://images.pexels.com/photos/6169028/pexels-photo-6169028.jpeg?auto=compress&cs=tinysrgb&w=1800"
                    alt="Containers on sea"
                    class="h-60 w-full object-cover opacity-75"
                >
                <div class="absolute inset-0 bg-black/45"></div>
                <div class="absolute inset-0 mx-auto flex max-w-6xl flex-col justify-center px-4 sm:px-6 lg:px-8">
                    <h3 class="font-display text-4xl font-bold leading-tight">Get tips, offers, and shipping news</h3>
                    <div class="mt-6 flex w-full max-w-xl flex-col gap-3 sm:flex-row">
                        <input type="email" placeholder="Enter your email" class="flex-1 border border-white/30 bg-white/15 px-4 py-3 text-sm placeholder:text-white/70 focus:outline-none">
                        <button class="bg-white px-5 py-3 text-sm font-semibold text-black">Subscribe</button>
                    </div>
                </div>

                <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">
                    <div>
                        <p class="font-display text-xl font-bold">SHIPIT</p>
                        <p class="mt-3 max-w-sm text-sm text-zinc-400">
                            Shipping should not be complicated. Kami fokus pada hal-hal yang bikin kerja tim jadi lebih cepat dan pelanggan makin puas.
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.15em] text-zinc-400">Pages</p>
                        <ul class="mt-3 space-y-2 text-sm text-zinc-300">
                            <li><a href="#about" class="hover:text-white">About Us</a></li>
                            <li><a href="#services" class="hover:text-white">Services</a></li>
                            <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                            <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.15em] text-zinc-400">Quick Access</p>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="border border-zinc-700 px-3 py-2 text-sm font-semibold text-zinc-200 hover:bg-zinc-900">Login</a>
                            <a href="{{ route('register') }}" class="bg-accent px-3 py-2 text-sm font-semibold text-white hover:bg-[#bf1828]">Register</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
