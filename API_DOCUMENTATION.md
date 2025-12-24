# 📚 API Documentation - Report Details

## Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. Get Report Detail
**Endpoint:** `GET /reports/{id}`

**Description:** Mengambil detail lengkap sebuah laporan berdasarkan ID

**Response:**
```json
{
  "id": 603,
  "report_date": "2024-12-22T04:02:59.000000Z",
  "input_date": null,
  "category": "Go Around",
  "classification": "Other Occurrences",
  "ssr_code": "A1234",
  "status": "Send to Analyst",
  "description": "07.19 LNI3898 PASSING FL150 VMC...",
  "location": "LUWUK",
  "ats_unit": "TWR",
  
  "flight_info": {
    "aircraft_id": "LNI3898",
    "registration": "PKLUF",
    "aircraft_type": "A320",
    "pic_name": "Captain John Doe",
    "operator": "BATIK AIR INDONESIA, PT",
    "flight_rules": "IFR",
    "flight_phase": "Approach",
    "departure": "WAAA",
    "destination": "WAFW",
    "flight_type": "Scheduled"
  },
  
  "weather": {
    "condition": "IMC",
    "visibility": "2500M",
    "wind": "220/11",
    "cloud": "BKN 2500",
    "temperature": "27/25"
  },
  
  "remark": "Additional remarks...",
  "status_investigasi": "Send To Investigator",
  
  "airport": {
    "id": "DPS",
    "name": "Cabang Denpasar",
    "city": "Denpasar",
    "provinsi": "Bali",
    "level": "cabang_utama"
  },
  
  "created_at": "2025-12-08T07:05:33.000000Z",
  "updated_at": "2025-12-08T07:05:33.000000Z",
  "formatted_date": "22 Dec 2024 04:02",
  "relative_date": "2 days ago"
}
```

**Error Response (404):**
```json
{
  "message": "No query results for model [App\\Models\\Report] 999"
}
```

---

### 2. Get Reports List (Simple)
**Endpoint:** `GET /airports/{airport_id}/reports-general`

**Description:** Mengambil daftar laporan (data ringkas untuk card/list)

**Query Parameters:**
- `category` (optional) - Filter by category
- `start_date` (optional) - Format: YYYY-MM-DD
- `end_date` (optional) - Format: YYYY-MM-DD
- `month` (optional) - Format: YYYY-MM (e.g., 2024-12)

**Example:**
```
GET /airports/DPS/reports-general?category=Go%20Around&month=2024-12
```

**Response:**
```json
[
  {
    "id": 603,
    "report_date": "2024-12-22T04:02:59.000000Z",
    "category": "Go Around",
    "status": "Send to Analyst"
  },
  {
    "id": 604,
    "report_date": "2024-12-23T10:15:00.000000Z",
    "category": "Bird Strike",
    "status": "Analysis Completed"
  }
]
```

---

### 3. Get Airport Hierarchy
**Endpoint:** `GET /airports/{airport_id}/hierarchy`

**Response:**
```json
{
  "cabang_pembantu": [
    {
      "id": "SUB",
      "name": "Cabang Surabaya",
      "city": "Surabaya",
      "provinsi": "Jawa Timur",
      "level": "cabang_pembantu",
      "reports_count": 150,
      "has_reports": true
    }
  ],
  "units": [
    {
      "id": "MLG",
      "name": "Unit Malang",
      "city": "Malang",
      "provinsi": "Jawa Timur",
      "level": "unit",
      "reports_count": 45,
      "has_reports": true
    }
  ],
  "total_children": 2
}
```

---

### 4. Get Airport Stats
**Endpoint:** `GET /airports/{airport_id}/stats`

**Query Parameters:**
- `start_date` (optional) - Format: YYYY-MM-DD
- `end_date` (optional) - Format: YYYY-MM-DD

**Response:**
```json
{
  "total": 500,
  "monthly_trend": [
    { "month": "2024-01", "count": 45 },
    { "month": "2024-02", "count": 52 }
  ],
  "top_categories": [
    { "category": "Go Around", "count": 125 },
    { "category": "Bird Strike", "count": 89 }
  ],
  "status_summary": {
    "open": 150,
    "closed": 300,
    "pending": 50
  }
}
```

---

## React Implementation Examples

### Fetch Report Detail
```javascript
const fetchReportDetail = async (reportId) => {
  try {
    const response = await fetch(`http://localhost:8000/api/reports/${reportId}`);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching report detail:', error);
    throw error;
  }
};

// Usage
const reportDetail = await fetchReportDetail(603);
```

### Display Report Detail Component
```jsx
import React, { useState, useEffect } from 'react';

const ReportDetail = ({ reportId }) => {
  const [report, setReport] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await fetch(`http://localhost:8000/api/reports/${reportId}`);
        const data = await response.json();
        setReport(data);
      } catch (error) {
        console.error('Error:', error);
      } finally {
        setLoading(false);
      }
    };
    
    fetchData();
  }, [reportId]);

  if (loading) return <div>Loading...</div>;
  if (!report) return <div>Report not found</div>;

  return (
    <div className="report-detail">
      <h2>{report.category}</h2>
      <p className="date">{report.formatted_date}</p>
      
      <div className="section">
        <h3>Location</h3>
        <p>{report.airport.name}, {report.airport.city}</p>
        <p>ATS Unit: {report.ats_unit}</p>
      </div>

      {report.flight_info.aircraft_id && (
        <div className="section">
          <h3>Flight Information</h3>
          <table>
            <tr><td>Flight Number:</td><td>{report.flight_info.aircraft_id}</td></tr>
            <tr><td>Registration:</td><td>{report.flight_info.registration}</td></tr>
            <tr><td>Aircraft Type:</td><td>{report.flight_info.aircraft_type}</td></tr>
            <tr><td>Operator:</td><td>{report.flight_info.operator}</td></tr>
            <tr><td>Phase:</td><td>{report.flight_info.flight_phase}</td></tr>
            <tr><td>Route:</td><td>{report.flight_info.departure} → {report.flight_info.destination}</td></tr>
          </table>
        </div>
      )}

      <div className="section">
        <h3>Weather Condition</h3>
        <table>
          <tr><td>Condition:</td><td>{report.weather.condition}</td></tr>
          <tr><td>Wind:</td><td>{report.weather.wind}</td></tr>
          <tr><td>Visibility:</td><td>{report.weather.visibility}</td></tr>
          <tr><td>Cloud:</td><td>{report.weather.cloud}</td></tr>
          <tr><td>Temperature:</td><td>{report.weather.temperature}</td></tr>
        </table>
      </div>

      <div className="section">
        <h3>Description</h3>
        <p className="description">{report.description}</p>
      </div>

      {report.remark && (
        <div className="section">
          <h3>Remarks</h3>
          <p>{report.remark}</p>
        </div>
      )}

      <div className="section">
        <h3>Status</h3>
        <p className={`status ${report.status.toLowerCase()}`}>
          {report.status}
        </p>
      </div>
    </div>
  );
};

export default ReportDetail;
```

---

## Mapping Excel Columns to Database

| Excel Column | Index | Database Field | Description |
|--------------|-------|----------------|-------------|
| C | 2 | report_date | Tanggal kejadian |
| E | 4 | location | Lokasi (nama cabang) |
| G | 6 | ats_unit | TWR/APP/ACC |
| H | 7 | category | Kategori laporan |
| I | 8 | classification | Klasifikasi detail |
| J | 9 | ssr_code | SSR Code |
| K | 10 | aircraft_id | Flight number |
| L | 11 | aircraft_reg | Registration |
| M | 12 | aircraft_type | Tipe pesawat |
| N | 13 | pic_name | Nama PIC |
| O | 14 | operator | Operator |
| P | 15 | flight_rules | IFR/VFR |
| Q | 16 | flight_phase | Phase of flight |
| R | 17 | departure_airport | Departure |
| S | 18 | destination_airport | Destination |
| T | 19 | flight_type | Scheduled/Non-scheduled |
| Y | 24 | weather_condition | IMC/VMC |
| AF | 31 | wind | Wind info |
| AG | 32 | visibility | Visibility |
| AI | 34 | cloud | Cloud info |
| AJ | 35 | temperature | Temperature |
| AK | 36 | remark | Remarks |
| AL | 37 | description | Kronologi lengkap |
| AM | 38 | status_investigasi | Status investigasi |
| AN | 39 | status | Status analyst |

---

## Testing the API

### Using cURL:
```bash
# Get report detail
curl http://localhost:8000/api/reports/603

# Get reports with filter
curl "http://localhost:8000/api/airports/DPS/reports-general?category=Go%20Around"
```

### Using Postman:
1. Create GET request to `http://localhost:8000/api/reports/603`
2. Click Send
3. View JSON response

---

## Status Mapping

| Excel Value | Mapped Status |
|-------------|---------------|
| "completed" atau "closed" | Analysis Completed |
| "analyst" | Analysis On Process |
| "investigator" | Send to Analyst |
| Default | Analysis On Process |

---

## Notes for Frontend Developer

1. **Lazy Loading**: List reports menggunakan endpoint `/reports-general` yang hanya return data ringkas (id, date, category, status)
2. **Detail on Demand**: Saat user klik card, baru fetch detail lengkap via `/reports/{id}`
3. **Caching**: Pertimbangkan cache detail report di state management (Redux/Zustand)
4. **Error Handling**: Always handle 404 error untuk report yang tidak ditemukan
5. **Date Formatting**: Backend sudah provide `formatted_date` dan `relative_date` yang ready to use
6. **Loading State**: Tampilkan skeleton/spinner saat fetch data

## Contact
Jika ada pertanyaan atau bug, silakan hubungi backend team.
