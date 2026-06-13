# Another Saas Starter Kit

Launch your next SaaS faster than ever. A production-ready, feature-rich boilerplate built on a solid architectural foundation.

## ✨ Core Features

Save weeks of development time with essential SaaS features already built, tested, and beautifully integrated.

- 🔐 **Complete Authentication**: Secure, ready-to-use authentication system powered by Laravel Fortify (Two-Factor Auth, registration, login, password reset, and email verification).
- 👥 **Team Management**: Team-centric by design. Users can create teams, invite members via email, and transfer team ownership seamlessly.
- 🛡️ **Advanced Permissions**: Fine-grained permission management based on roles and subscription plans.
- 💳 **Subscription Billing**: Stripe Cashier integration out-of-the-box. Manage plans, subscriptions, and view invoices with ease.
- 🌐 **Socialite Logins**: Allow users to register and log in effortlessly using their Google and GitHub accounts.
- ⚙️ **Admin Panel**: A dedicated admin panel provides an overview of all user subscriptions for straightforward management.
- 🎨 **Dynamic Theming**: Users can personalize their experience by choosing primary, secondary, and neutral colors for the UI.
- 📄 **Static Markdown Pages**: Easily create and manage static content like Privacy Policy or Terms of Service pages using simple Markdown files.
- 💬 **Flash Message Toasts**: Provide clear user feedback with beautifully rendered toast notifications.

## 🏛️ Architectural Pillars

- **Robust & Modern Backend**: Built on the latest **Laravel 13** with **PHP 8.5**. We enforce strict typing and high code quality standards for a robust foundation.
- **Clean & Scalable Architecture**: Logic is neatly organized for scalability. Data integrity is guaranteed by strongly-typed DTOs via `Spatie/Laravel-Data`.
- **SEO-Ready with SSR**: **Server-Side Rendering** is ready through Inertia and Vite.
  Nuxt UI powers the component layer.

## 🛠️ Tech Stack

- **Backend**: Laravel 13 on PHP 8.5
- **Frontend**: Vue 3
- **UI Framework**: Nuxt UI 4
- **Backend/Frontend Bridge**: Inertia.js
- **Build Tooling**: Vite with Inertia SSR support
- **Billing**: Laravel Cashier (Stripe)
- **Authentication**: Laravel Fortify
- **Deployment**: Ready for production.

## Local Commands

- Install/setup: `composer run setup`
- Dev server: `composer run dev`
- Build: `npm run build`
- SSR build: `npm run build:ssr`
- Backend tests: `php artisan test --compact`
- Frontend tests: `npm run test:frontend`
- Typecheck: `npm run types:check`
- Lint: `npm run lint:check`
- Format check: `npm run format:check`
- Static analysis: `vendor/bin/phpstan analyse --memory-limit=2G`

## 🚀 Getting Started

For full installation instructions and configuration details, please visit our official documentation:

**[doc.saasterkit.com](https://doc.saasterkit.com)**

The documentation provides a complete guide to getting your project up and running.

## 📄 License

This project is open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.
