# APS Advertising Website

A professional advertising agency website with full backend integration, content management system, and responsive design.

## 🚀 Features

- **Modern Responsive Design** - Beautiful red/black theme with smooth animations
- **Backend API** - Express.js with MongoDB for data persistence
- **Admin Panel** - Full CMS for managing content, feedback, and contacts
- **Contact Forms** - Integrated with backend API
- **Feedback System** - Customer reviews with rating system
- **Multi-language Support** - English and Hindi
- **Portfolio Showcase** - Audio and video demos
- **Partners Section** - Showcase business partners

## 📁 Project Structure

```
aps-advertising/
├── backend/
│   ├── controllers/       # API controllers
│   ├── middleware/        # Auth middleware
│   ├── models/           # MongoDB models
│   ├── routes/           # API routes
│   ├── server.js         # Express server
│   └── package.json      # Backend dependencies
├── public/
│   ├── admin/           # Admin panel
│   ├── female/          # Female voice demos
│   ├── male/            # Male voice demos
│   ├── img/             # Images and logos
│   └── js/              # JavaScript utilities
├── index.html           # Home page
├── about.html           # About page
├── contact.html         # Contact page
├── feedback.html        # Feedback page
├── demos.html           # Demos showcase
├── partners.html        # Partners page
└── README.md
```

## 🛠️ Setup Instructions

### Prerequisites

- PHP 7.4+ (installed and added to PATH)
- MySQL Database
- Web Server (Apache/Nginx) or PHP built-in server

### 1. Clone/Download the Project

```bash
cd aps-advertising
```

### 2. Database Setup

1. Open your MySQL client (phpMyAdmin, Workbench, or CLI).
2. Create a new database named `aps_advertising`.
3. Import the `database.sql` file provided in the project root.

```bash
# Example if using CLI
mysql -u root -p aps_advertising < database.sql
```

### 3. Backend Configuration

1. Open `backend/config/constants.php`.
2. Update the database credentials if valid:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your MySQL password
define('DB_NAME', 'aps_advertising');
```

### 4. Start the Server

You can use the built-in PHP server for development:

```bash
# Run from the project root
php -S localhost:5000
```

The API will be available at `http://localhost:5000/backend/api`.
The website will be served at `http://localhost:5000/`.

### 5. Open the Website

- Access `http://localhost:5000/index.html` in your browser.

## 📱 Admin Panel

Access the admin panel at: `http://localhost:5000/public/admin/index.html`

Default admin credentials:
- Email: admin@apsadvertising.com
- Password: (set in your .env file)

### Admin Features:
- Dashboard with statistics
- Content management
- Feedback moderation
- Contact message management
- Partner management
- User management

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/login` - Admin login
- `GET /api/auth/me` - Get current user

### Contact
- `POST /api/contact` - Submit contact form
- `GET /api/contact` - Get all contacts (admin)
- `PUT /api/contact/:id/status` - Update contact status

### Feedback
- `POST /api/feedback` - Submit feedback
- `GET /api/feedback` - Get approved feedback
- `PUT /api/feedback/:id/like` - Like feedback
- `PUT /api/feedback/:id/approve` - Approve feedback (admin)

### Content
- `GET /api/content` - Get all content
- `GET /api/content/page/:page` - Get page content
- `POST /api/content` - Create content (admin)
- `PUT /api/content/:id` - Update content (admin)

### Health Check
- `GET /api/health` - Server health status

## 🎨 Customization

### Colors (CSS Variables)
```css
:root {
    --primary-red: #FF003C;
    --accent-red: #FF3366;
    --primary-black: #000000;
    --dark-grey: #1A1A1A;
    --off-white: #F5F5F5;
}
```

### Adding New Pages
1. Create new HTML file in root
2. Copy header/footer structure from existing pages
3. Add to navigation in all pages

## 📞 Contact Information

- **Phone**: 7355576785
- **WhatsApp**: +91 73555 76785
- **Email**: apsadvertisingpr@gmail.com
- **Instagram**: @aps.advertising
- **YouTube**: @APS-Advertising

## 🔧 Troubleshooting

### MongoDB Connection Issues
- Ensure MongoDB is running
- Check MONGODB_URI in .env
- Verify network access for MongoDB Atlas

### CORS Issues
- Update `ALLOWED_ORIGINS` in .env
- Check server CORS configuration

### Forms Not Submitting
- Check browser console for errors
- Verify backend is running
- Check API_BASE_URL in frontend

## 📄 License

© 2024 APS Advertising. All Rights Reserved.

---

**Designed by Shivangi Chauhan**

