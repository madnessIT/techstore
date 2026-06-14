# TechStore Bolivia — Manual de Usuario

---

## SECCIÓN 1: Manual del Cliente (Tienda)

### 1.1 Navegación General

La tienda está organizada en secciones accesibles desde el **menú superior**:
- **Inicio** — Banner, productos destacados, categorías y ofertas
- **Categorías** — Menú desplegable con todas las categorías
- **Catálogo** — Todos los productos con filtros
- **Ofertas** — Productos con descuento

### 1.2 Buscar Productos

**Barra de búsqueda** (centro del menú):
1. Escribe el nombre, marca o modelo del producto
2. Aparece un **autocomplete** con sugerencias mientras escribes
3. Presiona Enter o el botón 🔍 para ver todos los resultados

### 1.3 Filtrar el Catálogo

En la página de catálogo, el **panel izquierdo** permite:
- **Filtrar por categoría** — Selecciona una categoría
- **Rango de precio** — Ingresa mínimo y máximo en Bs.
- **Marca** — Filtra por fabricante
- **Ordenar** — Por relevancia, precio, nombre o más nuevos

### 1.4 Ver Detalle de Producto

Al hacer clic en un producto:
- **Galería de imágenes** — Miniaturas al pie para navegar
- **Especificaciones técnicas** — Pestaña con todos los detalles
- **Stock disponible** — Indicador en tiempo real
- **Selector de cantidad** — Botones + / −
- **Agregar al carrito** — Agrega inmediatamente con notificación toast
- **Productos relacionados** — Al final de la página

### 1.5 Carrito de Compras

El **ícono del carrito** (esquina superior derecha) muestra la cantidad de items.

En la página del carrito (`/carrito`):
- Modifica cantidades con los botones + / −
- Elimina productos con el ícono 🗑️
- El **resumen** se actualiza automáticamente (subtotal, envío, total)
- **Envío gratis** en compras mayores a **Bs. 500**

### 1.6 Registro y Login

**Registro** (`/registro`):
1. Completa nombre, apellido, email y contraseña
2. Acepta los términos y condiciones
3. Haz clic en "Crear Cuenta Gratis"

**Login** (`/login`):
1. Ingresa tu email y contraseña
2. Opción "Mantener sesión iniciada"
3. En "Mi Cuenta" puedes ver tu perfil y pedidos

### 1.7 Proceso de Compra (Checkout)

1. **Ve al carrito** → Haz clic en "Proceder al Pago"
2. Si no estás logueado, serás redirigido al login
3. **Completa los datos de envío**: nombre, teléfono, dirección, ciudad
4. **Selecciona método de pago**: Efectivo, Transferencia o QR
5. Haz clic en **"Confirmar Pedido"**
6. Recibirás la página de confirmación con tu **número de orden**

### 1.8 Mi Cuenta

En `/mi-cuenta` puedes ver:
- **Tus datos personales** (nombre, email, ciudad)
- **Historial de pedidos** con estados:
  - 🟡 Pendiente → 🔵 Confirmado → 🔵 Procesando → 🟢 Enviado → 🟢 Entregado

---

## SECCIÓN 2: Manual del Administrador

### 2.1 Acceso al Panel

URL: `http://localhost/techstore/admin`

Usa las credenciales de administrador. El sistema tiene 3 roles:
| Rol | Permisos |
|---|---|
| **Vendedor** | Ver pedidos, catálogo y clientes |
| **Administrador** | Todo lo anterior + CRUD completo |
| **Super Admin** | Todo + gestión de usuarios admin |

### 2.2 Dashboard

Vista general con:
- **Ventas del día y del mes** en Bs.
- **Pedidos pendientes** (alerta en tiempo real)
- **Total de clientes** activos
- **Stock bajo** — Productos que necesitan reposición
- **Últimos pedidos** con estado
- **Top 5 productos** más vendidos

### 2.3 Gestión de Productos

Módulo: **Catálogo → Productos**

**Listar productos:**
- Busca por nombre, SKU o marca
- Filtra por categoría y estado
- Ve imagen, precio, stock y estado de cada producto

**Crear producto:**
1. Haz clic en "Nuevo Producto"
2. Completa: Nombre, Marca, Modelo, Descripción
3. Agrega **Especificaciones Técnicas** (clave → valor)
4. Configura: Categoría, SKU, Garantía, Estado (Activo/Destacado)
5. Ingresa **Precio** y opcionalmente **Precio de Oferta**
6. Define **Stock** y **Stock Mínimo** (para alertas)
7. Sube una imagen principal (JPG/PNG/WebP, máx. 5MB)
8. Haz clic en "Crear Producto"

**Editar producto:**
- Haz clic en el ícono ✏️ en la lista
- Modifica los campos necesarios → "Guardar Cambios"

**Eliminar producto:**
- Haz clic en 🗑️ → Confirma la acción
- El producto se marca como inactivo (no se borra definitivamente)

### 2.4 Gestión de Categorías

Módulo: **Catálogo → Categorías**

- **Crear categoría**: Nombre, Descripción, Icono Bootstrap (ej: `bi-laptop`), Orden
- **Editar/Eliminar**: Solo se puede eliminar si no tiene productos asociados
- El ícono se muestra en el menú y en las tarjetas de categoría

### 2.5 Gestión de Pedidos

Módulo: **Ventas → Pedidos**

**Listar pedidos:**
- Filtra por estado: Pendiente, Confirmado, Procesando, Enviado, Entregado, Cancelado
- Ve número de orden, cliente, total y fecha

**Ver detalle del pedido:**
- Lista completa de productos con cantidades y precios
- Datos del cliente y dirección de envío
- **Cambiar estado**: Selecciona el nuevo estado → "Actualizar Estado"

**Flujo recomendado:**
```
Pendiente → Confirmado → Procesando → Enviado → Entregado
```

### 2.6 Gestión de Clientes

Módulo: **Usuarios → Clientes**

- Ver lista de clientes registrados
- **Ver perfil**: Datos personales e historial de compras
- **Activar/Desactivar** cuenta de cliente

### 2.7 Gestión de Administradores

Módulo: **Usuarios → Administradores** *(Solo Admin y Super Admin)*

- Crear, editar y eliminar usuarios del panel
- Asignar roles: Vendedor, Administrador, Super Admin
- No puedes eliminarte a ti mismo

### 2.8 Reportes de Ventas

Módulo: **Ventas → Reportes**

Incluye:
- **Gráfico de barras**: Ventas mensuales en Bs. + número de pedidos (12 meses)
- **Gráfico de dona**: Ventas por categoría en porcentaje
- **Top 10 productos** más vendidos por unidades e ingresos
- **Clientes frecuentes**: Los que más han gastado
- **Resumen por estado**: Cuántos pedidos en cada estado

---

## Datos de Contacto para Soporte

- Email: info@techstore.bo
- WhatsApp: +591 71 234-567
- Horario: Lun-Vie 8:00-18:00

---

*TechStore Bolivia v1.0 — Manual de Usuario*
