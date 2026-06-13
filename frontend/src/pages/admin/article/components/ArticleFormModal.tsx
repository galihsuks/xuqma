import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useQueryClient } from "@tanstack/react-query";
import { Button, FormInput, Modal } from "../../../../components/ui";
import {
  useArticleDetailQuery,
  useCreateArticleMutation,
  useUpdateArticleMutation,
} from "../../../../api/article/articleQuery";
import { queryKeys } from "../../../../api/queryKeys";
import { useApiFormError } from "../../../../hooks/useApiFormError";
import { useNotificationStore } from "../../../../store/notifStore";
import { articleFormSchema, type ArticleFormSchemaType } from "../schema/ArticleFormSchema";

interface ArticleFormModalProps {
  open: boolean;
  mode: "create" | "edit";
  articleId?: string | null;
  onClose: () => void;
}

const articleStatusOptions = [
  { value: "draft", label: "Draft" },
  { value: "published", label: "Published" },
];

export const ArticleFormModal = ({ open, mode, articleId, onClose }: ArticleFormModalProps) => {
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { mutate: createArticleMutation, isPending: isCreateArticlePending } =
    useCreateArticleMutation();
  const { mutate: updateArticleMutation, isPending: isUpdateArticlePending } =
    useUpdateArticleMutation();
  const { handleApiFormError } = useApiFormError({ logEvent: "article_form_submit_failed" });
  const { data: articleDetailData, isPending: isArticleDetailPending } = useArticleDetailQuery(
    mode === "edit" ? (articleId ?? "") : "",
  );

  const { control, handleSubmit, reset } = useForm<ArticleFormSchemaType>({
    resolver: zodResolver(articleFormSchema),
    defaultValues: {
      title: "",
      slug: "",
      category: "",
      excerpt: "",
      content: "",
      author_name: "",
      status: "draft",
      published_at: "",
      read_time: "",
    },
  });

  useEffect(() => {
    if (!open) return;

    if (mode === "create") {
      reset({
        title: "",
        slug: "",
        category: "",
        excerpt: "",
        content: "",
        author_name: "",
        status: "draft",
        published_at: "",
        read_time: "",
      });
      return;
    }

    if (!articleDetailData?.data) return;

    reset({
      title: articleDetailData.data.title,
      slug: articleDetailData.data.slug,
      category: articleDetailData.data.category,
      excerpt: articleDetailData.data.excerpt ?? "",
      content: articleDetailData.data.content ?? "",
      author_name: articleDetailData.data.author_name ?? "",
      status: articleDetailData.data.status,
      published_at: articleDetailData.data.published_at ?? "",
      read_time: articleDetailData.data.read_time ?? "",
    });
  }, [articleDetailData?.data, mode, open, reset]);

  const isSubmitPending = isCreateArticlePending || isUpdateArticlePending;

  const onSubmit = (values: ArticleFormSchemaType) => {
    const payload = {
      title: values.title,
      slug: values.slug,
      category: values.category,
      excerpt: values.excerpt || null,
      content: values.content || null,
      author_name: values.author_name || null,
      status: values.status,
      published_at: values.published_at || null,
      read_time: values.read_time || null,
    };

    if (mode === "create") {
      createArticleMutation(payload, {
        onSuccess: (response) => {
          addToast(response.message, "success");
          void queryClient.invalidateQueries({ queryKey: ["article"] });
          onClose();
        },
        onError: (error) => {
          handleApiFormError(error, {
            form_mode: mode,
            article_slug: values.slug,
          });
        },
      });
      return;
    }

    if (!articleId) return;

    updateArticleMutation(
      { id: articleId, payload },
      {
        onSuccess: (response) => {
          addToast(response.message, "success");
          void queryClient.invalidateQueries({ queryKey: ["article"] });
          void queryClient.invalidateQueries({ queryKey: queryKeys.article.detail(articleId) });
          onClose();
        },
        onError: (error) => {
          handleApiFormError(error, {
            form_mode: mode,
            article_id: articleId,
            article_slug: values.slug,
          });
        },
      },
    );
  };

  return (
    <Modal
      open={open}
      onClose={onClose}
      title={mode === "create" ? "Create article" : "Edit article"}
      subtitle={
        mode === "create"
          ? "Add a new SEO-supporting article for the storefront."
          : "Update selected article content and publishing metadata."
      }
      className="max-w-4xl"
      footer={
        <div className="flex justify-end gap-3">
          <Button type="button" variant="light-outline" onClick={onClose}>
            Cancel
          </Button>
          <Button
            type="button"
            buttonType="submit"
            loading={isSubmitPending}
            onClick={handleSubmit(onSubmit)}
          >
            {mode === "create" ? "Save Article" : "Update Article"}
          </Button>
        </div>
      }
    >
      {mode === "edit" && isArticleDetailPending ? (
        <div className="grid gap-3 md:grid-cols-2">
          {Array.from({ length: 8 }).map((_, index) => (
            <div key={index} className="h-11 rounded-xl bg-light-100" />
          ))}
        </div>
      ) : (
        <form className="grid gap-4 md:grid-cols-2" onSubmit={handleSubmit(onSubmit)}>
          <FormInput
            control={control}
            name="title"
            label="Title"
            placeholder="How to choose a monitor..."
            className="md:col-span-2"
          />
          <FormInput
            control={control}
            name="slug"
            label="Slug"
            placeholder="how-to-pick-a-monitor..."
            className="md:col-span-2"
          />
          <FormInput
            control={control}
            name="category"
            label="Category"
            placeholder="Buying Guide"
          />
          <FormInput
            control={control}
            name="author_name"
            label="Author"
            placeholder="Editorial Team"
          />
          <FormInput
            control={control}
            name="status"
            type="dropdown"
            label="Status"
            placeholder="Select status"
            dropdownOptions={articleStatusOptions}
          />
          <FormInput
            control={control}
            name="published_at"
            type="datetime"
            label="Published At"
            placeholder="Select publish date and time"
          />
          <FormInput
            control={control}
            name="read_time"
            label="Read Time"
            placeholder="5 min read"
          />
          <FormInput
            control={control}
            name="excerpt"
            label="Excerpt"
            placeholder="Short summary for storefront article card"
            className="md:col-span-2"
          />
          <FormInput
            control={control}
            name="content"
            type="textarea"
            label="Content"
            placeholder="Article content"
            className="md:col-span-2"
          />
        </form>
      )}
    </Modal>
  );
};
