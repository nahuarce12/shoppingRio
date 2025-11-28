@component('mail::message')
# Nueva Promoción Pendiente de Aprobación

Hola Administrador,

Se ha creado una nueva promoción que requiere tu aprobación en **ShoppingRio**.

## Detalles de la Promoción:

- **Descripción:** {{ $promotion->description }}
- **Local:** {{ $storeName }}
- **Dueño:** {{ $storeOwner }}
- **Categoría de Cliente:** {{ $promotion->client_category }}
- **Vigencia:** {{ $promotion->start_date->format('d/m/Y') }} al {{ $promotion->end_date->format('d/m/Y') }}
- **Estado:** Pendiente de Aprobación

## Acciones Requeridas:

Por favor, inicia sesión en el panel de administrador para revisar y aprobar o rechazar esta promoción.

@component('mail::button', ['url' => route('admin.dashboard')])
Ir al Panel de Administración
@endcomponent

---

**ShoppingRio Admin**

@endcomponent
