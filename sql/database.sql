


CREATE TABLE plans (
    plan_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    plan_name varchar(255),
    plan_desc varchar(255),
    is_active int(11) DEFAULT 1
);
INSERT INTO plans (plan_name, plan_desc) VALUES ('CamperLyfe', 'A camper placed on some owned land. ');

CREATE TABLE assets (
    asset_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    asset_name varchar(255),
    asset_type varchar(255),
    asset_desc varchar(255),
    asset_owned int(11) DEFAULT 0,
    asset_mthly_finance decimal(18, 2),
    asset_price decimal(18, 2),
    is_active int(11) DEFAULT 1
);
INSERT INTO assets (asset_name, asset_type, asset_desc, asset_price, asset_mthly_finance)
VALUES ('Horseshoe Bend Rd LOT 14, Easley, SC 29642', 'Land', '0.82 Acres', 50000.00, 218.00);
INSERT INTO assets (asset_name, asset_type, asset_desc, asset_price, asset_mthly_finance)
VALUES ('2017 Starcraft Satellite 17RB', 'Camper', 'Used', 18900.00, 100.00);
INSERT INTO assets (asset_name, asset_type, asset_desc, asset_price, asset_mthly_finance)
VALUES ('RAM 2500 High Roof 159', 'Van', 'New, VanLyfe Build needed', 37000.00, 100.00);


CREATE TABLE plan_assets (
    plan_asset_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_plan int(11),
    id_asset int(11),
    is_active int(11) DEFAULT 1
);
INSERT INTO plan_assets (id_plan, id_asset) VALUES (3, 1);
INSERT INTO plan_assets (id_plan, id_asset) VALUES (3, 2);
INSERT INTO plan_assets (id_plan, id_asset) VALUES (1, 3);
INSERT INTO plan_assets (id_plan, id_asset) VALUES (1, 1);

CREATE TABLE finance_incomes (
  finance_income_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  fi_company varchar(255),
  fi_name varchar(255),
  fi_amount decimal(18, 2),
  fi_date datetime,
  fi_notes varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO finance_incomes (fi_company, fi_name, fi_amount, fi_date, fi_notes)
VALUES ('OnGen', 'Current Job', 1500.00, '2021-10-29 09:00:00', '');


CREATE TABLE finance_expenses (
  finance_expense_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  fe_company varchar(255),
  fe_name varchar(255),
  fe_amount decimal(18, 2),
  fe_date datetime,
  fe_notes varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO finance_expenses (fe_company, fe_name, fe_amount, fe_date, fe_notes)
VALUES ('Ingles', 'Got drinks for weekend.', 7.79, '2021-10-23', 'Central, SC');


CREATE TABLE finance_bills (
  finance_bill_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  fb_company varchar(255),
  fb_name varchar(255),
  fb_amount decimal(18, 2),
  fb_date datetime,
  fb_notes varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO finance_bills (fb_company, fb_name, fb_amount, fb_date, fb_notes)
VALUES ('Planet Fitness', 'Gym Black Card Membership', 23.04, '2021-10-23', '');
INSERT INTO finance_bills (fb_company, fb_name, fb_amount, fb_date, fb_notes)
VALUES ('Microsoft', 'Additional 1TB OneDrive Storage', 1.99, '2021-10-23', '');

/* selections */
SELECT pa.plan_asset_id,
       a.asset_id,
       a.asset_name,
       a.asset_desc,
       a.asset_owned,
       a.asset_mthly_finance,
       a.asset_price,
       pa.is_active
FROM plan_assets pa
LEFT JOIN plans p ON pa.id_plan = p.plan_id
LEFT JOIN assets a ON pa.id_asset = a.asset_id
WHERE p.plan_id = 3;
