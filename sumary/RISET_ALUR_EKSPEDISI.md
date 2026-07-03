# Riset Alur Ekspedisi Indonesia vs FastExpress

**Tujuan:** Bandingin alur FastExpress dengan ekspedisi terkemuka (J&T, JNE, SiCepat, Anteraja) untuk identifikasi gap & peluang improvement.

---

## 1. Shipment Status — Perbandingan

### Standar Industri (Gabungan Semua Ekspedisi)

| # | Status | J&T | JNE | SiCepat | Anteraja | FastExpress |
|---|--------|-----|-----|---------|----------|-------------|
| 1 | Order Created | ✅ | ✅ | ✅ | ✅ Pesanan Dibuat | ✅ `pending` |
| 2 | Picked Up | ✅ | ✅ | ✅ | ✅ Paket Dijemput | ❌ `processed` (beda makna) |
| 3 | Received at Hub/Origin | ✅ | ✅ Received at Origin | ✅ | ✅ Paket Diproses | ❌ |
| 4 | In Transit (Between Hubs) | ✅ | ✅ In Transit | ✅ | ✅ Di Hub | ❌ |
| 5 | Received at Destination Hub | ✅ | ✅ Processed at Destination | ✅ | ✅ Staging Store | ❌ |
| 6 | Out for Delivery | ✅ | ✅ With Delivery Courier | ✅ | ✅ SATRIA Mengantarkan | ❌ |
| 7 | Delivered | ✅ | ✅ | ✅ Terkirim | ✅ Paket Diterima | ✅ `delivered` |
| 8 | Failed Delivery | ✅ | ✅ Undelivered | ✅ | ✅ SATRIA Belum Antar | ❌ |
| 9 | Return to Sender | ✅ | ✅ | ✅ | ✅ | ❌ |

### Gap FastExpress:

**Status terlalu sedikit.** FastExpress cuma punya 5 status:
```
pending → processed → picked_up → in_transit → delivered
```

Ekspedisi nyata punya **9+ status** karena ada tahapan di hub, sorting, out for delivery, failed delivery, dan return.

---

## 2. Payment Flow — Perbandingan

| Aspek | J&T | JNE | SiCepat | Anteraja | FastExpress |
|-------|-----|-----|---------|----------|-------------|
| **Kapan bayar ongkir?** | Saat drop-off | Saat drop-off | Saat booking / drop-off | Saat order dibuat | Kapan aja (fleksibel) |
| **Metode bayar** | Cash di counter | Cash/card di counter | Cash, transfer, e-wallet | Transfer | Cash, transfer, e-wallet |
| **COD Support** | ✅ | ✅ (via marketplace) | ✅ (6-8 jam, major cities) | ❌ (via marketplace) | ❌ |
| **Bayar Tempo (Corporate)** | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Bukti bayar upload** | ❌ (langsung di counter) | ❌ (langsung di counter) | ❌ (langsung online) | ❌ (langsung online) | ✅ (upload bukti → admin verif) |
| **Verifikasi admin** | ❌ | ❌ | ❌ | ❌ | ✅ |

### Gap FastExpress:

1. **Tidak ada COD** — ini fitur wajib buat e-commerce/marketplace
2. **Bayar ongkir terlalu fleksibel** — customer bisa buat shipment dulu, bayar kapan aja. Di ekspedisi nyata: bayar saat order (prepaid) atau COD saat terima
3. **Verifikasi pembayaran manual** — ekspedisi nyata tidak perlu admin verifikasi bukti bayar ongkir. Bayar = langsung confirmed

---

## 3. Pickup Flow — Perbandingan

| Aspek | J&T | JNE | SiCepat | Anteraja | FastExpress |
|-------|-----|-----|---------|----------|-------------|
| **Drop-off (customer antar ke counter)** | ✅ 8000+ titik | ✅ 8000+ titik | ✅ gerai/agen | ✅ staging store | ❌ |
| **Request pickup (kurir jemput)** | ✅ via app/web | ✅ via MyJNE | ✅ via app/dashboard | ✅ (SATRIA jemput) | ❌ |
| **Pickup scheduling** | ✅ | ✅ | ✅ | ✅ (pilih slot waktu) | ❌ |
| **Minimum pickup** | ❓ | ❓ | ✅ (ada minimum berat) | ❓ | ❌ |

### Gap FastExpress:

**FastExpress tidak punya flow pickup.** Di ekspedisi nyata ada 2 cara masuk:
1. **Drop-off:** Customer bawa paket ke counter/cabang → petugas scan & terima
2. **Pickup request:** Customer request lewat app → kurir datang jemput ke alamat

FastExpress hanya punya "customer buat order → admin assign kurir → kurir pickup". Ini kurang fleksibel.

---

## 4. Courier Assignment — Perbandingan

| Aspek | J&T | JNE | SiCepat | Anteraja | FastExpress |
|-------|-----|-----|---------|----------|-------------|
| **Model assignment** | Zone-based (tetap) | Zone-based (tetap) | Zone-based + on-demand pickup | Zone-based (SATRIA tetap di rute) | Manual admin assign |
| **Siapa yang assign** | Sistem otomatis | Sistem otomatis | Sistem otomatis | Sistem otomatis | Admin manual |
| **Pickup kurir vs delivery kurir** | Bisa beda | Bisa beda | Bisa beda (SiGesit) | SATRIA seragam | 1 kurir dari awal sampai akhir |
| **Multi-leg shipment** | ✅ (berpindah kurir per hub) | ✅ | ✅ | ✅ | ❌ |

### Gap FastExpress:

1. **Assignment manual** — di ekspedisi nyata, assignment otomatis berdasarkan zona/wilayah. FastExpress pakai admin manual (tidak scalable)
2. **1 kurir dari pickup sampai delivered** — ini tidak realistis untuk pengiriman antar kota. Di ekspedisi nyata, paket berpindah kurir di setiap hub
3. **Tidak ada konsep "hub"** — FastExpress hanya punya "branches" tapi tidak ada flow paket berpindah antar hub

---

## 5. Failed Delivery & Return — Perbandingan

| Aspek | J&T | JNE | SiCepat | Anteraja | FastExpress |
|-------|-----|-----|---------|----------|-------------|
| **Maks attempt antar** | ~3x | 3x | ~3x | ~2-3x | ❌ |
| **Status gagal antar** | ✅ | ✅ Undelivered | ✅ | ✅ SATRIA Belum Antar | ❌ |
| **Reschedule antar** | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Return to Sender (RTS)** | ✅ (15 hari) | ✅ | ✅ | ✅ | ❌ |
| **Ganti alamat** | ✅ (hubungi CS) | ✅ | ✅ | ❓ | ❌ |
| **Klaim hilang/rusak** | ✅ (dengan/sans asuransi) | ✅ | ✅ | ✅ | ❌ |

### Gap FastExpress:

**Ini gap terbesar.** FastExpress tidak punya skenario gagal antar sama sekali. Di dunia nyata:
- Kurir gagal antar (penerima tidak ada, alamat salah, penerima tolak)
- Reschedule 2-3x percobaan
- Kalau tetap gagal → Return to Sender (RTS)
- Ada asuransi & klaim untuk barang hilang/rusak

---

## 6. Fitur yang Tidak Ada di FastExpress

| Fitur | Urgency | Keterangan |
|-------|---------|------------|
| **Failed delivery flow** | 🔴 Tinggi | Wajib ada. Tanpa ini, kalau kurir gagal antar, tidak ada jalan keluar |
| **Return to Sender (RTS)** | 🔴 Tinggi | Kelanjutan dari failed delivery |
| **COD (Cash on Delivery)** | 🟡 Sedang | Penting kalau target e-commerce/marketplace |
| **Pickup request (kurir jemput)** | 🟡 Sedang | Standar industri, tapi bisa phase 2 |
| **Drop-off flow** | 🟡 Sedang | Customer antar ke cabang langsung |
| **Asuransi & klaim** 🟡 Sedang | Penting untuk trust customer |
| **Auto-assignment kurir** | 🟢 Rendah | Bisa phase 2, manual dulu oke |
| **Multi-hub routing** | 🟢 Rendah | Penting kalau scale besar, tapi startup bisa single-hub dulu |
| **Invoicing/tempo (corporate)** | 🟢 Rendah | Fitur B2B, bisa nanti |

---

## 7. Rekomendasi Status Flow yang Lebih Realistis

### Status Baru (Pakai Standar Industri)

```
┌───────────┐    ┌───────────┐    ┌───────────┐    ┌───────────┐
│  CREATED  │───▶│ PICKED_UP │───▶│ AT_ORIGIN │───▶│ IN_TRANSIT│
│           │    │           │    │   _HUB    │    │           │
└───────────┘    └───────────┘    └───────────┘    └───────────┘
                                                          │
                                                          ▼
┌───────────┐    ┌───────────┐    ┌───────────┐    ┌───────────┐
│ RETURNED  │◀───│  FAILED   │◀───│   OUT_    │◀───│    AT_    │
│_TO_SENDER │    │_DELIVERY  │    │DELIVERY   │    │DEST_HUB   │
└───────────┘    └───────────┘    └───────────┘    └───────────┘
                                                          │
                                                          ▼
                                                    ┌───────────┐
                                                    │ DELIVERED │
                                                    └───────────┘
```

| Status | Label (Indonesia) | Siapa yang trigger | Keterangan |
|--------|-------------------|-------------------|------------|
| `created` | Pesanan Dibuat | Otomatis (customer buat) | Ganti dari `pending` |
| `picked_up` | Paket Dijemput | Kurir / Admin | Kurir sudah ambil paket dari pengirim |
| `at_origin_hub` | Di Hub Asal | Admin / Sistem | Paket sampai di hub/cabang asal, proses sorting |
| `in_transit` | Dalam Perjalanan | Admin / Sistem | Paket dalam perjalanan antar hub |
| `at_dest_hub` | Di Hub Tujuan | Admin / Sistem | Paket sampai di hub/cabang tujuan |
| `out_for_delivery` | Sedang Diantar | Kurir | Kurir bawa paket ke alamat penerima |
| `delivered` | Diterima | Kurir | Paket diterima penerima ✅ |
| `failed_delivery` | Gagal Dikirim | Kurir | Penerima tidak ada / alamat salah / ditolak |
| `returned_to_sender` | Dikembalikan | Admin / Sistem | Paket dikembalikan ke pengirim setelah X kali gagal |

### Flow Failed Delivery

```
failed_delivery (attempt 1)
    │
    ├──▶ Reschedule → out_for_delivery (attempt 2)
    │                       │
    │                       ├──▶ delivered ✅
    │                       │
    │                       └──▶ failed_delivery (attempt 2)
    │                               │
    │                               ├──▶ Reschedule → out_for_delivery (attempt 3)
    │                               │                       │
    │                               │                       ├──▶ delivered ✅
    │                               │                       └──▶ failed_delivery (attempt 3)
    │                               │                               │
    │                               │                               └──▶ returned_to_sender ❌
    │                               │
    │                               └──▶ returned_to_sender ❌
    │
    └──▶ returned_to_sender ❌ (customer cancel / penerima tolak permanen)
```

---

## 8. Rekomendasi Payment Flow

### Model yang Disarankan: Prepaid + COD

```
MODEL 1: PREPADD (Customer bayar ongkir di depan)
─────────────────────────────────────────────────
Customer buat order → Bayar ongkir langsung (snap payment)
    → Payment confirmed otomatis (no admin verify needed for ongkir)
    → Shipment diproses

MODEL 2: COD (Bayar di tempat)
─────────────────────────────────────────────────
Merchant buat order → Pilih COD
    → Kurir antar ke buyer
    → Buyer bayar cash ke kurir
    → Kurir setor ke sistem
    → Sistem remit ke merchant (T+1/T+3 hari kerja)
```

### Yang perlu diubah:
1. **Hapus verifikasi admin untuk ongkir** — terlalu lambat. Bayar = confirmed
2. **Tambah opsi COD** — bayar ongkir COD ke penerima, bukan ke pengirim
3. **Tambah model "tempo"** untuk corporate — tagihan bulanan

---

## 9. Ringkasan Gap Analysis

| Area | FastExpress Sekarang | Standar Industri | Gap Level |
|------|---------------------|------------------|-----------|
| Status stages | 5 status | 9+ status | 🔴 Besar |
| Failed delivery | Tidak ada | 3 attempt + RTS | 🔴 Besar |
| Pickup flow | Manual admin assign | Drop-off + Request pickup | 🟡 Sedang |
| COD | Tidak ada | Standar industri | 🟡 Sedang |
| Payment verif | Manual admin | Otomatis (snap) | 🟡 Sedang |
| Asuransi/klaim | Tidak ada | Ada (0.2% nilai barang) | 🟡 Sedang |
| Tracking detail | Ada tapi basic | Detail per hub scan | 🟡 Sedang |
| Assignment | Manual admin | Auto zone-based | 🟢 Kecil (ok untuk MVP) |
| Multi-hub routing | Tidak ada | Ada | 🟢 Kecil (ok untuk MVP) |

---

**Dokumen dibuat:** 2026-07-02
**Sumber:** J&T Express (jet.co.id), JNE (jne.co.id), SiCepat (sicepat.com), Anteraja (anteraja.id)
