<x-action-model
    :title="$isDelete ? 'Confirmar eliminacion' : 'Confirmar reactivacion'"
    :message="'Se dara de ' . ($isDelete ? 'baja' : 'alta') . ' al cliente ' . $clientName . '. Desea continuar?'"
    cancelMethod="cancelAction"
    confirmMethod="confirmAction"
    :variant="$isDelete ? 'danger' : 'success'"
/>