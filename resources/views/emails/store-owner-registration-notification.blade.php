@component('mail::message')
# Nueva Solicitud de Registro - Dueño de Local

Hola Administrador,

Se ha registrado una nueva solicitud para ser dueño de un local en **ShoppingRio**.

## Detalles del Solicitante:

- **Nombre:** {{ $storeOwner->name }}
- **Email:** {{ $storeOwner->email }}
- **Local:** {{ $storeName }} (ID: {{ $storeId }})

## Acciones Requeridas:

Por favor, inicia sesión en el panel de administrador para revisar y aprobar o rechazar esta solicitud.

@component('mail::button', ['url' => route('admin.dashboard')])
Ir al Panel de Administración
@endcomponent

---

**ShoppingRio Admin**

@endcomponent
