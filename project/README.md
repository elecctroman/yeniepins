# Dijital E-PIN Platformu

Premium koyu tema tasarımına sahip bu proje, E-PIN / Lisans / Dijital Hesap satışları için PHP 8.2 ve MySQL tabanlı, MVC benzeri bir mimari sunar. Uygulama PDO, hazırlıklı sorgular, rol bazlı yetkilendirme ve güvenlik odaklı yardımcı sınıflarla desteklenmiştir.

## Kurulum

1. Gerekli sürümler: PHP 8.2+, MySQL 8+, Apache/Nginx (public klasörünü kök olarak işaretleyin).
2. Depodaki `project/` dizinini web sunucunuza taşıyın.
3. `project/.env.example.php` dosyasını `project/.env.php` olarak kopyalayın ve veritabanı, ödeme anahtarları ile posta bilgilerini doldurun.
4. `project/config/database.php` içinde bağlantı parametrelerinizi güncelleyin.
5. Veritabanında sırasıyla `database/schema.sql` ve `database/seed.sql` dosyalarını çalıştırın.
6. `storage/` altındaki klasörlerin web sunucusu tarafından yazılabilir olduğundan emin olun.

## Geliştirme ve Ortam Ayarları

- `.env.php` içinde `APP_ENV=local` ayarı debug çıktılarının gösterilmesini sağlar. Canlı ortamda `APP_ENV=production` kullanarak hata mesajlarını kapatın.
- `APP_KEY` değeri (varsayılan XOR tabanlı şifreleme için) değiştirilmelidir.
- `config/config.php` dosyasında timezone, locale ve site adı gibi genel ayarlar bulunur.

## Demo Akış Önerisi

1. Yeni müşteri kaydı oluşturun ve e-posta doğrulaması akışını deneyin.
2. Vitrinden bir ürünü (ör. Valorant VP 125 veya Windows 11 Pro) sepete ekleyin.
3. Sepette kupon deneyin (`HOSGELDIN10`, `VALOGENEL5`, `STREAM15` vb.) ve KDV hesaplarını gözlemleyin.
4. Ödeme başlatma adımını çalıştırın, sandbox/sahte gateway seçimleriyle (PayTR, iyzico, Papara, FAST) süreci tamamlayın.
5. Ödeme onayı sonrasında otomatik teslim edilen anahtarları “Anahtarlarım” ekranından görüntüleyin ve kopyala butonlarını test edin.
6. İade talebi oluşturup admin panelinden süreci sonuçlandırın, cüzdana yansıyan bakiyeyi kontrol edin.

## Tema ve Arayüz Notları

- Hem müşteri hem yönetici tarafı aynı koyu premium tasarım dilini paylaşır; 16px radius, yumuşak gölgeler, yüksek kontrast ve modern tipografi standarttır.
- Grid, kart, modal ve toast bileşenleri `public/assets/css/theme.css` içinde merkezi değişkenlerle yönetilir.
- Admin panelinde geniş yan menü ve özet kartlar; müşteri tarafında kart bazlı ürün listeleri bulunur.

## Kabul Testleri

Aşağıdaki senaryolar canlıya çıkış öncesi mutlaka doğrulanmalıdır:

- Üyelik, oturum açma, OTP doğrulama ve brute force kısıtlamaları.
- CSRF token kontrolü ve form doğrulama hatalarının Türkçe geri bildirimleri.
- Sepet, kupon ve KDV hesaplamalarının ürün başına ve toplamda doğruluğu.
- Ödeme callback akışları (başarılı, başarısız, iptal) ve stok rezervasyon/satış güncellemeleri.
- E-PIN rezervasyon akışı, otomatik teslim edilen anahtarların kaydı ve stok uyarıları.
- İade talebi → admin onayı → cüzdan kredisi süreç zinciri.
- Rol bazlı yetki kısıtları (admin, editor, support, customer) ve bakım modu davranışı.

## Build / Asset Araçları

- `project/build.php` betiği, `public/assets/css` ve `public/assets/js` dizinlerindeki dosyaları basitçe minify ederek `.min.css` / `.min.js` çıktıları üretir. Komut satırından `php build.php` çalıştırın.
- Minify işlemi boşluk ve satır sonu sıkıştırmasına odaklanır; yorum satırları kaldırılır.

## Ek Notlar

- Seed verisi 50+ E-PIN, 20 dijital hesap, 10 kupon ve 30 örnek sipariş içerir; demo rapor ve stok ekranlarında zengin veri sağlar.
- `storage/logs/app.log` dosyası son 1000 satırlık günlükleri admin panelinde izlemeye uygundur.
- Geliştirme esnasında `storage/cache` klasörünü temizlemek için `php build.php --clear-cache` seçeneği kullanılabilir (isteğe bağlı parametre desteği için bkz. betik).
