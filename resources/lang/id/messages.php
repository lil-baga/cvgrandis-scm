<?php

return [
    // General
    'app_name' => 'StockHub',
    'welcome' => 'Selamat Datang',
    'login_title' => 'Masuk - Selamat Datang Kembali',
    'login_subtitle' => 'Masuk ke akun Anda',
    'email_placeholder' => 'Masukkan email Anda',
    'password_placeholder' => 'Masukkan kata sandi Anda',
    'login_button' => 'Masuk',
    'logout_button' => 'Keluar',
    'error_any' => 'Terjadi kesalahan.',
    'success_general' => 'Berhasil',
    'back_to_list' => 'Kembali ke Daftar',
    'home' => 'Beranda',
    'processing' => 'Memproses...',
    'search_placeholder' => 'Cari...',
    'filter_button' => 'Filter',
    'reset_button' => 'Atur Ulang',
    'actions' => 'Aksi',
    'view_button' => 'Lihat',
    'edit_button' => 'Ubah',
    'delete_button' => 'Hapus',
    'create_button' => 'Buat',
    'update_button' => 'Perbarui',
    'cancel_button' => 'Batal',
    'save_button' => 'Simpan',
    'no_data_found' => 'Data tidak ditemukan.',
    'are_you_sure_delete' => 'Apakah Anda yakin ingin menghapus ini?',
    'attention_needed' => 'Perhatian Dibutuhkan',
    'hey_user' => 'Hai, :name',
    'all_rights_reserved' => 'Hak Cipta Dilindungi.',
    'follow_us_social' => 'Ikuti kami di media sosial:',
    'details' => 'Detail',
    'confirm_delete_action_cannot_be_undone' => 'Apakah Anda yakin ingin menghapus :item ini? Tindakan ini tidak dapat diurungkan.',
    'optional_label' => '(Opsional)',
    'required_field_indicator' => '<span class="text-danger">*</span>', // For *
    'leave_blank_password_info' => 'Biarkan kosong untuk mempertahankan kata sandi saat ini.',
    'note' => 'Catatan',
    'notes' => 'Catatan',
    'back_button' => 'Kembali',
    'close_button' => 'Tutup', // For modal close buttons or similar

    // Navigation
    'nav_home' => 'Beranda',
    'nav_stock_adjustment' => 'Penyesuaian Stok',
    'nav_products' => 'Produk',
    'nav_inventory' => 'Inventaris',
    'nav_recipes' => 'Resep',
    'nav_suppliers' => 'Pemasok',
    'nav_users' => 'Pengguna',

    // Notifications
    'jit_reorder_signals' => 'Sinyal Pemesanan Ulang JIT',
    'no_new_jit_signals' => 'Tidak ada sinyal JIT baru.',
    'view_all_jit_signals' => 'Lihat semua Sinyal JIT',

    // Home Page
    'home_welcome_title' => 'Selamat Datang di StockHub',
    'home_welcome_subtitle' => 'Pusat kendali Anda untuk mengelola inventaris kafe.',
    'home_card_stock_adjustment_title' => 'Penyesuaian Stok',
    'home_card_stock_adjustment_text' => 'Catat penambahan atau pengurangan stok secara manual.',
    'home_card_products_title' => 'Produk',
    'home_card_products_text' => 'Kelola produk kafe Anda seperti minuman dan makanan ringan.',
    'home_card_raw_materials_title' => 'Bahan Baku',
    'home_card_raw_materials_text' => 'Lacak dan perbarui inventaris dan tingkat stok Anda.',
    'home_card_recipes_title' => 'Resep (BOM)',
    'home_card_recipes_text' => 'Tentukan resep dan penggunaan bahan untuk produk.',
    'home_card_suppliers_title' => 'Pemasok',
    'home_card_suppliers_text' => 'Lihat dan kelola pemasok bahan baku Anda.',
    'home_card_users_title' => 'Pengguna',
    'home_card_users_text' => 'Kelola akun pengguna dan peran mereka.',
    'home_jit_signals_title' => 'Sinyal JIT',
    'home_jit_order_item' => 'Pesan: :item_name',
    'home_jit_all_good_title' => 'Semua Baik!',
    'home_jit_all_good_subtitle' => 'Tidak ada sinyal JIT aktif. Inventaris optimal.',

    // Products
    'product_list' => 'Daftar Produk',
    'add_product' => 'Tambah Produk',
    'edit_product' => 'Ubah Produk',
    'product_details' => 'Detail Produk',
    'product_details_for' => 'Detail Produk: :name',
    'product_name' => 'Nama Produk',
    'selling_price' => 'Harga Jual',
    'category' => 'Kategori',
    'status' => 'Status',
    'active' => 'Aktif',
    'inactive' => 'Tidak Aktif',
    'description' => 'Deskripsi',
    'product_image' => 'Gambar Produk',
    'upload_image_instruction' => 'Klik untuk mengunggah atau seret & lepas',
    'image_preview' => 'Pratinjau Gambar',
    'base_price' => 'Harga Dasar',
    'base_price_from_bom' => 'Harga Dasar (dari BOM)',
    'producible_units' => 'Unit Dapat Diproduksi',
    'preparation_time' => 'Waktu Persiapan',
    'created_at' => 'Dibuat Pada',
    'updated_at' => 'Diperbarui Pada',
    'search_by_product_name' => 'Cari berdasarkan nama produk...',
    'all_categories' => 'Semua Kategori',
    'all_statuses' => 'Semua Status',
    'no_products_criteria' => 'Produk tidak ditemukan sesuai kriteria Anda.',
    'add_new_product_title' => 'Tambah Produk Baru',
    'add_new_product_subtitle' => 'Masukkan detail untuk produk baru.',
    'edit_product_title' => 'Ubah Produk',
    'modifying_product' => 'Mengubah: :name',
    'view_product' => 'Lihat Produk',
    'additional_details' => 'Detail Tambahan',
    'product_code_label' => 'Kode Produk: :code',
    'recommended_image_size' => 'Disarankan: 800x600px',
    'select_category_placeholder' => 'Pilih Kategori',


    // Raw Materials
    'raw_material_list' => 'Daftar Bahan Baku',
    'add_raw_material' => 'Tambah Bahan Baku',
    'edit_raw_material' => 'Ubah Bahan Baku',
    'raw_material_details_for' => 'Bahan Baku: :name',
    'raw_material_name' => 'Nama Bahan Baku',
    'current_stock' => 'Stok Saat Ini',
    'stock_unit' => 'Satuan Stok',
    'usage_unit' => 'Satuan Penggunaan',
    'unit_price' => 'Harga Satuan',
    'conversion_factor' => 'Faktor Konversi',
    'inventory_management_jit' => 'Manajemen Inventaris (Parameter JIT/Kanban)',
    'jit_parameters_info' => 'Nilai-nilai ini digunakan untuk notifikasi JIT dan analisis inventaris. Stok Pengaman dan Titik Sinyal dihitung secara otomatis berdasarkan penggunaan dan kebijakan.',
    'lead_time_days' => 'Waktu Tunggu (Hari)',
    'supplier_waiting_time' => 'Waktu tunggu pemasok.',
    'safety_stock_policy' => 'Kebijakan Stok Pengaman',
    'coverage_in_days' => 'Cakupan dalam hari.',
    'reorder_quantity' => 'Kuantitas Pemesanan Ulang',
    'units_per_order_in_stock_unit_form' => 'Unit per pesanan (dalam <strong id="displayStockUnit4">:unit</strong>).',
    'safety_stock_auto_in_stock_unit_form' => 'Stok Pengaman <small class="text-muted">(Otomatis, dalam <strong id="displayStockUnit5">:unit</strong>)</small>',
    'signal_point_auto_in_stock_unit_form' => 'Titik Sinyal <small class="text-muted">(Otomatis, dalam <strong id="displayStockUnit6">:unit</strong>)</small>',
    'conversion_factor_info_form' => 'Jumlah <strong id="displayUsageUnit">:usage_unit</strong> dalam 1 <strong id="displayStockUnit3">:stock_unit</strong>.',
    'unit_price_info_form' => 'Harga per <strong id="displayStockUnit2">:stock_unit</strong>.',
    'current_stock_in_unit_info_form' => 'Jumlah dalam <strong id="displayStockUnit1">:unit</strong>.',
    'raw_material_avg_daily_usage_calculated_show' => 'Rata2 Penggunaan Harian <small class="text-muted">(Dihitung)</small>',
    'raw_material_replenish_quantity_show' => 'Kuantitas Pemesanan Ulang', 
    'raw_material_safety_stock_calculated_show' => 'Stok Pengaman <small class="text-muted">(Dihitung)</small>',
    'raw_material_signal_point_calculated_show' => 'Titik Sinyal <small class="text-muted">(Dihitung)</small>', 
    'raw_material_code_label' => 'Kode: :code',
    'raw_material_unit_price_per_unit' => 'Harga Satuan (per :unit)',
    'raw_material_avg_daily_usage' => 'Rata2 Penggunaan Harian',
    'raw_material_lead_time_policy' => 'Waktu Tunggu (Kebijakan)',
    'raw_material_safety_stock_days_policy' => 'Hari Stok Pengaman (Kebijakan)',
    'raw_material_usage_unit_for_recipes' => 'Satuan Penggunaan untuk Resep',
    'raw_material_conversion_info' => '1 :stock_unit = :factor :usage_unit',
    'raw_material_supplier' => 'Pemasok',
    'used_in_products_recipe_in_unit' => 'Digunakan dalam Produk (Resep dalam :unit)',
    'quantity_required' => 'Kuantitas Dibutuhkan',
    'no_description_provided' => 'Tidak ada deskripsi yang diberikan.',
    'basic_information' => 'Informasi Dasar',
    'stock_units_pricing' => 'Stok, Satuan & Harga',
    'status_and_image' => 'Status & Gambar',
    'stock_unit_placeholder' => 'cth: kg, liter, sak',
    'stock_unit_info' => 'Satuan untuk pembelian & penyimpanan.',
    'usage_unit_placeholder' => 'cth: gram, ml, pcs',
    'usage_unit_info' => 'Satuan untuk resep/penggunaan.',
    'select_supplier_placeholder' => 'Pilih Pemasok',
    'add_new_raw_material_title' => 'Tambah Bahan Baku Baru',
    'add_new_raw_material_subtitle' => 'Masukkan detail untuk bahan baku baru.',
    'edit_raw_material_title' => 'Ubah Bahan Baku',
    'modifying_raw_material' => 'Mengubah: :name',
    'view_raw_material' => 'Lihat Bahan Baku',
    'image_preview' => 'Pratinjau Gambar',
    'upload_raw_material_image' => 'Unggah Gambar Bahan Baku',
    'recalculate_analysis_button' => 'Hitung Ulang Analisis',
    'recalculate_analysis_confirm' => 'Apakah Anda yakin ingin menghitung ulang analisis untuk bahan baku ini? Ini akan memperbarui semua nilai terkait seperti rata-rata penggunaan harian, stok pengaman, dan titik sinyal.',
    'recalculate_analysis_success' => 'Analisis bahan baku berhasil dihitung ulang.',
    'search_by_raw_material_name' => 'Cari berdasarkan nama bahan baku...',
    'all_raw_material_statuses' => 'Semua Status',
    'active_raw_materials' => 'Bahan Baku Aktif',
    'inactive_raw_materials' => 'Bahan Baku Tidak Aktif',
    'no_raw_materials_criteria' => 'Bahan baku tidak ditemukan sesuai kriteria Anda.',
    'order_status' => 'Status Pesanan',
    'needs_order'=> 'Perlu Pemesanan',
    'stock_ok' => 'Stok Oke',
    'raw_materials' => 'Bahan Baku',
    'avg_usage_x_safety_days' => 'Rata-rata Penggunaan x Hari Stok Pengaman',
    'avg_usage_x_lead_time_plus_safety_stock' => 'Rata-rata Penggunaan x Waktu Tunggu + Stok Pengaman',
    'image'=> 'Gambar',
    'needs_reordering_alert' => 'Perhatian: Bahan baku ini membutuhkan pemesanan ulang.',

    // Suppliers
    'supplier_list' => 'Daftar Pemasok',
    'add_supplier' => 'Tambah Pemasok',
    'edit_supplier' => 'Ubah Pemasok',
    'supplier_details_title' => 'Detail Pemasok: :name',
    'supplier_name' => 'Nama Pemasok',
    'contact_person' => 'Narahubung',
    'phone' => 'Telepon',
    'email' => 'Email',
    'address' => 'Alamat',
    'city' => 'Kota',
    'state_province' => 'Provinsi',
    'zip_postal_code' => 'Kode Pos',
    'country' => 'Negara',
    'supplier_status' => 'Status Pemasok',
    'search_by_supplier_details' => 'Cari berdasarkan nama, email, atau telepon...',
    'no_suppliers_criteria' => 'Pemasok tidak ditemukan sesuai kriteria Anda.',
    'add_new_supplier_title' => 'Tambah Pemasok Baru',
    'add_new_supplier_subtitle' => 'Masukkan detail untuk pemasok baru.',
    'edit_supplier_title' => 'Ubah Pemasok',
    'modifying_supplier' => 'Mengubah: :name',
    'view_supplier' => 'Lihat Pemasok',
    'contact_information' => 'Informasi Kontak',
    'address_details' => 'Detail Alamat',
    'full_address' => 'Alamat Lengkap',
    'supplied_raw_materials' => 'Bahan Baku yang Dipasok',
    'material_name' => 'Nama Bahan',
    'record_timestamps' => 'Cap Waktu Catatan',
    'supplier_and_contact_info' => 'Informasi Pemasok & Kontak',

     // Bill of Materials (BOM) / Recipes
    'bom_list_title' => 'Daftar Resep (Bill of Materials)',
    'add_bom_button' => 'Tambah Resep',
    'search_by_product_for_bom' => 'Cari berdasarkan produk...',
    'all_product_statuses' => 'Semua Status Produk',
    'active_products' => 'Produk Aktif',
    'inactive_products' => 'Produk Tidak Aktif',
    'bom_active_badge' => 'Resep Aktif',
    'bom_inactive_badge' => 'Resep Tidak Aktif',
    'can_produce_units' => 'Dapat Produksi: :units unit',
    'view_bom_button' => 'Lihat Resep',
    'edit_bom_button' => 'Ubah Resep',
    'no_boms_criteria' => 'Resep (Bill of Materials) tidak ditemukan sesuai kriteria Anda.',
    'create_new_bom_title' => 'Buat Resep Baru',
    'add_new_bom_title' => 'Tambah Resep Baru',
    'add_new_bom_subtitle' => 'Tentukan resep dan biaya bahan untuk produk.',
    'edit_bom_for_product_title' => 'Ubah Resep untuk: :product_name',
    'edit_bom_subtitle' => 'Ubah bahan dan kuantitas untuk resep produk ini.',
    'product' => 'Produk',
    'select_product_placeholder' => 'Pilih produk...',
    'recipe_ingredients' => 'Bahan Resep',
    'add_material_button' => 'Tambah Bahan',
    'select_raw_material_placeholder' => 'Pilih bahan baku',
    'quantity' => 'Kuantitas',
    'cost' => 'Biaya',
    'remove_material_button_title' => 'Hapus Bahan',
    'total_base_cost' => 'Total Biaya Dasar:',
    'create_recipe_button' => 'Buat Resep',
    'update_recipe_button' => 'Perbarui Resep',
    'bom_form_alert_no_material' => 'Harap tambahkan setidaknya satu bahan baku ke resep.',
    'bom_form_alert_duplicate_material' => 'Bahan baku duplikat dipilih. Harap pilih bahan yang unik untuk resep.',
    'bom_form_alert_empty_selection' => 'Harap pilih bahan baku untuk semua baris.',
    'bom_details_for_product' => 'Resep untuk: :product_name',
    'bom_product_code_label' => 'Kode: :code',
    'bom_status_active' => 'Aktif',
    'bom_status_inactive' => 'Tidak Aktif',
    'deactivate_bom_button' => 'Nonaktifkan',
    'deactivate_bom_confirm' => 'Apakah Anda yakin ingin menonaktifkan Resep ini?',
    'no_active_bom_for_product' => 'Produk ini tidak memiliki Resep (Bill of Material) aktif.',
    'setup_bom_link_text' => 'Atur sekarang.',
    'bom_unit_price_per_stock_unit' => 'Harga Satuan (per :unit)',
    'bom_total_cost' => 'Total Biaya',
    'product_not_found_or_bom_not_set_up' => 'Produk tidak ditemukan atau Resep tidak diatur untuk produk ini.',
    'go_back_to_bom_list' => 'Kembali ke Daftar Resep',


    // Stock Adjustments
    'stock_adjustment_form_title' => 'Formulir Penyesuaian Stok',
    'stock_adjustment_form_subtitle' => 'Pilih jenis penyesuaian dan isi detail yang diperlukan.',
    'stock_adjustment_history_title' => 'Riwayat Pergerakan Stok',
    'create_new_adjustment_button' => 'Buat Penyesuaian Baru',
    'adjustment_details' => 'Detail Penyesuaian',
    'adjustment_type' => 'Jenis Penyesuaian',
    'select_type_placeholder' => '-- Pilih Jenis --',
    'stock_addition' => 'Penambahan Stok',
    'stock_deduction' => 'Pengurangan Stok',
    'initial_stock' => 'Stok Awal',
    'correction' => 'Koreksi',
    'production_usage' => 'Penggunaan Produksi',
    'breakage' => 'Kerusakan',
    'manual_adjustment' => 'Penyesuaian Manual',
    'transfer_out' => 'Transfer Keluar',
    'transfer_in' => 'Transfer Masuk',
    'select_raw_material_placeholder_stock' => '-- Pilih Bahan Baku --',
    'finished_product' => 'Produk Jadi',
    'select_product_placeholder_finished' => '-- Pilih Produk --',
    'input_quantity_unit' => 'Satuan Kuantitas Input',
    'quantity_in_unit_label' => 'Kuantitas :unit_label',
    'quantity_help_text_default' => 'Masukkan kuantitas.',
    'quantity_help_text_finished_product' => 'Masukkan jumlah produk jadi yang dibuat.',
    'quantity_help_text_select_material' => 'Pilih bahan baku terlebih dahulu.',
    'adjustment_date' => 'Tanggal Penyesuaian',
    'notes_optional' => 'Catatan (Opsional)',
    'save_adjustment_button' => 'Simpan Penyesuaian',
    'movement_type' => 'Jenis Pergerakan',
    'all_types' => 'Semua Jenis',
    'start_date' => 'Tanggal Mulai',
    'end_date' => 'Tanggal Selesai',
    'quantity_stock_unit' => 'Kuantitas (Satuan Stok)',
    'unit_price_at_movement_per_stock_unit' => 'Harga Satuan saat Pergerakan (per Satuan Stok)',
    'total_value' => 'Total Nilai',
    'by_user' => 'Oleh',
    'no_stock_movement_history' => 'Tidak ada riwayat pergerakan stok.',
    'raw_material_name_label_filter' => 'Nama Bahan Baku',
    'search_raw_material_name_placeholder' => 'Cari nama bahan baku...',
    'stock_adjustment_saved_success' => 'Penyesuaian stok berhasil disimpan.',
    'stock_adjustment_failed_error' => 'Gagal menyimpan: :error',
    'addition' => 'Penambahan Stok',
    'deduction' => 'Pengurangan Stok',
    'initial_stock' => 'Stok Awal',
    'correction' => 'Koreksi',
    'production_usage' => 'Penggunaan Produksi',
    'breakage' => 'Kerusakan',
    'transfer_out' => 'Transfer Keluar',
    'transfer_in' => 'Transfer Masuk',
    'manual_adjustment' => 'Penyesuaian Manual',


    // Users
    'user_list_title' => 'Daftar Pengguna',
    'add_user_button' => 'Tambah Pengguna',
    'search_by_user_details' => 'Cari berdasarkan nama atau email...',
    'all_roles' => 'Semua Peran',
    'role' => 'Peran',
    'joined_on' => 'Bergabung Pada',
    'no_users_criteria' => 'Pengguna tidak ditemukan sesuai kriteria Anda.',
    'add_new_user_title' => 'Tambah Pengguna Baru',
    'add_new_user_subtitle' => 'Masukkan detail untuk pengguna baru.',
    'edit_user_title' => 'Ubah Pengguna',
    'modifying_user' => 'Mengubah: :name',
    'view_user' => 'Lihat Pengguna',
    'user_information' => 'Informasi Pengguna',
    'name' => 'Nama',
    'password' => 'Kata Sandi',
    'password_leave_blank_info' => 'Biarkan kosong untuk mempertahankan kata sandi saat ini.',
    'confirm_password' => 'Konfirmasi Kata Sandi',
    'user_details_page_title' => 'Detail Pengguna: :name',
    'full_name' => 'Nama Lengkap',
    'email_address' => 'Alamat Email',
    'delete_user_confirm' => 'Apakah Anda yakin ingin menghapus Pengguna ini? Tindakan ini tidak dapat diurungkan.',
    'select_role_placeholder' => 'Pilih Peran',
    'user_created_success' => 'Pengguna berhasil dibuat.',
    'user_updated_success' => 'Pengguna berhasil diperbarui.',
    'user_delete_own_account_error' => 'Anda tidak dapat menghapus akun Anda sendiri.',
    'user_deleted_success' => 'Pengguna berhasil dihapus.',

    // Restricted Page
    'access_restricted_title' => 'Akses Dibatasi',
    'access_denied_title' => 'Akses Ditolak',
    'access_denied_message_default' => 'Maaf, Anda tidak memiliki izin yang diperlukan untuk mengakses halaman ini.',
    'access_denied_message_contact_admin' => 'Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.',
    'go_to_homepage_button' => 'Ke Halaman Utama',
    'go_back_button' => 'Kembali',

    // Welcome Page (Laravel Default)
    'laravel_docs_link_text' => 'Dokumentasi',
    'laracasts_link_text' => 'Laracasts',
    'deploy_now_button' => 'Deploy sekarang',
    'welcome_get_started_title' => 'Mari kita mulai',
    'welcome_get_started_subtitle' => "Laravel memiliki ekosistem yang sangat kaya. \nKami menyarankan untuk memulai dengan yang berikut.",
    'read_the_documentation' => 'Baca',
    'watch_video_tutorials_at' => 'Tonton tutorial video di',

];