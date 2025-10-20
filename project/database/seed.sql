-- UTF-8 başlangıç verileri
SET NAMES utf8mb4;
SET time_zone = '+03:00';

INSERT INTO users (email, password_hash, name, phone, role, status, twofa_enabled, created_at)
VALUES
('admin@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Süper Admin', '+905551112233', 'admin', 'active', 1, '2024-01-02 08:00:00'),
('editor@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Editör Lider', '+905554446677', 'editor', 'active', 0, '2024-01-03 09:00:00'),
('support@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Destek Uzmanı', '+905557778899', 'support', 'active', 0, '2024-01-03 10:00:00'),
('musteri@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Demo Müşteri', '+905553334455', 'customer', 'active', 0, '2024-01-05 11:00:00'),
('oyunsever@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Oyun Sevdalısı', '+905559991122', 'customer', 'active', 0, '2024-01-06 12:00:00'),
('lisansci@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Lisans Avcısı', '+905550001234', 'customer', 'active', 0, '2024-01-07 13:00:00'),
('kurumsal@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Kurumsal Alıcı', '+905552223344', 'customer', 'active', 0, '2024-01-07 13:30:00'),
('streamer@epin.test', '$2y$10$u1/82EDuJeayQMZT6X0FJe8eRPu0sU36I51Onx5O20nQ2YSmHBZoK', 'Canlı Yayıncı', '+905558884433', 'customer', 'active', 0, '2024-01-08 09:30:00');

INSERT INTO categories (name, slug, parent_id, position, hidden, created_at)
VALUES
('Oyunlar', 'oyunlar', NULL, 1, 0, '2024-01-01 09:00:00'),
('Yazılım', 'yazilim', NULL, 2, 0, '2024-01-01 09:10:00'),
('Eğlence', 'eglence', NULL, 3, 0, '2024-01-01 09:20:00');

INSERT INTO products (category_id, type, name, slug, description, price, tax_rate, stock_policy, delivery, min_qty, max_qty, status, created_at)
VALUES
((SELECT id FROM categories WHERE slug='oyunlar'), 'epin', 'Valorant VP 125', 'valorant-vp-125', 'Riot Games Valorant için 125 VP dijital kodu.', 75.00, 18.00, 'track', 'instant', 1, 10, 'active', '2024-01-05 10:00:00'),
((SELECT id FROM categories WHERE slug='yazilim'), 'license', 'Windows 11 Pro Lisans', 'windows-11-pro', 'OEM dijital ürün anahtarı.', 899.00, 18.00, 'track', 'manual', 1, NULL, 'active', '2024-01-05 10:10:00'),
((SELECT id FROM categories WHERE slug='eglence'), 'account', 'Netflix UHD Hesap', 'netflix-uhd', 'Paylaşımlı Netflix UHD 1 Aylık hesap.', 220.00, 18.00, 'track', 'manual', 1, 2, 'active', '2024-01-05 10:20:00');

INSERT INTO product_variants (product_id, name, price_override, stock_override, created_at)
VALUES
((SELECT id FROM products WHERE slug='windows-11-pro'), 'Tek PC', NULL, NULL, '2024-01-06 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), '5 Kullanıcı', 3499.00, NULL, '2024-01-06 09:00:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), '1 Profil', NULL, NULL, '2024-01-06 09:05:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), '2 Profil', 380.00, NULL, '2024-01-06 09:05:00');

INSERT INTO coupons (code, type, value, max_uses, used_count, starts_at, ends_at, min_cart_total, active)
VALUES
('HOSGELDIN10', 'percent', 10.00, 500, 1, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 100.00, 1),
('VALOGENEL5', 'fixed', 5.00, NULL, 3, '2024-01-15 00:00:00', '2024-06-30 23:59:59', 50.00, 1),
('LISANS100', 'fixed', 100.00, 200, 2, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 500.00, 1),
('KURUM15', 'percent', 15.00, 100, 3, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 3000.00, 1),
('STREAM15', 'percent', 15.00, 150, 2, '2024-02-01 00:00:00', '2024-05-01 23:59:59', 150.00, 1),
('FAST20', 'percent', 20.00, 50, 0, '2024-03-01 00:00:00', '2024-03-31 23:59:59', 200.00, 1),
('EPIN25', 'fixed', 25.00, 400, 0, '2024-01-01 00:00:00', '2024-04-30 23:59:59', 100.00, 1),
('VIPCUSTOMER', 'percent', 12.00, 50, 0, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 250.00, 1),
('BAYRAM10', 'percent', 10.00, 500, 0, '2024-04-01 00:00:00', '2024-07-31 23:59:59', 150.00, 1),
('WEEKEND5', 'fixed', 5.00, NULL, 0, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 75.00, 1);

INSERT INTO epin_keys (product_id, code, batch_id, status, delivered_at, created_at)
VALUES
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0001', 'BATCH-202401', 'sold', '2024-02-01 10:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0002', 'BATCH-202401', 'sold', '2024-02-01 11:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0003', 'BATCH-202401', 'sold', '2024-02-01 11:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0004', 'BATCH-202401', 'sold', '2024-02-01 12:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0005', 'BATCH-202401', 'sold', '2024-02-01 12:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0006', 'BATCH-202401', 'sold', '2024-02-01 13:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0007', 'BATCH-202401', 'sold', '2024-02-01 14:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0008', 'BATCH-202401', 'sold', '2024-02-01 14:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0009', 'BATCH-202401', 'sold', '2024-02-01 15:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0010', 'BATCH-202401', 'sold', '2024-02-01 15:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0011', 'BATCH-202401', 'sold', '2024-02-01 16:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0012', 'BATCH-202401', 'sold', '2024-02-01 17:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0013', 'BATCH-202401', 'sold', '2024-02-01 17:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0014', 'BATCH-202401', 'sold', '2024-02-01 18:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0015', 'BATCH-202401', 'sold', '2024-02-01 18:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0016', 'BATCH-202401', 'sold', '2024-02-01 19:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0017', 'BATCH-202401', 'sold', '2024-02-01 20:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0018', 'BATCH-202401', 'sold', '2024-02-01 20:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0019', 'BATCH-202401', 'sold', '2024-02-01 21:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0020', 'BATCH-202401', 'sold', '2024-02-01 21:20:00', '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0021', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0022', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0023', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0024', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0025', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0026', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0027', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0028', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0029', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0030', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0031', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0032', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0033', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0034', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0035', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0036', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0037', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0038', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0039', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='valorant-vp-125'), 'VALO-125-KEY-0040', 'BATCH-202401', 'available', NULL, '2024-01-15 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0001', 'BATCH-202401', 'sold', '2024-02-02 10:20:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0002', 'BATCH-202401', 'sold', '2024-02-02 11:20:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0003', 'BATCH-202401', 'sold', '2024-02-02 12:20:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0004', 'BATCH-202401', 'sold', '2024-02-02 13:20:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0005', 'BATCH-202401', 'sold', '2024-02-02 14:20:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0006', 'BATCH-202401', 'sold', '2024-02-02 15:20:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0007', 'BATCH-202401', 'sold', '2024-02-02 16:15:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0008', 'BATCH-202401', 'sold', '2024-02-02 17:20:00', '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0009', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0010', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0011', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0012', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0013', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0014', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0015', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0016', 'BATCH-202401', 'reserved', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0017', 'BATCH-202401', 'reserved', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0018', 'BATCH-202401', 'reserved', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0019', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00'),
((SELECT id FROM products WHERE slug='windows-11-pro'), 'WIN11-PRO-KEY-0020', 'BATCH-202401', 'available', NULL, '2024-01-20 09:00:00');

INSERT INTO accounts (product_id, username, password_encrypted, extra_json, status, delivered_at, created_at)
VALUES
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix01', 'JgQeCmVdXLHTQ09U', '{"slot":"Profil 1"}', 'sold', '2024-02-03 10:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix02', 'JgQeCmVdXLHTQE9X', '{"slot":"Profil 2"}', 'sold', '2024-02-03 11:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix03', 'JgQeCmVdXLHTQU9W', '{"slot":"Profil 3"}', 'sold', '2024-02-03 12:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix04', 'JgQeCmVdXLHTRk9R', '{"slot":"Profil 4"}', 'sold', '2024-02-03 13:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix05', 'JgQeCmVdXLHTR09Q', '{"slot":"Profil 1"}', 'sold', '2024-02-03 14:15:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix06', 'JgQeCmVdXLHTRE9T', '{"slot":"Profil 2"}', 'sold', '2024-02-03 15:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix07', 'JgQeCmVdXLHTRU9S', '{"slot":"Profil 3"}', 'sold', '2024-02-03 16:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix08', 'JgQeCmVdXLHTSk9d', '{"slot":"Profil 4"}', 'sold', '2024-02-03 17:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix09', 'JgQeCmVdXLHTS09c', '{"slot":"Profil 1"}', 'sold', '2024-02-03 18:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix10', 'JgQeCmVdXLHTQ15EWlE=', '{"slot":"Profil 2"}', 'sold', '2024-02-03 19:19:00', '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix11', 'JgQeCmVdXLHTQ19EWlA=', '{"slot":"Profil 3"}', 'available', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix12', 'JgQeCmVdXLHTQ1xEWlM=', '{"slot":"Profil 4"}', 'available', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix13', 'JgQeCmVdXLHTQ11EWlI=', '{"slot":"Profil 1"}', 'available', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix14', 'JgQeCmVdXLHTQ1pEWlU=', '{"slot":"Profil 2"}', 'available', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix15', 'JgQeCmVdXLHTQ1tEWlQ=', '{"slot":"Profil 3"}', 'available', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix16', 'JgQeCmVdXLHTQ1hEWlc=', '{"slot":"Profil 4"}', 'available', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix17', 'JgQeCmVdXLHTQ1lEWlY=', '{"slot":"Profil 1"}', 'available', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix18', 'JgQeCmVdXLHTQ1ZEWlk=', '{"slot":"Profil 2"}', 'reserved', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix19', 'JgQeCmVdXLHTQ1dEWlg=', '{"slot":"Profil 3"}', 'reserved', NULL, '2024-01-22 09:30:00'),
((SELECT id FROM products WHERE slug='netflix-uhd'), 'demo.netflix20', 'JgQeCmVdXLHTQF5EWVE=', '{"slot":"Profil 4"}', 'reserved', NULL, '2024-01-22 09:30:00');

INSERT INTO orders (order_no, customer_id, email, total, subtotal, tax_total, discount_total, coupon_code, status, payment_method, payment_ref, created_at, paid_at)
VALUES
( 'Y202402011100', (SELECT id FROM users WHERE email='oyunsever@epin.test'), 'oyunsever@epin.test', 81.00, 75.00, 13.50, 7.50, 'HOSGELDIN10', 'paid', 'paytr', 'PAYTR-Y202402011100', '2024-02-01 10:15:00', '2024-02-01 10:20:00'),
( 'Y202402011101', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 172.00, 150.00, 27.00, 5.00, 'VALOGENEL5', 'paid', 'papara', 'PAPARA-Y202402011101', '2024-02-01 11:15:00', '2024-02-01 11:20:00'),
( 'Y202402011102', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 177.00, 150.00, 27.00, 0.00, NULL, 'paid', 'paytr', 'PAYTR-Y202402011102', '2024-02-01 12:15:00', '2024-02-01 12:20:00'),
( 'Y202402011103', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 88.50, 75.00, 13.50, 0.00, NULL, 'paid', 'papara', 'PAPARA-Y202402011103', '2024-02-01 13:15:00', '2024-02-01 13:20:00'),
( 'Y202402011104', (SELECT id FROM users WHERE email='oyunsever@epin.test'), 'oyunsever@epin.test', 177.00, 150.00, 27.00, 0.00, NULL, 'paid', 'paytr', 'PAYTR-Y202402011104', '2024-02-01 14:15:00', '2024-02-01 14:20:00'),
( 'Y202402011105', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 172.00, 150.00, 27.00, 5.00, 'VALOGENEL5', 'paid', 'papara', 'PAPARA-Y202402011105', '2024-02-01 15:15:00', '2024-02-01 15:20:00'),
( 'Y202402011106', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 88.50, 75.00, 13.50, 0.00, NULL, 'paid', 'paytr', 'PAYTR-Y202402011106', '2024-02-01 16:15:00', '2024-02-01 16:20:00'),
( 'Y202402011107', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 177.00, 150.00, 27.00, 0.00, NULL, 'paid', 'papara', 'PAPARA-Y202402011107', '2024-02-01 17:15:00', '2024-02-01 17:20:00'),
( 'Y202402011108', (SELECT id FROM users WHERE email='oyunsever@epin.test'), 'oyunsever@epin.test', 177.00, 150.00, 27.00, 0.00, NULL, 'paid', 'paytr', 'PAYTR-Y202402011108', '2024-02-01 18:15:00', '2024-02-01 18:20:00'),
( 'Y202402011109', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 83.50, 75.00, 13.50, 5.00, 'VALOGENEL5', 'paid', 'papara', 'PAPARA-Y202402011109', '2024-02-01 19:15:00', '2024-02-01 19:20:00'),
( 'Y202402011110', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 177.00, 150.00, 27.00, 0.00, NULL, 'cancelled', 'paytr', 'PAYTR-Y202402011110', '2024-02-01 20:15:00', '2024-02-01 20:20:00'),
( 'Y202402011111', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 177.00, 150.00, 27.00, 0.00, NULL, 'refunded', 'papara', 'PAPARA-Y202402011111', '2024-02-01 21:15:00', '2024-02-01 21:20:00'),
( 'Y202402022100', (SELECT id FROM users WHERE email='lisansci@epin.test'), 'lisansci@epin.test', 3603.97, 3499.00, 629.82, 524.85, 'KURUM15', 'paid', 'iyzico', 'IYZICO-Y202402022100', '2024-02-02 10:15:00', '2024-02-02 10:20:00'),
( 'Y202402022101', (SELECT id FROM users WHERE email='kurumsal@epin.test'), 'kurumsal@epin.test', 960.82, 899.00, 161.82, 100.00, 'LISANS100', 'paid', 'paytr', 'PAYTR-Y202402022101', '2024-02-02 11:15:00', '2024-02-02 11:20:00'),
( 'Y202402022102', (SELECT id FROM users WHERE email='lisansci@epin.test'), 'lisansci@epin.test', 1060.82, 899.00, 161.82, 0.00, NULL, 'paid', 'iyzico', 'IYZICO-Y202402022102', '2024-02-02 12:15:00', '2024-02-02 12:20:00'),
( 'Y202402022103', (SELECT id FROM users WHERE email='kurumsal@epin.test'), 'kurumsal@epin.test', 3603.97, 3499.00, 629.82, 524.85, 'KURUM15', 'paid', 'paytr', 'PAYTR-Y202402022103', '2024-02-02 13:15:00', '2024-02-02 13:20:00'),
( 'Y202402022104', (SELECT id FROM users WHERE email='lisansci@epin.test'), 'lisansci@epin.test', 1060.82, 899.00, 161.82, 0.00, NULL, 'paid', 'iyzico', 'IYZICO-Y202402022104', '2024-02-02 14:15:00', '2024-02-02 14:20:00'),
( 'Y202402022105', (SELECT id FROM users WHERE email='kurumsal@epin.test'), 'kurumsal@epin.test', 960.82, 899.00, 161.82, 100.00, 'LISANS100', 'paid', 'paytr', 'PAYTR-Y202402022105', '2024-02-02 15:15:00', '2024-02-02 15:20:00'),
( 'Y202402022106', (SELECT id FROM users WHERE email='lisansci@epin.test'), 'lisansci@epin.test', 3603.97, 3499.00, 629.82, 524.85, 'KURUM15', 'failed', 'iyzico', 'IYZICO-Y202402022106', '2024-02-02 16:15:00', NULL),
( 'Y202402022107', (SELECT id FROM users WHERE email='kurumsal@epin.test'), 'kurumsal@epin.test', 1060.82, 899.00, 161.82, 0.00, NULL, 'refunded', 'paytr', 'PAYTR-Y202402022107', '2024-02-02 17:15:00', '2024-02-02 17:20:00'),
( 'Y202402033100', (SELECT id FROM users WHERE email='streamer@epin.test'), 'streamer@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'paid', 'papara', 'PAPARA-Y202402033100', '2024-02-03 10:15:00', '2024-02-03 10:19:00'),
( 'Y202402033101', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'paid', 'fast', 'FAST-Y202402033101', '2024-02-03 11:15:00', '2024-02-03 11:19:00'),
( 'Y202402033102', (SELECT id FROM users WHERE email='streamer@epin.test'), 'streamer@epin.test', 226.60, 220.00, 39.60, 33.00, 'STREAM15', 'paid', 'papara', 'PAPARA-Y202402033102', '2024-02-03 12:15:00', '2024-02-03 12:19:00'),
( 'Y202402033103', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'paid', 'fast', 'FAST-Y202402033103', '2024-02-03 13:15:00', '2024-02-03 13:19:00'),
( 'Y202402033104', (SELECT id FROM users WHERE email='streamer@epin.test'), 'streamer@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'pending', 'papara', 'PAPARA-Y202402033104', '2024-02-03 14:15:00', NULL),
( 'Y202402033105', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'paid', 'fast', 'FAST-Y202402033105', '2024-02-03 15:15:00', '2024-02-03 15:19:00'),
( 'Y202402033106', (SELECT id FROM users WHERE email='streamer@epin.test'), 'streamer@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'paid', 'papara', 'PAPARA-Y202402033106', '2024-02-03 16:15:00', '2024-02-03 16:19:00'),
( 'Y202402033107', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 226.60, 220.00, 39.60, 33.00, 'STREAM15', 'paid', 'fast', 'FAST-Y202402033107', '2024-02-03 17:15:00', '2024-02-03 17:19:00'),
( 'Y202402033108', (SELECT id FROM users WHERE email='streamer@epin.test'), 'streamer@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'refunded', 'papara', 'PAPARA-Y202402033108', '2024-02-03 18:15:00', '2024-02-03 18:19:00'),
( 'Y202402033109', (SELECT id FROM users WHERE email='musteri@epin.test'), 'musteri@epin.test', 259.60, 220.00, 39.60, 0.00, NULL, 'paid', 'fast', 'FAST-Y202402033109', '2024-02-03 19:15:00', '2024-02-03 19:19:00');

INSERT INTO order_items (order_id, product_id, variant_id, qty, unit_price, tax_rate, delivered_json)
VALUES
((SELECT id FROM orders WHERE order_no='Y202402011100'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 1, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0001"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011101'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0002", "VALO-125-KEY-0003"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011102'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0004", "VALO-125-KEY-0005"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011103'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 1, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0006"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011104'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0007", "VALO-125-KEY-0008"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011105'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0009", "VALO-125-KEY-0010"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011106'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 1, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0011"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011107'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0012", "VALO-125-KEY-0013"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011108'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0014", "VALO-125-KEY-0015"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011109'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 1, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0016"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011110'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0017", "VALO-125-KEY-0018"]}'),
((SELECT id FROM orders WHERE order_no='Y202402011111'), (SELECT id FROM products WHERE slug='valorant-vp-125'), NULL, 2, 75.00, 18.00, '{"keys": ["VALO-125-KEY-0019", "VALO-125-KEY-0020"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022100'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='5 Kullanıcı'), 1, 3499.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0001"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022101'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='Tek PC'), 1, 899.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0002"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022102'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='Tek PC'), 1, 899.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0003"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022103'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='5 Kullanıcı'), 1, 3499.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0004"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022104'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='Tek PC'), 1, 899.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0005"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022105'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='Tek PC'), 1, 899.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0006"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022106'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='5 Kullanıcı'), 1, 3499.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0007"]}'),
((SELECT id FROM orders WHERE order_no='Y202402022107'), (SELECT id FROM products WHERE slug='windows-11-pro'), (SELECT id FROM product_variants WHERE product_id = (SELECT id FROM products WHERE slug='windows-11-pro') AND name='Tek PC'), 1, 899.00, 18.00, '{"keys": ["WIN11-PRO-KEY-0008"]}'),
((SELECT id FROM orders WHERE order_no='Y202402033100'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix01", "password": "DemoSifre1!1", "extra": {"slot": "Profil 1"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033101'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix02", "password": "DemoSifre2!2", "extra": {"slot": "Profil 2"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033102'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix03", "password": "DemoSifre3!3", "extra": {"slot": "Profil 3"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033103'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix04", "password": "DemoSifre4!4", "extra": {"slot": "Profil 4"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033104'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix05", "password": "DemoSifre5!5", "extra": {"slot": "Profil 1"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033105'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix06", "password": "DemoSifre6!6", "extra": {"slot": "Profil 2"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033106'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix07", "password": "DemoSifre7!7", "extra": {"slot": "Profil 3"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033107'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix08", "password": "DemoSifre8!8", "extra": {"slot": "Profil 4"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033108'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix09", "password": "DemoSifre9!9", "extra": {"slot": "Profil 1"}}'),
((SELECT id FROM orders WHERE order_no='Y202402033109'), (SELECT id FROM products WHERE slug='netflix-uhd'), NULL, 1, 220.00, 18.00, '{"username": "demo.netflix10", "password": "DemoSifre10!10", "extra": {"slot": "Profil 2"}}');

INSERT INTO refunds (order_id, reason, status, created_at, processed_at)
VALUES
((SELECT id FROM orders WHERE order_no='Y202402011111'), 'Teslim edilen kod çalışmadı', 'processed', '2024-02-01 21:30:00', '2024-02-02 09:00:00'),
((SELECT id FROM orders WHERE order_no='Y202402022107'), 'Kurumsal iade talebi', 'processed', '2024-02-02 18:10:00', '2024-02-03 18:30:00'),
((SELECT id FROM orders WHERE order_no='Y202402033108'), 'Hesap giriş problemi bildirildi', 'processed', '2024-02-03 18:30:00', '2024-02-04 09:00:00'),
((SELECT id FROM orders WHERE order_no='Y202402033104'), 'Ödeme bekleniyor doğrulama sürecinde', 'requested', '2024-02-03 14:45:00', NULL);

INSERT INTO wallets (customer_id, balance, updated_at)
VALUES
((SELECT id FROM users WHERE email='musteri@epin.test'), 177.00, '2024-02-02 09:05:00'),
((SELECT id FROM users WHERE email='oyunsever@epin.test'), 50.00, '2024-02-01 09:05:00'),
((SELECT id FROM users WHERE email='lisansci@epin.test'), 0.00, '2024-02-02 08:30:00'),
((SELECT id FROM users WHERE email='kurumsal@epin.test'), 1060.82, '2024-02-03 18:35:00'),
((SELECT id FROM users WHERE email='streamer@epin.test'), 259.60, '2024-02-04 09:05:00');

INSERT INTO wallet_tx (wallet_id, type, amount, reason, ref_id, created_at)
VALUES
((SELECT w.id FROM wallets w JOIN users u ON u.id = w.customer_id WHERE u.email='musteri@epin.test'), 'credit', 177.00, 'İade Y202402011111', 'refund:Y202402011111', '2024-02-02 09:05:00'),
((SELECT w.id FROM wallets w JOIN users u ON u.id = w.customer_id WHERE u.email='kurumsal@epin.test'), 'credit', 1060.82, 'İade Y202402022107', 'refund:Y202402022107', '2024-02-03 18:35:00'),
((SELECT w.id FROM wallets w JOIN users u ON u.id = w.customer_id WHERE u.email='streamer@epin.test'), 'credit', 259.60, 'İade Y202402033108', 'refund:Y202402033108', '2024-02-04 09:02:00'),
((SELECT w.id FROM wallets w JOIN users u ON u.id = w.customer_id WHERE u.email='oyunsever@epin.test'), 'credit', 50.00, 'Promosyon bakiyesi', 'promo:launch', '2024-02-01 09:00:00');

INSERT INTO logs (level, message, context_json, created_at)
VALUES
('info', 'Kullanıcı girişi başarılı', '{"email":"musteri@epin.test","ip":"85.100.120.10"}', '2024-02-01 09:55:12'),
('info', 'Sepet ödeme adımı başlatıldı', '{"order_no":"Y202402011102","gateway":"paytr"}', '2024-02-01 12:15:05'),
('warning', 'Ödeme callback doğrulama bekleniyor', '{"order_no":"Y202402033104","status":"pending"}', '2024-02-03 14:20:44'),
('error', 'İade talebi manuel onaylandı', '{"order_no":"Y202402022107","refund_id":2}', '2024-02-03 18:40:11'),
('info', 'Stok rezervasyonu tamamlandı', '{"product":"valorant-vp-125","qty":20}', '2024-02-01 10:05:30');

INSERT INTO settings (`key`, value)
VALUES
('site.name', 'E-Pin Premium Mağaza'),
('site.tagline', 'Dijital kodlarda elit deneyim'),
('site.support_email', 'destek@epin.test'),
('site.logo_path', '/assets/img/logo.svg'),
('site.maintenance_mode', 'off'),
('site.maintenance_message', 'Mağazamız kısa süreli bakımdadır.'),
('tax.default_rate', '18'),
('orders.min_total', '50'),
('orders.max_total', '25000'),
('payment.paytr.placeholder', 'paytr_sandbox_key'),
('payment.iyzico.placeholder', 'iyzico_sandbox_key'),
('payment.papara.placeholder', 'papara_sandbox_key'),
('payment.fast.placeholder', 'fast_sandbox_key'),
('wallet.refund_policy', 'wallet_credit');
