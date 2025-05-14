# Laravel Real-time Chat Application

A real-time chat application built with Laravel, Livewire, Laravel Volt, and Laravel Reverb.

## Features

- User-to-user private messaging
- Group chats with invitation links
- Real-time message updates using Laravel Reverb
- Typing indicators
- Read receipts
- User presence in group chats

## Technologies Used

- Laravel
- Laravel Reverb for real-time features
- Livewire for reactive UI components
- Laravel Volt for authentication
- Alpine.js for UI interactions
- Tailwind CSS for styling

## Setup Instructions

### Prerequisites

- PHP 8.1+
- Composer
- Node.js and NPM
- MySQL or PostgreSQL

### Installation

1. Clone the repository
2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Install JavaScript dependencies:

   ```bash
   npm install
   ```

4. Copy the environment file:

   ```bash
   cp .env.example .env
   ```

5. Generate application key:

   ```bash
   php artisan key:generate
   ```

6. Configure your database in the `.env` file:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=larachat
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Configure Laravel Reverb in the `.env` file:

   ```
   REVERB_APP_ID=my-app-id
   REVERB_APP_KEY=my-app-key
   REVERB_APP_SECRET=my-app-secret
   REVERB_HOST=localhost
   ```

8. Run database migrations:

   ```bash
   php artisan migrate
   ```

9. Build frontend assets:

   ```bash
   npm run build
   ```

10. Start the Laravel Reverb server:

    ```bash
    php artisan reverb:start
    ```

11. Start the Laravel development server:

    ```bash
    php artisan serve
    ```

## Application Structure

### Database Models

- `User` - Represents a user in the system
- `Message` - Represents a message sent between users or in groups
- `Group` - Represents a chat group
- `GroupUser` - Pivot table for managing group memberships

### Event Broadcasting

- `MessageSent` - Broadcasts when a private message is sent
- `GroupMessageSent` - Broadcasts when a message is sent to a group
- `UserTyping` - Broadcasts when a user is typing
- `UserJoinedGroup` - Broadcasts when a user joins a group

### Livewire Components

- `ContactsList` - Displays a list of user's contacts
- `PrivateChat` - Handles private messaging between users
- `GroupsList` - Displays a list of user's groups
- `GroupChat` - Handles group messaging
- `CreateGroup` - Form for creating a new group
- `GroupInvitation` - Handles group invitations via unique codes

### Broadcasting Channels

- `chat.{senderId}.{receiverId}` - Private channel for user-to-user messaging
- `group.{groupId}` - Presence channel for group messaging
- `typing.{receiverId}` - Private channel for typing indicators

## Usage

1. Register a new account or log in
2. Navigate to the chat page
3. To chat with another user, select them from the contacts list
4. To create a new group, click the "+" button in the groups tab
5. To invite someone to a group, copy the invitation link from the group options menu
6. To join a group, use the invitation link or enter the invitation code manually

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
