import { AlertTriangle } from "lucide-react";
import { useQueryClient } from "@tanstack/react-query";
import { Button, Modal } from "../../../../components/ui";
import { useDeleteProductMutation } from "../../../../api/product/productQuery";
import { useApiFormError } from "../../../../hooks/useApiFormError";
import { useNotificationStore } from "../../../../store/notifStore";

interface ProductDeleteTarget {
  id: string;
  name: string;
  sku: string;
}

interface ProductDeleteModalProps {
  open: boolean;
  target: ProductDeleteTarget | null;
  onClose: () => void;
}

export const ProductDeleteModal = ({ open, target, onClose }: ProductDeleteModalProps) => {
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { mutate: deleteProductMutation, isPending: isDeleteProductPending } = useDeleteProductMutation();
  const { handleApiFormError } = useApiFormError({ logEvent: "product_delete_failed" });

  const onDelete = () => {
    if (!target) return;

    deleteProductMutation(target.id, {
      onSuccess: (response) => {
        addToast(response.message, "success");
        void queryClient.invalidateQueries({ queryKey: ["product"] });
        onClose();
      },
      onError: (error) => {
        handleApiFormError(error, {
          product_id: target.id,
          product_sku: target.sku,
        });
      },
    });
  };

  return (
    <Modal
      open={open}
      onClose={onClose}
      title="Delete product"
      subtitle="Please confirm before removing this product."
      className="max-w-lg"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button type="button" variant="danger" loading={isDeleteProductPending} onClick={onDelete}>
            Delete Product
          </Button>
        </div>
      }
    >
      <div className="rounded-2xl border border-warning-200 bg-warning-50 p-4">
        <div className="flex items-start gap-3">
          <div className="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-warning-100 text-warning-700">
            <AlertTriangle className="h-5 w-5" />
          </div>
          <div>
            <p className="text-sm font-semibold text-dark-900">
              You are about to delete <span className="text-danger-700">{target?.name}</span>.
            </p>
            <p className="mt-1 text-sm text-dark-600">SKU: <span className="font-medium">{target?.sku}</span></p>
          </div>
        </div>
      </div>
    </Modal>
  );
};
