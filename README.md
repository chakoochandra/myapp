# MYAPP

## SCREENSHOT 
![alt text](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/1.png?raw=true)
![alt text](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/2.png?raw=true)
![alt text](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/3.png?raw=true)
![alt text](https://github.com/chakoochandra/myapp/blob/main/assets/images/ss/4.png?raw=true)


## INSTALASI
0. Duplikasi file index.example.php dan rename file duplikat mejadi index.php
1. Masuk ke folder application\config
2. Duplikasi file config.example.php dan rename file duplikat menjadi config.php
3. Duplikasi file database.example.php dan rename file duplikat menjadi database.php
4. Buka file database.php yang baru dibuat dan sesuaikan konfigurasi database (baris 4-12)
5. Tes jalankan aplikasi.
6. Setelah aplikasi jalan dengan baik, Anda bisa menghapus/comment baris 17-147 pada file application\config\database.php


##  MODIFIKASI KONFIGURASI DATABASE
Table: tmst_configs

```
* APP_VERSION
* APP_NAME 
* APP_SHORT_NAME
* SATKER_NAME
* DIALOGWA_API_URL --string. url api dialogwa.web.id
* DIALOGWA_TOKEN --string. token dialogwa.web.id
* DIALOGWA_SESSION --string. sesi online dialogwa.web.id
* WA_TEST_TARGET --string. nomor WA untuk tes penerima notifikasi, pisahkan dengan koma
```

## MODUL BHT
Table: tmst_configs

```
* WA_BHT_TARGET --string. nomor WA untuk penerima notifikasi BHT, pisahkan dengan koma
```

##  DEPLOY PRODUCTION
> [!CAUTION]
> STEP INI HANYA DILAKUKAN BILA:
> 1. Fungsi pengiriman notifikasi berhasil dilakukan
> 2. Notifikasi yang terkirim data dan teks sudah benar
> 3. Pengujian sistem notifikasi sudah dilakukan secara menyeluruh dan sesuai harapan
>
> 
> Setelah aplikasi siap untuk digunakan LIVE, berikut yang harus dilakukan :
> 1. Pada folder project, buka file index.php
> 2. Pada baris 57, ubah :
> 
> ```
> define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
> ```
> 
> menjadi
> 
> ```
> define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');
> ```


## OTOMATISASI NOTIFIKASI

### NOTIFIKASI BHT
```
# contoh cron kirim notifikasi setiap jam 09.00 dan 14.00
# 0 9,14 * * * http://[IP]/[NAMA FOLDER APLIKASI]/ck/bht/send_notif_rencana_bht
```

## KONTAK
https://chandra.ct.ws/