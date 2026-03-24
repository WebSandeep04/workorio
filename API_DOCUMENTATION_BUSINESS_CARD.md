# Business Card Scanner & Management API Documentation

This document outlines the API endpoints available for the Business Card Scanner feature in the mobile application.

## 1. Overview

The feature allows users to:
1.  **Scan** a business card using OCR and AI (Gemini) to extract details.
2.  **Save** the extracsted details to the database.
3.  **View, Edit, and Delete** saved business cards.
4.  **Convert** a business card into a Lead/Contact (handled via `is_converted` flag).

---

## 2. Authentication

All endpoints (except where noted) require a valid **Bearer Token** in the header.

**Headers:**
*   `Authorization`: `Bearer <your_access_token>`
*   `Accept`: `application/json`
*   `Content-Type`: `application/json`

---

## 3. AI Parsing Endpoint

Use this endpoint to parse raw text extracted from a business card image ON THE DEVICE.

### **Parse Business Card**
*   **Endpoint:** `POST /api/gemini/parse-card`
*   **Description:** Sends raw OCR text to Gemini AI and returns structured JSON.

**Request Body:**
```json
{
  "text": "John Doe\nSoftware Engineer\nTech Corp\njohn@techcorp.com\n+1-555-0123"
}
```

**One Success Response (200 OK):**
```json
{
    "name": "John Doe",
    "designation": "Software Engineer",
    "company_name": "Tech Corp",
    "email": "john@techcorp.com",
    "phone_primary": "+1-555-0123",
    "phone_secondary": null,
    "website": null,
    "address": null,
    "city": null,
    "state": null,
    "country": null,
    "pincode": null,
    "social_links": {
        "linkedin": null,
        "twitter": null,
        "facebook": null,
        "instagram": null,
        "other": []
    }
}
```

---

## 4. Business Card Management (CRUD)

### **A. List All Cards**
*   **Endpoint:** `GET /api/business-cards`
*   **Description:** Fetches a paginated list of scanned cards, ordered by newest first.

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "company_name": "Tech Corp",
                "email": "john@techcorp.com",
                "created_at": "2026-01-18T10:00:00.000000Z",
                "is_converted": false
                // ... other fields
            }
        ],
        "total": 50,
        "per_page": 20
    }
}
```

### **B. Save New Card**
*   **Endpoint:** `POST /api/business-cards`
*   **Description:** Saves a scanned card to the database. You should populate this request with the data returned from the `/gemini/parse-card` endpoint.

**Request Body:**
```json
{
    "name": "John Doe",
    "designation": "Manager",
    "company_name": "Tech Corp",
    "email": "john@techcorp.com",
    "phone_primary": "+1 555 1234",
    "phone_secondary": null,
    "website": "www.techcorp.com",
    "address": "123 Tech Park",
    "city": "New York",
    "state": "NY",
    "country": "USA",
    "pincode": "10001",
    "social_links": {
        "linkedin": "https://linkedin.com/in/johndoe"
    },
    "raw_text": "Original OCR text...",
    "raw_ai_response": { ... } // Optional: Store full AI response if needed
}
```

**Response:**
```json
{
    "success": true,
    "message": "Business card saved successfully.",
    "data": {
        "id": 15,
        "name": "John Doe",
        // ...
    }
}
```

### **C. Get Single Card**
*   **Endpoint:** `GET /api/business-cards/{id}`
*   **Description:** Fetch full details of a specific card.

### **D. Update Card**
*   **Endpoint:** `PUT /api/business-cards/{id}`
*   **Description:** Update fields of a specific card.

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "email": "john.new@techcorp.com"
}
```

### **E. Delete Card**
*   **Endpoint:** `DELETE /api/business-cards/{id}`
*   **Description:** Soft deletes the card.

**Response:**
```json
{
    "success": true,
    "message": "Business card deleted successfully."
}
```
