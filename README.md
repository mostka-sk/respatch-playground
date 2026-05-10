# Respatch Live Demo Server

This is the repository for the "Live Demo Server" of the Respatch application (Symfony Messenger Monitor). The server serves as a demo environment for real-time message monitoring.

## Installation and Startup

1. **Clone the repository and install dependencies:**
   ```bash
   git clone <repository_url>
   cd respatch-playground
   composer install
   ```

2. **Environment configuration:**
   Create a `.env.local` file from the `.env` template and set the necessary variables. The key ones are the database connection and the Messenger transport:
   ```dotenv
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/respatch_demo?serverVersion=8.0.32&charset=utf8mb4"
   MESSENGER_TRANSPORT_DSN="doctrine://default"
   ```

3. **Initialize database and demo data:**
   The project includes a ready-made script that completely drops and recreates the database schema and then populates it with demo messages (Fixtures).
   ```bash
   chmod +x bin/reset-db.sh
   ./bin/reset-db.sh
   ```

4. **Start the application:**
   You can start the project via the Symfony CLI, for example:
   ```bash
   symfony server:start -d
   ```

---

## Cron Jobs Configuration

The demo server requires automated processes to maintain demo data and periodically reset the database (every 30 minutes). These processes need to be set up using the system tool `cron`.

Open the crontab editor using the command:
```bash
crontab -e
```

Add the following lines (do not forget to replace `/path/to/project` with the actual absolute path to the project directory on your server, e.g., `/var/www/respatch-server-alpha`):

```cron
# 1. Periodic database reset (every 30 minutes)
# This script drops the schema, recreates it, and populates the database with demo messages
*/30 * * * * /path/to/project/bin/reset-db.sh >> /path/to/project/var/log/cron_reset.log 2>&1

# 2. Live messages generation (every minute)
# This command dispatches new random messages to the messenger so that real-time activity can be seen
* * * * * /usr/bin/php /path/to/project/bin/console app:demo:dispatch-messages >> /path/to/project/var/log/cron_dispatch.log 2>&1
```

---

## Supervisor Configuration for Messenger

A Symfony Messenger worker is used for continuous background processing of asynchronous messages. It is recommended to manage it using the **Supervisor** tool, which ensures that the worker runs continuously and automatically restarts in case of a crash.

1. **Install Supervisor (if it's not already installed on the server):**
   ```bash
   sudo apt-get install supervisor
   ```

2. **Create a configuration file for the worker:**
   Create a new configuration file, e.g., `/etc/supervisor/conf.d/respatch-messenger.conf`, with the following content:

   ```ini
   [program:messenger-consume]
   # Replace /path/to/project with the actual path to your project.
   command=/usr/bin/php /path/to/project/bin/console messenger:consume async failed --time-limit=3600 --memory-limit=128M
   
   # User under which the process will run (most often www-data on production servers, or a deployer user)
   user=www-data
   
   # Number of parallel workers
   numprocs=2
   
   startsecs=0
   autostart=true
   autorestart=true
   process_name=%(program_name)s_%(process_num)02d
   
   # Paths to log files (make sure the worker has write permissions to them)
   stdout_logfile=/path/to/project/var/log/messenger_worker.log
   stderr_logfile=/path/to/project/var/log/messenger_worker_err.log
   ```

3. **Activate the new configuration in Supervisor:**
   After creating the file, tell Supervisor to read the new configuration and start the processes:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start messenger-consume:*
   ```