-- UTF-8 başlangıç verileri
SET NAMES utf8mb4;
SET time_zone = '+03:00';

INSERT INTO users (email, password_hash, name, phone, role, status, twofa_enabled)
VALUES
('admin@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Süper Admin', '+905551112233', 'admin', 'active', 1),
('musteri@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Demo Müşteri', '+905553334455', 'customer', 'active', 0);

INSERT INTO wallets (customer_id, balance)
SELECT id, CASE role WHEN 'customer' THEN 150.00 ELSE 0 END FROM users;

INSERT INTO categories (name, slug, parent_id, position, hidden)
VALUES
('Oyunlar', 'oyunlar', NULL, 1, 0),
('Yazılım', 'yazilim', NULL, 2, 0),
('Eğlence', 'eglence', NULL, 3, 0);

INSERT INTO products (category_id, type, name, slug, description, price, tax_rate, stock_policy, delivery, min_qty, max_qty, status)
VALUES
((SELECT id FROM categories WHERE slug='oyunlar'), 'epin', 'Valorant VP 125', 'valorant-vp-125', 'Riot Games Valorant için 125 VP kodu.', 75.00, 18.00, 'track', 'instant', 1, 10, 'active'),
((SELECT id FROM categories WHERE slug='yazilim'), 'license', 'Windows 11 Pro Lisans', 'windows-11-pro', 'OEM dijital ürün anahtarı.', 899.00, 18.00, 'track', 'manual', 1, NULL, 'active'),
((SELECT id FROM categories WHERE slug='eglence'), 'account', 'Netflix UHD Hesap', 'netflix-uhd', 'Paylaşımlı Netflix UHD 1 Aylık hesap.', 220.00, 18.00, 'track', 'manual', 1, 2, 'active');

INSERT INTO epin_keys (product_id, code, batch_id, status)
VALUES
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0001', 'BATCH-202401', 'available'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0002', 'BATCH-202401', 'available'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0003', 'BATCH-202401', 'available');

INSERT INTO product_variants (product_id, name, price_override, stock_override)
VALUES
((SELECT id FROM products WHERE slug='windows-11-pro'), 'Tek PC', NULL, NULL),
((SELECT id FROM products WHERE slug='windows-11-pro'), '5 Kullanıcı', 3499.00, NULL),
((SELECT id FROM products WHERE slug='netflix-uhd'), '1 Profil', NULL, NULL),
((SELECT id FROM products WHERE slug='netflix-uhd'), '2 Profil', 380.00, NULL);

INSERT INTO accounts (product_id, username, password_encrypted, extra_json, status)
VALUES
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix1', AES_ENCRYPT('DemoSifre1!', 'demo-key'), '{"slot":"Profil 1"}', 'available'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix2', AES_ENCRYPT('DemoSifre2!', 'demo-key'), '{"slot":"Profil 2"}', 'available');

INSERT INTO coupons (code, type, value, max_uses, used_count, starts_at, ends_at, min_cart_total, active)
VALUES
('HOSGELDIN10', 'percent', 10.00, 500, 0, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH), 100.00, 1),
('KISASET', 'fixed', 50.00, NULL, 0, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 200.00, 1);

INSERT INTO settings (`key`, value)
VALUES
('site.name', 'E-Pin Premium Mağaza'),
('site.support_email', 'destek@epin.test'),
('orders.invoice_prefix', 'INV');
