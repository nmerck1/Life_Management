


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
  fi_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  fi_company varchar(255),
  fi_name varchar(255),
  fi_amount decimal(18, 2),
  fi_date datetime,
  fi_notes varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO finance_incomes (fi_company, fi_name, fi_amount, fi_date, fi_notes)
VALUES ('OnGen', 'Current Job', 1211.83, '2021-11-26', '');
INSERT INTO finance_incomes (fi_company, fi_name, fi_amount, fi_date, fi_notes)
VALUES ('eBay', 'Sold book', 1.23, '2021-10-30', '');

CREATE TABLE finance_expenses (
  fe_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  fe_company varchar(255),
  fe_name varchar(255),
  fe_category varchar(255), /* Food, Clothing, Entertainment, Work, Vehicle, etc. */
  fe_amount decimal(18, 2),
  fe_date datetime,
  fe_notes varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO finance_expenses (fe_company, fe_name, fe_category, fe_amount, fe_date, fe_notes)
VALUES ('Ingles', 'Got drinks for weekend.', 'Food', 7.79, '2021-10-23', 'Central, SC');
INSERT INTO finance_expenses (fe_company, fe_name, fe_category, fe_amount, fe_date, fe_notes)
VALUES ('Publix', 'got four drinks for each day of weekday', 'Food', 6.59, '2021-11-02', 'Clemson, SC');
INSERT INTO finance_expenses (fe_company, fe_name, fe_category, fe_amount, fe_date, fe_notes)
VALUES ('Planet Fatness', 'membership', 'Gym', 23.04, '2021-11-02', 'Seneca, SC');
INSERT INTO finance_expenses (fe_company, fe_name, fe_category, fe_amount, fe_date, fe_notes)
VALUES ('Dollar General', 'Red Bull + spicy nuts + snickers ice cream bar', 'Food', 4.49, '2021-11-03', 'Six Mile, SC');
/*
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
*/

CREATE TABLE users (
  user_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  user_role int(11),      -- 1=Admin, 2=Member, 3=Guest, 4=Uknown
  user_name varchar(255),
  user_fname varchar(255),
  user_lname varchar(255),
  user_dob datetime,
  user_notes varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO users (user_role, user_name, user_fname, user_lname, user_dob, user_notes)
VALUES (1, 'nmerck', 'Nathaniel', 'Merck', '1997-11-19 04:00:00', '');

/* this table is for basic needs that need to be maintained in my life in order to survive and thrive */
CREATE TABLE needs (
  need_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  need_name varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO needs(need_name) VALUES('Food');
INSERT INTO needs(need_name) VALUES('Shower');
INSERT INTO needs(need_name) VALUES('Bathroom');
INSERT INTO needs(need_name) VALUES('Water');
INSERT INTO needs(need_name) VALUES('Exercise');
INSERT INTO needs(need_name) VALUES('Laundry');

/* this table is for attaching these pros and cons to an asset so these pros and cons need to be somewhat broad */
CREATE TABLE pros_cons (
  pc_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  pc_name varchar(255), /* Examples: Cost Effective, Closer to Job, Closer to Family, More Money, Further from family, Further from friends, etc. */
  pc_type varchar(255), /* Either Pro or Con */
  pc_notes varchar(255),
  is_active int(11) DEFAULT 1
);
INSERT INTO pros_cons(pc_name, pc_type, pc_notes) VALUES('Closer to Job', 'Pro', '-Distance, -Gas, -Vehicle Miles, +Free Time');
INSERT INTO pros_cons(pc_name, pc_type, pc_notes) VALUES('Further from Job', 'Con', '+Distance, +Gas, +Vehicle Miles, -Free Time');
INSERT INTO pros_cons(pc_name, pc_type, pc_notes) VALUES('Peaceful Atmosphere', 'Pro', '-Stress, +Harmony');
INSERT INTO pros_cons(pc_name, pc_type, pc_notes) VALUES('Congested City/Town Area', 'Con', '+Stress, -Harmony');

CREATE TABLE projects (
  proj_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  proj_name varchar(255),
  proj_notes varchar(255),
  is_active int(11) DEFAULT 1
);

CREATE TABLE project_steps (
  ps_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  ps_project_id int(11),
  ps_name varchar(255),
  ps_desc varchar(255),
  is_active int(11) DEFAULT 1
);

CREATE TABLE current_bills (
  bill_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  bill_name varchar(255),
  bill_amount decimal(18, 2),
  bill_freq char(1),  /* This will be either W=Weekly, M=Monthly, D=Daily, BW=Bi-Weekly, Y=Yearly, etc. */
  bill_desc varchar(255),
  bill_created datetime,
  is_active int(11) DEFAULT 1
);
INSERT INTO current_bills (bill_name, bill_amount, bill_freq, bill_desc, bill_created) VALUES ('Gym', 30.00, 'M', '', '2021-11-02');
INSERT INTO current_bills (bill_name, bill_amount, bill_freq, bill_desc, bill_created) VALUES ('Insurance', 86.32, 'M', '', '2021-11-03');
INSERT INTO current_bills (bill_name, bill_amount, bill_freq, bill_desc, bill_created) VALUES ('Phone', 22.97, 'M', '', '2021-11-03');
INSERT INTO current_bills (bill_name, bill_amount, bill_freq, bill_desc, bill_created) VALUES ('Microsoft OneDrive 1GB Storage', 1.99, 'M', '', '2021-11-03');

CREATE TABLE passive_incomes (
  pi_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  pi_name varchar(255),
  pi_amount decimal(18, 2),
  pi_freq char(1),  /* This will be either W=Weekly, M=Monthly, D=Daily, BW=Bi-Weekly, Y=Yearly, etc. */
  pi_desc varchar(255),
  pi_created datetime,
  is_active int(11) DEFAULT 1
);
INSERT INTO passive_incomes (pi_name, pi_amount, pi_freq, pi_desc, pi_created) VALUES ('Roblox Game Development: Janitor Simulator', 0.01, 'M', '', '2021-10-28');


CREATE TABLE budgets (
  bud_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  bud_name varchar(255),
  bud_amount decimal(18, 2),
  bud_freq char(1),  /* This will be either W=Weekly, M=Monthly, D=Daily, BW=Bi-Weekly, Y=Yearly, etc. */
  bud_desc varchar(255),
  bud_created datetime,
  is_active int(11) DEFAULT 1
);
INSERT INTO budgets (bud_name, bud_amount, bud_freq, bud_created, bud_desc) VALUES ('Food', 250.00, 'M', '2021-10-27', '');
INSERT INTO budgets (bud_name, bud_amount, bud_freq, bud_created, bud_desc) VALUES ('Gym', 30.00, 'M', '2021-11-02', '');
INSERT INTO budgets (bud_name, bud_amount, bud_freq, bud_created, bud_desc) VALUES ('Donation', 50.00, 'M', '2021-11-03', '');
INSERT INTO budgets (bud_name, bud_amount, bud_freq, bud_created, bud_desc) VALUES ('Insurance', 100.00, 'M', '2021-11-03', '');
INSERT INTO budgets (bud_name, bud_amount, bud_freq, bud_created, bud_desc) VALUES ('Style', 50.00, 'M', '2021-11-03', '');


CREATE TABLE diet_logs (
  dl_id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  dl_name varchar(255),           /* Steamed Broccoli, Roasted Peanuts, Baked Chicken, Builders Bar */
  dl_category varchar(255),       /* Vegetable, Fruit, Nut, Dairy, Meat, Grain, etc. */
  dl_amount decimal(18, 2),       /* 1.1, 1.5, 25.37 */
  dl_measurement decimal(18, 2),  /* g (grams), mg (miligrams), l (liters), gal (gallons), oz (ounces)*/
  dl_calories decimal(18, 2),
  dl_protein decimal(18, 2),
  dl_fat decimal(18, 2),
  dl_carbs decimal(18, 2),
  dl_created datetime,
  is_active int(11) DEFAULT 1
);

CREATE TABLE exercise_logs (

);









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


SELECT bills.bill_name,
       bills.bill_amount,
       bills.bill_freq,
       SUM(bills.bill_amount) AS 'total_bills_amount'
FROM current_bills bills
WHERE is_active = 1;
