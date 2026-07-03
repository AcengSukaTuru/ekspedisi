<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Detail Shipment</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $shipment->tracking_number }}</p>
            </div>
            <a href="{{ route($backRoute) }}" class="btn-secondary btn-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @include('partials.flash-message')

        {{-- Status Progress Bar --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Status Pengiriman</h3>
            @php
                $progressStatuses = ['created', 'picked_up', 'at_origin_hub', 'in_transit', 'at_dest_hub', 'out_for_delivery', 'delivered'];
                $currentIndex = array_search($shipment->status, $progressStatuses);
                $isFailed = in_array($shipment->status, ['failed_delivery', 'returned_to_sender']);
            @endphp
            <div class="mt-5 flex items-center gap-1 overflow-x-auto pb-2">
                @foreach ($progressStatuses as $i => $ps)
                    @php
                        $reached = $currentIndex !== false && $i <= $currentIndex && ! $isFailed;
                        $isCurrent = $shipment->status === $ps && ! $isFailed;
                    @endphp
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div class="step-dot {{ $isCurrent ? 'step-dot-active' : ($reached ? 'step-dot-done' : 'step-dot-pending') }}">
                                @if ($reached && ! $isCurrent)
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <p class="mt-1 w-16 text-center text-[10px] leading-tight {{ $isCurrent ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-600' }}">
                                {{ $statusLabels[$ps] ?? $ps }}
                            </p>
                        </div>
                        @if (! $loop->last)
                            <div class="step-line {{ $reached && ! $isCurrent ? 'step-line-done' : 'step-line-pending' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if ($isFailed)
                <div class="mt-4 rounded-xl {{ $shipment->status === 'returned_to_sender' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400' }} p-3 text-sm font-semibold">
                    {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                    @if ($shipment->rts_reason)
                        <span class="font-normal"> &mdash; {{ $shipment->rts_reason }}</span>
                    @endif
                </div>
            @endif
            @if ($shipment->status === 'failed_delivery')
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Percobaan: {{ $shipment->failedAttemptCount() }} gagal dari {{ $shipment->max_delivery_attempts }} maks &middot;
                    Sisa: {{ $shipment->remainingAttempts() }} percobaan
                </p>
            @endif
        </div>

        {{-- Info Shipment + Payment --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card p-6 lg:col-span-2">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Informasi Shipment</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    @php
                        $fields = [
                            ['label' => 'Customer', 'value' => $shipment->customer?->name],
                            ['label' => 'Status', 'html' => '<span class="badge '.$shipment->statusColor().'">'.$shipment->statusLabel().'</span>'],
                            ['label' => 'Asal', 'value' => $shipment->originBranch?->branch_name.' - '.$shipment->originBranch?->city],
                            ['label' => 'Tujuan', 'value' => $shipment->destinationBranch?->branch_name.' - '.$shipment->destinationBranch?->city],
                            ['label' => 'Layanan', 'value' => ucfirst($shipment->service_type)],
                            ['label' => 'Tipe Pickup', 'html' => $shipment->pickup_type === 'pickup_request'
                                ? '<span class="badge bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Request Pickup</span>'
                                : '<span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Drop-off</span>'],
                            ['label' => 'Kendaraan', 'value' => $shipment->activeAssignment?->vehicle?->plate_number ?? $shipment->vehicle?->plate_number ?? '-'],
                            ['label' => 'Total Berat', 'value' => number_format((float) $shipment->total_weight, 2, ',', '.').' Kg'],
                            ['label' => 'Biaya', 'value' => 'Rp '.number_format((float) $shipment->shipping_cost, 0, ',', '.')],
                            ['label' => 'Tanggal Kirim', 'value' => optional($shipment->shipment_date)->format('d M Y')],
                            ['label' => 'Estimasi Tiba', 'value' => optional($shipment->estimated_arrival)->format('d M Y')],
                        ];
                    @endphp
                    @foreach ($fields as $f)
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $f['label'] }}</p>
                            <p class="mt-0.5 text-sm font-medium text-slate-900 dark:text-white">{!! $f['html'] ?? e($f['value']) !!}</p>
                        </div>
                    @endforeach
                </div>
                @if ($shipment->pickup_type === 'pickup_request' && $shipment->pickup_address)
                    <div class="mt-4 rounded-xl bg-blue-50 p-4 text-sm dark:bg-blue-900/20">
                        <p class="font-semibold text-blue-800 dark:text-blue-300">Info Penjemputan</p>
                        <p class="mt-1 text-blue-700 dark:text-blue-400">{{ $shipment->pickup_address }}</p>
                        @if ($shipment->pickup_contact_name)
                            <p class="text-blue-600 dark:text-blue-500">{{ $shipment->pickup_contact_name }} - {{ $shipment->pickup_contact_phone }}</p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="card p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Pembayaran</h3>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400">Tipe</p>
                        @if ($shipment->payment?->isCod())
                            <span class="badge bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 mt-1">COD</span>
                        @else
                            <span class="badge bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 mt-1">Prepaid</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400">Status</p>
                        @php $payStatus = $shipment->payment?->payment_status ?? 'pending'; @endphp
                        <span class="badge {{ match($payStatus) { 'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' } }} mt-1">
                            {{ $shipment->payment?->statusLabel() ?? 'Menunggu' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400">Nominal</p>
                        <p class="font-bold text-slate-900 dark:text-white">Rp {{ number_format((float) ($shipment->payment?->amount ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400">Metode</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $shipment->payment?->payment_method ?? '-' }}</p>
                    </div>
                    @if ($shipment->payment?->isCod() && $shipment->payment?->isCodCollected())
                        <div class="rounded-xl bg-emerald-50 p-3 text-xs text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                            COD dikumpulkan oleh {{ $shipment->payment->collectedBy?->name }} &middot; {{ optional($shipment->payment->cod_collected_at)->format('d M Y H:i') }}
                        </div>
                    @endif
                    @if ($shipment->payment && (auth()->user()->isAdmin() || auth()->user()->isCustomer()))
                        @php
                            $payRoute = auth()->user()->isAdmin()
                                ? route('admin.payments.show', $shipment->payment)
                                : route('customer.payments.show', $shipment->payment);
                        @endphp
                        <a href="{{ $payRoute }}" class="inline-block text-sm font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400">Detail Payment &rarr;</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Assignment Kurir --}}
        @if (auth()->user()->isAdmin() || auth()->user()->isCourier())
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Assignment Kurir</h3>
                    @if ($shipment->activeAssignment)
                        <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Aktif</span>
                    @else
                        <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Belum Ada</span>
                    @endif
                </div>

                @if ($shipment->activeAssignment)
                    @php $active = $shipment->activeAssignment; @endphp
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        @foreach ([['Kurir', $active->courier?->name], ['Kendaraan', ($active->vehicle?->plate_number ?? '-').($active->vehicle?->driver_name ? ' &middot; '.$active->vehicle->driver_name : '')], ['Ditugaskan', optional($active->assigned_at)->format('d M Y H:i').'<br><span class="text-xs text-slate-400">oleh '.e($active->assignedBy?->name ?? '-').'</span>']] as [$label, $val])
                            <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $label }}</p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-900 dark:text-white">{!! $val !!}</p>
                            </div>
                        @endforeach
                    </div>
                    @if ($active->notes)
                        <div class="mt-3 rounded-xl bg-amber-50 p-3 text-sm text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                            <span class="font-semibold">Catatan:</span> {{ $active->notes }}
                        </div>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <div class="mt-4 flex flex-wrap gap-2" x-data="{ showReassign: false, showCancel: false }">
                            <button @click="showReassign = !showReassign" class="btn-secondary btn-sm">Reassign</button>
                            <button @click="showCancel = !showCancel" class="btn-ghost btn-sm text-red-600 dark:text-red-400">Cancel</button>
                            <div x-show="showReassign" x-cloak class="mt-3 w-full rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                                <h4 class="text-sm font-bold text-blue-900 dark:text-blue-300">Reassign ke Kurir Lain</h4>
                                <form method="POST" action="{{ route('admin.assignments.reassign', $active) }}" class="mt-3 grid gap-3 md:grid-cols-3">
                                    @csrf @method('PATCH')
                                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Kurir Baru</label><select name="courier_id" class="select" required><option value="">Pilih</option>@foreach ($availableCouriers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Kendaraan</label><select name="vehicle_id" class="select" required><option value="">Pilih</option>@foreach ($availableVehicles as $v)<option value="{{ $v->id }}">{{ $v->plate_number }}</option>@endforeach</select></div>
                                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Alasan</label><input name="notes" type="text" class="input" placeholder="Alasan..." required></div>
                                    <div class="md:col-span-3"><button type="submit" class="btn-primary btn-sm">Konfirmasi</button></div>
                                </form>
                            </div>
                            <div x-show="showCancel" x-cloak class="mt-3 w-full rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                                <h4 class="text-sm font-bold text-red-900 dark:text-red-300">Batalkan Assignment</h4>
                                <form method="POST" action="{{ route('admin.assignments.cancel', $active) }}" class="mt-3 flex gap-3">
                                    @csrf @method('PATCH')
                                    <input name="notes" type="text" class="input flex-1" placeholder="Alasan pembatalan..." required>
                                    <button type="submit" class="btn-danger btn-sm">Batalkan</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @elseif (auth()->user()->isAdmin())
                    <div x-data="{ show: false }" class="mt-4">
                        <button @click="show = !show" class="btn-primary btn-sm">Assign Kurir & Kendaraan</button>
                        <div x-show="show" x-cloak class="mt-4 rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                            <form method="POST" action="{{ route('admin.shipments.assign', $shipment) }}" class="grid gap-3 md:grid-cols-3">
                                @csrf
                                <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Kurir</label><select name="courier_id" class="select" required><option value="">Pilih</option>@foreach ($availableCouriers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                                <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Kendaraan</label><select name="vehicle_id" class="select" required><option value="">Pilih</option>@foreach ($availableVehicles as $v)<option value="{{ $v->id }}">{{ $v->plate_number }} - {{ $v->driver_name }}</option>@endforeach</select></div>
                                <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Catatan</label><input name="notes" type="text" class="input" placeholder="Opsional"></div>
                                <div class="md:col-span-3"><button type="submit" class="btn-primary btn-sm">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Delivery Attempts --}}
        @if ($shipment->deliveryAttempts->count() > 0 || $shipment->status === 'out_for_delivery')
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Pengiriman</h3>
                    @if (auth()->user()->isCourier() && $shipment->status === 'out_for_delivery' && $shipment->canAttemptDelivery())
                        <a href="{{ route('courier.delivery-attempts.create', $shipment) }}" class="btn-primary btn-sm">Catat Hasil Antaran</a>
                    @endif
                </div>
                @if ($shipment->status === 'failed_delivery' && auth()->user()->isAdmin() && $shipment->canAttemptDelivery())
                    <form method="POST" action="{{ route('admin.shipments.reschedule', $shipment) }}" class="mt-3">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-secondary btn-sm border-orange-300 text-orange-700 hover:bg-orange-50 dark:border-orange-700 dark:text-orange-400 dark:hover:bg-orange-900/20">Reschedule ({{ $shipment->remainingAttempts() }} sisa)</button>
                    </form>
                @endif
                <div class="mt-4 space-y-3">
                    @forelse ($shipment->deliveryAttempts as $attempt)
                        <div class="rounded-xl border p-4 {{ $attempt->isSuccessful() ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/10' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/10' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="badge {{ $attempt->isSuccessful() ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-200 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                        #{{ $attempt->attempt_number }} &mdash; {{ $attempt->isSuccessful() ? 'Berhasil' : 'Gagal' }}
                                    </span>
                                    <span class="text-xs text-slate-500">{{ optional($attempt->attempted_at)->format('d M Y H:i') }}</span>
                                </div>
                                <span class="text-xs text-slate-400">{{ $attempt->attemptedBy?->name }}</span>
                            </div>
                            @if ($attempt->isFailed() && $attempt->failure_reason)
                                <p class="mt-2 text-sm text-red-700 dark:text-red-400">Alasan: {{ $attempt->failureReasonLabel() }}</p>
                            @endif
                            @if ($attempt->notes)
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $attempt->notes }}</p>
                            @endif
                            @if ($attempt->proof_path)
                                <a href="{{ asset('storage/'.$attempt->proof_path) }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-blue-600 dark:text-blue-400">Lihat Foto</a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada catatan pengiriman.</p>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- Sender & Receiver + Tracking --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="card p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Data Pengirim & Penerima</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    @foreach ([['Pengirim', $shipment->sender_name, $shipment->sender_phone, $shipment->sender_address], ['Penerima', $shipment->receiver_name, $shipment->receiver_phone, $shipment->receiver_address]] as [$label, $name, $phone, $addr])
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $label }}</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $phone }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $addr }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tracking Terbaru</h3>
                    @if (auth()->user()->isCustomer())
                        <a href="{{ route('customer.trackings.index', $shipment) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400">Lihat Semua</a>
                    @elseif (auth()->user()->isCourier())
                        <div class="flex gap-2">
                            <a href="{{ route('courier.trackings.index', $shipment) }}" class="btn-ghost btn-sm">Riwayat</a>
                            @if ($shipment->activeAssignment && ! $shipment->isFinal())
                                <a href="{{ route('courier.trackings.create', $shipment) }}" class="btn-primary btn-sm">Tambah</a>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($shipment->shipmentTrackings->take(5) as $tracking)
                        <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <span class="badge {{ $statusColors[$tracking->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">{{ $statusLabels[$tracking->status] ?? $tracking->status }}</span>
                                <span class="text-[10px] text-slate-400">{{ optional($tracking->tracked_at)->format('d M H:i') }}</span>
                            </div>
                            <p class="mt-1.5 text-sm font-medium text-slate-900 dark:text-white">{{ $tracking->location }}</p>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $tracking->description }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada tracking.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Barang Kiriman --}}
        <div class="card p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Barang Kiriman</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($shipment->shipmentItems as $item)
                    <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $item->item_name }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Jumlah: {{ $item->quantity }} &middot; {{ number_format((float) $item->weight, 2, ',', '.') }} Kg</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $item->description ?: 'Tidak ada deskripsi.' }}</p>
                        @if ($item->photo)
                            <img src="{{ asset('storage/'.$item->photo) }}" alt="{{ $item->item_name }}" class="mt-3 h-32 w-full rounded-xl object-cover">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Assignment History --}}
        @if (auth()->user()->isAdmin() && $shipment->assignments->count() > 0)
            <div class="card overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Assignment</h3>
                </div>
                <div class="table-wrapper border-0 rounded-none">
                    <table class="table">
                        <thead>
                            <tr><th>Kurir</th><th>Kendaraan</th><th>Status</th><th>Ditugaskan</th><th>Selesai</th><th>Oleh</th><th>Catatan</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($shipment->assignments as $a)
                                <tr>
                                    <td class="font-medium text-slate-900 dark:text-white">{{ $a->courier?->name ?? '-' }}</td>
                                    <td>{{ $a->vehicle?->plate_number ?? '-' }}</td>
                                    <td><span class="badge {{ match($a->status) { 'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-slate-100 text-slate-600' } }}">{{ $a->status }}</span></td>
                                    <td>{{ optional($a->assigned_at)->format('d M Y H:i') }}</td>
                                    <td>{{ optional($a->completed_at ?? $a->cancelled_at)->format('d M Y H:i') ?? '-' }}</td>
                                    <td>{{ $a->assignedBy?->name ?? '-' }}</td>
                                    <td class="max-w-[200px] truncate">{{ $a->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Update Status --}}
        @if (auth()->user()->isAdmin() && ! $shipment->isFinal())
            <div class="card p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Update Status</h3>
                @php $nextStatuses = $shipment->validNextStatuses(); @endphp
                @if (count($nextStatuses) > 0)
                    <form method="POST" action="{{ route('admin.shipments.update-status', $shipment) }}" class="mt-4 grid gap-4 md:grid-cols-2">
                        @csrf @method('PATCH')
                        <div>
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Status Berikutnya</label>
                            <select name="status" class="select" required>
                                @foreach ($nextStatuses as $ns)
                                    <option value="{{ $ns }}">{{ $statusLabels[$ns] ?? $ns }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-[10px] text-slate-400">Saat ini: {{ $shipment->statusLabel() }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Estimasi Tiba</label>
                            <input name="estimated_arrival" type="date" value="{{ optional($shipment->estimated_arrival)->format('Y-m-d') }}" class="input">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Lokasi</label>
                            <input name="tracking_location" type="text" class="input" placeholder="Gudang Jakarta">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Catatan</label>
                            <textarea name="tracking_description" rows="2" class="input" placeholder="Opsional"></textarea>
                        </div>
                        <div class="md:col-span-2"><button type="submit" class="btn-primary">Update Status</button></div>
                    </form>
                @else
                    <p class="mt-3 text-sm text-slate-400">Tidak ada transisi status valid.</p>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
