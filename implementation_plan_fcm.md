# Implement Firebase Push Notifications

This plan outlines the steps required to implement Firebase Cloud Messaging (FCM) in all three application states (foreground, background, and terminated/quit) for the React Native application.

## User Review Required

> [!WARNING]
> Before we can proceed with execution, you must set up the Firebase project in the Firebase Console and obtain the necessary configuration files.

Please provide the following:
1. **Android Configuration:** Download the `google-services.json` file from your Firebase project settings and place it in the `android/app` directory.
2. **iOS Configuration:** Download the `GoogleService-Info.plist` file from your Firebase project settings and place it in the `ios` directory (it must be added to the Xcode project properly).
3. **APNs Authentication:** For iOS push notifications to work via Firebase, you will need to upload your Apple Push Notification service (APNs) Authentication Key (.p8 file) to your Firebase project settings under Cloud Messaging.

## Open Questions

> [!IMPORTANT]
> 1. Do you already have a Firebase project created for this app?
> 2. Where should the FCM token be sent in the backend once generated? (e.g., a specific API endpoint to register the user's device).
> 3. Should we configure a specific default icon or color for the Android notifications?

## Proposed Changes

We will use `@react-native-firebase/app`, `@react-native-firebase/messaging` for FCM integration, and `@notifee/react-native` for displaying heads-up notifications while the app is in the foreground (since FCM does not automatically show notifications when the app is active).

---

### Dependencies

We will install the required React Native Firebase and Notifee packages.

#### [MODIFY] [package.json](file:///d:/workoriomobileapp/package.json)
- Add `@react-native-firebase/app`
- Add `@react-native-firebase/messaging`
- Add `@notifee/react-native`

---

### Android Native Setup

Configure the Android project to use Firebase Services.

#### [MODIFY] [android/build.gradle](file:///d:/workoriomobileapp/android/build.gradle)
- Add the `google-services` classpath to dependencies.

#### [MODIFY] [android/app/build.gradle](file:///d:/workoriomobileapp/android/app/build.gradle)
- Apply the `com.google.gms.google-services` plugin.

---

### iOS Native Setup

Configure the iOS project for Firebase and Push Notifications.

#### [MODIFY] [ios/Podfile](file:///d:/workoriomobileapp/ios/Podfile)
- Run `pod install` to install Firebase iOS SDKs.

#### [MODIFY] [ios/workorio/AppDelegate.mm](file:///d:/workoriomobileapp/ios/workorio/AppDelegate.mm) (Assuming 'workorio' is the iOS project name)
- Import Firebase header `#import <Firebase.h>`.
- Initialize Firebase in `didFinishLaunchingWithOptions`: `[FIRApp configure];`.

*(Note: We will also need to enable "Push Notifications" and "Background Modes -> Remote notifications" in Xcode Capabilities)*

---

### Application Logic

Implement the logic to request permissions, get the device token, and handle messages in all three states.

#### [MODIFY] [index.js](file:///d:/workoriomobileapp/index.js)
- Register the background/quit state message handler using `messaging().setBackgroundMessageHandler(...)` outside the React component lifecycle. This ensures notifications are processed when the app is closed or running in the background.

#### [NEW] [src/hooks/usePushNotifications.js](file:///d:/workoriomobileapp/src/hooks/usePushNotifications.js)
- Create a custom hook to encapsulate push notification logic.
- Request user permissions (required for iOS and Android 13+).
- Retrieve the FCM token.
- Handle foreground messages using `messaging().onMessage(...)`.
- Use Notifee to trigger a local heads-up notification when a message is received in the foreground.
- Handle interaction events (when a user taps a notification) using `messaging().onNotificationOpenedApp` and `messaging().getInitialNotification()`.

#### [MODIFY] [App.js](file:///d:/workoriomobileapp/App.js)
- Integrate the `usePushNotifications` hook at the root level of the app so it initializes when the app starts.

## Verification Plan

### Automated Tests
- Ensure the project builds successfully for both Android and iOS after installing dependencies and native modifications.

### Manual Verification
1. Open the app and verify that the notification permission prompt appears (on iOS or Android 13+).
2. Log the generated FCM token to the console.
3. Use the Firebase Console "Cloud Messaging" test tool or an API client (like Postman) to send test notifications to the logged FCM token.
4. **Foreground Test:** With the app open on the screen, send a message. Verify a heads-up notification appears via Notifee.
5. **Background Test:** Minimize the app. Send a message. Verify the system tray notification appears. Tap it and ensure it opens the app.
6. **Quit State Test:** Force close the app completely. Send a message. Verify the system tray notification appears. Tap it and ensure the app launches.
