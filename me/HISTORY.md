# مالي: full history (everything the owner said, every version, every edit)

This file is the memory of the conversation where مالي was built (one Claude Code session, 2026-11-05). Read it together with `AGENTS.md` (how the app works) and `START-HERE.md` (the current task). When this file and the code disagree, the code is the truth for *how it works*, and this file is the truth for *what the owner wants*.

The owner's pronouns were never stated; this file says "the owner" or "they".

---

## 1. Who the owner is (from the interviews)

- Has a **job they like** and want to grow in; the job covers "a bit of everything".
- Runs a **COD (cash-on-delivery) e-commerce store** in Algeria: launched, small sales.
- Has about **5–15 hours a week** for their own projects.
- **Goal:** save **money to get married**. That is what everything else serves.
- Ideas that keep coming back: business / money, creative work, e-commerce COD.
- What gets in the way:
  - **Too many ideas**, can't pick one, freezes.
  - **Doubt it's worth it**, mostly the fear it won't make money.
  - **Ads were eating food money every month**, and the numbers were only known at the end of a campaign: *"all counting is crazy, I'm losing money for food each month"*.
  - **No routine**; energy comes in **random bursts**.
- Checks things **whenever lost**, not on a schedule. Wants things **simple and minimal**.
- Writes in a mix of **Arabic, Algerian Darija, French words and English**. Wants the app in **Arabic**.
- Uses the app on a **phone** (Android session origin), from a home-screen shortcut.
- Also has a local **XAMPP project** at `C:\xamppp\htdocs\cod-studio` (maybe `C:\xampp`) with its own MySQL database.

## 2. Business facts the owner gave (these are the rules)

### Money places
- **Cash** (pocket, food money).
- **Home stash** (مخبأ البيت): money hidden at home, *"not the food money, it's something else"*.
- **CCP / BaridiMob** account.
- **dhd**: the delivery company. Money from delivered parcels sits there until they pay it out.
- **Bybit**: USDT, used to pay ads. USDT is bought with dinars. Rate *"around 255–259 DA = 1$"*.
- **People who owe money** ("nsal ness"). Some debts are in USDT.
- The owner **also owes people** sometimes.
- **Keep USDT and dinars separate**: *"The USDT keep it, you have in DA and USDT, don't transform it."*

### Weekly living money
- Gives themself about **5,000–10,000 DA per week** for living (food, transport, bills, flexy = phone top-up). Week starts **Saturday**. (Built in the big ledger, not in the current simple page.)

### Goods and places
- Stock is in **two places**: **home** and the **dhd warehouse** ("stock").
- Parcels go **out for delivery** (livraison).
- Buys stock **in advance**.
- Some products are kept in the warehouse and **each delivered parcel from the warehouse costs 50 DA**.
- **Weight fee:** over **5 kg**, **+50 DA per extra kg** (13 kg → +400 DA).
- **Sale price is not fixed:** during confirmation they sometimes give a **discount** (on price or delivery), sometimes a customer **takes two**.
- They pay **Yassir** to take parcels from home to the delivery company and back.

### Returns (retour)
- Two kinds:
  - **Direct:** comes back straight to where it shipped from. *"Just add the fee if there is from my home, and nothing if just go back to warehouse, because it's free."*
  - **Stuck:** *"the shipping company keeps it there and they take a long time to go back."* One box decor return was stuck **about 2 months**.
- Confirmed: a **stuck return from home also costs 50 DA** (*"Yes they took 50"*).

### Campaigns (ads)
- Four types:
  1. **Sale from stock** (warehouse).
  2. **Sale of a home product.**
  3. **Test** campaign.
  4. **Partner** campaign: the owner **only pays the ads in USDT**; a friend **buys and delivers** the product; *"at the end we calculate the expenses and split everything"*. They still need the sale price and buy price to judge it.
- **Break-even formula (the owner's):** margin = standard price − buy price; **break-even = (margin / 1.5) / (dollar rate)** in $ per order. Above it, *"you are not making money, you just sell to recover the capital, not for profit."*

### Feelings about the tool
- Wanted to be **grilled** with questions, then the app should **ask them** what happened, so they don't get lost.
- Some products they **hate** (tried to sell many times): *"give me an option, click this product, it disappears, goes somewhere where it's not there every day, because it gives me bad negative energy."*
- Strongest feedback: *"the reason of this tracker and this Claude session is to remove distraction, not to give me 200 buttons and numbers."*
- Then: *"Where is all what I told you about campaigns, types, product flows, retour flows and money flows?"* when the detail disappeared.
- Final direction: *"first start as the last one, great simple, then getting deeper in each path, all in the button 'what happened'."*

## 3. The owner's numbers (snapshot, 2026-11-05)

These are also loaded by `mali.sql`. The live numbers are in the database, not here.

| Money | Amount |
|---|---|
| Cash (they said the 20,000 is the cash; no separate stash) | 20,000 DA |
| CCP | 0 |
| At dhd | 86,200 DA |
| Bybit | 0 USDT |
| Owed to them | Imad 70 USDT, Dhayaa 60 USDT, Anis 5 USDT |

| Product | Pieces | Buy price | Where |
|---|---:|---:|---|
| Sport vélo (سبور فيلو) | 51 → 46 (they logged 5 lost/damaged) | 1,350 | 45 home + 1 return |
| Étagère noir (إيتاجير نوار) | 21 | 1,200 | unknown (counted at home) · **hidden** |
| Coin étagère (كوان إيتاجير) | 14 | 700 | unknown (counted at home) · **hidden** |
| Étagère (إيتاجير) | 13 | 3,200 | unknown (counted at home) · **hidden** |
| Box décor (بوكس ديكور) | 10 | 5,000 | 8 warehouse + 2 returns (one stuck ~2 months) |
| Planche (بلانش) | 3 | 2,100 | 1 warehouse, 1 out for delivery, 1 return |

Summary given in chat: dinar money now **106,200 DA**; with goods at buy price **307,950 DA + 135 USDT** (goods alone 201,750 DA, before the 5 sport vélo were written off). The owner then asked **not to add goods into the money total** and to keep USDT apart. No campaigns have been entered yet.

## 4. Versions, in order

| # | What | Where | Outcome |
|---|---|---|---|
| 1 | Personal organization notes: `README.md`, `now.md` (one project), `ideas.md` (park ideas + "pick one" scoring), `direction.md` (goal: wedding money), `profile.md` | `me/` | Kept. Later translated to Arabic. |
| 2 | "Store Money Tracker" page (English): monthly money check, break-even per product, daily campaign log, verdict | `me/tracker.html` → claude.ai artifact | Replaced. |
| 3 | Same page **in Arabic**, RTL | same | Replaced. |
| 4 | **Big ledger** "دفتر المال والمتجر": 4 tabs (Today, Store, Campaigns, Money), weekly allowance from Saturday, smart sentence input (Claude parses "flexy 200…"), quick buttons, product catalog, debts both ways, accounts cash/CCP/delivery/Bybit | same | Too complex for the owner. |
| 5 | Stock **per place**: home vs warehouse, stock moves | same | Folded into later versions. |
| 6 | **Variable payout**: pieces per parcel, discounts, weight fee (>5 kg, 50/kg), warehouse fee per parcel, optional actual amount from dhd statement | same | Kept in advanced. |
| 7 | **Wealth view**: everything owned and where, change since Saturday, other valuables | same | Owner later said: don't add goods into money. |
| 8 | **Product flow stages** (home ⇄ warehouse → out for delivery → sold / returned), daily questions, grouped buttons, Guide tab with diagrams | same | Kept in advanced. |
| 9 | **Direct vs stuck returns**, return fee only from home, stuck age counter, **home stash** account | same | Kept. |
| 10 | **Four campaign types**, break-even (margin/1.5/rate), **partner campaigns** with share and settlements | same | Kept. |
| 11 | Owner overwhelmed → asked in chat for money, grilled, gave numbers. Built **"مالي"**: one screen, two money cards, goods as counts, hide box, one wizard button, undo | `me/tracker.html` (same link) | Owner liked simple, but the detail was gone. |
| 12 | Owner: "where is everything I told you?" → old ledger saved as `me/tracker-advanced.html`, published on a **second link** | https://claude.ai/artifact/LmgBnmRpiuBQCjdpwnhk9D | Reference only, no data. |
| 13 | **Current "مالي"**: simple screen, **all paths inside "ماذا حدث؟"** (money, goods, returns, campaigns, USDT, fix), goods show one-line place breakdown + stuck ⚠, campaigns card | https://claude.ai/artifact/2eQDNyDDQ2SbvzwWAsTvu4 | **Current.** |
| 14 | Campaigns card **always visible** with "+ حملة جديدة" (it was hidden when empty and the owner couldn't find it) | same | Current. |
| 15 | **PHP version** for XAMPP: `mali.php` (same app, saves to MySQL table `mali_docs`), `mali.sql` (table + numbers) | `me/cod-studio/` | Gave **404** at `http://localhost/cod-studio/mali.php`, not yet fixed. |
| 16 | `AGENTS.md` (guide for agents), zip `mali-for-agent.zip` with `START-HERE.md` | `me/` | For the local agent "Greeting". |

All code is on GitHub: repo **k-v-der/c128**, branch **`claude/personal-theme-organization-xlp9k3`**, folder `me/`.

## 5. What the current app does (version 13–14)

Main screen, top to bottom:
1. **💵 مالك بالدينار**: one number = cash + stash + CCP + dhd; rows under it; DA debts and partner balances under a dashed line (not in the total).
2. **💲 USDT**: Bybit balance; who owes USDT underneath (not in the total).
3. **📦 سلعتك**: name + piece count; small line "البيت · المخزن · في التوصيل · مرتجع"; ⚠ when a stuck return is older than 7 days; "إخفاء" moves it into **الصندوق** (closed box).
4. **📣 حملاتك**: each campaign with cost per order / break-even and a verdict pill; "+ حملة جديدة" and "صرفت إعلانات".
5. **آخر ما حدث**: last 8 actions; **تراجع** undoes the newest.
6. Fixed bottom button **ماذا حدث؟**:
   - 💵 مال: spent · income · dhd paid me · moved money between places · DA debt
   - 📦 سلعة: bought (where to put it, paid from where) · shipped · customer received (sold) · return (from home −50 / warehouse free → back or stuck) · stuck return arrived (home / warehouse / lost) · moved goods · lost/damaged
   - 📣 حملة: ads spent (USDT + orders → instant verdict) · new campaign (4 types) · partner results · partner settlement · stop campaign
   - 💲 USDT: bought USDT (updates the rate) · USDT debt
   - ✏️ تصحيح رقم: any balance, a product's pieces per place, the dollar rate

Removed from the main page on purpose (still in `tracker-advanced.html`): weekly allowance, smart sentence input, daily question list, guide tab, wealth total with goods, per-product revenue tables.

## 6. Open items

1. **Fix the 404** for the PHP version and connect it to the cod-studio database (see `START-HERE.md`).
2. **Where are the three étagère products?** Home or warehouse? Currently counted at home. They are hidden.
3. **Second box décor return date** is unknown (set to 2026-11-05).
4. **No campaigns entered yet.** The owner may want to enter current campaigns and the partner campaign with their friend.
5. **Two data stores**: the claude.ai page and the PHP page do not share data. The owner should pick one. If the PHP one is chosen, the claude.ai one can stay as a backup or be dropped.
6. **Weekly living allowance** (5,000–10,000 DA from Saturday) was wanted early on but is not in the simple page. Add it only as a wizard path or a single line if the owner asks.
7. Optional: hosting so it can be edited from anywhere (GitHub Pages + Firebase/Supabase) was offered; the owner didn't choose it.

## 7. How to behave with this owner

- Be short. One question at a time, with choices when possible.
- Never show many buttons or numbers at once. Depth goes inside the wizard.
- Use their words: dhd, retour, stock, livraison, flexy, USDT, planche, box décor.
- When unsure about a business rule, **ask them**; they will answer quickly.
- Confirm numbers back in a small table with dinars and USDT in separate columns.
