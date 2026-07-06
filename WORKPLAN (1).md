# Development Workplan — Portfolio Builder System

**Developer:** David Kiamba
**Framework:** Laravel 12 (PHP)
**Database:** MySQL 8.0
**Submission:** Phase 1 — Database Design \& Documentation

\---

## Sprint Overview

|Item|Detail|
|-|-|
|Total Duration|8 Weeks|
|Methodology|Phase-gated (each phase reviewed before the next begins)|
|Framework|Laravel 12|
|Database|MySQL 8.0|
|Queue Driver|Database (Laravel Queues)|
|Auth|Custom (bcrypt, no third-party OAuth)|

\---

## Week 1–2 — Planning \& Database Design (Phase 1)

|Day|Task|Deliverable|
|-|-|-|
|Day 1–3|Framework selection, environment setup, repo scaffolding|Laravel project initialized|
|Day 4–7|Entity identification, relationship mapping, normalization pass|Draft ERD|
|Day 8–11|Finalize schema, write migrations, define constraints/indexes|`database/migrations/\*`|
|Day 12–14|Write README, workplan, and schema documentation|WORKPLAN.md, ERD.md, README.md|

**Gate:** Phase 1 submitted for review. No application code written until sign-off.

\---

## Week 3–4 — Custom Authentication \& RBAC (Phase 2)

|Day|Task|Deliverable|
|-|-|-|
|Day 1–4|Build custom auth (registration closed to public, login, bcrypt hashing, session handling)|Auth controllers/middleware|
|Day 5–8|Implement Role-Based Access Control (SuperAdmin, Admin, Editor, Author, Viewer)|`role` middleware, gates/policies|
|Day 9–11|Dashboard scaffolding + profile settings (password change/reset from dashboard)|Dashboard views|
|Day 12–14|Internal testing of auth edge cases (wrong role access, session expiry)|Test notes|

\---

## Week 5–6 — Core CRUD \& Background Workers (Phase 3)

|Day|Task|Deliverable|
|-|-|-|
|Day 1–4|Projects \& Tags CRUD (public + admin)|CRUD controllers/views|
|Day 5–8|Blog Posts, Skills, Education, Experience CRUD|CRUD controllers/views|
|Day 9–10|Certificates, Affiliations, Contact Messages CRUD|CRUD controllers/views|
|Day 11–12|Background worker setup (Laravel Queues) for welcome/activation emails on new user provisioning|Queue job + listener|
|Day 13–14|End-to-end testing of CRUD + queue delivery|Test notes|

\---

## Week 7–8 — Hardening \& Deployment (Phase 4)

|Day|Task|Deliverable|
|-|-|-|
|Day 1–3|Audit logging + notifications wiring|`audit\_logs`, `notifications` tables live|
|Day 4–7|Full regression test of auth, RBAC, CRUD, queues|Test report|
|Day 8–10|Deployment prep: env config, migration/seed scripts for sandbox|Deployment checklist|
|Day 11–14|Deploy to provided cloud sandbox, verify queue worker running|Live sandbox URL|

\---

## Status Snapshot (as of this submission)

|Phase|Status|Notes|
|-|-|-|
|Phase 1 — DB Design \& Docs|✅ Complete (this submission)|Schema, ERD, README attached|
|Phase 2 — Custom Auth \& RBAC|⏳  waiting for phase 1 approval||
|Phase 3 — CRUD \& Background Workers|⏳  Not Started||
|Phase 4 — Cloud Hosting|⏳ Not started||



