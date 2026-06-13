import { AlertTriangle } from "lucide-react";
import { useQueryClient } from "@tanstack/react-query";
import { Button, Modal } from "../../../../components/ui";
import { useDeleteProductCategoryMutation } from "../../../../api/productCategory/productCategoryQuery";
import { useApiFormError } from "../../../../hooks/useApiFormError";
import { useNotificationStore } from "../../../../store/notifStore";

interface CategoryDeleteTarget {
  id: string;
  name: string;
  slug: string;
}

interface CategoryDeleteModalProps {
  open: boolean;
  target: CategoryDeleteTarget | null;
  onClose: () => void;
}

export const CategoryDeleteModal = ({
  open,
  target,
  onClose,
}: CategoryDeleteModalProps) => {
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { mutate: deleteCategoryMutation, isPending: isDeleteCategoryPending } =
    useDeleteProductCategoryMutation();
  const { handleApiFormError } = useApiFormError({ logEvent: "category_delete_failed" });

  const onDelete = () => {
    if (!target) {
      return;
    }

    deleteCategoryMutation(target.id, {
      onSuccess: (response) => {
        addToast(response.message, "success");
        void queryClient.invalidateQueries({ queryKey: ["product-category"] });
        onClose();
      },
      onError: (error) => {
        handleApiFormError(error, {
          category_id: target.id,
          category_slug: target.slug,
        });
      },
    });
  };

  return (
    <Modal
      open={open}
      onClose={onClose}
      title="Delete category"
      subtitle="Please confirm before removing this category."
      className="max-w-lg"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button
            type="button"
            variant="danger"
            loading={isDeleteCategoryPending}
            onClick={onDelete}
          >
            Delete Category
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
            <p className="mt-1 text-sm text-dark-600">
              Slug: <span className="font-medium">{target?.slug}</span>
            </p>
            <p className="mt-2 text-sm text-dark-500">
              Categories that are still used by products will be rejected by the backend.
            </p>
          </div>
        </div>
      </div>
    </Modal>
  );
};
