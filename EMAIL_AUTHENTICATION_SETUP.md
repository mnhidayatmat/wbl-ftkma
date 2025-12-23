# Email Authentication Setup Guide

This document describes the email authentication features implemented in the WBL Management System.

## Features Implemented

### 1. Email Verification on Registration
- Users must verify their email address before they can log in
- Verification email is sent automatically upon registration
- Custom email template with WBL branding

### 2. Password Reset (Forgot Password)
- Users can request a password reset link via email
- Secure token-based password reset
- Custom email template with WBL branding

## Configuration

### Email Settings (.env)
Configure your email settings in the `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@wbl.umpsa.edu.my
MAIL_FROM_NAME="${APP_NAME}"
```

### For Gmail:
1. Enable 2-Step Verification
2. Generate an App Password
3. Use the App Password in `MAIL_PASSWORD`

### For Other SMTP Providers:
Update the `MAIL_HOST`, `MAIL_PORT`, and `MAIL_ENCRYPTION` accordingly.

## Routes

### Guest Routes (Unauthenticated)
- `GET /forgot-password` - Show forgot password form
- `POST /forgot-password` - Send password reset link
- `GET /reset-password/{token}` - Show reset password form
- `POST /reset-password` - Process password reset

### Authenticated Routes
- `GET /verify-email` - Show email verification prompt
- `GET /verify-email/{id}/{hash}` - Verify email address
- `POST /email/verification-notification` - Resend verification email

## User Flow

### Registration Flow:
1. User registers → Account created
2. Verification email sent automatically
3. User redirected to login page
4. User must verify email before logging in
5. After verification, user can log in normally

### Password Reset Flow:
1. User clicks "Forgot Password?" on login page
2. User enters email address
3. Password reset link sent to email
4. User clicks link in email
5. User enters new password
6. Password updated, user redirected to login

## Email Templates

### Verification Email (`resources/views/emails/verify-email.blade.php`)
- Custom HTML template with WBL branding
- Includes verification button
- Alternative text link if button doesn't work
- Expires in 60 minutes (configurable)

### Password Reset Email (`resources/views/emails/reset-password.blade.php`)
- Custom HTML template with WBL branding
- Includes reset password button
- Alternative text link if button doesn't work
- Security warning if user didn't request reset
- Expires in 60 minutes (configurable)

## Controllers

### `RegisteredUserController`
- Sends verification email on registration
- Does NOT auto-login user (requires verification first)

### `AuthenticatedSessionController`
- Checks email verification before allowing login
- Shows error if email not verified

### `PasswordResetLinkController`
- Handles forgot password requests
- Sends password reset email

### `NewPasswordController`
- Handles password reset form submission
- Validates token and updates password

### `EmailVerificationPromptController`
- Shows verification prompt page
- Allows resending verification email

### `VerifyEmailController`
- Processes email verification
- Marks email as verified

## Models

### `User` Model
- Implements `MustVerifyEmail` interface
- Custom `sendEmailVerificationNotification()` method
- Custom `sendPasswordResetNotification()` method

## Notifications

### `VerifyEmail` Notification
- Custom email verification notification
- Uses custom email template
- Generates signed verification URL

### `ResetPassword` Notification
- Custom password reset notification
- Uses custom email template
- Generates password reset URL with token

## Security Features

1. **Email Verification Required**: Users cannot log in until email is verified
2. **Signed URLs**: Verification links are signed and expire after 60 minutes
3. **Token-Based Reset**: Password reset uses secure tokens
4. **Rate Limiting**: Verification and reset requests are rate-limited
5. **Token Expiration**: Reset tokens expire after 60 minutes

## Testing

### Test Email Verification:
1. Register a new account
2. Check email inbox for verification link
3. Click verification link
4. Try to log in (should work after verification)

### Test Password Reset:
1. Go to login page
2. Click "Forgot Password?"
3. Enter email address
4. Check email inbox for reset link
5. Click reset link
6. Enter new password
7. Log in with new password

## Troubleshooting

### Emails Not Sending:
1. Check `.env` mail configuration
2. Verify SMTP credentials
3. Check spam folder
4. Test with `php artisan tinker`:
   ```php
   Mail::raw('Test email', function($message) {
       $message->to('your-email@example.com')->subject('Test');
   });
   ```

### Verification Link Not Working:
1. Check if link has expired (60 minutes)
2. Verify `APP_URL` in `.env` is correct
3. Check if user is already verified

### Password Reset Not Working:
1. Check if token has expired (60 minutes)
2. Verify email address matches
3. Check if user exists in database

## Notes

- Email verification is mandatory for all new registrations
- Password reset tokens are single-use
- Both verification and reset links expire after 60 minutes
- Users can resend verification email from the verification prompt page

