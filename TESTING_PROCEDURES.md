# Testing Procedures for DARTS

This document outlines the procedures for managing test data and setting up test environments.

## Table of Contents
1. [Clearing Test Data](#clearing-test-data)
2. [Creating Admin User](#creating-admin-user)

---

## Clearing Test Data

### Overview
This procedure will reset the entire database to a clean state, removing all test records while preserving the table structure and migrations.

### Prerequisites
- Laravel environment configured and running
- Database connection properly set up in `.env` file

### Steps

1. **Open Terminal**
   - Navigate to the project root directory:
   ```bash
   cd c:\Users\Papa\DARTS
   ```

2. **Run Fresh Migration**
   - Execute the fresh migration command to drop all tables and re-run migrations:
   ```bash
   php artisan migrate:fresh
   ```

3. **Wait for Completion**
   - The command will display progress for each migration
   - Once complete, you'll see the final confirmation

### What This Does
- ✅ Drops all existing tables
- ✅ Recreates empty tables with the latest schema
- ✅ Runs all migrations in the `database/migrations` directory
- ✅ Preserves migration files and structure

### Example Output
```
  Dropping all tables .......................................... 162.56ms DONE

   INFO  Preparing database.  

  Creating migration table ...................................... 19.43ms DONE

   INFO  Running migrations.  

  0001_01_01_000000_create_users_table .......................... 49.31ms DONE
  [... additional migrations ...]
  2026_04_28_133304_add_instructions_to_route_stages_table ....... 3.95ms DONE
```

### Notes
- This command will **permanently delete** all test data
- Use with caution in production environments
- After running this, you'll need to create a new admin user (see section below)

---

## Creating Admin User

### Overview
This procedure creates a new admin user in the database using Laravel Tinker (an interactive shell).

### Prerequisites
- Database is initialized and running
- Laravel is properly configured

### Steps

1. **Start Laravel Tinker**
   - Open terminal and navigate to project root:
   ```bash
   cd c:\Users\Papa\DARTS
   ```
   - Start the interactive Tinker shell:
   ```bash
   php artisan tinker
   ```

2. **Create Admin User**
   - Once in the Tinker shell (you'll see the `>` prompt), run:
   ```bash
   $user = App\Models\User::create(['name' => 'Admin', 'email' => 'admin@darts.com', 'password' => bcrypt('admin123'), 'is_admin' => true])
   ```

3. **Verify Creation**
   - Tinker will display the created user object:
   ```
   = App\Models\User {#8017
       name: "Admin",
       email: "admin@darts.com",
       #password: "$2y$12$...",
       is_admin: true,
       updated_at: "2026-04-30 04:21:48",
       created_at: "2026-04-30 04:21:48",
       id: 1,
     }
   ```

4. **Exit Tinker**
   - Type `exit` to exit the shell:
   ```bash
   exit
   ```

### Login Credentials
After creating the admin user, use these credentials to log in:

| Field | Value |
|-------|-------|
| **Email** | admin@darts.com |
| **Password** | admin123 |
| **Name** | Admin |
| **ID** | 1 |
| **Admin Status** | Yes |

### Customization
You can customize the admin user by changing the values in the create command:

```bash
$user = App\Models\User::create([
    'name' => 'Your Name',              # Change admin name
    'email' => 'your@email.com',        # Change email
    'password' => bcrypt('password'),   # Change password (will be hashed)
    'is_admin' => true                  # Set to true for admin, false for regular user
])
```

### Notes
- Passwords are automatically hashed using `bcrypt()`
- Do not share the password in version control
- For production, use strong passwords
- You can create multiple admin users by running the command multiple times with different emails

---

## Combined Quick Reference

### Reset Database and Create Admin User

```bash
# 1. Clear all test data
cd c:\Users\Papa\DARTS
php artisan migrate:fresh

# 2. Create admin user
php artisan tinker
$user = App\Models\User::create(['name' => 'Admin', 'email' => 'admin@darts.com', 'password' => bcrypt('admin123'), 'is_admin' => true])
exit
```

### Result
- Fresh database with no test data
- Admin user ready to login with email: `admin@darts.com` and password: `admin123`

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| **"SQLSTATE[HY000]"** | Check database connection in `.env` file |
| **"Target class [App\Models\User] not found"** | Ensure autoloader is running: `composer dumpautoload` |
| **Tinker not starting** | Run `composer install` to install dependencies |
| **User not created** | Verify User model exists at `app/Models/User.php` |

---

## Safety Notes

⚠️ **Important:**
- Always backup your database before running `migrate:fresh` in production
- Test these procedures in a development environment first
- Keep a separate admin account for security purposes
- Never commit passwords to version control

