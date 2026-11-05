# مالي (Mali): guide for coding agents

Read this before you touch `tracker.html`, `cod-studio/mali.php` or `cod-studio/mali.sql`.

## What it is

**مالي** ("my money") is a one-screen, Arabic, right-to-left page for one person who runs a small cash-on-delivery (COD) e-commerce business in Algeria alongside a regular job. It answers two questions at a glance:

1. **How much money do I have right now?** Dinars and USDT, shown apart.
2. **What goods do I have, and where are they?**

Everything else, such as sales, returns, ad campaigns and debts, is entered through one button, **"ماذا حدث؟"** ("What happened?"). It opens a step-by-step wizard.

## Why it exists

The owner was losing track of money: ad spend in USDT was eating food money, the delivery company held cash they couldn't see, and stock was spread between home, the delivery company's warehouse, parcels out for delivery and returns stuck for months. They also have many product ideas and freeze when choosing.

The goal is to **remove distraction**, not to add a dashboard. An earlier version had 13 buttons, four tabs and dozens of numbers. They rejected it: *"the reason of this tracker is to remove distraction, not to give me 200 buttons and numbers."* That version is kept as `tracker-advanced.html` for reference only.

## What the owner wants (rules for any change)

- **Simple first screen.** Two money cards, a goods list, a campaigns card and the last few events. Nothing more on the main screen.
- **All depth lives inside the wizard.** New features become new wizard paths, not new buttons, tabs or tables on the main screen.
- **Dinars and USDT are never mixed.** Do not convert USDT to dinars in any total.
- **Goods are not money.** Show products as piece counts and where they are. Never add their value into a money total.
- **Unwanted products can be hidden.** "إخفاء" moves a product into a closed "الصندوق" (box) at the bottom, so the owner doesn't see it every day. "أرجِعه" brings it back.
- **Arabic, RTL, plain words.** Algerian context: dinars (دج), dhd is the delivery company, CCP / BaridiMob is the postal account, Bybit holds USDT.
- **Mobile first.** The owner uses it on a phone from a home-screen shortcut.
- **Undo the last action.** Every wizard action can be undone from "آخر ما حدث".
- **Ask, don't assume.** When something about the business is unclear, ask the owner one short question.

## The three flows (the owner's own words, turned into rules)

### 1. Money

| Place | Key | Currency |
|---|---|---|
| Pocket cash (food money) | `cash` | DZD |
| Home savings, hidden at home, not food money | `stash` | DZD |
| CCP / BaridiMob account | `ccp` | DZD |
| Money held by dhd (the delivery company) from delivered parcels | `dhd` | DZD |
| Bybit | `bybit` | USDT |

The **💵 مالك بالدينار** total is `cash + stash + ccp + dhd`. USDT is shown on its own.

Debts are per person and per currency. A positive amount means that person owes the owner; a negative amount means the owner owes them. They are shown under each card and are **not** added to the totals.

### 2. Goods

Each product has pieces in four places:

```
bought → home ⇄ wh (dhd warehouse)
              ↓ shipped
           transit (out for delivery)
        ↓ delivered        ↓ refused
   sold (money → dhd)   return
                          ├─ direct: goes straight back to where it shipped from
                          └─ stuck: dhd keeps it, sometimes for months → stuck[] with a date
                                    ↓ "وصلني مرتجع عالق": back to home, warehouse, or lost
```

- **Return fee:** a returned parcel that shipped **from home** costs **50 DZD** (taken from `dhd`), whether direct or stuck. Returns that shipped **from the warehouse** are free.
- A sale or return takes pieces from `transit` first, then from the place they shipped from. The owner doesn't always record shipping.
- Stuck returns older than 7 days show a ⚠ with the number of days.

### 3. Campaigns (ads)

Ads are paid in USDT from Bybit. There are four campaign types:

| Type | Key | Meaning |
|---|---|---|
| بيع من المخزن | `stock` | sell stock held in the dhd warehouse |
| بيع منتج البيت | `home` | sell stock kept at home |
| تست | `test` | test a product |
| شراكة | `partner` | the owner only pays the ads in USDT; a friend buys and delivers the product; at the end they add up costs and split |

**Break-even ad cost per order (the owner's formula):**

```
break-even ($ per order) = (sale price − buy price) / 1.5 / dollar rate
```

The dollar rate is about 255–259 DZD per 1 USD, stored as `rate` (default 257). It updates when a USDT purchase is logged. Cost per order = ad spend in USD ÷ orders.

- cost per order ≤ break-even → **تربح** (making money)
- cost per order ≤ break-even × 1.5 → **تسترجع رأس المال فقط** (only recovering capital, not profit)
- above that → **تخسر** (losing)

For partner campaigns: total = delivered × (sell − buy) − ad spend in DZD. The owner's part = share% × total. The partner owes the owner the ad spend in DZD + the owner's part − settlements.

## Data model

Four collections. Every document is plain JSON.

```js
// state/main
{ cash, stash, ccp, dhd, bybit, rate, debts: [{ name, cur: "DA" | "USDT", amount }] }

// items/<id>
{ name, cost, price, home, wh, transit, stuck: [{ d: "YYYY-MM-DD", n }], hidden }
// older items may still have only { qty }; norm() in the code treats qty as home.

// camps/<id>
{ type, name, item, adUsd, orders, delivered, start, status: "active" | "closed",
  // partner only:
  partner, sell, buy, share, settled }

// log/<id>
{ ts, text, before: { state, docs: [{ col, d, isNew }] } }   // used for undo
```

## Files and where the data lives

| File | Runs on | Saves to |
|---|---|---|
| `me/tracker.html` | claude.ai artifact: https://claude.ai/artifact/2eQDNyDDQ2SbvzwWAsTvu4 | claude.ai artifact database (owner-only), with localStorage `mali-v2` as fallback |
| `me/cod-studio/mali.php` | XAMPP, `http://localhost/cod-studio/mali.php` | MySQL table `mali_docs (col, id, data JSON, updated_at)` in the cod-studio database |
| `me/cod-studio/mali.sql` | phpMyAdmin import | creates `mali_docs` and loads the starting numbers |
| `me/tracker-advanced.html` | reference only | the earlier detailed version; do not ship it as the main page |

The HTML/JS is the same in both versions. Only the storage block differs: the `// ---------- storage ...` section. In `mali.php`, the page calls its own `?api=1` endpoint with `{op: "load"}`, `{op: "set", col, id, data}` or `{op: "delete", col, id}`.

**The two versions do not share data.** If you change the app, change both files the same way, or ask the owner which one they use.

## How to run

- **claude.ai:** open the link above. To change it, ask Claude in any chat to update that artifact URL. Keep the collection names `state`, `items`, `camps`, `log`.
- **XAMPP:** put `mali.php` in `C:\xamppp\htdocs\cod-studio\`, set `$DB_NAME` (and `$DB_PASS` if any) at the top, import `mali.sql` into the project database, and open `http://localhost/cod-studio/mali.php`. The table is also created automatically on the first request.
- **Plain browser:** open `tracker.html` directly. It saves to localStorage only.

## How to add a feature

1. Add a wizard path in the `F` object: `ask` for choices, `askNum` for numbers, `askText` for names, and `finish(text, fn)` to apply the change.
2. Inside `finish`, change `ST` directly and change items or campaigns only through `touch("items", it)` or `touch("camps", c)`, so undo keeps working.
3. Show the result on the main screen only if the owner asked for it, and keep it to one line.
4. Test the wizard path in a browser at phone width (about 400px) and check the numbers by hand.

## Don'ts

- Don't add tabs, dashboards, charts or more buttons on the main screen.
- Don't sum product value or USDT into the dinar total.
- Don't rename collections or fields without migrating existing data.
- Don't put the owner's real numbers into the code; they live in the database.
- Don't remove the hidden-products box or the undo.
