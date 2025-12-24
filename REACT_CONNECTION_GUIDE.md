# 🔌 Cara Menghubungkan React dengan Laravel API

## Setup Laravel (Backend) - ✅ Sudah Selesai

### 1. CORS Configuration
File: `config/cors.php` sudah di-configure untuk menerima request dari:
- `http://localhost:3000` (Create React App)
- `http://localhost:5173` (Vite)
- `http://localhost:3001` (Alternative port)

### 2. Start Laravel Server
```bash
cd C:\xampp\htdocs\api-bandara
php artisan serve
```
Server akan berjalan di: `http://localhost:8000`

**Atau gunakan XAMPP:**
Akses via: `http://localhost/api-bandara/public/api`

---

## Setup React (Frontend)

### Struktur Folder React yang Disarankan
```
your-react-project/
├── src/
│   ├── config/
│   │   └── api.js              # API configuration
│   ├── services/
│   │   ├── reportService.js    # Report API calls
│   │   └── airportService.js   # Airport API calls
│   ├── hooks/
│   │   ├── useReportDetail.js  # Custom hook untuk detail
│   │   └── useReports.js       # Custom hook untuk list
│   ├── components/
│   │   ├── ReportList.jsx
│   │   ├── ReportDetail.jsx
│   │   └── ReportCard.jsx
│   └── App.jsx
```

### 1. Install Dependencies (Jika belum)
```bash
cd /path/to/your-react-project
npm install axios
# atau
npm install
```

### 2. Buat File Konfigurasi API

**File: `src/config/api.js`**
```javascript
const API_CONFIG = {
  // Pilih salah satu sesuai setup kamu:
  
  // Jika pakai php artisan serve:
  BASE_URL: 'http://localhost:8000/api',
  
  // Jika pakai XAMPP:
  // BASE_URL: 'http://localhost/api-bandara/public/api',
  
  // Jika pakai Laravel Valet/Herd:
  // BASE_URL: 'http://api-bandara.test/api',
};

export default API_CONFIG;
```

### 3. Buat Service untuk API Calls

**File: `src/services/reportService.js`**
```javascript
import axios from 'axios';
import API_CONFIG from '../config/api';

// Create axios instance dengan base config
const apiClient = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Report Service
const reportService = {
  // Get report detail
  getReportDetail: async (reportId) => {
    try {
      const response = await apiClient.get(`/reports/${reportId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching report detail:', error);
      throw error;
    }
  },

  // Get reports list (simple)
  getReportsList: async (airportId, filters = {}) => {
    try {
      const params = new URLSearchParams(filters).toString();
      const url = `/airports/${airportId}/reports-general${params ? '?' + params : ''}`;
      const response = await apiClient.get(url);
      return response.data;
    } catch (error) {
      console.error('Error fetching reports list:', error);
      throw error;
    }
  },

  // Upload Excel file
  uploadExcel: async (file, onProgress) => {
    try {
      const formData = new FormData();
      formData.append('file', file);

      const response = await apiClient.post('/upload-reports', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
          if (onProgress) {
            const percentCompleted = Math.round(
              (progressEvent.loaded * 100) / progressEvent.total
            );
            onProgress(percentCompleted);
          }
        },
      });

      return response.data;
    } catch (error) {
      console.error('Error uploading file:', error);
      throw error;
    }
  },

  // Delete reports by date range
  deleteReports: async (startDate, endDate) => {
    try {
      const response = await apiClient.post('/delete-reports', {
        start_date: startDate,
        end_date: endDate,
      });
      return response.data;
    } catch (error) {
      console.error('Error deleting reports:', error);
      throw error;
    }
  },
};

export default reportService;
```

**File: `src/services/airportService.js`**
```javascript
import axios from 'axios';
import API_CONFIG from '../config/api';

const apiClient = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

const airportService = {
  // Get all airports
  getAirports: async () => {
    try {
      const response = await apiClient.get('/airports');
      return response.data;
    } catch (error) {
      console.error('Error fetching airports:', error);
      throw error;
    }
  },

  // Get airport hierarchy
  getHierarchy: async (airportId) => {
    try {
      const response = await apiClient.get(`/airports/${airportId}/hierarchy`);
      return response.data;
    } catch (error) {
      console.error('Error fetching hierarchy:', error);
      throw error;
    }
  },

  // Get airport stats
  getStats: async (airportId, filters = {}) => {
    try {
      const params = new URLSearchParams(filters).toString();
      const url = `/airports/${airportId}/stats${params ? '?' + params : ''}`;
      const response = await apiClient.get(url);
      return response.data;
    } catch (error) {
      console.error('Error fetching stats:', error);
      throw error;
    }
  },
};

export default airportService;
```

### 4. Buat Custom Hook (Optional tapi Recommended)

**File: `src/hooks/useReportDetail.js`**
```javascript
import { useState, useEffect } from 'react';
import reportService from '../services/reportService';

const useReportDetail = (reportId) => {
  const [report, setReport] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!reportId) return;

    const fetchReport = async () => {
      try {
        setLoading(true);
        const data = await reportService.getReportDetail(reportId);
        setReport(data);
        setError(null);
      } catch (err) {
        setError(err.message || 'Failed to fetch report');
        setReport(null);
      } finally {
        setLoading(false);
      }
    };

    fetchReport();
  }, [reportId]);

  return { report, loading, error };
};

export default useReportDetail;
```

### 5. Contoh Component

**File: `src/components/ReportDetail.jsx`**
```javascript
import React from 'react';
import useReportDetail from '../hooks/useReportDetail';
import './ReportDetail.css';

const ReportDetail = ({ reportId, onClose }) => {
  const { report, loading, error } = useReportDetail(reportId);

  if (loading) {
    return (
      <div className="report-detail-modal">
        <div className="loading">Loading...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="report-detail-modal">
        <div className="error">Error: {error}</div>
        <button onClick={onClose}>Close</button>
      </div>
    );
  }

  if (!report) return null;

  return (
    <div className="report-detail-modal">
      <div className="modal-content">
        <div className="modal-header">
          <h2>{report.category}</h2>
          <button className="close-btn" onClick={onClose}>×</button>
        </div>

        <div className="modal-body">
          <div className="section">
            <h3>Informasi Dasar</h3>
            <table>
              <tbody>
                <tr><td>Tanggal:</td><td>{report.formatted_date}</td></tr>
                <tr><td>Lokasi:</td><td>{report.location}</td></tr>
                <tr><td>Airport:</td><td>{report.airport.name}, {report.airport.city}</td></tr>
                <tr><td>ATS Unit:</td><td>{report.ats_unit}</td></tr>
                <tr><td>Status:</td><td><span className={`status ${report.status}`}>{report.status}</span></td></tr>
              </tbody>
            </table>
          </div>

          {report.flight_info.aircraft_id && (
            <div className="section">
              <h3>Informasi Penerbangan</h3>
              <table>
                <tbody>
                  <tr><td>Flight Number:</td><td>{report.flight_info.aircraft_id}</td></tr>
                  <tr><td>Registration:</td><td>{report.flight_info.registration}</td></tr>
                  <tr><td>Aircraft Type:</td><td>{report.flight_info.aircraft_type}</td></tr>
                  <tr><td>Operator:</td><td>{report.flight_info.operator}</td></tr>
                  <tr><td>PIC:</td><td>{report.flight_info.pic_name}</td></tr>
                  <tr><td>Phase:</td><td>{report.flight_info.flight_phase}</td></tr>
                  <tr><td>Route:</td><td>{report.flight_info.departure} → {report.flight_info.destination}</td></tr>
                  <tr><td>Flight Type:</td><td>{report.flight_info.flight_type}</td></tr>
                </tbody>
              </table>
            </div>
          )}

          <div className="section">
            <h3>Kondisi Cuaca</h3>
            <table>
              <tbody>
                <tr><td>Condition:</td><td>{report.weather.condition}</td></tr>
                <tr><td>Wind:</td><td>{report.weather.wind}</td></tr>
                <tr><td>Visibility:</td><td>{report.weather.visibility}</td></tr>
                <tr><td>Cloud:</td><td>{report.weather.cloud}</td></tr>
                <tr><td>Temperature:</td><td>{report.weather.temperature}</td></tr>
              </tbody>
            </table>
          </div>

          <div className="section">
            <h3>Kronologi</h3>
            <div className="description">{report.description}</div>
          </div>

          {report.remark && (
            <div className="section">
              <h3>Catatan</h3>
              <div className="remark">{report.remark}</div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default ReportDetail;
```

**File: `src/components/ReportList.jsx`**
```javascript
import React, { useState, useEffect } from 'react';
import reportService from '../services/reportService';
import ReportDetail from './ReportDetail';

const ReportList = ({ airportId }) => {
  const [reports, setReports] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedReportId, setSelectedReportId] = useState(null);

  useEffect(() => {
    const fetchReports = async () => {
      try {
        setLoading(true);
        const data = await reportService.getReportsList(airportId);
        setReports(data);
      } catch (error) {
        console.error('Error:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchReports();
  }, [airportId]);

  if (loading) return <div>Loading...</div>;

  return (
    <div className="report-list">
      <h2>Reports</h2>
      <div className="reports-grid">
        {reports.map((report) => (
          <div 
            key={report.id} 
            className="report-card"
            onClick={() => setSelectedReportId(report.id)}
          >
            <div className="category">{report.category}</div>
            <div className="date">{new Date(report.report_date).toLocaleDateString()}</div>
            <div className={`status ${report.status}`}>{report.status}</div>
          </div>
        ))}
      </div>

      {selectedReportId && (
        <ReportDetail 
          reportId={selectedReportId}
          onClose={() => setSelectedReportId(null)}
        />
      )}
    </div>
  );
};

export default ReportList;
```

---

## Testing Connection

### 1. Test dari Browser Console
Buka browser console (F12) di React app dan jalankan:

```javascript
// Test connection
fetch('http://localhost:8000/api/airports')
  .then(res => res.json())
  .then(data => console.log('Airports:', data))
  .catch(err => console.error('Error:', err));

// Test report detail
fetch('http://localhost:8000/api/reports/603')
  .then(res => res.json())
  .then(data => console.log('Report:', data))
  .catch(err => console.error('Error:', err));
```

### 2. Start Both Servers

**Terminal 1 - Laravel:**
```bash
cd C:\xampp\htdocs\api-bandara
php artisan serve
# Running on http://localhost:8000
```

**Terminal 2 - React:**
```bash
cd /path/to/your-react-project
npm start
# Running on http://localhost:3000
```

---

## Troubleshooting

### Error: CORS policy
**Solusi:** Pastikan Laravel server sudah running dan config/cors.php sudah benar

### Error: Network Error / Failed to fetch
**Solusi:** 
1. Cek Laravel server masih running
2. Cek URL di `src/config/api.js` sudah benar
3. Cek firewall tidak memblokir port 8000

### Error: 404 Not Found
**Solusi:** Pastikan route API sudah benar, cek dengan:
```bash
php artisan route:list
```

### Error: 500 Internal Server Error
**Solusi:** Cek log Laravel di `storage/logs/laravel.log`

---

## Environment Variables (Production)

Untuk production, gunakan `.env` file di React:

**File: `.env`**
```
REACT_APP_API_URL=http://your-production-url.com/api
```

**Update `src/config/api.js`:**
```javascript
const API_CONFIG = {
  BASE_URL: process.env.REACT_APP_API_URL || 'http://localhost:8000/api',
};
```

---

## Next Steps

1. ✅ Copy file-file service & hook ke project React kamu
2. ✅ Update `API_CONFIG.BASE_URL` sesuai setup kamu
3. ✅ Test connection dengan browser console
4. ✅ Implement components sesuai kebutuhan
5. ✅ Build & deploy!

Jika ada error, cek console browser dan `storage/logs/laravel.log` 🚀
