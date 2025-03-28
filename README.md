# Task Api

A simple RESTful API to handle tasks, built with Laravel

- **Controllers**: Handle HTTP requests, interact with services, and return responses.
- **Request Validators**: Validate incoming data to ensure it meets the required rules.
- **Services**: Contain business logic and interact with models.

## Requirements

- PHP >= 8.3
- Composer
- Laravel >= 11.x
- MySQL or another supported databas

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/pe-r/taskapi.git
   ```

2. Navigate to the project directory:

   ```bash
   cd task-api
   ```

3. Install the dependencies:

   ```bash
   composer install
   ```

4. Copy the example environment file:

   ```bash
   cp .env.example .env
   ```

5. Generate the application key:

   ```bash
   php artisan key:generate
   ```

6. Rename the `.env.example` file to `.env` and configure your database settings.

## Running Migrations

Before testing the API, run the migrations to set up the database:

```bash
php artisan migrate
```

## Running Tests

To run the tests, use the following command:

```bash
vendor/bin/phpunit
```

## API Endpoints

### Tasks

All routes require authentication
- **GET** `/api/tasks` - Retrieve all tasks
- **POST** `/api/tasks` - Create a new task
- **GET** `/api/tasks/{id}` - Retrieve a specific task (only user that created the task or admin)
- **PUT** `/api/tasks/{id}` - Update a specific task (only user that created the task or admin)
- **DELETE** `/api/tasks/{id}` - Delete a specific task (only user that created the task or admin)

- **GET** `/api/tasks.user/{id}` - Retrieve all tasks for one user
- **GET** `/api/tasks.project/{id}` - Retrieve all tasks that belong to one project
- **GET** `/api/projects` - Retrieve a list of all projects
- **GET** `/api/tasks.overdue` - Retrieve all tasks where deadline is overdue

## API Documentation

### Task CRUD

#### Get Tasks
- **Endpoint**: `GET /api/tasks`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "data": [
        {
        "id": 1,
        "title": "Task 1",
        "description": "Descreption of task 1",
        "status": "todo",
        "user_id": 1,
        "created_at": "2025-03-01T00:00:00.000000Z",
        "updated_at": "2025-03-01T00:00:00.000000Z",
        "deadline": "2025-04-19",
        "project_id": 1
        },
        ...
    ]
  }
  ```

#### Show Task
- **Endpoint**: `GET /api/tasks/{taskId}`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Parameters**:
  - `taskId`: ID of the task to be returned.
- **Response**:
  - **200 OK**
  ```json
  {
    "data": {
    "id": 1,
    "title": "Task 1",
    "description": "Descreption of task 1",
    "status": "todo",
    "user_id": 1,
    "created_at": "2025-03-01T00:00:00.000000Z",
    "updated_at": "2025-03-01T00:00:00.000000Z",
    "deadline": "2025-04-19",
    "project_id": 1
    }
  }
  ```

#### Create Task
- **Endpoint**: `POST /api/tasks`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Request Body**:
  ```json
  {
    "title": "New Task",
    "description": "Description of the new task",
    "status": "todo",
    "deadline": "2025-04-15",
    "project": "Project name for new task"
  }
  ```
- **Response**:
  - **201 Created**
  ```json
  {
    "message": "Task added."
  }
  ```

#### Update Task
- **Endpoint**: `PUT /api/tasks/{taskId}`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Request Body**:
  ```json
  {
    "title": "Updated Task",
    "description": "Updated description of the task",
    "status": "in_progress",
    "deadline": "2025-05-15",
    "project": "Updated task project name"
  }
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "message": "Task updated."
  }
  ```

#### Delete Task
- **Endpoint**: `DELETE /api/tasks/{taskId}`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "message": "Task deleted."
  }
  ```

#### Get User Tasks
- **Endpoint**: `GET /api/tasks.user/{userId}`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "data": [
        {
        "id": 1,
        "title": "Task 1",
        "description": "Descreption of task 1",
        "status": "todo",
        "user_id": 1,
        "created_at": "2025-03-01T00:00:00.000000Z",
        "updated_at": "2025-03-01T00:00:00.000000Z",
        "deadline": "2025-04-19",
        "project_id": 1
        },
        ...
    ]
  }
  ```

#### Get Project Tasks
- **Endpoint**: `GET /api/tasks.project/{projectId}`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "data": [
        {
        "id": 1,
        "title": "Task 1",
        "description": "Descreption of task 1",
        "status": "todo",
        "user_id": 1,
        "created_at": "2025-03-01T00:00:00.000000Z",
        "updated_at": "2025-03-01T00:00:00.000000Z",
        "deadline": "2025-04-19",
        "project_id": 1
        },
        ...
    ]
  }
  ```

#### Get Projects
- **Endpoint**: `GET /api/projects`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "data": [
        {
        "id": 1,
        "name": "First Project",
        "created_at": "2025-03-01T00:00:00.000000Z",
        "updated_at": "2025-03-01T00:00:00.000000Z",
        },
        ...
    ]
  }
  ```
