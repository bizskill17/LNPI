-- LNPI schema (mirrors the Excel "tables")

CREATE TABLE IF NOT EXISTS item_groups (
  item_group VARCHAR(255) PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS uoms (
  uom VARCHAR(64) PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS items (
  item_id VARCHAR(64) PRIMARY KEY,
  item_group VARCHAR(255) NOT NULL,
  item_name VARCHAR(255) NOT NULL,
  erp VARCHAR(255) NULL,
  tally_post_timestamp DATETIME NULL,
  CONSTRAINT fk_items_group FOREIGN KEY (item_group) REFERENCES item_groups(item_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS material_in (
  id VARCHAR(64) PRIMARY KEY,
  timestamp DATETIME NULL,
  entry_email_id VARCHAR(255) NULL,
  date DATE NULL,
  invoice_no VARCHAR(128) NULL,
  inv_date DATE NULL,
  supplier_name VARCHAR(255) NULL,
  total_amount DECIMAL(12,2) NULL,
  ph_timestamp DATETIME NULL,
  ph_email_id VARCHAR(255) NULL,
  md_timestamp DATETIME NULL,
  md_email_id VARCHAR(255) NULL,
  tally_timestamp DATETIME NULL,
  status VARCHAR(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS material_in_items (
  material_items_id VARCHAR(64) PRIMARY KEY,
  material_in_id VARCHAR(64) NOT NULL,
  date DATE NULL,
  invoice_no VARCHAR(128) NULL,
  inv_date DATE NULL,
  supplier_name VARCHAR(255) NULL,
  item_id VARCHAR(64) NOT NULL,
  item_name VARCHAR(255) NULL,
  item_group VARCHAR(255) NULL,
  erp VARCHAR(255) NULL,
  qty DECIMAL(12,3) NULL,
  uom VARCHAR(64) NULL,
  rate DECIMAL(12,4) NULL,
  basic_value DECIMAL(12,2) NULL,
  CONSTRAINT fk_mii_header FOREIGN KEY (material_in_id) REFERENCES material_in(id),
  CONSTRAINT fk_mii_item FOREIGN KEY (item_id) REFERENCES items(item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS consumption (
  consumption_id VARCHAR(64) PRIMARY KEY,
  timestamp DATETIME NULL,
  email_id VARCHAR(255) NULL,
  date DATE NULL,
  item_id VARCHAR(64) NOT NULL,
  item_group VARCHAR(255) NULL,
  item_name VARCHAR(255) NULL,
  erp VARCHAR(255) NULL,
  qty DECIMAL(12,3) NULL,
  uom VARCHAR(64) NULL,
  rate DECIMAL(12,4) NULL,
  value DECIMAL(12,2) NULL,
  CONSTRAINT fk_consumption_item FOREIGN KEY (item_id) REFERENCES items(item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS production (
  production_id VARCHAR(64) PRIMARY KEY,
  date DATE NULL,
  timestamp DATETIME NULL,
  email_id VARCHAR(255) NULL,
  item_id VARCHAR(64) NOT NULL,
  item_name VARCHAR(255) NULL,
  item_group VARCHAR(255) NULL,
  erp VARCHAR(255) NULL,
  qty DECIMAL(12,3) NULL,
  rate DECIMAL(12,4) NULL,
  value DECIMAL(12,2) NULL,
  CONSTRAINT fk_production_item FOREIGN KEY (item_id) REFERENCES items(item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

