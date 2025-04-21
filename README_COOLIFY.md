# Deploying Prime Instinct to Coolify

This guide provides step-by-step instructions for deploying the Prime Instinct e-commerce application to Coolify.

## Prerequisites

- A Coolify instance up and running
- Access to a MySQL or MariaDB database
- Access to your DNS settings if you want to use a custom domain

## Deployment Steps

### 1. Prepare Your Database

Before deploying to Coolify, you need to ensure your database schema is properly exported:

1. Export your database schema from your development environment:
   ```bash
   mysqldump -u root -p prime > database/schema.sql
   ```
2. Make sure your schema.sql file is in the `database` directory of this project.

### 2. Set Up Project in Coolify

1. Log in to your Coolify dashboard
2. Click on "New Resource" and select "Application"
3. Choose "Docker Build" as the deployment method
4. Connect your Git repository or use the "Upload Files" option
5. If using Git, select the appropriate branch (e.g., main or master)

### 3. Configure Environment Variables

In the Coolify dashboard, add the following environment variables:

```
DB_HOST=your_mysql_host
DB_USER=your_mysql_user
DB_PASSWORD=your_mysql_password
DB_NAME=your_database_name
DEBUG=false
TIMEZONE=America/Bogota
```

Replace the placeholder values with your actual database credentials.

### 4. Configure Persistent Storage

For proper functioning of the application, add the following persistent volumes:

- `/var/www/html/uploads` - For product images and user uploads
- `/var/www/html/logs` - For application logs

### 5. Deploy the Application

1. Click on "Deploy" to start the deployment process
2. Monitor the deployment logs for any errors
3. Once deployed, you should be able to access your application at the URL provided by Coolify

### 6. Post-Deployment Steps

1. Access the application to verify it's working correctly
2. Log in with the admin credentials:
   - Email: admin@primeinstinct.com
   - Password: admin123
3. Update admin password immediately

## Troubleshooting

If you encounter issues:

1. Check the application logs in Coolify
2. Verify that the database connection is working
3. Check file permissions for uploads and logs directories
4. Try the diagnostic page: `/diagnostico.php` (password: prime2025)

## Maintenance

- **Backups**: Regularly backup your database and uploaded files
- **Updates**: Pull latest changes from your repository and redeploy
- **Monitoring**: Use Coolify's built-in monitoring to keep track of your application's performance

## Support

If you need assistance, please contact the development team.

---

Happy selling with Prime Instinct!
