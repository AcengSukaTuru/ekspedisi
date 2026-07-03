# Analisis Masalah dan Kekurangan Sistem Ekspedisi

**Tanggal Analisis:** 2026-07-01  
**Status:** Transisi dari sistem vehicle_id langsung ke sistem ShipmentAssignment

## 🚨 MASALAH KRITIS (Harus Segera Diperbaiki)

### 1. TrackingController Masih Menggunakan vehicle_id Lama

**Lokasi:** [app/Http/Controllers/TrackingController.php:38,48](app/Http/Controllers/TrackingController.php#L38)

**Masalah:**
```php
abort_unless($shipment->vehicle_id !== null, 403, 'Shipment ini belum memiliki kendaraan.');
```

Kode ini masih memeriksa `vehicle_id` secara langsung, padahal sistem sudah migrasi ke ShipmentAssignment.

**Dampak:**
- Courier tidak bisa menambahkan tracking jika shipment hanya punya assignment tapi tidak ada vehicle_id langsung
- Inconsistency antara sistem lama dan baru
- Test `test_complete_expedition_workflow_from_order_to_delivery` memerlukan workaround

**Solusi:**
Ganti pengecekan vehicle_id dengan pengecekan activeAssignment:
```php
$assignment = $shipment->activeAssignment;
abort_unless($assignment !== null, 403, 'Shipment ini belum memiliki assignment kurir.');
```

---

### 2. ShipmentController::updateStatus Masih Menyimpan vehicle_id Langsung

**Lokasi:** [app/Http/Controllers/ShipmentController.php:163](app/Http/Controllers/ShipmentController.php#L163)

**Masalah:**
```php
'vehicle_id' => $validated['vehicle_id'] ?? null,
```

Admin masih bisa set vehicle_id langsung tanpa membuat ShipmentAssignment. Ini bypass sistem assignment yang seharusnya menjadi satu-satunya cara untuk assign courier/vehicle.

**Dampak:**
- Data assignment tidak lengkap (tidak ada record siapa yang assign, kapan)
- Tidak ada audit trail
- Courier tidak bisa query shipments mereka via assignment relationship
- Inconsistency data

**Solusi:**
- Hapus vehicle_id dari form update status
- Buat endpoint terpisah untuk assignment management
- Atau, buat ShipmentAssignment otomatis ketika vehicle_id diisi

---

### 3. ShipmentTracking Tidak Mencatat created_by

**Lokasi:** [app/Http/Controllers/TrackingController.php:57](app/Http/Controllers/TrackingController.php#L57)

**Masalah:**
Migration sudah menambahkan field `created_by` ke shipment_trackings, tapi controller tidak pernah mengisinya:
```php
$shipment->shipmentTrackings()->create($validated);
```

**Dampak:**
- Tidak ada record siapa yang membuat tracking update
- Audit trail tidak lengkap
- Tidak bisa tracking activity per courier

**Solusi:**
Tambahkan created_by:
```php
$shipment->shipmentTrackings()->create([
    ...$validated,
    'created_by' => $request->user()->id,
]);
```

Sama untuk ShipmentController line 173.

---

### 4. Migration Data Menggunakan Eloquent Models

**Lokasi:** [database/migrations/2026_06_22_080927_migrate_old_shipment_assignments.php](database/migrations/2026_06_22_080927_migrate_old_shipment_assignments.php#L13)

**Masalah:**
Migration menggunakan `Shipment::whereNotNull('vehicle_id')->get()` yang bisa fail jika model berubah setelah migration dibuat.

**Best Practice:**
Gunakan DB query builder untuk migration:
```php
$shipments = DB::table('shipments')->whereNotNull('vehicle_id')->get();
```

---

## ⚠️ FITUR YANG HILANG (Harus Diimplementasikan)

### 5. Tidak Ada Controller untuk Shipment Assignment

**Missing:** `ShipmentAssignmentController`

**Fungsi yang Dibutuhkan:**
- `assign(Request $request, Shipment $shipment)` - Admin assign courier + vehicle ke shipment
- `reassign(Request $request, ShipmentAssignment $assignment)` - Admin reassign ke courier lain
- `cancel(Request $request, ShipmentAssignment $assignment)` - Admin cancel assignment
- `complete(ShipmentAssignment $assignment)` - System/courier complete assignment saat delivered

**Routes yang Dibutuhkan:**
```php
Route::prefix('admin')->middleware('role:admin')->group(function () {
    Route::post('shipments/{shipment}/assign', [ShipmentAssignmentController::class, 'assign'])
        ->name('admin.shipments.assign');
    Route::patch('assignments/{assignment}/reassign', [ShipmentAssignmentController::class, 'reassign'])
        ->name('admin.assignments.reassign');
    Route::patch('assignments/{assignment}/cancel', [ShipmentAssignmentController::class, 'cancel'])
        ->name('admin.assignments.cancel');
});
```

**Dampak Saat Ini:**
- Admin tidak bisa assign courier via UI, hanya bisa set vehicle_id (sistem lama)
- Tidak ada cara untuk cancel/reassign
- Test harus manually create assignment via model

---

### 6. Tidak Ada Policy untuk ShipmentAssignment

**Missing:** `ShipmentAssignmentPolicy`

**Authorization yang Dibutuhkan:**
- `assign` - Hanya admin
- `reassign` - Hanya admin
- `cancel` - Hanya admin
- `view` - Admin atau courier yang di-assign
- `complete` - Admin atau courier yang di-assign

---

### 7. Tidak Ada Auto-Complete Assignment Saat Delivered

**Masalah:**
Ketika shipment status menjadi `delivered`, assignment status harus otomatis menjadi `completed`.

**Solusi:**
Buat Observer atau Event Listener:
```php
// ShipmentObserver.php
public function updated(Shipment $shipment)
{
    if ($shipment->isDirty('status') && $shipment->status === Shipment::STATUS_DELIVERED) {
        $shipment->activeAssignment()?->update([
            'status' => ShipmentAssignment::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }
}
```

---

### 8. Tidak Ada UI/View untuk Assignment Management

**Missing Views:**
- Form untuk assign courier/vehicle (should replace vehicle dropdown)
- List active assignments per courier
- Assignment history per shipment
- Reassignment form

---

## 📊 KEKURANGAN ALUR EKSPEDISI

### Alur Lengkap yang Seharusnya:

1. ✅ Customer create shipment → Status: `pending`
2. ✅ Payment auto-created → Status: `unpaid`
3. ✅ Customer submit payment proof → Status: `pending`
4. ✅ Admin verify payment → Status: `paid`
5. ❌ **Admin assign courier + vehicle** (MISSING CONTROLLER/UI)
6. ✅ Admin update status → `processed`
7. ⚠️ Courier pickup → `picked_up` (Works but checks wrong field)
8. ⚠️ Courier transit → `in_transit` (Works but checks wrong field)
9. ⚠️ Courier deliver → `delivered` (Works but doesn't auto-complete assignment)
10. ❌ **Assignment auto-completed** (MISSING LOGIC)

### Masalah di Setiap Step:

**Step 5 (Assignment):**
- ❌ Tidak ada endpoint/controller
- ❌ Tidak ada form di UI
- ❌ Admin masih pakai cara lama (set vehicle_id)

**Step 7-9 (Tracking):**
- ⚠️ Masih check vehicle_id instead of assignment
- ⚠️ Tidak record created_by
- ⚠️ Courier authorization via assignment tapi tracking check vehicle_id

**Step 10 (Completion):**
- ❌ Tidak ada auto-complete logic
- ❌ Assignment tetap status `active` meski shipment `delivered`

---

## 🔍 MASALAH DATA INTEGRITY

### 1. Shipment Bisa Punya vehicle_id Tanpa Assignment
```sql
-- Ini mungkin terjadi saat ini:
SELECT * FROM shipments WHERE vehicle_id IS NOT NULL 
AND id NOT IN (SELECT shipment_id FROM shipment_assignments WHERE status = 'active');
```

### 2. Assignment Bisa Tetap Active Meski Shipment Delivered
```sql
-- Ini PASTI terjadi saat ini:
SELECT * FROM shipment_assignments 
WHERE status = 'active' 
AND shipment_id IN (SELECT id FROM shipments WHERE status = 'delivered');
```

### 3. Multiple Active Assignments untuk 1 Shipment
Tidak ada unique constraint untuk mencegah:
```sql
-- Ini secara teknis bisa terjadi:
SELECT shipment_id, COUNT(*) FROM shipment_assignments 
WHERE status = 'active' 
GROUP BY shipment_id 
HAVING COUNT(*) > 1;
```

**Solusi:**
Tambah constraint atau gunakan DB transaction + locking.

---

## 🧪 COVERAGE TEST

### Test yang Sudah Ada:
- ✅ `Fase1ShipmentAssignmentTest` - Relationship dan basic assignment
- ✅ `AuthorizationTest` - Policy authorization untuk shipment dan payment
- ✅ `CompleteExpeditionWorkflowTest` - End-to-end workflow (dengan workaround untuk bugs)

### Test yang Masih Kurang:
- ❌ ShipmentAssignmentController tests
- ❌ Assignment policy tests
- ❌ Auto-complete assignment tests
- ❌ Reassignment workflow tests
- ❌ Multiple courier scenarios
- ❌ Edge cases (cancel active assignment, etc.)
- ❌ Data integrity constraint tests

---

## 🎯 PRIORITAS PERBAIKAN

### Priority 1 (Critical - Harus Segera):
1. Fix TrackingController untuk check assignment instead of vehicle_id
2. Fix ShipmentController untuk tidak set vehicle_id langsung
3. Tambah created_by di tracking creation

### Priority 2 (High - Needed for Complete Feature):
4. Buat ShipmentAssignmentController
5. Buat ShipmentAssignmentPolicy
6. Buat auto-complete assignment logic
7. Update UI untuk assignment management

### Priority 3 (Medium - Data Integrity):
8. Fix migration untuk gunakan DB query
9. Tambah unique constraint untuk active assignment
10. Buat validation untuk prevent multiple active assignments

### Priority 4 (Low - Nice to Have):
11. Assignment history view
12. Courier performance dashboard
13. Assignment analytics

---

## 📝 CATATAN TAMBAHAN

### Hal yang Sudah Benar:
- ✅ Model relationships sudah lengkap
- ✅ Migration structure sudah benar (kecuali data migration)
- ✅ Policy untuk Shipment dan Payment sudah bagus
- ✅ Authorization via Gate sudah konsisten
- ✅ Courier query via activeAssignment relationship sudah benar

### Breaking Changes yang Perlu Dipertimbangkan:
Jika ingin fully migrate ke assignment system:
1. Buat vehicle_id nullable di shipments table
2. Deprecate direct vehicle_id usage
3. Selalu gunakan assignment untuk semua query
4. Tambah validation: shipment must have active assignment before status can be changed to picked_up

### Backward Compatibility:
Saat ini masih maintain backward compatibility dengan vehicle_id. Pertimbangkan:
- Apakah mau keep dual system (assignment + vehicle_id)?
- Atau full migration ke assignment only?
- Jika full migration, perlu migration untuk set semua old shipment vehicle_id = NULL setelah assignment created

---

## 🚀 NEXT STEPS

1. **Review dokumen ini dengan tim**
2. **Decide: Full migration atau dual system?**
3. **Implement Priority 1 fixes** (critical bugs)
4. **Implement Priority 2 features** (missing functionality)
5. **Run all tests** untuk verify fixes
6. **Update dokumentasi** CLAUDE.md dengan assignment workflow
7. **Deploy dan monitor**

---

**Disusun oleh:** Claude Code  
**Untuk:** AcengSukaTuru  
**Projek:** Ekspedisi App - ShipmentAssignment System Migration
