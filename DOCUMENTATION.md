# NAPPS Conference Project Setup Documentation

## Overview
This documentation provides a comprehensive guide to setting up the NAPPS Conference project, which utilizes Laravel as the backend and Vue.js as the frontend. Follow the steps below to get your development environment up and running.

## Prerequisites
Ensure that you have the following installed on your machine:
- **PHP** (version 8.1 or higher)
- **Composer** (for PHP dependency management)
- **Node.js** (version 18 or higher)
- **NPM** (Node Package Manager)
- **Git** (for version control)

## Project Structure
Your project should have the following structure:
```
napps-conference/
├── app/
├── bootstrap/
├── config/
├── database/
├── lang/
├── public/
├── resources/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
├── vendor/
├── .env
├── .env.example
├── deploy.yml
├── package.json
└── composer.json
```

## Setting Up the Backend (Laravel)
1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd napps-conference
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Set Up Environment Variables**
   - Copy the example environment file:
     ```bash
     cp .env.example .env
     ```
   - Update the `.env` file with your database and application configurations.

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Serve the Laravel Application**
   ```bash
   php artisan serve
   ```

## Setting Up the Frontend (Vue.js)
1. **Navigate to the Frontend Directory**
   If your Vue.js files are in `resources/js`, navigate there:
   ```bash
   cd resources/js
   ```

2. **Install Node Dependencies**
   ```bash
   npm install
   ```

3. **Run the Vue.js Application**
   ```bash
   npm run dev
   ```

## Deployment
1. **Configure Deployment Settings**
   - Ensure your [deploy.yml](cci:7://file:///c:/Users/HP/Desktop/napps-conference/.github/workflows/deploy.yml:0:0-0:0) file is configured with the correct environment variables and commands to start both the Laravel and Vue.js applications.

2. **Deploy to Digital Ocean**
   - Push your changes to the main branch to trigger the deployment process.

## Additional Notes
- **Supervisor**: Consider using Supervisor to manage your Laravel application in production.
- **Nginx**: Configure Nginx to serve your application and handle requests appropriately.
- **Environment Variables**: Make sure to secure sensitive information in your `.env` file.

## Troubleshooting
- If you encounter issues during deployment, check the logs in Digital Ocean for any errors.
- Ensure that all dependencies are correctly installed and that the environment variables are set.

## Conclusion
This documentation should help you set up and deploy your Laravel and Vue.js project effectively. If you have any further questions or need clarification on any steps, feel free to ask!
