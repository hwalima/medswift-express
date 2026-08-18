# Role & Objective
You are an expert full-stack Laravel developer. Help me build a web application named **MedSwift Express** (`medswift.express`), an AI-driven medical courier and logistics service designed for medical laboratories, biological sample transit, and medical supplies transport.

---

# 1. Tech Stack & Environment
- **Framework:** Laravel 11 (or latest stable)
- **Database:** MySQL (local XAMPP setup)
- **Frontend Stack:** Tailwind CSS + Alpine.js (or Livewire) with dynamic Light/Dark mode support
- **Local Dev:** XAMPP (Apache + MySQL)
- **Version Control:** GitHub (`https://github.com/hwalima/medswift-express`)
- **Production Server:** cPanel shared hosting (`medswift.express`) via SSH/Deploy keys

---

# 2. Design System & Theming
Implement Tailwind CSS custom color variables and a dark mode toggle stored in `localStorage`:
- **Teal / Main Brand:** `#1697a9`
- **Secondary / Lab Emerald:** `#1da287`
- **Muted Steel / Slate:** `#98aeb1`
- **Deep Cyan:** `#2b9297`
- **Background Light / Off-White:** `#fafcfa`
- **Dark Mode Backgrounds:** Dark slate grey / charcoal (`#0d1719` / `#142225`) complementing the teal palette.

---

# 3. Key Inspiration & Feature Requirements
Incorporate industry best practices inspired by top logistics platforms (e.g., RAM Logistics, DHL South Africa, Fastway):

### Core Courier Features
1. **Real-time Sample & Shipment Tracking:**
   - Public & internal lookup using tracking numbers/barcodes.
   - Statuses optimized for bio-specimens: *Picked Up, Cold-Chain Validated, In-Transit, Lab Arrived, Delivered, Exception / Delay*.
2. **Booking & Dispatch System:**
   - Urgent pickup requests, routine route scheduling, temperature-sensitive sample flags (Ambient, Refrigerated, Frozen).
3. **Multi-Role Portal:**
   - **Clients (Labs/Clinics):** Request pickup, track shipments, download proof of delivery (POD).
   - **Couriers/Drivers:** Mobile-friendly dispatch dashboard to update status and upload digital sign-offs.
   - **Admin/Ops:** Dashboard to monitor active routes, drivers, and flagged bio-hazard/urgent deliveries.

### AI Integration (AI Assistant)
- **AI Logistics Assistant ("MedSwift AI"):**
  - Integrated using Laravel OpenAI API / LLM Wrapper.
  - **Public User Features:** Natural language tracking queries (e.g., "Where is sample #10928?"), service estimated pickup time calculator.
  - **Admin Features:** AI route optimization summaries, automated delay alert generation, and compliance validation checking for medical transit rules.

---

# 4. Automated Deployment Setup (CI/CD to cPanel)
Do not require manual cPanel logins for future updates. Provide the configuration files for automated deployment:

1. **`.cpanel.yml` (Root deployment script for cPanel Git Version Control):**
   - Configured to automatically move files to the live domain directory upon deployment.
   - Run `php artisan migrate --force`, `php artisan config:cache`, `php artisan route:cache`, and assets optimization.

2. **`.github/workflows/deploy.yml` (GitHub Actions workflow):**
   - Triggered on `push` to `main`.
   - Setup SSH deployment using `secrets.SSH_PRIVATE_KEY` / `Deploy Keys`.
   - Automatically push code to `medswift.express` via cPanel Git Webhook or SSH runner.

---

# 5. Initial Task Instructions
Please generate:
1. **Laravel Database Schema / Migrations** for `users`, `shipments`, `shipment_status_logs`, and `courier_routes`.
2. **Tailwind Config File (`tailwind.config.js`)** with the custom color palette predefined.
3. **Blade Layout Template** featuring the Light/Dark mode switcher and the custom color scheme.
4. **The Deployment Files:** `.cpanel.yml` and `.github/workflows/deploy.yml`.