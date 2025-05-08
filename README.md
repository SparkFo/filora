# iPhone 16 Giveaway Landing Page

A multilingual (Arabic/English) landing page for iPhone 16 giveaway campaigns with admin panel.

## Features

- Responsive design for all devices
- Multilingual support (Arabic and English)
- Dynamic countdown timer that persists across page loads
- Dynamic comments section with random rotation
- Admin panel for managing site settings and comments
- SEO optimized

## Admin Access

You can access the admin panel in multiple ways:
1. Direct URL: `example.com/admin/`
2. Case-insensitive URL: `example.com/AdMiN`, `example.com/ADMIN`, etc.
3. Footer link: "Admin Login" link at the bottom of the page

Default admin credentials:
- Username: `admin`
- Password: `admin123`

## Installation

1. Upload all files to your web server
2. Make sure your server has PHP 7.0+ installed
3. Ensure the following files are writable by the web server:
   - `site-settings.json`
   - `comments.json`
4. Access the admin panel to configure your site

## Server Requirements

- PHP 7.0 or higher
- Apache with mod_rewrite enabled or Nginx/IIS with URL rewriting
- Write permissions for the web server on JSON files

## Customization

You can customize the following through the admin panel:
- Site name and description
- Offer link
- Countdown duration
- Hero section content
- About section content
- Features
- CTA section content
- Comments section title
- Footer content
- Social media links

## Languages

The site supports Arabic and English languages. Users can switch between languages using the language switcher in the navigation menu.

## Security

- Admin credentials should be changed after first login
- JSON files are protected from direct access
- Directory browsing is disabled
- Sensitive files are protected

## License

This project is licensed under the MIT License - see the LICENSE file for details.
