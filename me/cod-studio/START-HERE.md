# START HERE (for the agent)

You are helping the owner set up **مالي**, a one-screen Arabic money and stock tracker, inside their local XAMPP project `cod-studio`.

Read `AGENTS.md` first, then `HISTORY.md` (everything the owner said, every version and edit, open items). It explains what the app is, why it exists, the owner's rules (keep it simple, all detail inside the "ماذا حدث؟" wizard, dinars and USDT never mixed, goods shown as counts not money), the money, goods, return and campaign flows, and the data model.

## What is in this zip

| File | What it is |
|---|---|
| `AGENTS.md` | Full guide to the app. Read before changing anything. |
| `HISTORY.md` | The whole conversation: who the owner is, their business rules in their words, their numbers, all versions and edits, open items. |
| `mali.php` | The app for XAMPP. One file: the page plus a small JSON endpoint (`?api=1`) that saves to MySQL. |
| `mali.sql` | Creates the table `mali_docs` and loads the owner's current numbers (money, debts, products). |
| `tracker-claude-ai.html` | The same app as it runs on claude.ai (saves to the claude.ai database). Reference only. |

## Your task

1. **Find the project.** The owner says it is `C:\xamppp\htdocs\cod-studio` (note: maybe `C:\xampp`, two p's). Confirm which folder Apache actually serves: read `httpd.conf` (`DocumentRoot`, `Listen`) under the XAMPP install's `apache\conf`.
2. **Fix the 404.** `http://localhost/cod-studio/mali.php` currently returns 404. Likely causes: the file is not in the served `cod-studio` folder, it is in a subfolder, it was saved as `mali.php.txt`, or Apache serves a different folder or port. Put `mali.php` and `AGENTS.md` directly in the served `cod-studio` folder.
3. **Connect the database.** At the top of `mali.php`, set `$DB_NAME` to the cod-studio project's MySQL database (look at the project's own config/connection file to find it), and `$DB_USER` / `$DB_PASS` if they are not `root` / empty.
4. **Load the data.** Import `mali.sql` into that same database (phpMyAdmin → Import, or the mysql CLI). It only adds the table `mali_docs`; it does not touch other tables. If the table already has rows, ask the owner before importing again (it replaces the starting rows).
5. **Check it.** Open the page. The status line under the title should say "محفوظ في قاعدة بيانات cod-studio." Make one small action through "ماذا حدث؟", reload, and confirm it is still there. Then undo it from "آخر ما حدث".
6. **Report back** in plain Arabic: the URL that works, and anything you changed.

Do not redesign the app or add buttons to the main screen. If the owner asks for changes, follow `AGENTS.md` → "How to add a feature".
