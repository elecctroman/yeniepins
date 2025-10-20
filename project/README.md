# Dijital E-PIN Platformu

Bu proje, E-PIN / Lisans / Dijital Hesap satışları için MVC benzeri bir PHP 8.2 uygulamasının başlangıç iskeletini içerir.

## Kurulum

1. `project/` dizinini web sunucunuzun kök dizinine taşıyın.
2. `.env.example.php` dosyasını `.env.php` olarak kopyalayın ve ayarlarınızı girin.
3. `database/schema.sql` dosyasını veritabanınıza import edin.
4. Web sunucunuzun kök dizini olarak `public/` klasörünü işaretleyin.

## Özellikler

- PDO + Prepared Statements
- CSRF ve oturum yönetimi
- MVC benzeri yapı
- Koyu tema kullanıcı arayüzü iskeleti

Geliştirme için PHP 8.2+ ve MySQL 8+ önerilir.
