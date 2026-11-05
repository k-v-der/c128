-- مالي: جدول جديد داخل قاعدة بيانات cod-studio، مع أرقامك الحالية.
-- في phpMyAdmin: اختر قاعدة بيانات المشروع ثم Import هذا الملف (أو انسخه في تبويب SQL).
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS mali_docs (
  col VARCHAR(20) NOT NULL,
  id VARCHAR(64) NOT NULL,
  data LONGTEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (col, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
REPLACE INTO mali_docs (col, id, data) VALUES
  ('state', 'main', '{"bybit": 0, "cash": 20000, "ccp": 0, "stash": 0, "rate": 257, "debts": [{"amount": 70, "cur": "USDT", "name": "عماد"}, {"amount": 60, "cur": "USDT", "name": "ضياء"}, {"amount": 5, "cur": "USDT", "name": "أنيس"}], "dhd": 86200}'),
  ('items', 'box-decor', '{"cost": 5000, "hidden": false, "home": 0, "name": "بوكس ديكور", "stuck": [{"d": "2026-09-05", "n": 1}, {"d": "2026-11-05", "n": 1}], "transit": 0, "wh": 8}'),
  ('items', 'etagere', '{"cost": 3200, "hidden": true, "name": "إيتاجير", "qty": 13}'),
  ('items', 'etagere-noir', '{"cost": 1200, "hidden": true, "name": "إيتاجير نوار", "qty": 21}'),
  ('items', 'planche', '{"cost": 2100, "hidden": false, "home": 0, "name": "بلانش", "stuck": [{"d": "2026-11-05", "n": 1}], "transit": 1, "wh": 1}'),
  ('items', 'product-700', '{"cost": 700, "hidden": true, "name": "كوان إيتاجير", "qty": 14}'),
  ('items', 'sport-velo', '{"cost": 1350, "hidden": false, "home": 45, "name": "سبور فيلو", "stuck": [{"d": "2026-11-05", "n": 1}], "transit": 0, "wh": 0}');
