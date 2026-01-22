# 🚀 API Documentation - Airport Safety Map

**Base URL:** `http://localhost:8000/api`

---

## 📍 Airport Endpoints

### 1. Get All Airports
**Endpoint:** `GET /airports`  
**Deskripsi:** Mendapatkan list semua bandara dengan koordinat untuk ditampilkan di peta

**Response:**
```json
[
  {
    "id": 1,
    "name": "Cabang Medan",
    "city": "Medan",
    "provinsi": "Sumatera Utara",
    "level": "cabang_utama",
    "coordinates": [-122.4194, 37.7749],
    "reports_count": 150,
    "has_reports": true
  }
]
```

---

### 2. Get Airport Statistics
**Endpoint:** `GET /airports/{id}/stats`  
**Deskripsi:** Mendapatkan statistik dan grafik untuk sidebar

**Query Parameters:**
- `start_date` (optional): Format `YYYY-MM-DD`
- `end_date` (optional): Format `YYYY-MM-DD`

**Example:** `GET /airports/1/stats?start_date=2024-01-01&end_date=2024-12-31`

**Response:**
```json
{
  "airport_name": "Cabang Medan",
  "total_display": 45,
  "total_all_time": 150,
  "trend_info": {
    "has_data": true,
    "percentage": 15.3,
    "direction": "up",
    "label": "vs 01 Des - 31 Des 2025"
  },
  "monthly_trend": {
    "Jan 2024": 12,
    "Feb 2024": 15,
    "Mar 2024": 18
  },
  "top_categories": {
    "Runway Incursion": 25,
    "Bird Strike": 12,
    "Ground Handling": 8
  },
  "status_summary": {
    "open": 5,
    "closed": 35,
    "pending": 5
  }
}
```

---

### 3. Get Airport Hierarchy
**Endpoint:** `GET /airports/{id}/hierarchy`  
**Deskripsi:** Mendapatkan cabang pembantu dan unit dari cabang utama

**Response:**
```json
{
  "cabang_pembantu": [
    {
      "id": 2,
      "name": "Cabang Pembantu Sibolga",
      "city": "Sibolga",
      "provinsi": "Sumatera Utara",
      "level": "cabang_pembantu",
      "reports_count": 8,
      "has_reports": true
    }
  ],
  "units": [
    {
      "id": 3,
      "name": "Unit Gunungsitoli",
      "city": "Gunungsitoli",
      "level": "unit",
      "reports_count": 3,
      "has_reports": true
    }
  ],
  "total_children": 5
}
```

---

### 4. Get Airport Reports
**Endpoint:** `GET /airports/{id}/reports`  
**Deskripsi:** Mendapatkan list laporan (summary) untuk ditampilkan di card

**Query Parameters:**
- `category` (optional): Filter berdasarkan kategori
- `start_date` (optional): Format `YYYY-MM-DD`
- `end_date` (optional): Format `YYYY-MM-DD`
- `month` (optional): Format `YYYY-MM` (misal: `2024-03`)

**Example:** `GET /airports/1/reports?category=Runway Incursion&start_date=2024-01-01`

**Response:**
```json
[
  {
    "id": 123,
    "report_date": "2024-03-15 14:30:00",
    "category": "Runway Incursion",
    "status": "Analysis Completed"
  },
  {
    "id": 124,
    "report_date": "2024-03-16 09:15:00",
    "category": "Bird Strike",
    "status": "Analysis On Process"
  }
]
```

---

## 📄 Report Endpoints

### 5. Get Report Detail
**Endpoint:** `GET /reports/{id}`  
**Deskripsi:** Mendapatkan detail lengkap satu laporan (untuk modal)

**Response:**
```json
{
  "id": 123,
  "report_date": "2024-03-15 14:30:00",
  "input_date": "2024-03-15 15:00:00",
  "category": "Runway Incursion",
  "classification": "Serious",
  "ssr_code": "RI-001",
  "status": "Analysis Completed",
  "description": "Aircraft entered runway without clearance",
  "location": "Runway 05",
  "ats_unit": "Tower",
  
  "flight_info": {
    "aircraft_id": "GA123",
    "registration": "PK-GFA",
    "aircraft_type": "B737-800",
    "pic_name": "Capt. John Doe",
    "operator": "Garuda Indonesia",
    "flight_rules": "IFR",
    "flight_phase": "Landing",
    "departure": "CGK",
    "destination": "KNO",
    "flight_type": "Scheduled"
  },
  
  "weather": {
    "condition": "CAVOK",
    "visibility": "10km",
    "wind": "270/10",
    "cloud": "FEW020",
    "temperature": "28°C"
  },
  
  "remark": "Pilot misheard clearance",
  "status_investigasi": "Completed",
  
  "airport": {
    "id": 1,
    "name": "Cabang Medan",
    "city": "Medan",
    "provinsi": "Sumatera Utara",
    "level": "cabang_utama"
  },
  
  "formatted_date": "15 Mar 2024 14:30",
  "relative_date": "2 months ago"
}
```

---

## 📤 File Upload Endpoints

### 6. Upload Reports
**Endpoint:** `POST /reports/upload`  
**Deskripsi:** Upload file Excel/CSV dengan data laporan

**Headers:**
```
Content-Type: multipart/form-data
```

**Body:**
- `file`: Excel/CSV file

**Response (Background Processing):**
```json
{
  "message": "Upload dimulai. Proses berjalan di background.",
  "upload_id": "550e8400-e29b-41d4-a716-446655440000",
  "total_rows": 1500,
  "check_url": "/api/reports/upload-status/550e8400-e29b-41d4-a716-446655440000"
}
```

**Status Code:** `202 Accepted`

---

### 7. Check Upload Status
**Endpoint:** `GET /reports/upload-status/{uploadId}`  
**Deskripsi:** Cek progress upload yang sedang berjalan

**Response (Processing):**
```json
{
  "status": "processing",
  "progress": {
    "processed": 500,
    "total": 1500,
    "percentage": "33%"
  }
}
```

**Response (Completed):**
```json
{
  "status": "completed",
  "success_count": 1450,
  "skipped_count": 30,
  "error_count": 20,
  "errors": [
    {"row": 15, "reason": "Invalid date format"},
    {"row": 23, "reason": "Airport not found"}
  ]
}
```

---

## 🗑️ Data Management Endpoints

### 8. Delete Reports by Date Range
**Endpoint:** `DELETE /reports/range`  
**Deskripsi:** Soft delete laporan berdasarkan rentang tanggal

**Body:**
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-01-31"
}
```

**Response:**
```json
{
  "message": "Anda berhasil hapus 45 data dari tanggal 01 Jan 2024 s/d 31 Jan 2024.",
  "count": 45,
  "note": "Data dapat di-restore dalam 30 hari"
}
```

---

### 9. Restore Deleted Reports
**Endpoint:** `POST /reports/restore`  
**Deskripsi:** Restore laporan yang sudah di-soft delete

**Body:**
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-01-31"
}
```

**Response:**
```json
{
  "message": "Berhasil restore 45 data dari tanggal 01 Jan 2024 s/d 31 Jan 2024.",
  "count": 45
}
```

---

## 🔧 Utility Endpoints

### 10. Clear Cache
**Endpoint:** `POST /cache/clear`  
**Deskripsi:** Menghapus semua cache aplikasi

**Response:**
```json
{
  "success": true,
  "message": "Cache berhasil dihapus",
  "timestamp": "2024-03-15T14:30:00.000000Z"
}
```

---

### 11. Check Database
**Endpoint:** `GET /airports/check-db`  
**Deskripsi:** Diagnostic endpoint untuk cek konsistensi data

**Response:**
```json
{
  "status": "Diagnostic Result",
  "message": "Selamat! Semua data sinkron.",
  "total_mismatch": 0,
  "data_mismatch": [],
  "valid_ids_sample": [1, 2, 3, 4, 5]
}
```

---

### 12. Test Coordinates
**Endpoint:** `GET /airports/test-coordinates`  
**Deskripsi:** Test validasi format koordinat bandara

**Response:**
```json
{
  "message": "Coordinates validation",
  "sample": [
    {
      "id": 1,
      "name": "Cabang Medan",
      "coordinates_raw": "[-122.4194, 37.7749]",
      "coordinates_casted": [-122.4194, 37.7749],
      "coordinates_type": "array",
      "is_valid": true
    }
  ],
  "cache_status": "cached"
}
```

---

## 🔐 Rate Limiting

Beberapa endpoint memiliki rate limit untuk mencegah abuse:

- **Upload:** 10 requests per menit
- **Delete/Restore:** 20 requests per menit

**Response (Rate Limited):**
```json
{
  "message": "Too Many Requests",
  "retry_after": 60
}
```
**Status Code:** `429 Too Many Requests`

---

## ❌ Error Responses

### Standard Error Format:
```json
{
  "error": "Error Type",
  "message": "Human-readable error message",
  "debug": "Detailed error (only in development mode)"
}
```

### Common HTTP Status Codes:
- `200` - OK
- `202` - Accepted (Background processing)
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

---

## 🔄 Migration Guide (Old → New)

### Endpoint Changes:

| Old Endpoint | New Endpoint | Method Change |
|-------------|--------------|---------------|
| `/airports/{id}/reports-general` | `/airports/{id}/reports` | - |
| `/reports/{id}/detail` | `/reports/{id}` | - |
| `/upload-reports` | `/reports/upload` | - |
| `/upload-status/{id}` | `/reports/upload-status/{id}` | - |
| `POST /delete-reports` | `DELETE /reports/range` | ✅ POST → DELETE |
| `POST /restore-reports` | `POST /reports/restore` | - |
| `GET /clear-cache` | `POST /cache/clear` | ✅ GET → POST |

---

## 🎯 Best Practices

1. **Caching:** Data airports di-cache selama 5 menit
2. **Pagination:** Untuk large datasets, tambahkan parameter `?page=1&per_page=50`
3. **Error Handling:** Selalu cek response status code
4. **Date Format:** Gunakan format `YYYY-MM-DD` untuk tanggal
5. **Background Processing:** File besar (>1000 rows) diproses di background
6. **Soft Delete:** Data yang dihapus bisa di-restore dalam 30 hari

---

**Last Updated:** January 21, 2026  
**API Version:** 2.0  
**Laravel Version:** 9.x
