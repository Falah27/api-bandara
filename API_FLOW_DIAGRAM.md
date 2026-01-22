# 🔄 API Flow Diagram

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         REACT FRONTEND                                  │
│                      (localhost:3000)                                   │
│                                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐                │
│  │   MapDisplay  │  │AirportSidebar│  │UploadButton  │                │
│  │              │  │              │  │              │                │
│  │ - Show Map   │  │ - Stats      │  │ - Upload     │                │
│  │ - Markers    │  │ - Charts     │  │ - Progress   │                │
│  │ - Clusters   │  │ - Reports    │  │              │                │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘                │
│         │                  │                  │                         │
└─────────┼──────────────────┼──────────────────┼─────────────────────────┘
          │                  │                  │
          ▼                  ▼                  ▼
    ┌─────────────────────────────────────────────────┐
    │           API BASE URL                          │
    │      http://localhost:8000/api                  │
    └─────────────────────────────────────────────────┘
          │                  │                  │
          ▼                  ▼                  ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                      LARAVEL BACKEND                                    │
│                     (localhost:8000)                                    │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐ │
│  │                       routes/api.php                             │ │
│  │                                                                  │ │
│  │  /airports/*           → AirportController                       │ │
│  │  /reports/*            → AirportController / UploadController    │ │
│  │  /cache/clear          → Closure                                 │ │
│  └──────────────────────────────────────────────────────────────────┘ │
│                              ▼                                          │
│  ┌──────────────────────────────────────────────────────────────────┐ │
│  │                    CONTROLLERS                                   │ │
│  │                                                                  │ │
│  │  ┌──────────────────────┐  ┌─────────────────────────┐         │ │
│  │  │  AirportController   │  │ ReportUploadController  │         │ │
│  │  │                      │  │                         │         │ │
│  │  │ - index()            │  │ - upload()              │         │ │
│  │  │ - stats()            │  │ - uploadStatus()        │         │ │
│  │  │ - hierarchy()        │  │ - deleteRange()         │         │ │
│  │  │ - getReports()       │  │ - restoreRange()        │         │ │
│  │  │ - detailReport()     │  │                         │         │ │
│  │  └──────────────────────┘  └─────────────────────────┘         │ │
│  └──────────────────────────────────────────────────────────────────┘ │
│                              ▼                                          │
│  ┌──────────────────────────────────────────────────────────────────┐ │
│  │                        MODELS                                    │ │
│  │                                                                  │ │
│  │  ┌─────────────┐         ┌─────────────┐                        │ │
│  │  │  Airport    │◄────────┤   Report    │                        │ │
│  │  │             │  1:N    │             │                        │ │
│  │  │ - id        │         │ - id        │                        │ │
│  │  │ - name      │         │ - airport_id│                        │ │
│  │  │ - city      │         │ - category  │                        │ │
│  │  │ - coordinates│        │ - status    │                        │ │
│  │  └─────────────┘         └─────────────┘                        │ │
│  └──────────────────────────────────────────────────────────────────┘ │
│                              ▼                                          │
│  ┌──────────────────────────────────────────────────────────────────┐ │
│  │                       DATABASE                                   │ │
│  │                      (MySQL/MariaDB)                             │ │
│  │                                                                  │ │
│  │  ┌─────────────────┐     ┌──────────────────┐                  │ │
│  │  │ airports table  │     │  reports table   │                  │ │
│  │  │                 │     │                  │                  │ │
│  │  │ Stores:         │     │  Stores:         │                  │ │
│  │  │ - Airport info  │     │  - Report data   │                  │ │
│  │  │ - Coordinates   │     │  - Statistics    │                  │ │
│  │  │ - Hierarchy     │     │  - Timestamps    │                  │ │
│  │  └─────────────────┘     └──────────────────┘                  │ │
│  └──────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Request Flow Examples

### 1. Load Map Data
```
┌──────────┐
│ React    │ GET /api/airports
│ MapDisplay│────────────────────────────┐
└──────────┘                            │
                                        ▼
                              ┌───────────────────┐
                              │ AirportController │
                              │   index()         │
                              └─────────┬─────────┘
                                        │
                                        ▼
                              ┌───────────────────┐
                              │  Cache Check      │
                              │  (5 min TTL)      │
                              └─────────┬─────────┘
                                        │
                        ┌───────────────┴───────────────┐
                        │ Hit                           │ Miss
                        ▼                               ▼
              ┌─────────────────┐           ┌─────────────────┐
              │ Return Cached   │           │ Query Database  │
              └─────────────────┘           │ Count Reports   │
                                            │ Store in Cache  │
                                            └─────────────────┘
                                                    │
                                                    ▼
┌──────────┐                              ┌─────────────────┐
│ React    │◄─────────────────────────────│ JSON Response   │
│ Render   │ [airports with coords]       └─────────────────┘
└──────────┘
```

---

### 2. Click Airport → Show Sidebar
```
┌──────────┐
│ User     │ Click Marker (airport_id: 1)
│ Click    │────────────────────────────────┐
└──────────┘                                │
                                            ▼
┌──────────────┐                 ┌─────────────────────┐
│ React        │                 │ 3 Parallel Requests │
│ Sidebar Open │                 └─────────┬───────────┘
└──────────────┘                           │
                        ┌──────────────────┼──────────────────┐
                        │                  │                  │
                        ▼                  ▼                  ▼
            GET /airports/1/stats  GET /airports/1/  GET /airports/1/
                                        hierarchy         reports
                        │                  │                  │
                        ▼                  ▼                  ▼
              ┌─────────────────┐ ┌──────────────┐ ┌────────────────┐
              │ Statistics       │ │ Children     │ │ Reports List   │
              │ - Total          │ │ - Branches   │ │ - Summary Only │
              │ - Trends         │ │ - Units      │ │ (id, date,     │
              │ - Charts         │ │ - Count      │ │  category)     │
              └─────────────────┘ └──────────────┘ └────────────────┘
                        │                  │                  │
                        └──────────────────┴──────────────────┘
                                           │
                                           ▼
┌──────────────┐                 ┌─────────────────┐
│ React        │◄────────────────│ Render Sidebar  │
│ Display Data │                 │ - Charts        │
└──────────────┘                 │ - List          │
                                 │ - Hierarchy     │
                                 └─────────────────┘
```

---

### 3. Click Report Card → Show Detail
```
┌──────────┐
│ User     │ Click Report Card (id: 123)
│ Click    │────────────────────────────┐
└──────────┘                            │
                                        ▼
┌──────────────┐            GET /api/reports/123
│ React        │─────────────────────────────────┐
│ Modal Open   │                                 │
└──────────────┘                                 ▼
                                    ┌────────────────────────┐
                                    │ AirportController      │
                                    │ detailReport(123)      │
                                    └───────────┬────────────┘
                                                │
                                                ▼
                                    ┌────────────────────────┐
                                    │ Query Database         │
                                    │ Report::with('airport')│
                                    │   ->find(123)          │
                                    └───────────┬────────────┘
                                                │
                                                ▼
                                    ┌────────────────────────┐
                                    │ Full Report Data       │
                                    │ - Description          │
                                    │ - Flight Info          │
                                    │ - Weather              │
                                    │ - Airport Info         │
                                    └───────────┬────────────┘
                                                │
                                                ▼
┌──────────────┐                    ┌────────────────────────┐
│ React        │◄───────────────────│ JSON Response          │
│ Show Modal   │ Full detail data   └────────────────────────┘
└──────────────┘
```

---

### 4. Upload File
```
┌──────────┐
│ User     │ Select Excel File
│ Upload   │────────────────────────────┐
└──────────┘                            │
                                        ▼
┌──────────────┐         POST /api/reports/upload
│ React        │         FormData (file)
│ UploadButton │──────────────────────────────────┐
└──────────────┘                                  │
                                                  ▼
                                    ┌──────────────────────────┐
                                    │ ReportUploadController   │
                                    │ upload()                 │
                                    └─────────┬────────────────┘
                                              │
                                              ▼
                                    ┌──────────────────────────┐
                                    │ Parse Excel File         │
                                    │ Generate Upload ID       │
                                    └─────────┬────────────────┘
                                              │
                                              ▼
                                    ┌──────────────────────────┐
                                    │ Dispatch to Queue        │
                                    │ ProcessReportUpload::    │
                                    │   dispatch($data, $id)   │
                                    └─────────┬────────────────┘
                                              │
                                              ▼
┌──────────────┐                    ┌──────────────────────────┐
│ React        │◄───────────────────│ 202 Accepted             │
│ Start Polling│ {upload_id, url}   │ Background Processing    │
└──────┬───────┘                    └──────────────────────────┘
       │
       │ Every 3 seconds
       │
       ▼
GET /api/reports/upload-status/{id}
       │
       ▼
┌──────────────────────────┐
│ Check Cache:             │
│ - upload_progress_{id}   │
│ - upload_result_{id}     │
└─────────┬────────────────┘
          │
          ├─ Processing → Return Progress (%)
          └─ Completed  → Return Results
                          (success, errors, skipped)
          │
          ▼
┌──────────────┐
│ React        │ Show Progress / Results
│ Update UI    │ Reload page on complete
└──────────────┘
```

---

### 5. Delete Reports
```
┌──────────┐
│ User     │ Select Date Range
│ Delete   │ Click Delete Button
└──────────┘────────────────────┐
                                │
                                ▼
┌──────────────┐    DELETE /api/reports/range
│ React        │    Body: {start_date, end_date}
│ DeleteButton │─────────────────────────────────┐
└──────────────┘                                 │
                                                 ▼
                                    ┌────────────────────────┐
                                    │ ReportUploadController │
                                    │ deleteRange()          │
                                    └───────────┬────────────┘
                                                │
                                                ▼
                                    ┌────────────────────────┐
                                    │ Soft Delete            │
                                    │ Report::whereDate()    │
                                    │   ->delete()           │
                                    └───────────┬────────────┘
                                                │
                                                ▼
                                    ┌────────────────────────┐
                                    │ Clear Cache            │
                                    │ - airports_index       │
                                    │ - hierarchy tags       │
                                    └───────────┬────────────┘
                                                │
                                                ▼
┌──────────────┐                    ┌────────────────────────┐
│ React        │◄───────────────────│ Success Response       │
│ Reload Page  │ {count, message}   └────────────────────────┘
└──────────────┘
```

---

## Data Flow Summary

```
┌─────────────────────────────────────────────────────────────────────┐
│                        DATA FLOW LAYERS                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  1. PRESENTATION LAYER (React Components)                          │
│     ├─ MapDisplay: Visualize airports on map                       │
│     ├─ AirportSidebar: Show statistics & reports                   │
│     ├─ UploadButton: File upload interface                         │
│     └─ DeleteButton: Data management                               │
│                          ▼                                          │
│  2. API LAYER (RESTful Endpoints)                                  │
│     ├─ /airports/*  : Airport data & hierarchy                     │
│     ├─ /reports/*   : Report details & management                  │
│     └─ /cache/clear : System utilities                             │
│                          ▼                                          │
│  3. CONTROLLER LAYER (Business Logic)                              │
│     ├─ AirportController: Data processing & queries                │
│     └─ ReportUploadController: File processing & jobs              │
│                          ▼                                          │
│  4. MODEL LAYER (Data Abstraction)                                 │
│     ├─ Airport Model: Airport entity & relations                   │
│     └─ Report Model: Report entity & scopes                        │
│                          ▼                                          │
│  5. DATABASE LAYER (Data Persistence)                              │
│     ├─ airports table: Airport records                             │
│     └─ reports table: Report records (soft deletes)                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Caching Strategy

```
┌─────────────────────────────────────────────────────────────────────┐
│                          CACHE LAYERS                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Level 1: airports_index (5 minutes)                               │
│  ├─ All airports with coordinates                                  │
│  ├─ Report counts per airport                                      │
│  └─ Cleared on: upload, delete                                     │
│                                                                     │
│  Level 2: airport_hierarchy_{id} (5 minutes)                       │
│  ├─ Children (branches & units)                                    │
│  ├─ Report counts per child                                        │
│  └─ Cleared on: upload, delete                                     │
│                                                                     │
│  Level 3: airport_query_strategy_{id} (1 hour)                     │
│  ├─ Smart query detection                                          │
│  ├─ Name/city/direct matching                                      │
│  └─ Cleared on: manual cache clear                                 │
│                                                                     │
│  Level 4: Upload Progress (Temp)                                   │
│  ├─ upload_progress_{id}                                           │
│  ├─ upload_result_{id}                                             │
│  └─ TTL: Auto-expire after completion                              │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

**Last Updated:** January 21, 2026
