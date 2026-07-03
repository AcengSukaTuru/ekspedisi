# Alur Lengkap Sistem Ekspedisi FastExpress

Dokumen ini menjelaskan alur FastExpress setelah disesuaikan dengan riset ekspedisi besar Indonesia seperti J&T, JNE, SiCepat, dan Anteraja.

## Ringkasan

FastExpress sekarang memakai pola industri:

- 9 status shipment.
- Drop-off dan request pickup.
- Prepaid dan COD.
- Assignment kurir lewat `ShipmentAssignment`.
- Delivery attempt maksimal 3 kali.
- Failed delivery dan return to sender.
- Tracking history untuk setiap perubahan penting.

## Akun Demo

Seeder membuat akun berikut. Semua password: `password`.

| Role | Email | Password | Nama |
|---|---|---|---|
| Admin | `admin@ekspedisi.test` | `password` | Admin Utama |
| Admin | `admin2@ekspedisi.test` | `password` | Admin Cabang |
| Courier | `kurir1@ekspedisi.test` | `password` | Rudi Hartono |
| Courier | `kurir2@ekspedisi.test` | `password` | Andi Saputra |
| Courier | `kurir3@ekspedisi.test` | `password` | Siti Lestari |
| Customer | `customer@ekspedisi.test` | `password` | Budi Santoso |
| Customer | `customer2@ekspedisi.test` | `password` | Nadia Prameswari |
| Customer / Merchant | `toko@ekspedisi.test` | `password` | Toko Sentosa |

## Role dan Tanggung Jawab

### Customer

Customer bisa:

- buat shipment;
- pilih `drop_off` atau `pickup_request`;
- pilih `prepaid` atau `cod`;
- lihat shipment sendiri;
- lihat tracking sendiri;
- submit bukti pembayaran prepaid.

Customer tidak bisa:

- lihat shipment customer lain;
- assign kurir;
- update status;
- verifikasi pembayaran.

### Admin

Admin bisa:

- lihat semua shipment;
- assign/reassign/cancel kurir dan kendaraan;
- update status sesuai transisi valid;
- verifikasi pembayaran prepaid;
- lihat payment COD;
- reschedule shipment gagal kirim;
- lihat histori assignment dan delivery attempts.

Admin tidak lagi mengatur kurir lewat `shipments.vehicle_id` langsung. Assignment resmi lewat `shipment_assignments`.

### Courier

Courier bisa:

- lihat shipment aktif yang di-assign ke dirinya;
- tambah tracking;
- catat delivery attempt saat status `out_for_delivery`;
- catat sukses/gagal antar;
- upload proof photo saat tracking/attempt.

Courier tidak bisa:

- lihat shipment tanpa assignment aktif;
- mengubah assignment;
- verifikasi pembayaran;
- update shipment final.

## Status Shipment

Alur status utama:

```text
created
  ↓
picked_up
  ↓
at_origin_hub
  ↓
in_transit
  ↓
at_dest_hub
  ↓
out_for_delivery
  ↓
delivered
```

Alur gagal kirim:

```text
out_for_delivery
  ↓
failed_delivery
  ├─ admin reschedule → out_for_delivery
  └─ attempt habis → returned_to_sender
```

| Status | Label | Trigger | Arti |
|---|---|---|---|
| `created` | Pesanan Dibuat | Customer buat shipment | Order baru masuk sistem |
| `picked_up` | Paket Dijemput | Admin / courier | Paket diterima dari pengirim/cabang |
| `at_origin_hub` | Di Hub Asal | Admin / sistem | Paket masuk hub asal untuk sorting |
| `in_transit` | Dalam Perjalanan | Admin / sistem | Paket bergerak antar hub/kota |
| `at_dest_hub` | Di Hub Tujuan | Admin / sistem | Paket sampai hub tujuan |
| `out_for_delivery` | Sedang Diantar | Admin / courier | Paket dibawa courier ke penerima |
| `delivered` | Diterima | Courier | Paket diterima penerima, status final |
| `failed_delivery` | Gagal Dikirim | Courier | Percobaan antar gagal |
| `returned_to_sender` | Dikembalikan ke Pengirim | Sistem / admin | Paket RTS, status final |

## Transisi Valid

```text
created → picked_up
picked_up → at_origin_hub | in_transit
at_origin_hub → in_transit
in_transit → at_dest_hub
at_dest_hub → out_for_delivery
out_for_delivery → delivered | failed_delivery
failed_delivery → out_for_delivery | returned_to_sender
delivered → final
returned_to_sender → final
```

Transisi loncat ditolak. Shipment final tidak bisa diubah lagi.

## Alur Customer Buat Shipment

1. Customer login.
2. Buka `New Shipment`.
3. Pilih cara masuk paket:
   - `drop_off`: customer antar paket ke cabang;
   - `pickup_request`: kurir jemput ke alamat customer.
4. Isi cabang asal, cabang tujuan, service type, berat.
5. Isi data pengirim, penerima, dan barang.
6. Pilih pembayaran:
   - `prepaid`: ongkir dibayar di depan;
   - `cod`: penerima bayar ke kurir saat diterima.
7. Sistem otomatis membuat:
   - tracking number;
   - shipment status `created`;
   - item shipment;
   - payment;
   - tracking awal.

## Alur Drop-off

```text
Customer buat shipment drop_off
  ↓
Customer antar paket ke cabang
  ↓
Admin/cabang terima paket
  ↓
Status picked_up
  ↓
at_origin_hub → in_transit → at_dest_hub → out_for_delivery → delivered
```

## Alur Pickup Request

```text
Customer buat shipment pickup_request
  ↓
Customer isi alamat pickup
  ↓
Admin assign kurir pickup
  ↓
Courier jemput paket
  ↓
Status picked_up
  ↓
at_origin_hub → in_transit → at_dest_hub → out_for_delivery → delivered
```

## Alur Assignment Kurir

1. Admin buka detail shipment.
2. Admin pilih `Assign Kurir & Kendaraan`.
3. Admin pilih courier dan vehicle.
4. Sistem membuat `ShipmentAssignment` status `active`.
5. Courier baru bisa melihat shipment di daftar tugas.
6. Jika perlu ganti kurir, admin pakai `Reassign`.
7. Assignment lama jadi `cancelled`, assignment baru jadi `active`.
8. Saat shipment `delivered` atau RTS, assignment aktif jadi `completed`.

## Alur Delivery Attempt

Delivery attempt hanya ada saat status `out_for_delivery`.

### Attempt Berhasil

```text
out_for_delivery
  ↓
courier catat success
  ↓
delivered
  ↓
assignment completed
```

Sistem membuat:

- `delivery_attempts` status `success`;
- tracking `delivered`;
- update shipment ke `delivered`;
- complete assignment aktif.

### Attempt Gagal

```text
out_for_delivery
  ↓
courier catat failed
  ↓
failed_delivery
```

Alasan gagal:

- penerima tidak ada;
- alamat salah;
- penerima menolak;
- penerima tidak bisa dihubungi;
- lainnya.

Jika masih ada sisa attempt:

```text
failed_delivery
  ↓
admin reschedule
  ↓
out_for_delivery
```

Jika attempt gagal sudah mencapai batas maksimum:

```text
failed_delivery
  ↓
returned_to_sender
```

Default maksimum attempt: 3.

## Alur Pembayaran

### Prepaid

```text
Customer buat shipment prepaid
  ↓
Payment pending
  ↓
Customer submit/upload bukti bayar
  ↓
Admin verifikasi
  ↓
Payment paid / failed
```

Catatan: berdasarkan riset industri, prepaid idealnya nanti memakai payment gateway otomatis. Untuk MVP masih manual verification.

### COD

```text
Customer/merchant buat shipment COD
  ↓
Payment type cod + pending
  ↓
Courier antar paket
  ↓
Penerima bayar ke courier
  ↓
Courier/setoran dicatat
  ↓
Payment paid
```

## Data Utama

### Shipment

Menyimpan:

- tracking number;
- customer;
- cabang asal/tujuan;
- pengirim/penerima;
- berat, biaya, layanan;
- pickup type;
- status;
- max delivery attempts;
- RTS reason.

### ShipmentAssignment

Menyimpan:

- shipment;
- courier;
- vehicle;
- assigned by;
- assigned at;
- completed/cancelled at;
- status active/completed/cancelled;
- notes.

### ShipmentTracking

Menyimpan:

- status;
- location;
- description;
- tracked at;
- created by;
- proof path.

### DeliveryAttempt

Menyimpan:

- attempt number;
- attempted by;
- success/failed;
- failure reason;
- notes;
- proof photo;
- next retry time.

### Payment

Menyimpan:

- amount;
- payment type prepaid/cod;
- payment status pending/paid/failed;
- method;
- proof;
- verifier;
- COD collector.

## UI/UX Saat Ini

UI sekarang diarahkan ke gaya logistics control center:

- sidebar fixed untuk navigasi role;
- navbar sticky;
- cards konsisten light/dark;
- status badge berbeda warna per status;
- progress status di detail shipment;
- assignment panel di detail shipment;
- delivery attempts panel;
- background grid halus sebagai identitas logistics/route network;
- dark mode instant tanpa reload.

## Yang Sudah Mengikuti Riset

- Status diperluas dari 5 menjadi 9 status.
- Drop-off tersedia.
- Request pickup tersedia.
- COD tersedia.
- Failed delivery tersedia.
- Reschedule tersedia.
- Return to sender tersedia.
- Assignment courier/vehicle punya histori.
- Tracking history lengkap.
- Delivery attempt punya data terpisah.

## Phase 2 yang Masih Bisa Ditambah

- Payment gateway otomatis untuk prepaid.
- Auto-assignment courier berdasarkan zona.
- Multi-leg assignment antar hub/kota.
- SLA dan estimasi otomatis per rute.
- Asuransi dan klaim barang rusak/hilang.
- Corporate billing / bayar tempo.
- Notifikasi WhatsApp/email.

**Updated:** 2026-07-02
