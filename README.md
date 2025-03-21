# Hudsyn – A Free Headless CMS for Laravel (dev project, pre-composer)

**Hudsyn** is a lightweight, headless content management system (CMS) designed specifically for Laravel projects. Named after my newborn daughter, Hudsyn was built to solve a common problem I encountered while developing awesome Laravel applications: the landing page (or main website) was often an afterthought, even though it’s crucial to deliver a fast, beautifully-rendered public web presence.

## Why Hudsyn?

- **Seamless Integration:**  
  Hudsyn is built entirely in Laravel. It lives alongside your internal tools, SaaS apps, or any other Laravel-based architecture without jeopardizing performance or adding bulky dependencies.

- **Performance:**  
  Hudsyn uses static file generation for pages, press releases, and blog posts. This approach delivers blazing-fast load times while still allowing easy content management via an admin panel.

- **Lightweight Alternative:**  
  Unlike bulky solutions such as WordPress or expensive commercial CMSs like Statamic, Hudsyn offers a free, open-source solution that is simple, modular, and designed specifically for the Laravel ecosystem.

- **Flexibility:**  
  Manage your pages, blog posts, press releases, custom routes, layouts, global settings, and file uploads (including an integrated image gallery for WYSIWYG editors). Everything is designed to be extended and customized to your needs.

## Key Features

- **Dashboard & Administration:**  
  An intuitive admin panel that allows you to manage users, pages, blog posts, press releases, custom routes, layouts, and settings—all within your Laravel project.

- **Static File Generation:**  
  Automatically generates static HTML files for your public content, ensuring fast page load times and low server overhead.

- **File Upload & Gallery:**  
  Upload files through a dedicated interface that shows thumbnails for image files and provides direct links to the filesystem. The gallery popup integrates with your WYSIWYG editor for easy image insertion.

- **WYSIWYG Editor Integration:**  
  Integrate CKEditor (or your favorite editor) with custom configuration for uploading and inserting images from your gallery.

- **Custom Template Placeholders:**  
  Use key/value pairs defined in settings to inject dynamic content into your pages via simple placeholders (e.g. `{{get:keyname}}`).
