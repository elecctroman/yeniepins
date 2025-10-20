# Dijital E-PIN Platformu

Premium koyu tema tasarımına sahip bu proje, E-PIN / Lisans / Dijital Hesap satışları için PHP 8.2 ve MySQL tabanlı, MVC benzeri bir mimari sunar. Uygulama PDO, hazırlıklı sorgular, rol bazlı yetkilendirme ve güvenlik odaklı yardımcı sınıflarla desteklenmiştir.

## Kurulum

1. Gerekli sürümler: PHP 8.2+, MySQL 8+, Apache/Nginx.
2. Depodaki `project/` dizinini web sunucunuza taşıyın.
3. `project/config/database.php` içinde veritabanı bağlantı bilgilerinizi (sunucu, kullanıcı, parola) güncelleyin.
4. `project/config/mail.php` dosyasında SMTP bilgilerinizi ve varsayılan gönderen adresini tanımlayın.
5. `config/config.php` içindeki `app_key`, `env`, `site_name` değerlerini ortamınıza göre güncelleyin. `base_url` alanını boş bırakırsanız uygulama domaini otomatik algılar.
6. Veritabanında sırasıyla `database/schema.sql` ve `database/seed.sql` dosyalarını çalıştırın.
7. `storage/` altındaki klasörlerin web sunucusu tarafından yazılabilir olduğundan emin olun (CPanel için 775 veya sağlayıcınızın önerdiği değer).

## Geliştirme ve Ortam Ayarları

- `config/config.php` içindeki `env` değerini yerel geliştirmede `local`, canlı ortamda `production` olarak belirleyin. Değer otomatik olarak hata raporlama seviyesini günceller.
- Paylaşımlı hosting (CPanel) üzerinde proje köküne bıraktığınız `.htaccess` ve `index.php` dosyaları, alan adını `public/index.php`'ye yönlendirir. Yine de mümkünse domain kökünü doğrudan `public/` klasörüne işaretlemeniz önerilir.
- XOR tabanlı basit şifreleme için kullanılan `app_key` değerini benzersiz ve tahmin edilemez bir dize ile değiştirin.
- Aynı dosyada timezone, locale ve site adı gibi genel ayarlar bulunur.

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
- `storage/logs/app.log` dosyası son 1000 satırlık günlükleri admin panelinde izlemeye uygundur; dizin yazılabilir değilse günlükler PHP `error_log` dosyasına düşer.
- Geliştirme esnasında `storage/cache` klasörünü temizlemek için `php build.php --clear-cache` seçeneği kullanılabilir (isteğe bağlı parametre desteği için bkz. betik).

## Sorun Giderme (500 / 403 Hataları)

- 403 hatası alıyorsanız, kök dizinde bulunan `.htaccess` ve `index.php` dosyalarının birlikte yüklendiğinden emin olun. Bu ikili tüm istekleri `public/index.php`'ye yönlendirir.
- 500 hatasında ilk olarak `storage/logs/app.log` veya (izin yoksa) hosting kontrol panelindeki `error_log` dosyasını kontrol edin. Veritabanı bağlantı hataları burada detaylı şekilde tutulur.
- `project/core/Bootstrap.php` uygulama açılışında `storage/logs`, `storage/cache` ve `storage/uploads/csv` klasörlerini otomatik oluşturur ve yazma izinlerini doğrular. Yetki sorunu sürerse klasör izinlerini manuel olarak 775/755 değerlerine çekin.
- Veritabanı erişimi için `config/database.php` dosyasındaki `host`, `port`, `user`, `pass` ve `dbname` değerlerini kontrol edin. SSL sertifikası veya özel sürücü parametreleri gerekiyorsa `options` alanına PDO seçenekleri eklenebilir.
