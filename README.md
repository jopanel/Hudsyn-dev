
<img width="861" alt="Hudsyn Dashboard" src="https://github.com/user-attachments/assets/d4e83177-320f-4040-8a66-fd62a2c58dad" />

# Hudsyn – A Free Headless CMS for Laravel

**Hudsyn** is a lightweight, headless content management system designed specifically for Laravel projects. Born from the need to quickly manage a beautiful, fast-loading landing page without the overhead of systems like WordPress or expensive alternatives such as Statamic, Hudsyn allows you to manage pages, blog posts, press releases, custom routes, layouts, settings, and file uploads—all from within your Laravel application.

## What is Hudsyn?

Hudsyn is a modular CMS that integrates directly into your Laravel project. It provides:

- **Admin Dashboard:**  
  A comprehensive admin interface to manage your content, including:
  - **Pages:** Create, edit, and publish pages that generate static HTML files for lightning-fast public display.
  - **Blog Posts:** Manage blog posts with rich text editing, author assignment, and static file generation.
  - **Press Releases:** Similar to blog posts, for press announcements.
  - **Social:** Post on X, Instagram, Facebook, and LinkedIn both instantly and scheduled. With tiny link creation to your blog or press release posts.
  - **Custom Routes:** Define custom URL mappings to any content type.
  - **Layout Management:** Configure header and footer layouts that can be applied to your pages.
  - **Global Settings:** Manage key–value pairs that can be injected into your content.
  - **File Upload & Gallery:** Upload files, view image thumbnails, and quickly insert images into your WYSIWYG editor.

- **Public-Facing Pages:**  
  Hudsyn generates static HTML files for public pages, ensuring your landing page loads quickly and efficiently.

- **WYSIWYG Editor Integration:**  
  The admin interface includes a rich text editor (CKEditor) with support for direct image uploads and gallery browsing.

## Why Use Hudsyn?

- **Seamless Integration:**  
  Designed to be installed into any Laravel project without interfering with your existing architecture.

- **Performance:**  
  Static file generation provides a high-performance public site while allowing dynamic content management.

- **Flexibility & Customization:**  
  Easily extend or modify any aspect of the CMS to meet your specific needs.

- **Cost-Effective:**  
  A free, open-source solution that avoids the complexity of larger CMS systems.

## Installation

Hudsyn is packaged as a Composer package for easy integration into your existing Laravel project.

### Step 1: Require the Package

Run the following Composer command from your Laravel project root:

```bash
composer require jopanel/hudsyn
```

### Step 2: Publish the Package Assets

Publish Hudsyn’s assets (views, migrations, seeders, and public assets) to your Laravel project using Artisan:

```bash
php artisan vendor:publish --tag=hudsyn-config
php artisan vendor:publish --tag=hudsyn-views
php artisan vendor:publish --tag=hudsyn-migrations
php artisan vendor:publish --tag=hudsyn-seeders
php artisan vendor:publish --tag=hudsyn-public
```

> **Note:** The public assets include folders for `static/blog`, `static/pages`, and `static/press`. Ensure these directories are created in your `public/vendor/hudsyn` folder after publishing.

### Step 3: Run the Migrations

Create the necessary database tables by running:

```bash
php artisan migrate
```

### Step 4: (Optional) Seed the Database

If you wish to create an initial admin user or other sample data, run the seeder provided:

```bash
php artisan db:seed --class=AdminUserSeeder
```

### Step 5: Configure Middleware and Routes

Hudsyn comes with its own routes and a custom middleware that protects the admin interface. The package’s service provider automatically registers these. Ensure your authentication is set up and that your custom middleware alias (`hudsyn`) is recognized if you need to customize it.

## How Hudsyn Works

- **Admin Interface:**  
  Once installed, you can access the Hudsyn admin panel by navigating to `/hudsyn` in your browser. Here you can:
  - Log in and manage users, pages, blog posts, press releases, custom routes, layouts, and global settings.
  - Use the integrated WYSIWYG editor for rich content creation.
  - Upload files and view an image gallery with thumbnail previews.

- **Static File Generation:**  
  When you publish content (pages, blog posts, or press releases), Hudsyn automatically generates a static HTML file in the appropriate folder (e.g., `public/static/pages`). This ensures fast load times for public-facing content.

- **Public Routes:**  
  The package registers public routes that serve your landing page, blog posts (e.g., `/blog/{slug}`), press releases (e.g., `/press/{slug}`), or any custom route you define. The CMS checks for a corresponding static file and serves it if available.

## Customization

Hudsyn is designed to be modular and easily extendable:

- **Views:**  
  All admin and public views are published to your project’s `resources/views/vendor/hudsyn` folder, so you can modify them to match your design.

- **Migrations & Seeders:**  
  The database structure is published so you can customize fields if needed.

- **Service Provider:**  
  The package’s service provider (`Jopanel\Hudsyn\HudsynServiceProvider`) registers routes, views, migrations, and public assets. You can modify this if you require custom behavior.

  Below is an updated section for your README.md that outlines how to retrieve and configure the necessary API keys and credentials for social posting. This section explains which keys to set in your Hudsyn settings and where to obtain them (or at least what they represent) so that users know how to configure Hudsyn for posting to Instagram, X (Twitter), LinkedIn, and Facebook.

---

## Social Media API Configuration

To enable Hudsyn to post to social media platforms, you must configure API credentials in the global settings. These credentials are used by Hudsyn’s social posting feature, and the keys must be added to your settings table (either via the provided seeders or manually in the admin interface). Here’s what you need to configure:

### **1. Twitter (X)**
- **Setting Key:** `social_x_api_key`
- **Description:**  
  This should be your Twitter API Bearer token (for Twitter API v2) with the necessary posting scopes.  
- **How to Obtain:**  
  Create a Twitter Developer account, set up your app with read/write permissions, and generate a Bearer token.

### **2. Instagram**
- **Setting Key:** `social_instagram_api_key`
- **Description:**  
  A long‑lived Facebook Page access token that has been granted Instagram Publishing permissions (e.g. `instagram_content_publish`).  
- **How to Obtain:**  
  Configure your Facebook Developer account to connect your Instagram Business Account to a Facebook Page, and generate a long‑lived access token via Facebook’s Graph API.

- **Additional Setting:**  
  `social_instagram_ig_user_id` –  
  The Instagram Business Account (IG User) ID. This is required to target the correct account when publishing.

### **3. LinkedIn**
- **Setting Keys:**  
  - `social_linkedin_api_key`  
    (Your LinkedIn OAuth2 access token with posting permissions such as `w_member_social`.)  
  - `social_linkedin_author_urn`  
    (The LinkedIn URN for the posting account, e.g. `urn:li:person:XXXXXXXX`.)
- **How to Obtain:**  
  Set up a LinkedIn Developer app with the required permissions and complete the OAuth flow to obtain an access token. Then, obtain the user’s URN (this may require an API call to `/me`).

### **4. Facebook**
- **Setting Keys:**  
  - `social_facebook_api_key`  
    (A Facebook Graph API access token with permissions to post to a user feed or a Facebook Page.)  
  - `social_facebook_page_id` (Optional)  
    (If you want to post as a Facebook Page, specify the Page ID here.)
- **How to Obtain:**  
  Create a Facebook Developer app, set up the necessary permissions (e.g. `publish_to_groups`, `pages_manage_posts`), and generate an access token for your user or page.

---

### **Seeder for Social Credentials**

Hudsyn includes a seeder that will insert these keys into your settings table with empty values (so you can fill them in via the admin interface). To run it, use:

```bash
php artisan db:seed --class=SocialCredentialsSeeder
```

This will create entries for the following keys in your `hud_settings` table:
- `social_instagram_api_key`
- `social_x_api_key`
- `social_linkedin_api_key`
- `social_linkedin_author_urn`
- `social_facebook_api_key`
- `social_facebook_page_id`

**Note:** Make sure you update these values with your actual API credentials before attempting to post to social media.

---

## How Social Posts Works

When you create a social post in Hudsyn, the system will:
- Retrieve the necessary API keys from the settings using the keys listed above.
- Use these keys to authenticate API calls to each social media platform.
- Post the content (text, photo/video, and an optional tiny URL) to each platform.  
- Log any errors encountered so that you can manually retry failed posts if needed.

This design ensures that Hudsyn can seamlessly integrate with Twitter, Instagram, LinkedIn, and Facebook without interfering with your main Laravel application’s authentication.

Below is an updated section you can add to your README.md under the "Installation" or "Usage" section to instruct users on setting up a cron job for scheduled social posts:

---

## Scheduling Social Posts with Cron

To ensure that scheduled social posts are processed and published at the appropriate time, you need to set up a cron job that sends a GET request to Hudsyn's cron route. This route, `/hudsyn/cron`, will handle all scheduled posting tasks.

### **Setting Up a Cron Job**

1. **Determine Your Domain:**  
   Replace `yourdomain.com` with your actual domain name (include `https://` if your site uses SSL).

2. **Create the Cron Entry:**  
   Open your server's crontab by running:
   ```bash
   crontab -e
   ```
   Then add the following line to execute the cron route every minute (adjust frequency as needed):

   ```bash
   * * * * * curl -s https://yourdomain.com/hudsyn/cron >/dev/null 2>&1
   ```

   - This cron job uses `curl` to silently (with `-s`) call the `/hudsyn/cron` route every minute.
   - The output is redirected to `/dev/null` to prevent emails or log clutter.

3. **Verify the Cron Job:**  
   After saving your crontab, confirm that your scheduled posts are being processed by checking your application logs or by viewing the status of your social posts in Hudsyn’s admin panel.

### **Notes:**

- **HTTPS:**  
  If your application uses HTTPS, ensure that the URL in your cron job starts with `https://`.

- **Frequency:**  
  Adjust the cron frequency as needed. Running every minute is recommended for testing or high-frequency scheduling, but you might use every 5 or 10 minutes in production.

- **Firewall/Access Restrictions:**  
  Ensure that your server allows inbound requests from cron (or from the IP address that runs the cron job) and that no security settings (like IP restrictions) block the request.

By setting up this cron job, your Hudsyn installation will automatically process and publish scheduled social posts without manual intervention.

---

## Contributing

Contributions are welcome! Please fork the repository, make your changes, and submit a pull request. For any major changes, please open an issue first to discuss your ideas.

## License

This project is open-sourced under the [MIT license](LICENSE).

---

Happy coding and enjoy using Hudsyn in your Laravel projects!
```
