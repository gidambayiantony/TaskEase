# TaskEase

TaskEase is a comprehensive task management application designed to help users stay organized and manage their tasks effectively. The application features traditional login, OAuth integration, and an analytics dashboard to provide insights into task management.

## Features

### User Authentication
- Traditional login with username and password.
- OAuth integration with Google, GitHub, and LinkedIn.

### Task Management
- Create, edit, and delete tasks.
- Set priorities, due dates, and descriptions for tasks.
- Organize tasks into categories.
- Add subtasks to main tasks.

### Analytics Dashboard
- View completed tasks, overdue tasks, and total tasks.
- Task completion rate visualization.

### Customization
- Switch between light and dark modes.
- Upload custom background images.

## Installation

### Prerequisites
- PHP 7.4 or higher
- Composer
- MySQL
- PHP Extensions: `pdo_mysql`, `mbstring`

1. **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/TaskEase.git
    cd TaskEase
    ```

2. **Install dependencies:**
    ```bash
    composer install
    ```

3. **Configure the database:**
    - Create a `.env` file with the following content:
      ```env
      DB_HOST=127.0.0.1
      DB_NAME=todolist
      DB_USER=root
      DB_PASS=
      ```
    - Run the database migrations:
      ```bash
      php migrate.php
      ```

4. **Configure OAuth providers:**
    - Create an `oauth_config.php` file with your OAuth provider credentials. Refer to [Google OAuth Documentation](https://developers.google.com/identity/protocols/oauth2) for setup.
    ```php
    return [
        'google' => [
            'clientId'     => 'your-google-client-id',
            'clientSecret' => 'your-google-client-secret',
            'redirectUri'  => 'your-google-redirect-url',
        ],
        'github' => [
            'clientId'     => 'your-github-client-id',
            'clientSecret' => 'your-github-client-secret',
            'redirectUri'  => 'your-github-redirect-url',
        ],
        'linkedin' => [
            'clientId'     => 'your-linkedin-client-id',
            'clientSecret' => 'your-linkedin-client-secret',
            'redirectUri'  => 'your-linkedin-redirect-url',
        ],
    ];
    ```

5. **Run the application:**
    ```bash
    php -S localhost:8000
    ```

## Usage

### Login
Users can log in using either traditional username and password or via OAuth providers (Google, GitHub, LinkedIn).

### Task Management
- **Create Task:** Fill in the task details and click "Add Task."
- **Edit Task:** Click on the edit button next to the task and update the details.
- **Delete Task:** Click on the delete button next to the task.

### Analytics Dashboard
The analytics dashboard provides an overview of completed tasks, overdue tasks, and the task completion rate.

### Customization
Users can switch between light and dark modes and upload custom background images.

### Scripts

#### JavaScript (`script.js`)
This file contains the client-side logic for:
- Toggle between light and dark modes
- Handling task editing via modals
- Theme selection and background image upload
- Drag-and-drop functionality for reordering tasks

## Testing

### Adding a New Task

1. **Open the TaskEase Application:**
   - Navigate to `http://localhost:8000/todo_list` in your web browser.

2. **Access the Add Task Modal:**
   - Click the "Add Task" button, typically located at the bottom of the task list or in a prominent position.

3. **Fill in the Task Details:**
   - **Todo:** Enter a name or title for your task.
   - **Priority:** Select the priority level (Low, Medium, High).
   - **Due Date:** Choose a due date from the date picker.
   - **Description:** Provide a brief description of the task.
   - **Category:** Assign a category (e.g., Work, Personal).
   - **Subtasks:** (Optional) Add any subtasks if needed.
   - **Tags:** (Optional) Add tags to categorize the task further.
   - **Attachment:** (Optional) Upload a file if necessary.

4. **Submit the Form:**
   - Click the "Add Task" button within the modal to submit the new task.

5. **Verify the Task is Added:**
   - Ensure that the task appears in the task list with the correct details after submission.

6. **Check for Errors:**
   - Look for any error messages or issues. If the task does not appear, verify the following:
     - Check the `tasks` table in your MySQL database for the new entry.
     - Ensure the form data is correctly processed.
     - Review PHP and server error logs for any issues.

### Additional Testing

- **Test Task Editing:**
  - Edit existing tasks to ensure that changes are saved and reflected correctly.

- **Test Task Deletion:**
  - Delete tasks and confirm that they are removed from the task list and database.

- **Test OAuth Login:**
  - Verify that OAuth logins (Google, GitHub, LinkedIn) work as expected.


## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing
- If you’d like to contribute, please open an issue or submit a pull request.

## Acknowledgments
- Thanks to the [League OAuth2 Client](https://github.com/thephpleague/oauth2-client) for the OAuth integration.
- Chart.js for the analytics charts.
