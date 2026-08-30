<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Sql_Cheat_Sheet extends TextCraft_Tool_Base {
    public function get_name(): string { return 'sql_cheat_sheet'; }
    public function get_title(): string { return 'SQL Cheat Sheet'; }
    public function get_icon(): string { return 'eicon-database'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Quick reference for SQL commands including SELECT, INSERT, UPDATE, DELETE, JOINs, indexes, and more. Searchable and copy-ready.</div>

        <div class="tc-input-group" style="margin-bottom:20px">
            <input type="text" class="tc-input" id="sql-search" placeholder="Search commands... (e.g. JOIN, WHERE, GROUP BY)">
        </div>

        <div class="tctp-result" id="sql-result" style="display:block">
            <div id="sql-content">
                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">SELECT Queries</h3>
                    <pre class="tctp-code-block"><code>-- Basic SELECT
SELECT column1, column2 FROM table_name;

-- SELECT with WHERE
SELECT * FROM users WHERE age > 25;

-- SELECT with ORDER BY
SELECT * FROM products ORDER BY price DESC;

-- SELECT with LIMIT
SELECT * FROM orders LIMIT 10 OFFSET 20;

-- SELECT DISTINCT
SELECT DISTINCT country FROM customers;

-- SELECT with LIKE
SELECT * FROM users WHERE name LIKE '%john%';

-- SELECT with IN
SELECT * FROM products WHERE category IN ('electronics', 'books');

-- SELECT with BETWEEN
SELECT * FROM orders WHERE date BETWEEN '2024-01-01' AND '2024-12-31';

-- SELECT with NULL check
SELECT * FROM users WHERE email IS NOT NULL;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Aggregate Functions</h3>
                    <pre class="tctp-code-block"><code>-- COUNT
SELECT COUNT(*) FROM users;

-- SUM / AVG / MIN / MAX
SELECT SUM(amount), AVG(amount), MIN(amount), MAX(amount) FROM orders;

-- GROUP BY
SELECT category, COUNT(*) as count FROM products GROUP BY category;

-- HAVING (filter groups)
SELECT category, COUNT(*) as count
FROM products
GROUP BY category
HAVING COUNT(*) > 5;

-- Multiple aggregates
SELECT
  department,
  COUNT(*) as employees,
  AVG(salary) as avg_salary
FROM staff
GROUP BY department
ORDER BY avg_salary DESC;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">JOINs</h3>
                    <pre class="tctp-code-block"><code>-- INNER JOIN (matching rows only)
SELECT users.name, orders.total
FROM users
INNER JOIN orders ON users.id = orders.user_id;

-- LEFT JOIN (all from left + matching from right)
SELECT users.name, orders.total
FROM users
LEFT JOIN orders ON users.id = orders.user_id;

-- RIGHT JOIN (all from right + matching from left)
SELECT users.name, orders.total
FROM users
RIGHT JOIN orders ON users.id = orders.user_id;

-- FULL OUTER JOIN (all rows from both)
SELECT users.name, orders.total
FROM users
FULL OUTER JOIN orders ON users.id = orders.user_id;

-- SELF JOIN
SELECT a.name AS employee, b.name AS manager
FROM employees a
INNER JOIN employees b ON a.manager_id = b.id;

-- Multiple JOINs
SELECT o.id, u.name, p.title
FROM orders o
INNER JOIN users u ON o.user_id = u.id
INNER JOIN products p ON o.product_id = p.id;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">INSERT / UPDATE / DELETE</h3>
                    <pre class="tctp-code-block"><code>-- INSERT single row
INSERT INTO users (name, email, age)
VALUES ('John', 'john@example.com', 30);

-- INSERT multiple rows
INSERT INTO users (name, email) VALUES
('Alice', 'alice@example.com'),
('Bob', 'bob@example.com');

-- UPDATE with WHERE
UPDATE users SET age = 31 WHERE id = 5;

-- UPDATE multiple columns
UPDATE products SET price = 29.99, stock = 100 WHERE id = 42;

-- DELETE with WHERE
DELETE FROM users WHERE id = 5;

-- DELETE all rows (truncate is faster)
DELETE FROM temp_data;
TRUNCATE TABLE temp_data;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Subqueries & CTEs</h3>
                    <pre class="tctp-code-block"><code>-- Subquery in WHERE
SELECT * FROM users
WHERE id IN (SELECT user_id FROM orders WHERE total > 100);

-- Subquery in FROM
SELECT avg_total FROM (
  SELECT AVG(total) as avg_total FROM orders
) as stats;

-- CTE (Common Table Expression)
WITH active_users AS (
  SELECT id, name FROM users WHERE status = 'active'
)
SELECT * FROM active_users;

-- Recursive CTE
WITH RECURSIVE org_chart AS (
  SELECT id, name, manager_id, 1 as level
  FROM employees WHERE manager_id IS NULL
  UNION ALL
  SELECT e.id, e.name, e.manager_id, oc.level + 1
  FROM employees e
  INNER JOIN org_chart oc ON e.manager_id = oc.id
)
SELECT * FROM org_chart;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Indexes & Performance</h3>
                    <pre class="tctp-code-block"><code>-- Create index
CREATE INDEX idx_users_email ON users(email);

-- Composite index
CREATE INDEX idx_orders_user_date ON orders(user_id, order_date);

-- Unique index
CREATE UNIQUE INDEX idx_users_email_unique ON users(email);

-- Drop index
DROP INDEX idx_users_email;

-- EXPLAIN query
EXPLAIN SELECT * FROM users WHERE email = 'john@example.com';

-- ANALYZE TABLE
ANALYZE TABLE users;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">DDL (CREATE / ALTER / DROP)</h3>
                    <pre class="tctp-code-block"><code>-- Create table
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Alter table
ALTER TABLE users ADD COLUMN phone VARCHAR(20);
ALTER TABLE users DROP COLUMN phone;
ALTER TABLE users MODIFY COLUMN name VARCHAR(150);
ALTER TABLE users RENAME COLUMN name TO full_name;

-- Drop table
DROP TABLE IF EXISTS temp_users;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Window Functions</h3>
                    <pre class="tctp-code-block"><code>-- ROW_NUMBER
SELECT name, salary,
  ROW_NUMBER() OVER (ORDER BY salary DESC) as rank
FROM employees;

-- RANK with partition
SELECT name, department, salary,
  RANK() OVER (PARTITION BY department ORDER BY salary DESC) as dept_rank
FROM employees;

-- LAG / LEAD
SELECT date, revenue,
  LAG(revenue) OVER (ORDER BY date) as prev_day,
  LEAD(revenue) OVER (ORDER BY date) as next_day
FROM daily_sales;

-- Running total
SELECT date, amount,
  SUM(amount) OVER (ORDER BY date) as running_total
FROM transactions;</code></pre>
                </div>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
