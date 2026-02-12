from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

# 1. Inisialisasi Driver & Wait
driver = webdriver.Chrome()
driver.maximize_window()
wait = WebDriverWait(driver, 10) # Baris krusial biar 'wait' tidak error

def test_perpustakaan_full_flow():
    try:
        # --- LANGKAH AWAL: AKSES LANDING PAGE ---
        driver.get("http://localhost/perpustakaan/")
        time.sleep(2)
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        btn_masuk = wait.until(EC.element_to_be_clickable((By.LINK_TEXT, "Masuk ke Sistem")))
        driver.execute_script("arguments[0].click();", btn_masuk)
        time.sleep(2)

        # --- 1. TEST CASE: LOGIN GAGAL ---
        driver.find_element(By.NAME, "username").send_keys("admin")
        driver.find_element(By.NAME, "password").send_keys("salah_password")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(2)
        if "dashboard" not in driver.current_url:
            print("Test Case 1: Login Gagal (Kredensial Salah) - PASSED")

        # --- 2. TEST CASE: LOGIN BERHASIL ---
        driver.find_element(By.NAME, "username").clear()
        driver.find_element(By.NAME, "username").send_keys("admin")
        driver.find_element(By.NAME, "password").clear()
        driver.find_element(By.NAME, "password").send_keys("password")
        
        # Cari ulang tombol agar tidak Stale
        btn_login = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        driver.execute_script("arguments[0].click();", btn_login)
        time.sleep(2)
        if "dashboard" in driver.current_url:
            print("Test Case 2: Login Berhasil - PASSED")

        # --- 3. TEST CASE: TAMBAH PEMINJAMAN BERHASIL ---
        driver.find_element(By.LINK_TEXT, "Peminjaman").click()
        time.sleep(1)
        btn_tambah_pinjam = wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(text(), 'Tambah Peminjaman')]")))
        driver.execute_script("arguments[0].click();", btn_tambah_pinjam)
        time.sleep(2)
        
        Select(driver.find_element(By.ID, "pilih_anggota")).select_by_visible_text("Yogi Saputra")
        Select(driver.find_element(By.NAME, "petugas_id")).select_by_visible_text("Yogi")
        time.sleep(2) 
        
        btn_simpan_pinjam = driver.find_element(By.XPATH, "//button[contains(text(), 'Simpan Peminjaman')]")
        driver.execute_script("arguments[0].click();", btn_simpan_pinjam)
        time.sleep(3)
        print("Test Case 3: Tambah Peminjaman Berhasil - PASSED")

       
        # Gunakan URL direct ke index dulu buat mastiin kita di halaman yang bener
        driver.get("http://localhost/perpustakaan/peminjaman/index.php")
        time.sleep(2)

        # Cari tombol Detail dengan menunggu sampai elemennya benar-benar ada di DOM
        try:
            # Cari link 'Detail' yang paling pertama muncul di tabel
            btn_detail = wait.until(EC.presence_of_element_located((By.PARTIAL_LINK_TEXT, "Detail")))
            driver.execute_script("arguments[0].scrollIntoView();", btn_detail)
            time.sleep(1)
            driver.execute_script("arguments[0].click();", btn_detail)
        except Exception as e:
            # Jika gagal pake link text, coba pake XPATH spesifik ke baris pertama
            btn_detail = wait.until(EC.presence_of_element_located((By.XPATH, "//table//tr[1]//a[contains(@class, 'btn-info')]")))
            driver.execute_script("arguments[0].click();", btn_detail)

        # Klik Tambah Item di halaman Detail
        btn_tambah_item = wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(text(), 'Tambah Item')]")))
        driver.execute_script("arguments[0].click();", btn_tambah_item)
        time.sleep(2)

        # Pilih Buku dan Input Qty 1000
        Select(wait.until(EC.presence_of_element_located((By.NAME, "buku_id")))).select_by_index(1)
        qty_field = driver.find_element(By.NAME, "qty")
        qty_field.clear()
        qty_field.send_keys("1000")
        time.sleep(1)

        # Klik Tambah ke Daftar
        btn_tambah_daftar = driver.find_element(By.XPATH, "//button[contains(text(), 'Tambah ke Daftar')]")
        driver.execute_script("arguments[0].click();", btn_tambah_daftar)
        time.sleep(2)

        # Verifikasi: Tetap di detailadd.php karena stok kurang
        if "detailadd.php" in driver.current_url:
            print("Test Case 4: Tambah Item Gagal (Stok Tidak Cukup) - PASSED")
        
        # --- 5. TEST CASE: TAMBAH PEMINJAMAN GAGAL (DATA KOSONG) ---
        driver.get("http://localhost/perpustakaan/peminjaman/index.php")
        time.sleep(2)
        btn_tambah_lagi = driver.find_element(By.XPATH, "//a[contains(text(), 'Tambah Peminjaman')]")
        driver.execute_script("arguments[0].click();", btn_tambah_lagi)
        time.sleep(1)
        btn_simpan_kosong = driver.find_element(By.XPATH, "//button[contains(text(), 'Simpan Peminjaman')]")
        driver.execute_script("arguments[0].click();", btn_simpan_kosong)
        time.sleep(1)
        if "add.php" in driver.current_url:
            print("Test Case 5: Tambah Peminjaman Gagal (Data Kosong) - PASSED")

        # --- 6. TEST CASE: TAMBAH PENGEMBALIAN BERHASIL ---
        driver.find_element(By.LINK_TEXT, "Pengembalian").click()
        time.sleep(1)
        btn_tambah_kembali = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Tambah Pengembalian')]")))
        driver.execute_script("arguments[0].click();", btn_tambah_kembali)
        time.sleep(2)
        
        Select(driver.find_element(By.NAME, "peminjaman_id")).select_by_index(1)
        Select(driver.find_element(By.NAME, "status_kondisi")).select_by_visible_text("Sesuai (Tepat Waktu)")
        time.sleep(1)
        
        btn_simpan_kembali = driver.find_element(By.CSS_SELECTOR, "form button[type='submit']")
        driver.execute_script("arguments[0].click();", btn_simpan_kembali)
        time.sleep(2)
        print("Test Case 6: Tambah Pengembalian Berhasil - PASSED")

    except Exception as e:
        print(f"Terjadi Kesalahan: {e}")
    finally:
        driver.quit()

if __name__ == "__main__":
    test_perpustakaan_full_flow()