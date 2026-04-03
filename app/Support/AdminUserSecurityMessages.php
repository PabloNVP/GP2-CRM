<?php

/**
 * Este archivo contiene mensajes de seguridad relacionados con la gestión de usuarios en el panel de administración.
 * Estos mensajes se utilizan para informar a los administradores sobre errores o restricciones al intentar cambiar roles o estados de los usuarios, especialmente en situaciones críticas como la gestión del último administrador activo.     
 * @ToDo: Considerar en el futuro implementar un sistema de traducción para soportar múltiples idiomas y mejorar la mantenibilidad de los mensajes.
*/

namespace App\Support;

final class AdminUserSecurityMessages
{
    public const INVALID_ROLE = 'El rol seleccionado no es valido.';
    public const INVALID_STATE = 'El estado seleccionado no es valido.';
    public const USER_NOT_FOUND = 'El usuario seleccionado no existe.';

    public const ROLE_ALREADY_ASSIGNED = 'El usuario ya tiene el rol seleccionado.';
    public const STATE_ALREADY_ASSIGNED = 'El usuario ya tiene el estado seleccionado.';

    public const LAST_ACTIVE_ADMIN_ROLE_CHANGE = 'No se puede quitar el rol administrador al ultimo admin activo.';
    public const LAST_ACTIVE_ADMIN_DEACTIVATION = 'No se puede desactivar al ultimo admin activo.';
    public const SELF_DEACTIVATION_FORBIDDEN = 'No puede desactivarse a si mismo.';

    public const ROLE_PENDING_ACTION_MISSING = 'No se encontro una accion de rol pendiente.';
    public const STATE_PENDING_ACTION_MISSING = 'No se encontro una accion de estado pendiente.';

    public const ROLE_UPDATED_SUCCESS = 'Rol de usuario actualizado correctamente.';
    public const STATE_UPDATED_SUCCESS = 'Estado de usuario actualizado correctamente.';
}
