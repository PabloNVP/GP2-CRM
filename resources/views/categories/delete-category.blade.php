<x-action-model
    title="Confirmar baja"
    :message="'Se dara de baja la categoria ' . $categoryName . '. Desea continuar?'"
    cancelMethod="cancelAction"
    confirmMethod="confirmAction"
    variant="danger"
/>