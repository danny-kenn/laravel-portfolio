# Portfolio Builder — Technical Documentation

**Developer:** David Kiamba
**Phase:** 1 — Database Design \& Documentation 

\---

## 1\. Technical Stack

|Component|Choice|Reason|
|-|-|-|
|Framework|Laravel 12 (PHP)|Built-in auth scaffolding to build custom logic on top of, Eloquent ORM, Blade templating, native Queue system for background jobs|
|Database|MySQL 8.0|ACID-compliant, strong relational/FK support, native JSON columns, team-familiar|
|Authentication|Custom (bcrypt hashing, session-based)|No third-party OAuth per requirements — full control over hashing, sessions, and RBAC|
|Queue Driver|Database queue (Laravel Queues)|Simple to set up on shared/sandbox hosting without a Redis dependency; upgradeable to Redis later|
|Frontend|Blade + Bootstrap 5|Server-rendered, fast to iterate, matches the minimalist high-contrast aesthetic already approved|

\---

## 2\. Why Custom Auth (Not Laravel Breeze/Fortify/Jetstream defaults as-is)

Public registration is closed — only admins can provision accounts — so the standard "self-register" flow those starter kits assume isn't used. Instead:

* Accounts are created by a **SuperAdmin/Admin** from the dashboard, not by public sign-up.
* Passwords are hashed with **bcrypt** (Laravel's default `Hash::make()`), which is deliberately slow and salts automatically, making brute-force and rainbow-table attacks impractical.
* Sessions are handled through Laravel's built-in session guard — no external OAuth providers, per the assignment's "no third-party auth" requirement.
* On account creation, a **welcome/activation email** is dispatched to a **background queue job** rather than sent synchronously, so admin actions don't block on SMTP latency.

\---

## 3\. Role-Based Access Control (RBAC)

Five roles, enforced via middleware and Laravel policies/gates:

|Role|Permissions|
|-|-|
|**SuperAdmin**|Full system control — manage users/roles, all content, view audit logs|
|**Admin**|Manage all content, view users and contact messages, cannot delete SuperAdmin accounts|
|**Editor**|Create, edit, publish all portfolio content (projects, blog, skills, etc.)|
|**Author**|Create/edit own blog drafts only; requires Editor/Admin approval to publish|
|**Viewer**|Read-only dashboard access (e.g. intern/attaché reviewing before promotion to a content role)|

Role checks happen at two layers: route-level middleware (blocks access to whole dashboard sections) and policy-level checks (blocks specific actions like "delete" vs "edit" within an allowed section).

\---

## 4\. Database Schema — Design Decisions

Full schema and ERD are in `ERD.md`. Summary of key decisions:

1. **Single-portfolio model** — content tables aren't scoped by `user\_id` since this backs one person's public portfolio; `users` exists for dashboard auth/RBAC only.
2. **Projects ↔ Tags, Blog ↔ Categories** — both modeled as proper many-to-many relationships via junction tables (`project\_tags`, `blog\_post\_categories`) rather than comma-separated strings, so tags/categories stay queryable and de-duplicated.
3. **Audit logging** — every create/update/delete in the dashboard writes to `audit\_logs` (who, what, old vs new data as JSON, IP, user agent) for accountability during review.
4. **Notifications table** — supports in-app notifications plus an `is\_emailed` flag, decoupling "shown in dashboard" from "sent via background email job."
5. **Soft-disable over hard-delete** — `projects`, `certificates`, and `users` use an `is\_active` flag rather than deleting rows outright, preserving history and avoiding orphaned foreign keys.

\---

## 5\. Background Workers (Phase 3)

* **Trigger**: A SuperAdmin/Admin provisions a new user account from the dashboard.
* **Mechanism**: The request handler dispatches a `SendWelcomeEmailJob` onto Laravel's default queue connection (database driver) instead of sending the email inline — the HTTP response returns immediately.
* **Worker**: `php artisan queue:work` runs as a persistent background process, picking jobs off the `jobs` table and executing them (with automatic retry on failure, logged to `failed\_jobs`).
* **Why this matters**: keeps the admin's request/response cycle fast and avoids timeouts if the mail provider is slow, and gives a retry mechanism if delivery fails transiently.

\---

## 6\. Tables Summary

|Table|Purpose|
|-|-|
|`users`|Auth + RBAC for the admin dashboard|
|`profiles`|Public-facing bio/contact info (1:1 with a user)|
|`education`|Education history entries|
|`skills`|Skill list with category + proficiency|
|`experiences`|Work experience timeline|
|`projects`|Portfolio project entries|
|`tags`|Tech-stack tags|
|`project\_tags`|Projects ↔ Tags junction|
|`blog\_posts`|Blog articles|
|`blog\_categories`|Blog category taxonomy|
|`blog\_post\_categories`|Blog posts ↔ Categories junction|
|`certificates`|Certificates/badges with image or PDF|
|`affiliations`|Professional affiliations/memberships|
|`contact\_messages`|Public contact form submissions|
|`audit\_logs`|System-wide action audit trail|
|`notifications`|In-app + emailed notifications|

\---

## 7\. Deployment Plan (Phase 4 — pending sandbox access)

1. Configure `.env` for the sandbox environment (DB credentials, mail driver, queue connection).
2. Run `php artisan migrate --force` against the sandbox database.
3. Seed the initial SuperAdmin account via a dedicated seeder (not public registration).
4. Start `php artisan queue:work` as a persistent process (or configure via Supervisor if available on the sandbox).
5. Verify: login, RBAC restrictions, CRUD operations, and end-to-end welcome-email delivery through the queue.

\---

## 8\. Open Questions for Review

* Confirm whether the sandbox environment supports a persistent queue worker process (e.g. via Supervisor), or whether `queue:work` needs to run via a scheduled cron-triggered `queue:work --stop-when-empty` instead.
* Confirm final list of RBAC roles — currently five (`SuperAdmin`, `Admin`, `Editor`, `Author`, `Viewer`); happy to collapse/expand to match team convention if there's an existing standard.

