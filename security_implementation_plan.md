# Implement Cloudflare Protection & Rate Limiting

This plan implements the security strategy for `app.workorio.com` when placed behind Cloudflare. It ensures real visitor IPs are detected, rate limiting is handled intelligently (Account + IP), and brute-force attempts are mitigated gradually.

## User Review Required

> [!IMPORTANT]
> **CAPTCHA Provider:** The plan includes adding a CAPTCHA. Since you are moving to Cloudflare, **Cloudflare Turnstile** is highly recommended over Google reCAPTCHA as it is more privacy-focused and often invisible to legitimate users. I will need your Turnstile Site Key and Secret Key to implement this.

## Open Questions

> [!WARNING]
> 1. **Thresholds:** What are your preferred limits? My proposed defaults are:
>    - 3 failed attempts: Log warning
>    - 5 failed attempts: Add a 2-second delay to the response
>    - 7 failed attempts: Require CAPTCHA on the next attempt
>    - 10 failed attempts: Temporary lockout for 15 minutes
>    Are these thresholds acceptable?
> 2. **Cloudflare Turnstile:** Do you already have a Turnstile Site Key and Secret Key created in your Cloudflare dashboard?

## Proposed Changes

---

### Middleware & Core Config

#### [MODIFY] [app.php](file:///d:/DontDelete/laravel/leadmanagement%20%28akrati%20ui%20work%29/bootstrap/app.php)
- Update the middleware configuration to trust all proxies `*` (since the app will be exclusively behind Cloudflare). This ensures `$request->ip()` returns the actual user IP rather than the Cloudflare edge node IP.

---

### Authentication Logic

#### [MODIFY] [AuthController.php](file:///d:/DontDelete/laravel/leadmanagement%20%28akrati%20ui%20work%29/app/Http/Controllers/AuthController.php)
- Inject `RateLimiter` and `Log` facades.
- Inside the `login` method, create a composite throttle key: `$throttleKey = Str::lower($request->input('login_id')) . '|' . $request->ip();`.
- **Implement Gradual Protection:**
  - Check attempts against the `$throttleKey`.
  - If attempts >= 10: Reject request with a "Too many login attempts" error (Lockout).
  - If attempts >= 7: Validate the CAPTCHA response (Turnstile).
  - If attempts >= 5: Execute `sleep(2)` to artificially delay the response.
- **Implement Logging:**
  - On failed login: `Log::warning(...)` recording the `login_id`, `ip`, user agent, and failure status.
  - On successful login: `Log::info(...)` and clear the rate limiter using `RateLimiter::clear($throttleKey)`.

---

### Views (Frontend)

#### [MODIFY] [login.blade.php](file:///d:/DontDelete/laravel/leadmanagement%20%28akrati%20ui%20work%29/resources/views/auth/login.blade.php)
- Add Cloudflare Turnstile JavaScript script tag.
- Conditionally render the Turnstile widget inside the form if the user has reached the CAPTCHA threshold (passed down via session errors/variables from the controller).

## Verification Plan

### Manual Verification
1. **IP Detection:** Attempt a login and check `storage/logs/laravel.log` to verify your real public IP is recorded, not a Cloudflare IP.
2. **Gradual Delay:** Intentionally fail login 5 times. On the 6th attempt, verify the request takes noticeably longer (2 seconds).
3. **CAPTCHA Trigger:** Fail login 7 times. Verify the login page now renders a Turnstile widget.
4. **Lockout:** Fail 10 times. Verify the account+IP combo is temporarily locked out, but a different IP trying the same account (or the same IP trying a different account) is not blocked (unless their specific combo reaches the limit).
