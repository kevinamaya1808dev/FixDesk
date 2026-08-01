# FixBound - Sistema de Control y Seguimiento de Reparaciones

**FixBound** es una plataforma SaaS Multi-Tenant diseñada para optimizar la gestión operativa de talleres de reparación técnica (dispositivos electrónicos, computadoras, smartphones, etc.). Permite registrar equipos, dar seguimiento en tiempo real con tiempos de resolución (SLA), gestionar clientes y técnicos, y ofrecer a los usuarios finales un portal público de rastreo transparente.

---

## Características Principales

- **Multi-Tenancy (SaaS):** Cada taller opera con sus datos aislados (`taller_id`) y su propio código público de identificación.
- **Rastreo Público de Órdenes:** Los clientes pueden consultar el estado de su equipo introduciendo su **Folio** en `/rastrear` o mediante un token único directo, sin necesidad de crear una cuenta.
- **Niveles de Servicio (SLA) y Retardos:** Clasificación de reparaciones en 5 niveles con tiempos máximos de entrega. Monitoreo automático de retardos y notificaciones al administrador.
- **Chat Integrado Técnico-Cliente:** Canal de comunicación directo dentro de la orden para aclaraciones y actualizaciones.
- **Gestión de Roles (Admin y Técnico):** El administrador administra el equipo técnico, asigna órdenes y supervisa el centro de mando.
- **Planes y Suscripciones:** Soporte para planes (Básico, Pro, Taller Plus) con límites de técnicos y habilitación de clientes mayoristas.

---

## Stack Tecnológico

| Capa | Tecnología |
|---|---|
| **Backend** | PHP `^8.2`, Laravel `^12.0` |
| **Frontend** | Blade, Tailwind CSS `^3.1`, Alpine.js `^3.4`, Vite `^7.0` |
| **Base de Datos** | MySQL 8+ / MariaDB |
| **Autenticación** | Laravel Breeze |
| **Colas y Tareas** | Laravel Queues & Scheduler |

---

## Instalación Local

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/GarbageBoy8/Seguimiento-de-Reparaciones.git
   cd Seguimiento-de-Reparaciones
   ```

2. **Instalar dependencias:**
   ```bash
   composer install
   npm install
   ```

3. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Ejecutar migraciones y datos de prueba:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Iniciar el servidor de desarrollo:**
   ```bash
   composer run dev
   ```

### Credenciales Demo (Sembradas)
- **Admin:** `admin@fixbound.test` | **Contraseña:** `password`
- **Técnico:** `tecnico@fixbound.test` | **Contraseña:** `password`

---

## Despliegue en Producción (Docker / VPS / Coolify)

FixBound incluye soporte para Docker mediante `Dockerfile` multi-stage y `docker-compose.prod.yml` (App, Worker, Scheduler y MySQL). Es totalmente compatible con plataformas de despliegue como Coolify o servidores VPS en DigitalOcean.

Para consultar la guía detallada de despliegue, configuración en Coolify y variables de entorno en producción, revisa el archivo [technical_transfer.md](technical_transfer.md).

---

## Comandos CLI Administrativos

El sistema incluye comandos Artisan personalizados para administrar talleres, cambiar planes de suscripción y gestionar accesos directamente desde la terminal del servidor o consola.

Para consultar la lista completa de comandos CLI disponibles y sus parámetros, revisa la sección correspondiente en [technical_transfer.md](technical_transfer.md).

---

## Licencia

Este proyecto es de uso privado e interno.
