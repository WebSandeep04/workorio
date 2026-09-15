# Integrate AI Task Detection for WhatsApp Messages

This plan outlines the steps to integrate the AI-powered task detection logic (currently found in your Node.js `signal` codebase) natively into this Laravel application. The goal is to automatically read raw WhatsApp messages from the `signal_whatsapp_msg` table, use an LLM (like OpenAI's GPT models) to determine if a message contains an actionable task, and automatically create or update tasks in the system.

## Proposed Architecture

1. **Scheduled Artisan Command:**
   A scheduled job (e.g., `php artisan signal:process-ai-tasks`) that runs periodically to fetch new, unprocessed messages from the `signal_whatsapp_msg` table.

2. **AI Task Detector Service (`App\Services\AiTaskDetectorService`):**
   This service will replicate the exact core logic of `aiTaskDetector.js`. It will:
   - Use the exact `SYSTEM_PROMPT` strings and logic from the Node.js project.
   - Use the exact same OpenAI API `/v1/chat/completions` endpoint and model settings (e.g. `gpt-4o-mini`).
   - Gather recent unprocessed messages (`NEW_MESSAGES`), context messages (`CONTEXT_MESSAGES`), and currently tracked AI tasks (`OPEN_TASKS`).

3. **Intermediate AI Tasks Table (`signal_ai_tasks`):**
   Instead of injecting directly into the main `tasks` table, AI-detected tasks will be stored in a separate table.
   - When the AI decides `new_task`, a record is created here with the `summary` (Title) and original message `text` (Description).
   - This keeps the AI guesses separate from the production task workflow.

4. **Frontend Integration (`signal-task.blade.php`):**
   - The Signal Tasks table will display a new "Create Task" / "Assign" button next to AI-detected tasks.
   - Clicking this button will open the complete task creation modal (already used in the main Tasks blade).
   - The modal HTML for `#createTaskModal` will be copied from `task.blade.php` into `signal-task.blade.php` so it functions exactly the same way without a page reload.
   - The user can then manually select the Assignee, Customer, etc., and submit to save it to the main `tasks` table.

5. **Database Migrations Required:**
   - Create a `signal_ai_tasks` table with columns: `id`, `message_id` (fk), `title`, `description`, `ai_assigned_to`, `ai_requested_by`, `status` (e.g. pending, converted), timestamps.
   - Modify the `signal_whatsapp_msg` table to add a boolean column `is_ai_processed` (default: `false`).

## Implementation Steps

### Step 1: Database Migration
- Create a migration to add `is_ai_processed` (boolean, default false) to `signal_whatsapp_msg`.

### Step 2: Create the Service Class
- Create `App\Services\AiTaskDetectorService.php`.
- Port the `SYSTEM_PROMPT` and `buildPrompt` logic from Node.js to PHP.
- Implement the OpenAI API call using Laravel's `Http::withToken()->post(...)` targeting `https://api.openai.com/v1/chat/completions`.

### Step 3: Action Handlers
- Implement the logic to parse the AI's JSON response and map the `decision` field to actual Eloquent Model queries (e.g., `Task::create()`, `TaskRemark::create()`).

### Step 4: Console Command & Scheduling
- Create the Artisan command `App\Console\Commands\ProcessSignalTasksCommand`.
- Register the command in `app/Console/Kernel.php` to run `everyFiveMinutes()`.

## Verification Plan

### Automated/Manual Testing
- Create mock WhatsApp messages in the `signal_whatsapp_msg` table.
- Run `php artisan signal:process-ai-tasks` manually from the terminal.
- Verify that a valid task is successfully created in the `tasks` table with the correct assignment.
- Verify that subsequent follow-up messages are correctly mapped as remarks on the existing task rather than creating duplicate tasks.
