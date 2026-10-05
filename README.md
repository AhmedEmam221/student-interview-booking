# Student Interview Booking System

A complete full-stack student interview booking system built with **Laravel 8**, **MySQL**, **Blade**, and **Tailwind CSS**.

## Features

### Public Registration Form
- Multi-section form with validation
- Personal Information (Name, Email, Phone, Major, Academic Year)
- Track Selection (Technical / Non-Technical - database driven)
- Strengths & Weaknesses (Checkboxes - database driven with many-to-many relationships)
- Student Activity History
- Hobbies & Comments
- Interview Slot Selection (Real-time availability)

### Time Slot Management
- Working hours: 9:00 AM - 4:00 PM
- 30-minute appointment slots (09:00, 09:30, 10:00, ..., 15:30, 16:00)
- Weekly schedule management for admins
- Date + Time + Track = Multiple board members per slot
- **1 board member = 1 interview per 30-minute slot**
- Automatic board member assignment (round-robin)
- Slots disappear when fully booked
- Database transactions/locking to prevent race conditions

### Board Members
- CRUD operations
- Track assignment (many-to-many)
- Schedule slot assignment
- Interview count tracking
- Referral statistics
- Unique referral codes/links (`/register?ref=ABC123XYZ`)
- Separate referral board member vs interview board member

### Admin Panel
- Authentication with admin middleware
- Dashboard with statistics:
  - Total/Pending/Confirmed/Completed applications
  - Applications per track
  - Applications per day (last 7 days)
  - Applications per board member (interviews)
  - Referrals per board member
  - Available/Full slots
- Tracks management
- Board Members management
- Strengths/Weaknesses management
- Applications management with filters
- Weekly Schedule management (bulk create, copy week)
- Reports with print-friendly view and Excel/CSV export

### Reports / Printing
- Filter by track, date range, status
- Print-friendly interview sheet
- Browser Print → Save as PDF support
- Excel export (Maatwebsite/Excel)
- CSV export
- Sorted by date and time

### Security & Validation
- Admin authentication/authorization
- CSRF protection
- Server-side validation
- Rate limiting for public submissions
- Secure referral codes
- Duplicate submission prevention
- Database transactions for booking
- Backend availability validation

## Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Public/RegistrationController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── TrackController.php
│   │       ├── BoardMemberController.php
│   │       ├── StrengthController.php
│   │       ├── WeaknessController.php
│   │       ├── ApplicationController.php
│   │       ├── ScheduleController.php
│   │       └── ReportController.php
│   ├── Requests/
│   │   ├── StoreApplicationRequest.php
│   │   ├── AdminStoreTrackRequest.php
│   │   ├── AdminStoreBoardMemberRequest.php
│   │   └── AdminStoreScheduleSlotRequest.php
│   └── Middleware/AdminMiddleware.php
├── Models/
│   ├── Track.php
│   ├── BoardMember.php
│   ├── Strength.php
│   ├── Weakness.php
│   ├── Application.php
│   └── ScheduleSlot.php
├── Services/
│   ├── AvailabilityService.php
│   └── BookingService.php
└── Exports/
    └── InterviewSheetExport.php
```

## Database Schema

- `users` - Admin users with `is_admin` flag
- `tracks` - Interview tracks (Technical, Non-Technical)
- `board_members` - Board members with referral codes
- `strengths` - Predefined strengths
- `weaknesses` - Predefined weaknesses
- `applications` - Student applications
- `application_strength` - Many-to-many pivot
- `application_weakness` - Many-to-many pivot
- `schedule_slots` - Date + Time + Track slots
- `slot_board_member` - Board members assigned to slots
- `board_member_track` - Board member track assignments

## Installation

### Requirements
- PHP 8.0+
- MySQL 5.7+
- Composer
- Node.js & NPM (for frontend assets if needed)

### Setup

1. **Clone/Extract the project**
```bash
cd student-interview-booking
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Configure environment**
```bash
cp .env.example .env
# Edit .env with your database credentials
```

4. **Generate application key**
```bash
php artisan key:generate
```

5. **Create database**
```sql
CREATE DATABASE student_interview_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

6. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

7. **Start the server**
```bash
php artisan serve
```

8. **Access the application**
- Public: http://localhost:8000/register
- Admin: http://localhost:8000/login
- Default admin: `admin@example.com` / `password`

## Default Data

After seeding, you'll have:
- **Tracks**: Technical, Non-Technical
- **Strengths**: Time Management, Public Speaking, Team Work, Leadership, Self-Confidence, Flexibility, Creativity
- **Weaknesses**: Can't work under pressure, Low Problem Solving, Aggressive, Adapt to Sudden Change, Overthinking, Difficulty saying no, Perfectionist
- **Board Members**: 4 sample members (2 per track)
- **Admin User**: admin@example.com / password

## Usage

### Creating Schedule Slots (Admin)
1. Login to admin panel
2. Go to Schedule → "Add Slot" or "Bulk Create"
3. Select week, track, time slots, max interviews per slot
4. Assign board members to slots
5. Use "Copy Week" to replicate schedule

### Student Registration
1. Visit `/register` or use referral link `/register?ref=ABC123XYZ`
2. Fill all sections of the form
3. Select available time slot (real-time availability)
4. Submit - board member automatically assigned

### Reports
1. Go to Reports in admin panel
2. Filter by track, date range, status
3. Click "Print View" for browser printing
4. Export to Excel or CSV

## API Endpoints (Public)

- `GET /register` - Show registration form
- `POST /register` - Submit application
- `GET /register/slots?track_id=1&date=2026-01-15` - Get available slots (AJAX)
- `GET /register/success/{id}` - Success page

## Admin Routes (Protected)

All routes under `/admin` require authentication and admin role:
- `GET /admin` - Dashboard
- `GET /admin/tracks` - Tracks CRUD
- `GET /admin/board-members` - Board Members CRUD
- `GET /admin/strengths` - Strengths CRUD
- `GET /admin/weaknesses` - Weaknesses CRUD
- `GET /admin/applications` - Applications management
- `GET /admin/schedule` - Weekly schedule management
- `GET /admin/reports` - Reports with export

## Testing

```bash
php artisan test
```

## License

MIT License