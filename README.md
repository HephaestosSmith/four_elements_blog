# four_elements_blog（前後端分離）

## 架構說明
- `frontend`：Vue 3（目前專案根目錄）
- `backend`：PHP API（位於 `backend/public`，保留原有 `/controllers/*.php` 路徑）
- `mysql`：MySQL 8

> 前端以 `/api` 為 baseURL，開發時由 Vue dev server proxy 到 `http://localhost:8000`（backend）。

## 啟動方式（Docker，推薦）
```bash
docker compose up -d --build
```

啟動後：
- 前端：http://localhost:8080
- 後端 API：http://localhost:8000
- MySQL：localhost:3306

## 純前端本機啟動（選用）
```bash
npm install
npm run serve
```

## 目前 API 相容性
- 前端呼叫維持 `axios.defaults.baseURL = '/api'`
- API 路徑維持：
  - `/api/controllers/Command.php`
  - `/api/controllers/Install.php`
  - `/api/controllers/upload.php`

## 下一步（Laravel 化）
本次先完成前後端分離與 API 路徑相容。
若要完整遷移 Laravel，可在 `backend` 目錄建立 Laravel 專案，並逐步把 `controllers/*.php` 邏輯搬移到 Laravel 的 `routes/api.php` + `app/Http/Controllers`。
