-- =====================================================================
-- GuidanceCare baseline schema for PostgreSQL (Supabase)
-- Creates the FINAL tables directly. Replaces the 34 SQLite-era migrations
-- for the Postgres build only (they stay in database/migrations for SQLite).
--
-- Run by: database/migrations_pgsql/2026_11_20_000000_create_guidancecare_baseline.php
-- Schema: whatever DB_SCHEMA puts first on the search_path (guidancecare or guidancecare_test).
--
-- Delete rules (see the teaching manuscript, Part 6):
--   RESTRICT  - anything that points at a student or a case: counseling history can't be erased by a delete
--   SET NULL  - optional links (a counselor who left, the referral/request a case came from)
--   CASCADE   - not used for counseling data
-- =====================================================================

-- ---------- 1. People ----------
CREATE TABLE users (
    id                   bigserial PRIMARY KEY,
    name                 varchar(255) NOT NULL,
    email                varchar(255) NOT NULL UNIQUE,
    email_verified_at    timestamp(0) NULL,
    password             varchar(255) NOT NULL,
    remember_token       varchar(100) NULL,
    role                 varchar(20)  NOT NULL DEFAULT 'student'
                         CONSTRAINT users_role_check CHECK (role IN ('student','teacher','counselor','clinic','disciplinary','admin')),
    student_number       varchar(255) NULL,
    employee_id          varchar(255) NULL,
    course_id            bigint NULL,          -- kept for code compatibility; not used by any screen
    year_level           varchar(255) NULL,    -- kept for code compatibility
    section_id           bigint NULL,          -- kept for code compatibility
    must_change_password boolean NOT NULL DEFAULT false,
    is_active            boolean NOT NULL DEFAULT true,   -- (new) deactivate instead of delete
    created_at           timestamp(0) NULL,
    updated_at           timestamp(0) NULL
);
CREATE INDEX users_role_index ON users (role);

-- ---------- 2. Things that start a case ----------
CREATE TABLE referrals (
    id               bigserial PRIMARY KEY,
    teacher_id       bigint NOT NULL REFERENCES users (id) ON DELETE RESTRICT,
    student_id       bigint NULL     REFERENCES users (id) ON DELETE RESTRICT,
    concern_type     varchar(255) NOT NULL,
    description      text NOT NULL,
    urgency          varchar(10) NOT NULL DEFAULT 'Medium'
                     CONSTRAINT referrals_urgency_check CHECK (urgency IN ('Low','Medium','High')),
    status           varchar(20) NOT NULL DEFAULT 'Pending'
                     CONSTRAINT referrals_status_check CHECK (status IN ('Pending','Accepted','In Progress','Closed')),
    student_response varchar(10) NOT NULL DEFAULT 'Pending'
                     CONSTRAINT referrals_student_response_check CHECK (student_response IN ('Pending','Accepted','Declined')),
    -- copies kept until the code reads identity from users only (audit B7)
    student_name     varchar(255) NULL,
    student_number   varchar(255) NULL,
    year_section     varchar(255) NULL,
    course_id        bigint NULL,
    year_level       varchar(255) NULL,
    section_id       bigint NULL,
    created_at       timestamp(0) NULL,
    updated_at       timestamp(0) NULL
);
CREATE INDEX referrals_teacher_id_index ON referrals (teacher_id);
CREATE INDEX referrals_student_id_index ON referrals (student_id);
CREATE INDEX referrals_status_index     ON referrals (status);

CREATE TABLE counseling_requests (
    id                 bigserial PRIMARY KEY,
    student_id         bigint NOT NULL REFERENCES users (id) ON DELETE RESTRICT,
    concern_type       varchar(255) NOT NULL,
    description        text NOT NULL,
    is_anonymous       boolean NOT NULL DEFAULT false,
    status             varchar(20) NOT NULL DEFAULT 'Pending'
                       CONSTRAINT counseling_requests_status_check CHECK (status IN ('Pending','Accepted','Scheduled','Closed')),
    preferred_datetime timestamp(0) NULL,
    -- copies kept until the code reads identity from users only (audit B7)
    student_name       varchar(255) NULL,
    student_number     varchar(255) NULL,
    year_section       varchar(255) NULL,
    program            varchar(255) NULL,
    created_at         timestamp(0) NULL,
    updated_at         timestamp(0) NULL
);
CREATE INDEX counseling_requests_student_id_index ON counseling_requests (student_id);
CREATE INDEX counseling_requests_status_index     ON counseling_requests (status);

-- ---------- 3. The official record ----------
CREATE TABLE cases (
    id             bigserial PRIMARY KEY,
    case_number    varchar(255) NOT NULL UNIQUE,
    student_id     bigint NULL REFERENCES users (id)               ON DELETE RESTRICT,
    counselor_id   bigint NULL REFERENCES users (id)               ON DELETE SET NULL,
    referral_id    bigint NULL REFERENCES referrals (id)           ON DELETE SET NULL,
    request_id     bigint NULL REFERENCES counseling_requests (id) ON DELETE SET NULL,
    concern_type   varchar(255) NOT NULL,
    summary        text NULL,
    status         varchar(20) NOT NULL DEFAULT 'Pending'
                   CONSTRAINT cases_status_check CHECK (status IN ('Pending','Ongoing','For Follow-up','Completed','Referred','Closed')),
    opened_at      date NULL,
    closed_at      date NULL,
    -- copies kept until the code reads identity from users only (audit B7)
    student_name   varchar(255) NULL,
    student_number varchar(255) NULL,
    year_section   varchar(255) NULL,
    created_at     timestamp(0) NULL,
    updated_at     timestamp(0) NULL
);
CREATE INDEX cases_student_id_index   ON cases (student_id);
CREATE INDEX cases_counselor_id_index ON cases (counselor_id);
CREATE INDEX cases_status_index       ON cases (status);

-- ---------- 4. Everything that happens to a case ----------
CREATE TABLE appointments (
    id                   bigserial PRIMARY KEY,
    case_id              bigint NOT NULL REFERENCES cases (id) ON DELETE RESTRICT,
    student_id           bigint NULL     REFERENCES users (id) ON DELETE RESTRICT,   -- (new) real FK
    counselor_id         bigint NULL     REFERENCES users (id) ON DELETE SET NULL,
    appointment_datetime timestamp(0) NOT NULL,
    location             varchar(255) NULL,
    purpose              text NULL,
    status               varchar(20) NOT NULL DEFAULT 'Scheduled'
                         CONSTRAINT appointments_status_check CHECK (status IN ('Scheduled','Completed','Cancelled','Rescheduled','Requested')),
    reminder_sent_at     timestamp(0) NULL,
    created_at           timestamp(0) NULL,
    updated_at           timestamp(0) NULL
);
CREATE INDEX appointments_case_id_index    ON appointments (case_id);
CREATE INDEX appointments_student_id_index ON appointments (student_id);
CREATE INDEX appointments_datetime_index   ON appointments (appointment_datetime);

CREATE TABLE appointment_requests (
    id                 bigserial PRIMARY KEY,
    student_id         bigint NOT NULL REFERENCES users (id)        ON DELETE RESTRICT,
    case_id            bigint NULL     REFERENCES cases (id)        ON DELETE SET NULL,
    preferred_datetime timestamp(0) NOT NULL,
    purpose            text NULL,
    status             varchar(20) NOT NULL DEFAULT 'Pending'
                       CONSTRAINT appointment_requests_status_check CHECK (status IN ('Pending','Scheduled','Declined')),
    counselor_id       bigint NULL     REFERENCES users (id)        ON DELETE SET NULL,
    appointment_id     bigint NULL     REFERENCES appointments (id) ON DELETE SET NULL,
    counselor_remarks  varchar(255) NULL,
    created_at         timestamp(0) NULL,
    updated_at         timestamp(0) NULL
);
CREATE INDEX appointment_requests_student_id_index ON appointment_requests (student_id);
CREATE INDEX appointment_requests_status_index     ON appointment_requests (status);

CREATE TABLE case_notes (
    id           bigserial PRIMARY KEY,
    case_id      bigint NOT NULL REFERENCES cases (id) ON DELETE RESTRICT,
    counselor_id bigint NULL     REFERENCES users (id) ON DELETE SET NULL,
    note         text NOT NULL,
    created_at   timestamp(0) NULL,
    updated_at   timestamp(0) NULL
);
CREATE INDEX case_notes_case_id_index ON case_notes (case_id);

CREATE TABLE office_referrals (
    id               bigserial PRIMARY KEY,
    case_id          bigint NOT NULL REFERENCES cases (id) ON DELETE RESTRICT,
    referred_by      bigint NOT NULL REFERENCES users (id) ON DELETE RESTRICT,
    office           varchar(20) NOT NULL
                     CONSTRAINT office_referrals_office_check CHECK (office IN ('Clinic','Disciplinary')),
    reason           text NOT NULL,
    notes_for_office text NULL,
    status           varchar(20) NOT NULL DEFAULT 'Pending'
                     CONSTRAINT office_referrals_status_check CHECK (status IN ('Pending','Received','In Progress','Completed','Closed')),
    handled_by       bigint NULL REFERENCES users (id) ON DELETE SET NULL,   -- (new) who completed it
    completed_at     timestamp(0) NULL,                                      -- (new) when
    created_at       timestamp(0) NULL,
    updated_at       timestamp(0) NULL
);
CREATE INDEX office_referrals_case_id_index ON office_referrals (case_id);
CREATE INDEX office_referrals_office_status_index ON office_referrals (office, status);

-- ---------- 5. The diary (new) ----------
CREATE TABLE activity_logs (
    id           bigserial PRIMARY KEY,
    actor_id     bigint NULL REFERENCES users (id) ON DELETE SET NULL,
    action       varchar(100) NOT NULL,          -- e.g. case.status_changed, office_referral.completed
    subject_type varchar(50)  NOT NULL,          -- e.g. cases, office_referrals
    subject_id   bigint NULL,
    meta         jsonb NULL,                     -- extra details, e.g. {"from":"Ongoing","to":"Closed"}
    created_at   timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX activity_logs_subject_index ON activity_logs (subject_type, subject_id);
CREATE INDEX activity_logs_actor_index   ON activity_logs (actor_id);

-- ---------- 6. Laravel's own tables ----------
CREATE TABLE password_reset_tokens (
    email      varchar(255) PRIMARY KEY,
    token      varchar(255) NOT NULL,
    created_at timestamp(0) NULL
);

CREATE TABLE sessions (
    id            varchar(255) PRIMARY KEY,
    user_id       bigint NULL,
    ip_address    varchar(45) NULL,
    user_agent    text NULL,
    payload       text NOT NULL,
    last_activity integer NOT NULL
);
CREATE INDEX sessions_user_id_index       ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);

CREATE TABLE cache (
    key        varchar(255) PRIMARY KEY,
    value      text NOT NULL,
    expiration integer NOT NULL
);
CREATE INDEX cache_expiration_index ON cache (expiration);

CREATE TABLE cache_locks (
    key        varchar(255) PRIMARY KEY,
    owner      varchar(255) NOT NULL,
    expiration integer NOT NULL
);
CREATE INDEX cache_locks_expiration_index ON cache_locks (expiration);

CREATE TABLE jobs (
    id           bigserial PRIMARY KEY,
    queue        varchar(255) NOT NULL,
    payload      text NOT NULL,
    attempts     smallint NOT NULL,
    reserved_at  integer NULL,
    available_at integer NOT NULL,
    created_at   integer NOT NULL
);
CREATE INDEX jobs_queue_index ON jobs (queue);

CREATE TABLE job_batches (
    id             varchar(255) PRIMARY KEY,
    name           varchar(255) NOT NULL,
    total_jobs     integer NOT NULL,
    pending_jobs   integer NOT NULL,
    failed_jobs    integer NOT NULL,
    failed_job_ids text NOT NULL,
    options        text NULL,
    cancelled_at   integer NULL,
    created_at     integer NOT NULL,
    finished_at    integer NULL
);

CREATE TABLE failed_jobs (
    id         bigserial PRIMARY KEY,
    uuid       varchar(255) NOT NULL UNIQUE,
    connection text NOT NULL,
    queue      text NOT NULL,
    payload    text NOT NULL,
    exception  text NOT NULL,
    failed_at  timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------- 7. Locks: Row Level Security on every table, no policies ----------
-- The table owner (the login Laravel uses) is not blocked by RLS, so the app keeps working.
-- Anyone else (Supabase's anon/authenticated API roles) gets nothing.
ALTER TABLE users                 ENABLE ROW LEVEL SECURITY;
ALTER TABLE referrals             ENABLE ROW LEVEL SECURITY;
ALTER TABLE counseling_requests   ENABLE ROW LEVEL SECURITY;
ALTER TABLE cases                 ENABLE ROW LEVEL SECURITY;
ALTER TABLE appointments          ENABLE ROW LEVEL SECURITY;
ALTER TABLE appointment_requests  ENABLE ROW LEVEL SECURITY;
ALTER TABLE case_notes            ENABLE ROW LEVEL SECURITY;
ALTER TABLE office_referrals      ENABLE ROW LEVEL SECURITY;
ALTER TABLE activity_logs         ENABLE ROW LEVEL SECURITY;
ALTER TABLE password_reset_tokens ENABLE ROW LEVEL SECURITY;
ALTER TABLE sessions              ENABLE ROW LEVEL SECURITY;
ALTER TABLE cache                 ENABLE ROW LEVEL SECURITY;
ALTER TABLE cache_locks           ENABLE ROW LEVEL SECURITY;
ALTER TABLE jobs                  ENABLE ROW LEVEL SECURITY;
ALTER TABLE job_batches           ENABLE ROW LEVEL SECURITY;
ALTER TABLE failed_jobs           ENABLE ROW LEVEL SECURITY;
