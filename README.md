# TaskEase

TaskEase is a task management application that allows users to create, edit, and track their tasks. The application supports both traditional login and OAuth-based login through Google, GitHub, and LinkedIn. Additionally, it provides an analytics dashboard to track task completion and overdue tasks.

## Features

- User authentication (traditional and OAuth)
- Task creation, editing, and deletion
- Analytics dashboard for task tracking
- Light and dark mode
- Background image customization
- Drag-and-drop functionality for reordering tasks

## Installation

### Prerequisites

- PHP 7.4 or higher
- Composer
- MySQL

### Steps

1. **Clone the repository:**

    ```bash
    git clone https://github.com/gidambayiantony/TaskEase.git
    cd TaskEase
    ```

2. **Install dependencies:**

    ```bash
    composer install
    ```

3. **Set up the database:**

    - Create a MySQL database.
    - Import the `database.sql` file to set up the necessary tables.

4. **Configure the application:**

    - Rename `.env.example` to `.env` and fill in your database and OAuth credentials.
    - Configure `db.php` to connect to your MySQL database.

5. **Start the application:**

    - Ensure your PHP server is running and navigate to the project directory.
    - Open your web browser and go to `http://localhost/TaskEase`.

## OAuth Configuration

To enable OAuth login, you need to set up OAuth applications on Google, GitHub, and LinkedIn. After setting up, you will receive client IDs and client secrets which you need to add to your `oauth_config.php` file.

```php
return [
    'google' => [
        'clientId'     => 'your-google-client-id',
        'clientSecret' => 'your-google-client-secret',
        'redirectUri'  => 'http://localhost/TaskEase/login.php?provider=google',
    ],
    'github' => [
        'clientId'     => 'your-github-client-id',
        'clientSecret' => 'your-github-client-secret',
        'redirectUri'  => 'http://localhost/TaskEase/login.php?provider=github',
    ],
    'linkedin' => [
        'clientId'     => 'your-linkedin-client-id',
        'clientSecret' => 'your-linkedin-client-secret',
        'redirectUri'  => 'http://localhost/TaskEase/login.php?provider=linkedin',
    ],
];

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

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments
- Thanks to the [League OAuth2 Client](https://github.com/thephpleague/oauth2-client) for the OAuth integration.
- Chart.js for the analytics charts.
