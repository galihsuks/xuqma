import { AlertTriangle } from "lucide-react";
import { useQueryClient } from "@tanstack/react-query";
import { Button, Modal } from "../../../../components/ui";
import { useDeleteArticleMutation } from "../../../../api/article/articleQuery";
import { useApiFormError } from "../../../../hooks/useApiFormError";
import { useNotificationStore } from "../../../../store/notifStore";

interface ArticleDeleteTarget {
  id: string;
  title: string;
  slug: string;
}

interface ArticleDeleteModalProps {
  open: boolean;
  target: ArticleDeleteTarget | null;
  onClose: () => void;
}

export const ArticleDeleteModal = ({ open, target, onClose }: ArticleDeleteModalProps) => {
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { mutate: deleteArticleMutation, isPending: isDeleteArticlePending } = useDeleteArticleMutation();
  const { handleApiFormError } = useApiFormError({ logEvent: "article_delete_failed" });

  const onDelete = () => {
    if (!target) return;

    deleteArticleMutation(target.id, {
      onSuccess: (response) => {
        addToast(response.message, "success");
        void queryClient.invalidateQueries({ queryKey: ["article"] });
        onClose();
      },
      onError: (error) => {
        handleApiFormError(error, {
          article_id: target.id,
          article_slug: target.slug,
        });
      },
    });
  };

  return (
    <Modal
      open={open}
      onClose={onClose}
      title="Delete article"
      subtitle="Please confirm before removing this article."
      className="max-w-lg"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button type="button" variant="danger" loading={isDeleteArticlePending} onClick={onDelete}>
            Delete Article
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
              You are about to delete <span className="text-danger-700">{target?.title}</span>.
            </p>
            <p className="mt-1 text-sm text-dark-600">Slug: <span className="font-medium">{target?.slug}</span></p>
          </div>
        </div>
      </div>
    </Modal>
  );
};
