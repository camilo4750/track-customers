# Track Clients

Sistema de gestión de clientes desarrollado con Laravel y Vue.js, implementando arquitectura hexagonal y mejores prácticas de desarrollo moderno.

## 📋 Tabla de Contenidos

- [Características](#características)
- [Stack Tecnológico](#stack-tecnológico)
- [Requisitos Previos](#requisitos-previos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Uso](#uso)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Comandos Útiles](#comandos-útiles)
- [Documentación](#documentación)
- [Desarrollo](#desarrollo)
- [Testing](#testing)
- [Licencia](#licencia)

## ✨ Características

- 🔐 **Autenticación JWT** - Sistema de autenticación basado en tokens JWT
- 👥 **Gestión de Usuarios** - CRUD completo de usuarios con roles y permisos
- 🛡️ **Roles y Permisos** - Sistema de control de acceso basado en roles (RBAC) usando Spatie Laravel Permission
- 📊 **Dashboard Interactivo** - Panel de control con KPIs y métricas
- 🎨 **UI Moderna** - Interfaz construida con PrimeVue 4 y Tailwind CSS 4
- 🌙 **Dark Mode** - Soporte para modo oscuro con persistencia
- 📱 **Responsive Design** - Diseño adaptable a diferentes dispositivos
- 📄 **Tablas Avanzadas** - Componente de tabla con filtros, exportación y búsqueda
- 🔔 **Sistema de Modales** - Sistema global de modales reutilizables
- 📚 **Documentación API** - Documentación Swagger/OpenAPI integrada
- 🏗️ **Arquitectura Hexagonal** - Separación clara de responsabilidades siguiendo principios SOLID

## 🛠️ Stack Tecnológico

### Backend
- **Laravel 12** - Framework PHP
- **PHP 8.2+** - Lenguaje de programación
- **PostgreSQL 14** - Base de datos
- **JWT Auth** (tymon/jwt-auth) - Autenticación basada en tokens
- **Spatie Laravel Permission** - Gestión de roles y permisos
- **L5-Swagger** - Documentación API

### Frontend
- **Vue 3.5+** - Framework JavaScript (Composition API)
- **Inertia.js 2.0** - SPA framework
- **PrimeVue 4.5+** - Componentes UI (Tema Aura)
- **Tailwind CSS 4.0** - Framework CSS utility-first
- **Vite 7+** - Build tool
- **Axios 1.11+** - Cliente HTTP
- **Ziggy 2.6+** - Rutas nombradas de Laravel en JavaScript
- **FontAwesome 7.1+** - Iconos
- **js-cookie** - Manejo de cookies

## 📦 Requisitos Previos

- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18.x
- npm >= 9.x
- PostgreSQL >= 14
- Git

## 💻 Uso

### Acceso a la aplicación

- **Frontend**: http://localhost:8000
- **Documentación API**: http://localhost:8000/api/documentation

### Usuarios por defecto

Después de ejecutar las migraciones, se crean roles y permisos por defecto. Consulta los seeders para más información sobre usuarios de prueba.

## 📁 Estructura del Proyecto

```
track-clients/
├── app/                          # Laravel app (mínimo)
│   ├── Models/                   # Modelos Eloquent
│   └── Http/                     # Controllers y Middleware base
├── src/Internal/                 # Arquitectura hexagonal/DDD
│   ├── Auth/                     # Módulo de autenticación JWT
│   │   ├── Application/          # Casos de uso
│   │   ├── Infrastructure/       # Implementaciones
│   │   └── Test/                 # Tests del módulo
│   ├── Users/                    # Módulo de usuarios
│   │   ├── Application/          # Handlers y casos de uso
│   │   ├── Infrastructure/       # Controllers, Repositories
│   │   └── Test/                 # Tests del módulo
│   └── Shared/                   # Código compartido
│       ├── Entity/               # Entidades base
│       ├── Exceptions/           # Excepciones personalizadas
│       └── Http/                 # Utilidades HTTP
├── resources/
│   ├── js/
│   │   ├── app.js                # Configuración principal Vue
│   │   ├── bootstrap.js          # Configuración Axios e interceptores
│   │   ├── components/           # Componentes globales
│   │   │   ├── AdvancedDataTable.vue
│   │   │   └── GlobalModal.vue
│   │   ├── composables/          # Composables Vue
│   │   │   ├── useAuth.js
│   │   │   ├── useModal.js
│   │   │   ├── useTheme.js
│   │   │   └── useToast.js
│   │   ├── layouts/              # Layouts de la aplicación
│   │   │   └── BaseLayout.vue
│   │   ├── modules/              # Módulos frontend
│   │   │   ├── auth/
│   │   │   ├── dashboard/
│   │   │   ├── home/
│   │   │   └── users/
│   │   └── utils/                # Utilidades
│   │       ├── authGuard.js
│   │       └── formatters.js
│   └── css/
│       └── app.css               # Estilos globales
├── routes/
│   ├── web.php                   # Rutas web (Inertia)
│   └── api.php                   # Rutas API
├── database/
│   ├── migrations/               # Migraciones de base de datos
│   └── seeders/                  # Seeders
├── config/                       # Configuración Laravel
├── docs/                         # Documentación del proyecto
└── tests/                        # Tests automatizados
```

## 🐳 Docker (Opcional)

El proyecto incluye configuración Docker para desarrollo en `.devops/docker/develop/`:

---

**Desarrollado con ❤️ usando Laravel y Vue.js**
