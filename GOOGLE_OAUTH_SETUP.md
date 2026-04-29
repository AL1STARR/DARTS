# DARTS - Production Deployment Guide

## Google OAuth Configuration for Production

### Step 1: Create a Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click "Select a Project" → "NEW PROJECT"
3. Enter project name (e.g., "DARTS Intelligence")
4. Click "CREATE"

### Step 2: Enable Google+ API
1. In the Cloud Console, go to "APIs & Services" → "Library"
2. Search for "Google+ API"
3. Click on it and select "ENABLE"

### Step 3: Create OAuth 2.0 Credentials
1. Go to "APIs & Services" → "Credentials"
2. Click "CREATE CREDENTIALS" → "OAuth client ID"
3. If prompted, click "Configure Consent Screen" first:
   - Choose "External" user type
   - Fill in app name: "DARTS Intelligence"
   - Add test users' emails
   - Add required scopes: email, profile, openid
4. After configuring consent screen, create OAuth 2.0 Client ID:
   - Application type: "Web application"
   - Add Authorized redirect URIs:
     - For Development: `http://localhost:8000/auth/google/callback`
     - For Staging: `http://staging.darts.local/auth/google/callback`
     - For Production: `https://darts.yourdomain.com/auth/google/callback`

### Step 4: Update .env Configuration
Copy the credentials into your `.env` file:

```env
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID_HERE
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET_HERE
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
```

### Step 5: Test OAuth Flow
1. Navigate to login page
2. Click "Sign in with Google"
3. Sign in with a Google account
4. You should be redirected back to DARTS dashboard

## Current Configuration

### Development Environment
- ✅ Google OAuth is already configured for localhost
- ✅ Client ID: `226182537429-tfoacas949boufq25tlsmqnkqd3ntli7.apps.googleusercontent.com`
- ✅ Redirect URI: `http://127.0.0.1:8000/auth/google/callback`
- ⚠️ NOTE: This client ID is for development only and should be replaced for production

### Production Environment
To deploy to production:

1. Create a new Google OAuth application (do not reuse development credentials)
2. Update `.env` with production credentials
3. Ensure your domain is properly configured in Google Cloud Console
4. Test the OAuth flow in staging before deploying to production
5. Store credentials securely (use environment variables, not hardcoded)

## Security Notes
- Never commit `.env` file to version control
- Always use HTTPS for production URLs
- Rotate credentials periodically
- Use separate OAuth apps for development, staging, and production
- Restrict authorized redirect URIs to your domain only

## Troubleshooting

### Error: "No account found for this Google email"
- User must first request access through the DARTS registration form
- Admin must approve the request before user can log in via Google
- The Google email must match the email used in the registration request

### Error: "Redirect URI mismatch"
- Ensure the redirect URI in Google Cloud Console exactly matches `GOOGLE_REDIRECT_URI` in `.env`
- Check for trailing slashes - they matter!
- Make sure the domain/port matches exactly

### OAuth not working in development
- Clear Laravel's configuration cache: `php artisan config:clear`
- Verify `.env` file has correct credentials
- Check that Google+ API is enabled in Cloud Console
- Ensure test users are added to OAuth consent screen
