# SunUltra5G Project Architecture & Coding Standards

This document outlines the strict architectural and coding standards for the **SunUltra5G** project. All developers must follow these rules to ensure scalability, security, and maintainability.

---

## 🏗️ Core Architecture Rules

1.  **DB Transactions**: Always use DB transactions for all write operations (Create, Update, Delete).
2.  **Service Layer**: Business logic must reside in the Service Layer. Controllers must remain thin (Request/Response handling only).
3.  **Repository Pattern**: All database interactions must go through Repositories. No direct Model calls in Controllers or Services.
4.  **No Raw Queries**: Never write raw DB queries inside controllers.
5.  **FormRequests**: Use FormRequest classes for all validations. No inline validation in controllers.
6.  **Error Handling**: Use `try-catch` blocks with structured logging for all critical operations.
7.  **Queue System**: Use queues for heavy operations (Emails, Bulk Inserts, PDF Generation, etc.).

---

## 📂 Code Structure

Follow this folder structure strictly within the `app/` directory:

```text
app/
├── Http/Controllers/   # Thin controllers
├── Http/Requests/      # FormRequest validation classes
├── Services/           # Business logic layer
├── Repositories/       # Data access layer
├── Models/             # Eloquent models
├── DTOs/               # Data Transfer Objects
├── Traits/             # Reusable traits
├── Helpers/            # Global helper functions
```

---

## 🗄️ Database Rules

1.  **Eloquent ORM**: Always prefer Eloquent over raw queries.
2.  **N+1 Prevention**: Prevent N+1 queries using eager loading (`with()`).
3.  **Indexing**: Use indexing on frequently searched/filtered columns.
4.  **Explicit Selection**: Never use `SELECT *`. Always specify required columns.
5.  **Pagination**: Always use pagination for large datasets.

---

## 🔐 Security Rules

1.  **SQL Injection**: Protect against injection by using Eloquent or prepared statements.
2.  **CSRF Protection**: Ensure CSRF protection is active for all forms and AJAX requests.
3.  **Sanitization**: Validate and sanitize all user inputs.
4.  **Data Exposure**: Never expose sensitive data (passwords, tokens, keys) in responses.

---

## 📡 API Response Format

All API/AJAX responses must follow this standardized JSON format:

```json
{
    "status": true,
    "message": "Success message here",
    "data": {
        "key": "value"
    }
}
```

---

## 🖱️ AJAX (jQuery) Rules

1.  **Error Handling**: Implement proper error handling for all AJAX calls.
2.  **Debounce**: Use debouncing for real-time search or frequent inputs.
3.  **Loader States**: Always handle and display loader states during requests.
4.  **CSRF Headers**: Send CSRF tokens in the request headers for all state-changing operations.

---

## 📝 Logging Rules

Use structured logging to facilitate debugging:

*   **Info**: `Log::info('Action Name', ['key' => $value]);`
*   **Error**: `Log::error('Error message', ['exception' => $e]);`

---

## 🛠️ Coding Standards

1.  **Naming**: Use meaningful, camelCase function names (e.g., `createUser`, `updateOrderStatus`).
2.  **Modularity**: Keep functions small and focused on a single responsibility.
3.  **Reuse**: Reuse logic via Traits or Services to stay DRY.
4.  **PSR**: Follow PSR standards for PHP coding style.

---

## 🚀 Execution Workflow

Whenever creating a new feature, generate:
1.  **Controller**
2.  **Service**
3.  **Repository**
4.  **FormRequest** (if validation is needed)

**Priority Order:**
1. Transaction Safety
2. Data Integrity
3. Clean Architecture
