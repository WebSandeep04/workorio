# React Native Android TV App (55") Implementation Plan

This document outlines the architecture and implementation strategy for building a React Native application targeted for a 55-inch Android TV. The app will utilize the existing tenant-based authentication system and display an automated, paginated dashboard of tasks.

## 1. Backend Analysis: Tenant-Based Authentication

### How it currently works
The existing backend handles multi-tenancy beautifully through dynamic database connections:
1. **Login Flow (Mobile App's `AuthController`)**:
   - The TV app will use the exact same `POST /api/login` endpoint that the current Mobile App uses.
   - The user provides `login_id` and `password`.
   - The `AuthController` loops through all tenant databases (configured in the `mysql` master database).
   - It attempts to find the user in each tenant's database.
   - Once found and the password verified, it generates a Sanctum token and returns it along with the `tenant_id` and user permissions.
2. **Authenticated Requests Flow**:
   - The React Native TV app must send two critical headers in every subsequent API request, just like the mobile app:
     - `Authorization: Bearer <token>`
     - `X-Tenant-ID: <tenant_id>`
   - The `SetTenantDatabase` middleware catches the `X-Tenant-ID` header, queries the master database for the tenant's connection details, and dynamically switches the DB connection (`TenantDatabaseService::setDefaultConnection`) before reaching the controllers.

## 2. TV App Architecture (React Native)

### Setup & Dependencies
- **Framework**: React Native CLI (selected for optimal Android TV D-Pad focus events and native module integration).
- **Navigation**: React Navigation (configured for spatial/D-Pad navigation).
- **State Management**: Zustand or Redux Toolkit for managing tasks and authentication state.
- **Styling**: Tailored for a 1920x1080 (1080p) or 3840x2160 (4K) 55-inch display. Fonts and touch targets must be upscaled (e.g., minimum font size of 24sp).

### Core Components

#### A. Authentication Module
- A TV-friendly login screen using an onscreen keyboard.
- Will call the existing mobile app's `POST /api/login` API and securely store the Sanctum `token` and `tenant_id` using `AsyncStorage` or `react-native-encrypted-storage`.

#### B. Dashboard / Task Board
- **Data Fetching**: 
  - Call `GET /api/tasks/assigned` to fetch the user's due/assigned tasks.
  - Call the API endpoint (to be added/configured) for `SignalTaskController@fetchImmediateTasks` to fetch tasks from the `immediate_tasks` database table.
  - Both task lists will be combined and sorted appropriately.
- **Auto-Pagination Logic**:
  - The tasks will be chunked into pages (e.g., 6 to 8 tasks per screen).
  - A `useEffect` hook with a timer will trigger a state change every **15 seconds** to advance the current page index.
  - When the last page is reached, it will loop back to page 1 and trigger a background refresh of the APIs to pull new tasks.

#### C. UI/UX Considerations for 55" Display
- **Dark Mode**: High contrast, dark themes prevent eye strain in a room setting.
- **Typography**: Large, highly legible fonts (e.g., Inter or Roboto). No text smaller than 18pt.
- **Spatial Navigation**: The app must handle standard remote inputs (Up, Down, Left, Right, OK). Components must use the `focusable={true}` prop to capture remote focus.

## Proposed Changes

### [Backend Repository]
#### [MODIFY] `routes/api.php`
- Expose the `fetchImmediateTasks` route from `SignalTaskController` under the API middleware group so the TV app can fetch immediate tasks using Bearer tokens.

### [React Native App Repository]

#### [NEW] `App.tsx`
Main entry point, configuring React Navigation and standard layout wrappers.

#### [NEW] `src/services/api.ts`
Axios instance pre-configured to inject `Authorization` and `X-Tenant-ID` headers into all requests.

#### [NEW] `src/screens/LoginScreen.tsx`
TV-optimized login form capturing username and password.

#### [NEW] `src/screens/TaskDashboardScreen.tsx`
The primary automated screen. Contains the timer for 15-second auto-pagination and fetches the regular tasks and immediate tasks arrays.

#### [NEW] `src/components/TaskCard.tsx`
A large, highly visible card displaying task details, tailored for a 55-inch display.

## Verification Plan

### Automated Tests
- N/A for initial prototype setup.

### Manual Verification
1. Expose the immediate tasks route via API.
2. Build the APK using React Native CLI.
3. Install the APK on an Android TV Emulator (1080p resolution) or a physical Android TV.
4. Test the Login flow using the TV remote (D-pad), confirming it hits the existing Mobile App Auth Controller.
5. Verify that both regular tasks and immediate tasks load correctly from the specific tenant's database.
6. Let the app sit idle and confirm that the auto-pagination triggers reliably every 15 seconds.
