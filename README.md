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

1. **Clone the repository:**
    ```bash
    git clone https://github.com/gidambayiantony/TaskEase.git
    cd TaskEase
    ```

2. **Install dependencies:**
    ```bash
    composer install
    ```

3. **Configure the database:**
    - Create a `.env` file and add your database configuration.
    - Run the database migrations:
      ```bash
      php migrate.php
      ```

4. **Configure OAuth providers:**
    - Create an `oauth_config.php` file with your OAuth provider credentials.
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

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments
- Thanks to the [League OAuth2 Client](https://github.com/thephpleague/oauth2-client) for the OAuth integration.
- Chart.js for the analytics charts.
