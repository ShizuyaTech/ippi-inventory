# Material Control System
## Aplikasi Inventory Management untuk Gudang Manufaktur Stamping

Aplikasi web inventory management yang mirip dengan SAP tapi lebih sederhana dan mudah digunakan, khusus untuk gudang manufaktur stamping.

## 🎯 Fitur Utama

### Master Data
- **Material Master** - Kelola data material (Raw Material, WIP, Finished Goods, Consumables, Tools)
- **Supplier Master** - Data supplier untuk pembelian material
- **Customer Master** - Data customer untuk penjualan produk
- **Warehouse Master** - Lokasi penyimpanan material

### Transaksi Stock
- **Stock IN** - Penerimaan material dari supplier
- **Stock OUT** - Pengeluaran material ke customer/produksi
- **Stock Adjustment** - Penyesuaian stock
- **Stock Opname** - Penghitungan fisik stock dengan approval workflow (DRAFT → APPROVED → POSTED)

### Dashboard & Monitoring
- Overview total material dan nilai stock
- Alert material dengan stock minimum
- Aktivitas stock harian (IN/OUT)
- History transaksi terbaru

## 📊 Struktur Database

### Tables
1. **materials** - Master material
2. **suppliers** - Master supplier
3. **customers** - Master customer
4. **warehouses** - Master warehouse/lokasi
5. **stock_transactions** - Transaksi stock (IN/OUT/ADJUSTMENT)
6. **stock_opname** - Stock opname/stock count
7. **users** - User management

### Material Categories
- Raw Material (Bahan Baku)
- WIP (Work In Process)
- Finished Goods (Barang Jadi)
- Consumables (Barang Habis Pakai)
- Tools (Peralatan)

### Unit of Measure (UOM)
- PCS (Pieces)
- KG (Kilogram)
- TON
- M (Meter)
- M2 (Square Meter)
- ROLL
- BOX

## 🚀 Cara Menggunakan

### Login
- Email: `admin@materialcontrol.com`
- Password: `password`

### Workflow Stock Opname
1. Buat stock opname baru (status: DRAFT)
2. Input physical stock hasil penghitungan fisik
3. Sistem otomatis hitung difference (physical - system stock)
4. Approve stock opname (status: APPROVED)
5. Post stock opname (status: POSTED)
6. Sistem otomatis create adjustment transaction

### Menu Navigasi
- **Dashboard** - Overview dan monitoring
- **Material** - Kelola master material
- **Supplier** - Kelola master supplier
- **Customer** - Kelola master customer
- **Warehouse** - Kelola master warehouse
- **Stock Transaction** - Transaksi IN/OUT/Adjustment
- **Stock Opname** - Stock counting

## 📝 Sample Data

Aplikasi sudah include sample data:
- 10 Material (Raw Material, WIP, Finished Goods, Consumables, Tools)
- 3 Suppliers
- 3 Customers
- 3 Warehouses

## 🎨 Teknologi

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Tailwind CSS
- **Icons**: Font Awesome 6
- **Database**: MySQL/PostgreSQL/SQLite

## 💡 Tips Penggunaan

1. **Stock Minimum Alert**: Material dengan current stock ≤ minimum stock akan ditandai merah
2. **Transaction Number**: Auto-generated dengan format TRX-YYYYMMDD-XXXX
3. **Opname Number**: Auto-generated dengan format OPN-YYYYMMDD-XXXX
4. **Stock Auto Update**: Stock akan otomatis terupdate saat create transaction
5. **Material Location**: Bisa menggunakan format seperti WH01-A-001 untuk kemudahan tracking

## 🔒 Keamanan

- Semua transaction tercatat dengan user dan timestamp
- Soft restriction pada delete: tidak bisa delete jika ada transaksi terkait
- Stock opname harus diapprove sebelum mempengaruhi stock

## 📱 User Interface

- **Responsive Design**: Bisa diakses dari desktop dan mobile
- **Clean & Simple**: Mirip SAP tapi lebih intuitif
- **Color Coding**: 
  - Hijau untuk Stock IN
  - Merah untuk Stock OUT
  - Biru untuk Adjustment
  - Kuning untuk warning/approval needed

## 🔄 Development Roadmap

Fitur yang bisa ditambahkan:
- [ ] Export ke Excel/PDF
- [ ] Barcode scanning
- [ ] Multi-user roles & permissions
- [ ] Approval workflow untuk transactions
- [ ] Stock reservation
- [ ] Batch/Lot tracking
- [ ] Serial number tracking
- [ ] Stock aging report
- [ ] Material movement history
- [ ] Cost tracking & valuation (FIFO/LIFO/Average)

## 📧 Support

Untuk pertanyaan atau request fitur tambahan, silakan hubungi administrator.
