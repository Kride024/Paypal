# PHP + AJAX CRUD with PayPal Integration — Assignment Demo

This is a small full-stack demo you can run locally, then screen-record for
the submission the recruiter asked for (PHP + AJAX CRUD + PayPal API integration).

## What it does

- **CRUD**: Add / edit / delete "products" in a MySQL table, all through
  AJAX (`fetch()`) calls to `api.php` — no page reloads.
- **PayPal integration**: Each product row has a live PayPal Buy button.
  Clicking it creates a real PayPal Sandbox order (`paypal_create_order.php`),
  and after approval, captures the payment server-side
  (`paypal_capture_order.php`) using PayPal's REST API and OAuth2 client-credentials flow.

## Setup (5–10 minutes)

1. **Requirements**: PHP 7.4+ with the `curl` and `pdo_mysql` extensions, and MySQL/MariaDB.
   Easiest path: install XAMPP / MAMP / Laragon, or run `php -S localhost:8000` with a local MySQL server.

2. **Create the database**:
   ```
   mysql -u root -p < schema.sql
   ```
   (Edit `includes/db.php` first if your MySQL user/password differ from `root` / empty.)

3. **Get free PayPal Sandbox API credentials**:
   - Go to https://developer.paypal.com/dashboard/applications/sandbox
   - Create (or use the default) sandbox REST API app
   - Copy the **Client ID** and **Secret**
   - Paste the Client ID into `index.html` (the `<script src="https://www.paypal.com/sdk/js?client-id=...">` line)
   - Paste the Client ID and Secret into `includes/paypal_config.php`

4. **Run it**:
   ```
   php -S localhost:8000
   ```
   Then open http://localhost:8000/index.html

5. **Test a payment**: Use a PayPal Sandbox *personal* (buyer) test account to
   log in and approve the payment — PayPal auto-creates these test accounts
   in your developer dashboard under "Sandbox > Accounts".

## What to show in the video

1. Open the page — show the product list loading via AJAX (open DevTools → Network tab, point out the `api.php` request returning JSON, no full page reload).
2. Add a new product — show it appear instantly in the table (AJAX `POST`).
3. Edit a product — show the update happen instantly (AJAX `PUT`).
4. Delete a product — show it disappear instantly (AJAX `DELETE`).
5. Click a PayPal button on any product, log in with a sandbox buyer account, approve the payment, and show the "Payment COMPLETED" status message that comes back from `paypal_capture_order.php`.
6. Briefly show the code: `api.php` for the CRUD switch statement, and `paypal_create_order.php` / `paypal_capture_order.php` for the server-side PayPal calls (mention that the price is looked up server-side from the DB, not trusted from the browser — a good practice to call out).

## Notes

- This uses PayPal's **Sandbox** environment — no real money moves. That's normal and expected for a test/demo submission.
- To move to production later, you'd swap `PAYPAL_BASE_URL` to `https://api-m.paypal.com` and use live (not sandbox) credentials.
